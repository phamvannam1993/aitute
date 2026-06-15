<?php

namespace App\Http\Controllers\Client;

use App\Constants\AIModel;
use App\Helper\Helpers;
use App\Http\Controllers\Controller;
use App\Models\AffKey;
use App\Models\User;
use App\Services\DocumentReaderService;
use App\Services\TokenCounter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditController extends Controller
{
    public function check_credit(Request $request) {
        $model_type = $request->get('type') ?? '';
        $model_code = $request->get('model') ?? '';
        $number_result = $request->get('number_result') ?? 30;
        $action = $request->get('action') ?? '';
        $text = $request->get('text') ?? '';
        $totalInputTokens = $request->get('totalInputTokens') ?? '';
        $totalOutputTokens = $request->get('totalOutputTokens') ?? '';
        $user = $request->get('user') ?? new User();
        $user = Helpers::runInCronJobs() ? $user : Auth::user();
        $tokenCounter = new TokenCounter();
        try {
            $total_price = 0;
            if (!in_array($model_code , ['d-id'])) {
                $model_code = AIModel::getModel($model_code);
            }
            switch ($model_type) {
                case 'text':
                    if (!in_array($action,['ai-chat', 'ai-text'])) {
                        $text = $this->get_text($request);
                    }
                    $input_tokens = $tokenCounter->countTokens($text, $model_code);
                    $output_tokens = $tokenCounter->estimateOutputTokens($input_tokens, $model_code, 'translation');
                    $total_price = Helpers::calculateCredit($model_code, $input_tokens, $output_tokens, 0, 0);
                    break;
                case 'business':
                    $total_price = Helpers::calculateCredit('claude-3-5-sonnet-20240620', $totalInputTokens, $totalOutputTokens, 0, 0);
                    Log::info("Token Input calculate: " . $totalInputTokens . " Token Output calculate: " . $totalOutputTokens . " with model: claude-3-5-sonnet-20240620 "  . " Price: " . $total_price);
                    break;
                case 'suggestBusiness':
                    $total_price = 5000;
                    break;
                case 'mappingVideo':
                    $total_price = Helpers::calculateCredit('d-id', 0, 0, 60, 0);
                    break;
                case 'suggestContentImagevideo':
                    $total_price = 1000;
                    break;
                case 'image':
                case 'video':
                    $total_price = Helpers::calculateCredit($model_code, 0, 0, 0, 1);
                    break;
                case 'gen3-alphaturbo':
                    $total_price = Helpers::calculateCredit('gen3-alphaturbo', 0, 0, 0, $number_result);
                    break;
                case 'lipsync':
                    $total_price = Helpers::calculateCredit('lipsync', 0, 0, $number_result, 0);
                    break;
                case 'mc_virtual':
                    $total_price = Helpers::calculateCredit($model_code ?? 'd-id', 0, 0, $number_result, 0);
                    break;
                case 'caption':
                    $total_price = Helpers::calculateCredit('caption', 0, 0, $number_result, 0);
                    break;
                case 'creatomate':
                    $total_price = Helpers::calculateCredit('creatomate', 0, 0, $number_result, 0);
                    break;
                case 'flux-schnell':
                    $total_price = Helpers::calculateCredit('flux-schnell', 0, 0, 0, 1);
                    break;
                case 'flux-1.1-pro':
                    $total_price = Helpers::calculateCredit('flux-1.1-pro', 0, 0, 0, 1);
                    break;
                case 'consistent-character':
                    $total_price = Helpers::calculateCredit('consistent-character', 0, 0, 0, 1);
                    break;
                case 'photo-beauty':
                    $total_price = Helpers::calculateCredit('photo-beauty', 0, 0, 0, 1);
                    break;
                case 'animate-diff':
                    $total_price = Helpers::calculateCredit('animate-diff', 0, 0, 0, 1);
                    break;
                case 'kling':
                    $total_price = Helpers::calculateCredit('kling', 0, 0, 0, 1);
                    break;
                case 'latent-consistency':
                    $total_price = Helpers::calculateCredit('latent-consistency', 0, 0, 0, 1);
                    break;
                case 'Aesthetic':
                    $total_price = Helpers::calculateCredit('aesthetic', 0, 0, 0, 1);
                    break;
                case 'text_to_video':
                    $total_price = Helpers::calculateCredit('text_to_video', 0, 0, 0, $number_result);
                    break;
                case 'image_to_image':
                    $total_price = Helpers::calculateCredit('image_to_image', 0, 0, 0, 1);
                    break;
                case 'image_to_video':
                    $total_price = Helpers::calculateCredit('image_to_video', 0, 0, 0, 1);
                    break;
                case 'text_to_music':
                    $total_price = Helpers::calculateCredit('text_to_music', 0, 0, 0, 1);
                    break;
                case 'music_to_text':
                    $total_price = Helpers::calculateCredit('music_to_text', 0, 0, 0, 1);
                    break;
                case 'ai-audio':
                    $total_price = Helpers::calculateCredit('ai-audio', 0, 0, 0, 1);
                    break;
                case 'elevenlab':
                    $total_price = Helpers::calculateCredit($model_code, 0, 0, 0, $number_result);
                    break;
                case 'pebblely':
                    $total_price = Helpers::calculateCredit('pebblely', 0, 0, 0, 1);
                    break;
                case 'heygen_video_avatar':
                    $total_price = Helpers::calculateCredit('heygen_video_avatar', 0, 0, $number_result, 0);
                    break;
                case 'heygen_photo_avatar':
                    $total_price = Helpers::calculateCredit('heygen_photo_avatar', 0, 0, $number_result, 0);
                    break;
                case 'pictory_create_content':
                    $total_price = Helpers::calculateCredit('pictory_create_content', 0, 0, $number_result, 0);
                    break;
                case 'pictory_create_video':
                    $total_price = Helpers::calculateCredit('pictory_create_video', 0, 0, $number_result, 0);
                    break;
                case 'my_ai_image':
                    $total_price = Helpers::calculateCredit('my_ai_image', 0, 0, 0, 1);
                    break;
                case 'upscale-image':
                    $total_price = Helpers::calculateCredit('upscale-image', 0, 0, 0, 1);
                    break;
                case 'generate-image-background':
                    $total_price = Helpers::calculateCredit('generate-image-background', 0, 0, 0, 1);
                    break;
                case 'image_combine':
                    $total_price = Helpers::calculateCredit('image_combine', 0, 0, 0, 1);
                    break;
            }
            if(strtotime($user->vip_expired_at) < time()) {

                $affCode = $this->getAffCodeByUser($user);

                return response()->json([
                    'success' => false,
                    'message' => 'Vip expired',
                    'is_vip_expired' => true,
                    'affCode' => $affCode
                ]);
            }

            if ($user->credit >= ceil($total_price)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đủ số credit thanh toán',
                    'data' => [
                        'text' => $text ?? '',
                        'total_price' => $total_price,
                        'credit' => $user->credit,
                        'input_tokens' => $input_tokens ?? 0,
                        'output_tokens' => $output_tokens ?? 0,
                    ]
                ], 200);
            }

        } catch (\Exception $e) {
            Log::error("Error Credit : " . $e);
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi trong quá trình thanh toán',
                'data' => []
            ], 500);
        }

        return response()->json([
            'success' => false,
            'message' => 'Vui lòng nạp thêm credit',
            'data' => [
                'text' => $text ?? '',
                'total_price' => $total_price,
                'credit' => $user->credit,
                'input_tokens' => $input_tokens ?? 0,
                'output_tokens' => $output_tokens ?? 0,
            ]
        ], 200);
    }

    public function get_text(Request $request) {
        $request->validate([
            'message' => 'nullable|string',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);
        $message = $request->message;
        $description = $request->description;
        $description = preg_replace('/\[[^\]]*\]/', $message, $description);
        $contentUserSupplier = '';
        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $documentReader = app(DocumentReaderService::class);
                $pageLimit = env('PAGE_IS_READ') ?? 10;
                $extension = $file->getClientOriginalExtension();
                if ($extension === 'pdf') {
                    if ($documentReader->checkPageLimit($file, $pageLimit)) {
                        return response()->json([
                            'success' => false,
                            'errors' => ['file' => "File PDF không được vượt quá $pageLimit trang."]
                        ], 422);
                    }
                    $content = $documentReader->readContentPdfLimitPage($file, 10, 'vi');
                }

                if ($extension === 'doc' || $extension === 'docx') {
                    $content = $documentReader->readContent($file, 'vi');
                }
                $contentUserSupplier = ". Yêu cầu ưu tiên lấy thông tin từ nội dùng tại đây nếu không có thông tin trong tài liệu cung cấp mới được lấy thông tin bên ngoài: \"$content\" .
                ";
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tải lên file. Vui lòng thử lại.',
                ], 500);
            }
        }
        return  $description . "sử dụng cho sản phẩm : $message $contentUserSupplier .
                Dựa trên nội dung được cung cấp về $message ";

    }

    public function insertHistory(Request $request) {
        $user = $request->get('user') ?? new User();
        $user = Helpers::runInCronJobs() ? $user : Auth::user();

        DB::table('credit_histories')->insert([
            "user_id" => $user->id,
            "screen_id" => $request['screen_id'],
            "credit" => $request['credit'],
            'feature_id' => $request['feature_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        return response()->json(["success" => true]);
    }

    private function getAffCodeByUser($user) {
        $affCode = null;
        if ($user) {
            $codes = AffKey::join('config_aff', 'config_aff.id', '=', 'aff_keys.config_aff_id')
                ->select('code')
                ->where('user_id', $user->id)
                ->pluck('code')
                ->toArray();

            if (in_array('aff_trial', $codes) && in_array('aff_account', $codes)) {
                $affCode = null;
            } else {
                $aff = AffKey::join('config_aff', 'config_aff.id', '=', 'aff_keys.config_aff_id')
                    ->select('code')
                    ->where('code', 'aff_trial')
                    ->where('user_id', $user->id)
                    ->first();

                if (!is_null($aff) && !empty($aff->code)) {
                    $affCode = $aff->code;
                }
            }
        }
        return $affCode;
    }
}
