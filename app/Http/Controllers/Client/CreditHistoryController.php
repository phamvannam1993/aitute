<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\CreditHistoryService;
use Illuminate\Support\Facades\Auth;

class CreditHistoryController extends Controller
{
    private $creditHistoryService;


    public function __construct(
        CreditHistoryService $creditHistoryService,
    ) {
        $this->creditHistoryService = $creditHistoryService;
    }

    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        return Inertia::render('Client/CreditHistory/Index', []);
    }

    public function ajaxGetData(Request $request)
    {
        $userId = Auth::user()->id;
        $limit =  $request['limit'] ? $request['limit'] : 10;
        $list = $this->creditHistoryService->getListCreditHistory($limit, $userId);
        return response()->json(["list" => $list]);
    }
}
