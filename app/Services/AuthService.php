<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Helper\ApiErrorsMessageBag;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Traits\Instance;
use Illuminate\Support\Facades\Log;

class AuthService
{
    use Instance;


    public function __construct(
        protected UserRepository $userRepository,
        protected AdminRepository $adminRepository,
        protected AdminService $adminService,
        protected UserService $userService,
    ) {}


    public function adminLogin($data)
    {
        $repo = app(AdminRepository::class);

        return $this->actionCheck($repo, 'admin', $data);
    }

    private function actionCheck($repo, $gaurd, $data)
    {
        $record = $repo->findByFilter([
            'email' => $data['email']
        ]);

        if (empty($record->id)) {
            return [
                'result' => false,
                'errors' => [
                    'email' => 'invalid email!'
                ]
            ];
        } else if (!Hash::check($data['password'], $record->password)) {
            return [
                'result' => false,
                'errors' => [
                    'password' => 'incorrect password!'
                ]
            ];
        } else {
            Auth::guard($gaurd)->login($record, true);

            return [
                'result' => true
            ];
        }
    }

    public function getUserBySocial($social_type)
    {
        $this->setInstance(
            UserService::class,
        );

        try {
            DB::beginTransaction();
            $socialUser = Socialite::driver($social_type)->stateless()->user();
            $email = $this->getSocialEmail($socialUser, $social_type);
            $admin = $this->adminRepository->getUserWithSocials($socialUser->id, $social_type, $email);

            if (!$admin) {
                $data['email'] = $email;
                $admin = $this->adminRepository->findUserByEmail(collect($data));
            }
            if ($admin) {
                auth('admin')->login($admin, true);
                DB::commit();
                return $admin;
            }

            $user = $this->adminRepository->getUserWithSocials($socialUser->id, $social_type, $email);

            if (!$user) {
                $user = $this->userRepository->findUserByEmail(collect(['email' => $email]));
            }

            if ($user) {
                auth('web')->login($user, true);
                DB::commit();
                return $user;
            }
            $data['name'] = $socialUser->name;
            $data['email'] = $email;
            $data['password'] = Hash::make(Str::random(10));

            $result = $this->userService->store(collect($data));
            Log::info("User tạo mới: ", [$result]);

            if (!$result['status']) {
                DB::rollBack();
                return null;
            }

            // Đăng nhập sau khi tạo mới user
            $user = $result['user'];
            auth('web')->login($user, true);

            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return null;
        }
    }


    protected function getSocialEmail($socialUser, $social_type)
    {
        return (is_null($socialUser->email) || $socialUser->email == '' || !$socialUser->email) ? $socialUser->id . '@' . $social_type . '.com' : $socialUser->email;
    }

    public function changePassword($data)
    {
        $user = auth()->user();
        $user->password = Hash::make($data['new_password']);
        if ($user->save()) {
            return [
                'status' => true,
                'statusCode' => JsonResponse::HTTP_OK
            ];
        }

        return [
            'status' => false,
            'errors' => new ApiErrorsMessageBag(['form' => 'Can\'t save password']),
            'statusCode' => JsonResponse::HTTP_BAD_REQUEST
        ];
    }

    public function forgotPassword($data)
    {
        $status = Password::sendResetLink(
            ['email' => $data['email']]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return [
                'status' => true,
                'statusCode' => JsonResponse::HTTP_OK
            ];
        }

        return [
            'status' => false,
            'errors' => new ApiErrorsMessageBag(['form' => 'Can\'t sent email']),
            'statusCode' => JsonResponse::HTTP_BAD_REQUEST
        ];
    }

    public function resetPassword($data)
    {

        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return [
                'status' => true,
                'statusCode' => JsonResponse::HTTP_OK
            ];
        }

        return [
            'status' => false,
            'errors' => new ApiErrorsMessageBag(['form' => 'Can\'t reset password']),
            'statusCode' => JsonResponse::HTTP_BAD_REQUEST
        ];
    }


    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    public function loginFacebook(array $fbUser): User
    {
        $this->setInstance(
            UserService::class,
        );

        try {
            $socialUser = new \stdClass();
            $socialUser->email = $fbUser['facebook_user_email'];
            $socialUser->id =  $fbUser['facebook_user_id'];

            $email = $this->getSocialEmail($socialUser, 'facebook');
            $user = $this->userRepository->findUserByEmail($email);
            if ($user) {
                auth('web')->login($user, true);
                return $user;
            }

            $newUser = [
                'name' => $fbUser['facebook_user_name'],
                'email' =>  $email,
                'password' => Str::random(10),
            ];

            $result = $this->userService->store(collect($newUser));

            return $result['user'];
        } catch (\Exception $e) {
            throw new \Exception("Đăng nhập không thành công: {$e->getMessage()}");
        }
    }
}
