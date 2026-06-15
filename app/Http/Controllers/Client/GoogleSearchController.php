<?php

namespace App\Http\Controllers\Client;

use App\Helper\ApiErrorsMessageBag;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Jobs\FetchGmapsDataJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\GoogleSearchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Helper\Helpers;
use App\Models\City;
use Inertia\Inertia;
use Inertia\Response;

class GoogleSearchController extends Controller
{
    private $googleSearchService;

    public function __construct(
        GoogleSearchService $googleSearchService,
    ) {
        $this->googleSearchService = $googleSearchService;
    }

    public function index(Request $request): Response
    {
        $provices = $this->googleSearchService->getCitiesAndDistricts();
        return Inertia::render('Client/GoogleSearch/Index', [
            'provices' => $provices
        ]);
    }

    public function search(Request $request)
    {
        $timeOut = 120;
        $key = $request->key ?? '';
        if (!$key) {
            return response()->json(['data' => []]);
        }
        $params = $request->only(['key', 'location', 'limit', 'page', 'provice_id', 'district_id', 'district', 'input_customer_search']);
        $userSearch = DB::table('user_google_search')->where('key', $key)->where('city_id', data_get($params, 'provice_id'))->first();
        $data = $this->googleSearchService->getListBy($params);
        if ($userSearch) {
            return response()->json($data);
        }
        // trường hợp dữ liệu cũ -> chưa có dữ liệu trong user_google_search nhưng đã có dữ liệu ở google_search
        if ($data->count()) {
            return response()->json($data);
        }

        // search gmap
        $apiUrl = 'https://cloud.gmapsextractor.com/api/v2/search';
        $apiKey = env('GMAPS_API_KEY');
        $zoomLevel = "13z";
        $cityForSearching = City::where('id', data_get($params, 'provice_id'))->first();
        $coordinatesForSearch = "@" . data_get($cityForSearching, 'latitude') . "," . data_get($cityForSearching, 'longitude') . ",$zoomLevel";
        $page = 1;
        $processResult = true;
        $data = [
            "q" => $request->input('key', 'spa, làm đẹp'),
            "ll" => $coordinatesForSearch,
            "hl" => $request->input('hl', 'vi'),
            "gl" => $request->input('gl', 'vn'),
            "extra" => $request->input('extra', true)
        ];
        $result = [];
        while (true) {
            $data["page"] = $page;
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->timeout($timeOut)->post($apiUrl, $data);

            if ($response->successful()) {
                $dataResponse = $response->json();
                $result = array_merge($result, data_get($dataResponse, 'data'));
                if (data_get($dataResponse, 'total') < 20) {
                    break;
                } else {
                    $page++;
                }
            } else {
                $processResult = false;
                break;
            }
        }
        if ($processResult) {
            $this->googleSearchService->insertGoogleSearch($result, $params);
            $this->googleSearchService->insertUserGgSearch([
                [
                    'key' => $request->input('key'),
                    'city_id' => data_get($params, 'provice_id'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            ]);
            //   FetchGmapsDataJob::dispatch($data['q'], $data['ll']); // job xử lý dữ liệu lấy từ gmap - tạm thời chưa dùng

            // response data from DB
            $dataSearching = $this->googleSearchService->getListBy($params);
            return response()->json($dataSearching);
        }

        return response()->json([
            'error' => 'Không thể lấy dữ liệu từ API GMapsExtractor',
            'message' => $response->body()
        ], $response->status());
    }

    public function searchKey(Request $request)
    {
        $key = $request["key"];
        $location = $request["location"];
        $limit = $request["limit"] ? $request["limit"] : 100;
        $params = [
            "limit" => $limit
        ];
        if(!empty($location)) {
            $params["location"] = $location;
        }
        if(!empty($key)) {
            $params["title"] = $key;
        }
        $records = $this->googleSearchService->getListBy($params);
        return response()->json(["data" => $records]);
    }

    public function insertData()
    {
        $urlS3 = "files/serperPlace.places.json";
        $urlS3 = Helpers::preSignedS3Url($urlS3);
        $data = file_get_contents($urlS3);
        $dataArr = json_decode($data, true);
        foreach ($dataArr as $item) {
            $checkExist = $this->googleSearchService->getOneBy(["title" => $item["title"]]);
            if (!$checkExist) {
                $dataSave = [
                    'position' => $item["position"],
                    'title'  => $item["title"],
                    'address' => isset($item["address"]) ? $item["address"] : "",
                    'latitude' => $item["latitude"],
                    'longitude' => $item["longitude"],
                    'rating' => $item["rating"],
                    'ratingCount' => $item["ratingCount"],
                    'type' => isset($item["type"]) ? $item["type"] : "",
                    'types' => json_encode($item["types"]),
                    'phoneNumber' => isset($item["phoneNumber"]) ? $item["phoneNumber"] : "",
                    'openingHours' => isset($item["openingHours"]) ? json_encode($item["openingHours"]) : "",
                    'thumbnailUrl' => isset($item["thumbnailUrl"]) ? $item["thumbnailUrl"] : ""
                ];
                $this->googleSearchService->save($dataSave);
            }
        }
    }

    public function note(Request $request)
    {
        try {
            $result = $this->googleSearchService->note($request);

            if ($result) {
                return [
                    'status' => true,
                ];
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return [
                'status' => false,
                'errors' => new ApiErrorsMessageBag(['message' => 'SERVER ERROR: Please contact the useristrator']),
                'statusCode' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }
}
