<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\AjaxException;
use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Services\MyAIImageTemplateService;
use App\Traits\ApiResponses;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class AdminMyAiImageTemplateController extends Controller
{
    use ApiResponses;
    public function __construct(
        private MyAIImageTemplateService $myAIImageTemplateService
    ) {}


    /**
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        try {
            $admin = auth('admin')->user();
            if ($admin->cannot('my-ai-image-templates.destroy')) {
                throw new DomainException('Không có quyền', Response::HTTP_FORBIDDEN);
            }

            $this->myAIImageTemplateService->destroy($id);
            return $this->okResponse([]);
        } catch (DomainException $e) {
            throw new AjaxException($e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new AjaxException('Có lỗi xảy ra', Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
    }
}
