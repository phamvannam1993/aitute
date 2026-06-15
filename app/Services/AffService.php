<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Repositories\AffKeyRepository;
use App\Services\FacebookApiService;
use Illuminate\Http\Response;


class AffService
{
    private FacebookApiService $facebookApiService;
    public function __construct(
        private FacebookService $facebookService,
        private AuthService $authService,
        private AffKeyRepository $afffKeyRepository
    ) {}


    public function handleCallback()
    {
        $this->facebookApiService = app(FacebookApiService::class);
        $fbUser = $this->facebookApiService->handleCallback();
        if (!auth('web')->check()) {
            $this->authService->loginFacebook($fbUser);
        }

        $this->facebookService->update($fbUser);

        return $fbUser;
    }

    public function paginateKeys(array $params)
    {
        return $this->afffKeyRepository->paginateKeys($params);
    }

    public function getFilteredKeys(array $params)
    {
        return $this->afffKeyRepository->getFilteredKeys($params);
    }

    public function destroy(int $id): bool
    {
        $key = $this->afffKeyRepository->find($id);
        if (!$key) {
            throw new DomainException('Không tìm thấy key', Response::HTTP_NOT_FOUND);
        }

        return $key->delete();
    }

    public function getKeyById(int $id)
    {
        $key = $this->afffKeyRepository->getKeyById($id);
        if (!$key) {
            throw new DomainException('Không tìm thấy người dùng', Response::HTTP_NOT_FOUND);
        }

        return $key;
    }

}
