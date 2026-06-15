<?php

namespace App\Services;

use App\Http\Controllers\Client\CreditController;
use Illuminate\Http\Request;

class CreditService
{
    public function __construct(
    ) {}


    public function checkCredit(array $params)
    {
        $defaults = [
            'type' => 'text',
            'model' => null,
            'number_result' => null,
            'action' => null,
            'text' => null,
            'feature_id' => 0,
            'screen_id' => 0,
            'user' => auth()->user()
        ];

        $params = array_merge($defaults, $params);

        $request = new Request();
        $request->merge($params);

        $creditController = new CreditController();
        $result = $creditController->check_credit($request);
        return $result->getData(true);
    }
    
}
