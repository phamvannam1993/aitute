<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Exports\UsersExport;
use App\Helper\ApiErrorsMessageBag;
use App\Mail\PurchaseMail;
use App\Models\AffKey;
use App\Models\ConfigAff;
use App\Models\OrderHistory;
use App\Models\User;
use App\Models\UserSale;
use App\Repositories\AffKeyRepository;
use App\Repositories\ConfigAffRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AffKeyRepository $affKeyRepository,
        protected ConfigAffRepository $configAffRepository,
        protected ActivityLogService $activityLogService
    ) {}

    public function store(Collection $data, bool $loginAfterRegister = true): array
    {
        try {
            $email = $data->get('email');
            $password = $data->get('password');
            $name = $data->get('name');
            $data = [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ];

            $user = $this->userRepository->updateOrCreate(['email' => $email], $data);
            $token = $loginAfterRegister ? auth('web')->login($user, true) : '';

            return [
                'status' => true,
                'token' => $token,
                'user' => $user,
                'statusCode' => JsonResponse::HTTP_OK
            ];
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return [
                'status' => false,
                'errors' => new ApiErrorsMessageBag(['message' => 'SERVER ERROR: Please contact the useristrator']),
                'statusCode' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }

    public function paginateUsers(array $params)
    {
        $users = $this->userRepository->paginateUsers($params);
        return $users;
    }

    public function calculateTotalRevenue($params)
    {
       return $this->userRepository->calculateTotalRevenue($params);
    }

    public function getPackages()
    {
        return ConfigAff::select('id', 'name', 'credit', 'price')->orderBy('price')->get();
    }

    public function getTotalSuccessAmount(array $params = [])
    {
        $query = OrderHistory::query()
            ->select('order_histories.*')
            ->where('order_histories.status', 'PAID')
            ->join('users', 'users.id', '=', 'order_histories.user_id')
            ->leftJoin('aff_keys', function ($join) {
                $join->on(
                    'aff_keys.id',
                    '=',
                    DB::raw('(
                    SELECT aff_keys.id
                    FROM aff_keys
                    WHERE aff_keys.user_id = users.id AND aff_keys.is_used = 1
                    LIMIT 1
                    )')
                );
            })
            ->leftJoin('config_aff', 'aff_keys.config_aff_id', '=', 'config_aff.id');

        // Search filter - Sửa lại cách dùng LIKE
        if (!empty($params['search'])) {
            $query->where(function ($q) use ($params) {
                $q->where('users.email', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $params['search'] . '%')
                    ->orWhere('users.phone', 'LIKE', '%' . $params['search'] . '%');
            });
        }

        // Date range filter
        if (!empty($params['from_date'])) {
            $fromDate = Carbon::parse($params['from_date'])->startOfDay();
            $query->where('order_histories.created_at', '>=', $fromDate);
        }

        if (!empty($params['to_date'])) {
            $toDate = Carbon::parse($params['to_date'])->endOfDay();
            $query->where('order_histories.created_at', '<=', $toDate);
        }

        // VIP expiration filter
        if (!empty($params['time_remaining_until_expiration'])) {
            $now = now();
            $dateExpiration = now()->addDays((int)$params['time_remaining_until_expiration']);
            $query->whereBetween('users.vip_expired_at', [$now, $dateExpiration]);
        }

        // Package filter
        if (!empty($params['package_id'])) {
            $query->where('config_aff.id', $params['package_id']);
        }

        // Filter by gift status
        if (isset($params['is_gift'])) {
            $query->where('users.is_gift', $params['is_gift']);
        }

        return $query->sum('order_histories.amount');
    }

    public function storeUserInAdmin(array $params): User
    {
        try {
            DB::beginTransaction();
            $data = [
                'name' => $params['name'],
                'email' => $params['email'],
                'password' => Hash::make($params['password']),
                'email_verified_at' => now(),
                'phone' => $params['phone'],
                'is_gift' => $params['is_gift'],
            ];

            $user = $this->userRepository->create($data);
            if (!$user) {
                throw new DomainException('Lỗi khi tạo người dùng', Response::HTTP_BAD_REQUEST);
            }

            if (isset($params['config_aff_id']) && $params['config_aff_id']) {
                $configAff = $this->configAffRepository->find($params['config_aff_id']);
                if (!$configAff) {
                    throw new DomainException('Không tìm thấy config aff', Response::HTTP_NOT_FOUND);
                }

                $affKeyUser = $this->createAffKeyUser($user, $params);

                if (!$affKeyUser) {
                    throw new DomainException('Lỗi tạo aff key', Response::HTTP_BAD_REQUEST);
                }

                $this->updateCredit(
                    $user,
                    $configAff->credit,
                    $configAff->month,
                    $configAff->day,
                );
            }

            $changes = [
                'old' => [],
                'attributes' => [
                    'config_aff_id' => $params['config_aff_id'] ?? null,
                    'credit' => $user->credit,
                    'vip_expired_at' => $user->vip_expired_at?->format('Y-m-d h:i:s'),
                    'email' => $user->email,
                ],
            ];

            if($params["isSendMail"] == "true") {
                Log::info('Bắt đầu gửi mail đến user -> email: '.$user->email);
                $this->sendMail($user, $params['phone']);
                Log::info('Kết thúc gửi mail đến user -> email: '.$user->email);
            }
            $this->activityLogService->activityLog($user, $changes, 'created');

            DB::commit();
            return $user;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function updateCredit(User $user, int $credit = 0, int $month = 0, int $day = 0)
    {
        $userCredit = $user->credit ?: 0;
        $vipExpiredAt = Carbon::parse($user->vip_expired_at);

        if ($vipExpiredAt->greaterThanOrEqualTo(now())) {
            $user->vip_expired_at = $vipExpiredAt->addMonths($month)->addDays($day);
        } else {
            $user->vip_expired_at = now()->addMonths($month)->addDays($day);
        }

        $user->credit = $userCredit + $credit;
        $user->save();
    }

    private function createAffKeyUser(User $user, array $params): AffKey
    {
        do {
            $randomKey = Str::random(12);
            $isExisted = $this->affKeyRepository->exists([
                'key' => $randomKey
            ]);
        } while ($isExisted);

        $affKeyUser = $this->affKeyRepository->create([
            'config_aff_id' => $params['config_aff_id'],
            'key' => $randomKey,
            'is_used' => true,
            'user_id' => $user->id
        ]);

        return $affKeyUser;
    }

    public function destroy(int $id): bool
    {
        try {
            DB::beginTransaction();

            $user = $this->userRepository->find($id);
            if (!$user) {
                throw new DomainException('Không tìm thấy người dùng', Response::HTTP_NOT_FOUND);
            }

            $changes = [
                'old' => [
                    'email' => $user->email,
                ],
                'attributes' => [
                    'email' => '',
                ],
            ];

            $this->activityLogService->activityLog($user, $changes, 'deleted');

            DB::commit();
            return  $user->delete();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getUserById(int $id)
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            throw new DomainException('Không tìm thấy người dùng', Response::HTTP_NOT_FOUND);
        }

        $user->config_aff_id = $user->affKey?->config_aff_id;

        return $user;
    }

    public function updateUserInAdmin(array $params, int $id): User
    {
        DB::beginTransaction();

        try {
            $user = $this->userRepository->find($id);
            if (!$user) {
                throw new DomainException('Không tìm thấy người dùng', Response::HTTP_NOT_FOUND);
            }

            $old = [
                'config_aff_id' => $user->affKey?->config_aff_id,
                'credit' => $user->credit,
                'vip_expired_at' => (Carbon::parse($user->vip_expired_at))?->format('Y-m-d h:i:s'),
            ];

            $data = [
                'name' => $params['name'],
                'phone' => $params['phone'],
                'credit' => $params['credit'],
                'is_gift' => $params['is_gift'],
            ];

            if ($params['password']) {
                $data['password'] = Hash::make($params['password']);
            }

            $user->update($data);

            if (is_null($params['config_aff_id'])) {
                $this->removeAffKeyUser($user);
                $this->logActivityUpdate($user, $old, $params);
                DB::commit();
                return  $user;
            }

            if ($user->affKey?->config_aff_id !== $params['config_aff_id']) {
                $this->handleAffKeyUpdate($user, $params);
            }

            $this->logActivityUpdate($user, $old, $params);

            DB::commit();
            return $user;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function logActivityUpdate(User $user, array $old, array $params): void
    {
        $changes = [
            'old' => $old,
            'attributes' => [
                'config_aff_id' => $params['config_aff_id'] ?? null,
                'credit' => $user->credit,
                'vip_expired_at' => (Carbon::parse($user->vip_expired_at))?->format('Y-m-d h:i:s'),
            ],
        ];

        $this->activityLogService->activityLog($user, $changes, 'updated');
    }

    private function handleAffKeyUpdate(User $user, array $params): void
    {
        $configAff = $this->configAffRepository->find($params['config_aff_id']);
        if (!$configAff) {
            throw new DomainException('Không tìm thấy config aff', Response::HTTP_NOT_FOUND);
        }

        if ($user->affKey) {
            $affKeyUser = $this->updateAffKeyUser($user, $params);
        } else {
            $affKeyUser = $this->createAffKeyUser($user, $params);
        }

        if (!$affKeyUser) {
            throw new DomainException('Lỗi tạo aff key', Response::HTTP_BAD_REQUEST);
        }

        $this->updateCredit(
            $user,
            $configAff->credit,
            $configAff->month,
            $configAff->day,
        );
    }

    private function removeAffKeyUser(User $user): bool
    {
        if ($user->affKey) {
            return $user->affKey->delete();
        }
        return true;
    }

    private function updateAffKeyUser(User $user, array $params): AffKey
    {
        $user->affKey->config_aff_id = $params['config_aff_id'];
        $user->affKey->save();
        return $user->affKey;
    }

    public function addCredit(array $params)
    {
        $user = $this->userRepository->find($params['user_id']);

        if (!$user) {
            throw new DomainException('Không tìm thấy người dùng', Response::HTTP_NOT_FOUND);
        }

        $old = [
            'credit' => $user->credit,
            'vip_expired_at' => (Carbon::parse($user->vip_expired_at))?->format('Y-m-d h:i:s'),
        ];

        $credit = $params['credit_add'] ?: 0;
        $month = $params['vip_expired_at_add'] ? $params['vip_expired_at_add']['month'] : 0;
        $day = $params['vip_expired_at_add'] ? $params['vip_expired_at_add']['day'] : 0;
        $this->updateCredit(
            $user,
            $credit,
            $month,
            $day
        );

        $changes = [
            'old' => $old,
            'attributes' => [
                'credit' => $user->credit,
                'vip_expired_at' => $user->vip_expired_at?->format('Y-m-d h:i:s'),
            ],
        ];

        $this->activityLogService->activityLog($user, $changes, 'addCredit');
    }

    public function export(array $params)
    {
        return (new UsersExport($params))->download('users.csv', Excel::CSV);
    }

    private function sendMail($user, $phone) {
        if ($user?->email) {
            $mail = (new PurchaseMail($user?->id, $phone))->onQueue('email-purchase-queue');
            $bcc = explode(',', config('mail.custom.bcc_email_receive_roadmap'));
            $bcc[] = config('mail.custom.email_receive_roadmap');
            Mail::to($user?->email)->bcc($bcc)->queue($mail);
        }
    }
}
