<?php

namespace App\Services;

use App\Repositories\SlideAiTemplateRepository;

class SlideAiTemplateService
{
    public function __construct(
        private SlideAiTemplateRepository $slideAiTemplateRepository
    ) {
    }

    public function getAll()
    {
        return $this->slideAiTemplateRepository->getModel()->all()->toArray();
    }

    public function getRandomTemplateId()
    {
        $templates = $this->getAll();
        if (empty($templates)) {
            return null;
        }

        $randomIndex = array_rand($templates); 
        return $templates[$randomIndex]['id']; 
    }
}
