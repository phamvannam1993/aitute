<?php

namespace App\Models;

use App\Exceptions\UsageLimitExceededException;
use App\Helper\Helpers;
use App\Http\Controllers\Client\CreditController;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use App\Notifications\CustomResetPasswordNotification;
use App\Models\Traits\LikeScope;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use GuzzleHttp\Client;
class User extends Authenticatable
{
    use HasFactory, Notifiable, LikeScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'credit',
        'vip_expired_at',
        'clone_voice',
        'my_ai_image_id',
        'email_verified_at',
        'phone',
        'ai_doctor_status',
        'ai_doctor_mode',
        'address',
        'is_gift',
        'tone_config',
        'dify_dataset_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function decrementUsage($assistantType)
    {
        DB::transaction(function () use ($assistantType) {
            if (!$this->is_vip) {
                throw new UsageLimitExceededException("Tài khoản của bạn chưa được nâng cấp để sử dụng chức năng này. Để được sử dụng mọi tính năng, vui lòng nâng cấp gói tháng.");
            }
            if ($this->credit <= 0) {
                throw new UsageLimitExceededException("Hết số lần dùng thử và credit không đủ.");
            }
        });
    }

    public function assistantFavorites()
    {
        return $this->hasMany(AssistantFavorites::class, 'user_id', 'id');
    }

    public function favorites()
    {
        return $this->hasMany(AssistantFavorites::class, 'user_id', 'id');
    }

    public function facebooks(): MorphToMany
    {
        return $this->morphToMany(Facebook::class, 'facebookable');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_users', 'user_id', 'category_id');
    }

    public function socialPosts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }
    public function voiceTypes()
    {
        return $this->hasMany(VoiceType::class);
    }
    // public function facebookables()
    // {
    //     return $this->hasOne(Facebookable::class, 'facebookable_id');
    // }
    public function affKeys()
    {
        return $this->hasMany(AffKey::class, 'user_id', 'id');
    }
    public function ofSales(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'user_sale', 'user_id', 'sale_id');
    }

    public function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    // public function bankAccount()
    // {
    //     return $this->hasOne(UserBankAccount::class, 'user_id', 'id');
    // }
    public function facebookConfigs()
    {
        return $this->hasMany(FacebookConfigs::class);
    }
    protected function isVip(): Attribute
    {
        return Attribute::make(
            get: fn() => !is_null($this->vip_expired_at) && Carbon::now()->lt($this->vip_expired_at),
        );
    }

    public function chargeCredit($type, $model = null, $number_result = null, $action = null, $text = null, $screenId = 0, $featureId = 0, $totalInputTokens = 0, $totalOutputTokens = 0)
    {
        try {
            $request = new Request();
            $request->merge([
                'type' => $type,
                'model' => $model,
                'number_result' => $number_result,
                'action' => $action,
                'text' => $text,
                'feature_id' => $featureId,
                'screen_id' => $screenId,
                'totalInputTokens' => $totalInputTokens,
                'totalOutputTokens' => $totalOutputTokens,
                'user' => $this,

            ]);
            DB::transaction(function () use ($request) {
                $creditController = new CreditController();
                $result = $creditController->check_credit($request);
                if ($result->getData()->success) {
                    $total_price = $result->getData()->data->total_price;
                    if ($this->credit >= ceil($total_price)) {

                        $this->decrement('credit', $total_price);
                        $request["credit"] = $total_price;
                        $creditController->insertHistory($request);
                    } else {
                        throw new UsageLimitExceededException("Không đủ credit: " . json_encode($request->all()));
                    }
                } else {
                    throw new UsageLimitExceededException($result->getData()->message . ': ' . json_encode($request->all()));
                }
            });

            return response()->json([
                'success' => true,
            ]);
        } catch (UsageLimitExceededException $e) {
            report($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function package()
    {
        return $this->hasOne(AffKey::class, 'user_id', 'id')
            ->join('config_aff', 'aff_keys.config_aff_id', '=', 'config_aff.id')
            ->select('config_aff.name as package_name', 'aff_keys.user_id', 'config_aff.id as package_id', 'config_aff.code as package_code')
            ->orderBy('aff_keys.created_at', 'desc');
    }

    public function myAiImages()
    {
        return $this->hasMany(MyAIImage::class, 'user_id');
    }

    public function affKey()
    {
        return $this->hasOne(AffKey::class);
    }

    public function getDifyDatasetName()
    {
        return config('constant.APP_ENV') . '_' . config('app.name') . '_agent_' . $this->id;
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id', 'id');
    }

    public function chatTrainingDocuments()
    {
        return $this->hasMany(ChatTrainingDocument::class, 'user_id', 'id');
    }

    public function getDifyDatasetId()
    {
        try {
            if (!empty($this->dify_dataset_id)) {
                return $this->dify_dataset_id;
            }

            $client = new Client();
            $params = [
                'name' => $this->getDifyDatasetName(),
                'permission' => 'all_team_members',
            ];
            $response = $client->post(config('services.dify.base_url') . '/v1/datasets', [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.dify.api_access_key'),
                ],
                'json' => $params
            ]);

            $difyDatasetId = data_get(json_decode($response->getBody(), true), 'id');

            $this->dify_dataset_id = $difyDatasetId;

            $this->save();

            return $difyDatasetId;
        } catch (\Exception $e) {
            Helpers::logException($e);
            return null;
        }
    }

    public function getDifyAppChatKey()
    {
        return config('services.dify_chat.api_key');
    }
}
