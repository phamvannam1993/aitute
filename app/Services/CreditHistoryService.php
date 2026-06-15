<?php

namespace App\Services;
use App\Repositories\CreditHistoryRepository;
use App\Models\CreditHistory;
use App\Models\AiAssistant;

class CreditHistoryService
{
    public function __construct(
        private CreditHistoryRepository $creditHistoryRepository,
    ) {}

    public function getListCreditHistory($per_page, $userId) {
        $list = $this->creditHistoryRepository->getListCreditHistory($per_page, $userId);
        $listFeature = CreditHistory::listFeature();
        $listScreen = CreditHistory::listScreen();
        for($i = 0; $i < count($list); $i++) {
            if($list[$i]["screen_id"] == 13) {
                $aiAssistant = AiAssistant::find($list[$i]["feature_id"]);
                if($aiAssistant) {
                    $name = $aiAssistant->name;
                    $list[$i]["feature_id"] = 'Sử dụng trợ lý ảo '.$name;
                } else {
                    $list[$i]["feature_id"] = 'Sử dụng trợ lý ảo';
                } 
            } else {
                $list[$i]["feature_id"] = isset($listFeature[$list[$i]["feature_id"]]) ? $listFeature[$list[$i]["feature_id"]] : "";
            }
            $list[$i]["screen"] = isset($listScreen[$list[$i]["screen_id"]]) ? $listScreen[$list[$i]["screen_id"]] : "";
            $list[$i]["created_date"] = date('d-m-Y H:i:s', strtotime($list[$i]["created_at"]));
        }
        return $list;
    }

    public function create($data = []) {
        return $this->creditHistoryRepository->create($data);
    }
}
