<?php

namespace App\Services;

use App\Enums\ShareLinkableTypeEnum;
use App\Exceptions\ShareLinkServiceException;
use App\Models\AIAssistantHistories;
use App\Models\AiGeneratedBannerPoster;
use App\Models\AIGeneratedMedia;
use App\Models\AIVideo;
use App\Models\Video;
use App\Models\CreatomateVideo;
use App\Models\McVirtual;
use App\Models\PictoryVideo;
use App\Models\SwapFace;
use App\Models\ProductImage;
use App\Models\Lipsync;
use App\Models\Music;
use App\Models\PebblelyVideo;
use App\Models\SocialPost;
use App\Repositories\AIAssistantHistoriesRepository;
use App\Repositories\AiGeneratedBannerPosterRepository;
use App\Repositories\AiVideoRepository;
use App\Repositories\AIGeneratedMediaRepository;
use App\Repositories\AIMusicRepository;
use App\Repositories\CreatomateVideoRepository;
use App\Repositories\McVirtualRepository;
use App\Repositories\ShareLinkRepository;
use App\Repositories\SwapfaceRepository;
use Illuminate\Http\Response;
use App\Repositories\LipsyncRepository;
use App\Repositories\VideoRepository;
use App\Repositories\PebblelyVideoRepository;
use App\Repositories\PictoryVideoRepository;
use App\Repositories\SocialPostRepository;
use App\Repositories\ProductImageRepository;

class ShareLinkService
{
    public function __construct(
        private AIAssistantHistoriesRepository $aIAssistantHistoriesRepository,
        private ShareLinkRepository $shareLinkRepository,
        private AIGeneratedMediaRepository $aIGeneratedMediaRepository,
        private AiVideoRepository $aIVideoRpository,
        private SwapfaceRepository $swapfaceRepository,
        private LipsyncRepository $lipsyncRepository,
        private McVirtualRepository $mcVirtualRepository,
        private PebblelyVideoRepository $pebblelyVideoRepository,
        private PictoryVideoRepository $pictoryVideoRepository,
        private AiGeneratedBannerPosterRepository $aiGeneratedBannerPosterRepository,
        private CreatomateVideoRepository $creatomateVideoRepository,
        private AIMusicRepository $aIMusicRepository,
        private VideoRepository $videoRepository,
        private SocialPostRepository $socialPostRepository,
        private ProductImageRepository $productImageRepository
    ) {}

    public function store(array $params): array
    {
        $socialPostableType =  ShareLinkableTypeEnum::array()[$params['share_linkable_type']];
        $socialPostable = match ($socialPostableType) {
            AIAssistantHistories::class =>
            $this->aIAssistantHistoriesRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            AIGeneratedMedia::class =>
            $this->aIGeneratedMediaRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            ProductImage::class =>
            $this->productImageRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            Video::class =>
            $this->videoRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            Lipsync::class =>
            $this->lipsyncRepository->findByWhereOrWhere([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ], [
                ['lip_sync_id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            AIVideo::class =>
            $this->aIVideoRpository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            SwapFace::class =>
            $this->swapfaceRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            McVirtual::class =>
            $this-> mcVirtualRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            PebblelyVideo::class =>
            $this->pebblelyVideoRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            PictoryVideo::class =>
            $this->pictoryVideoRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            AiGeneratedBannerPoster::class =>
            $this->aiGeneratedBannerPosterRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            CreatomateVideo::class =>
            $this->creatomateVideoRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            Music::class =>
            $this->aIMusicRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            SocialPost::class =>
            $this->socialPostRepository->findByFilter([
                ['id', $params['share_linkable_id']],
                ['user_id', auth('web')->id()],
            ]),
            default => null,
        };

        if (!$socialPostable) {
            throw new ShareLinkServiceException('Không tìm thấy bài viết cần chia sẻ', Response::HTTP_NOT_FOUND);
        }

        $shareLink = $socialPostable->shareLinks()->create([
            'user_id' => auth('web')->id(),
            'link_token' => hash('sha256', time() . auth('web')->id() . $socialPostable->id)
        ]);

        return $shareLink->only(['link_token']);
    }

    public function show(string $linkToken)
    {
        $shareLink = $this->shareLinkRepository->findByFilter([
            ['link_token', $linkToken],
        ]);

        if (!$shareLink) {
            throw new ShareLinkServiceException('Link không tồn tại', Response::HTTP_NOT_FOUND);
        }

        return $shareLink->shareLinkable;
    }
}
