<?php

namespace App\Services;

use App\Constant\Activities;
use App\Models\Activity;
use App\Models\SlideAiComponent;
use App\Models\SlideCloud;
use App\Repositories\SlideAiComponentRepository;
use App\Repositories\SlideCloudRepository;
use Illuminate\Support\Facades\Log;

class SlideAiComponentService
{
    public function __construct(
        private SlideAiComponentRepository $slideAiComponentRepository,
    ) {
    }

    public function getAll()
    {
        return $this->slideAiComponentRepository->getModel()->all();
    }

    public function getSlideComponentsByTemplateId($id)
    {
        $components = SlideAiComponent::where('template_id', $id)->get();
        return $components;
    }

    public function getSlideComponentsNameByTemplateId($id, $type = null)
    {
        $query = SlideAiComponent::where('template_id', $id);

        if ($type) {
            $query->where('type', $type);
        }

        $componentNames = $query->pluck('component_name')->toArray();
        return $componentNames;
    }

    public function getSlideComponentsName()
    {
        $componentNames = SlideAiComponent::pluck('component_name')->toArray();
        return $componentNames;
    }
    public function storeToSlideClound($title, $slides, $theme)
    {
        $slide_type = 'ai_slide';
        $data = [
            'slide_type' => $slide_type,
            'title' => $title, //hard title
            'ai_slide_data' => json_encode($slides),
            'ai_slide_theme' => json_encode($theme),
            'created_by' =>  auth('admin')->id(),
        ];
        Log::info('Store: '.json_encode($data));
    }

    /**
     * @throws \JsonException
     */
    public function updateSlideCloundAi($id, $title, $slides, $theme)
    {
        $attributes = ['id' => $id];

        $values = [
            'ai_slide_data' => is_array($slides) ? json_encode($slides, JSON_THROW_ON_ERROR) : $slides,
            'ai_slide_theme' => is_array($theme) ? json_encode($theme, JSON_THROW_ON_ERROR) : $theme,
        ];
        Log::info('Update: '.json_encode($values));
    }
}
