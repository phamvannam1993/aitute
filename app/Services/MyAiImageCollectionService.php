<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Models\MyAIImageCollection;
use App\Repositories\MyAiImageCollectionRepository;
use App\Repositories\MyAiImageTemplateCategoryRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class MyAiImageCollectionService
{
    public function __construct(
        private StorageService $storageService,
        private MyAiImageCollectionRepository $myAiImageCollectionRepository,
        private MyAiImageTemplateCategoryRepository $myAiImageTemplateCategoryRepository,
    ) {}

    public function paginateCollections(array $params)
    {
        $collections = $this->myAiImageCollectionRepository->paginateCollections($params);
        return $collections;
    }

    public function store(array $params): MyAIImageCollection
    {
        $s3 = $this->storageService->putToS3($params['image'], 'my_ai_image_collection');

        $data = [
            'title' => $params['title'],
            's3_url' => $s3['path'],
        ];

        $imageCollection = $this->myAiImageCollectionRepository->create($data);

        return $imageCollection;
    }

    public function getImageCollectionById(array $params, int $id)
    {
        $imageCollection = $this->myAiImageCollectionRepository->find($id);

        return [
            'imageCollection' => $imageCollection,
        ];
    }

    public function all()
    {
        return $this->myAiImageCollectionRepository->all();
    }

    public function update(array $params, int $id): MyAIImageCollection
    {
        $imageCollection = $this->myAiImageCollectionRepository->find($id);
        if (!$imageCollection) {
            throw new DomainException('Không tìm thấy dữ liệu', Response::HTTP_NOT_FOUND);
        }

        $data = [
            'title' => $params['title'],
        ];

        try {
            DB::beginTransaction();
            if (!empty($params['image'])) {
                $this->storageService->deleteFromS3($imageCollection->s3_url);
                $s3 = $this->storageService->putToS3($params['image'], 'my_ai_image_collection');
                $data['s3_url'] = $s3['path'];
            }

            $imageCollection->update($data);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }


        return $imageCollection;
    }


    public function destroy(int $id): bool
    {
        $imageCollection = $this->myAiImageCollectionRepository->find($id);
        if (!$imageCollection) {
            throw new DomainException('Không tìm thấy dữ liệu', Response::HTTP_NOT_FOUND);
        }

        return  $imageCollection->delete();
    }
}
