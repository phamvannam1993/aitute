<?php

namespace App\Services;

use App\Models\UserNoteGoogleSearch;
use App\Repositories\UserNoteGoogleSearchRepository;
use App\Models\City;
use App\Models\District;
use Exception;
use App\Models\GoogleSearch;
use App\Models\UserGoogleSearch;

class GoogleSearchService
{
    public function __construct(
        protected UserNoteGoogleSearchRepository $userNoteGoogleSearchRepository,
    ) {}
    public function getOneBy($params = [])
    {
        return  GoogleSearch::where($params)->first();
    }

    public function getListBy($params = [])
    {
        $query = GoogleSearch::query();

        if (!empty($params["key"])) {
            $keywords = $params["key"];
            $query->where(function ($q) use ($keywords) {
                $keywords = explode(',', strtolower($keywords));
                foreach ($keywords as $keyword) {
                    $q->orWhereRaw("LOWER(type) LIKE ?", ["%" . trim($keyword) . "%"]);
                }
            });
        }

        if (!empty(data_get($params, "provice_id"))) {
            $query->where(function($q) use ($params) {
                $q->where('city_id', $params['provice_id'])
                  ->orWhere('state', 'LIKE', '%' . $params['location'] . '%'); // đáp ứng dữ liệu cũ
            });
        }
        if (!empty(data_get($params, "district_id"))) {
            $districtByCity = District::where('city_id', data_get($params, 'provice_id'))->pluck('name', 'id')->toArray();
            list($districtId, $districtName) = $this->_checkDistrictInCity($districtByCity, data_get($params, 'district'));
            $query->where(function($q) use ($params, $districtName) {
                $q->where('district_id', $params['district_id'])
                  ->orWhere('district', 'LIKE', "%$districtName%"); // đáp ứng dữ liệu cũ
            });
        }

        if (!empty($params["input_customer_search"])) {
            $keywords = $params["input_customer_search"];
            $query->where(function ($q) use ($keywords) {
                $q->whereRaw("LOWER(title) LIKE ?", ["%" . trim($keywords) . "%"])
                    ->orWhereRaw("LOWER(address) LIKE ?", ["%" . trim($keywords) . "%"])
                    ->orWhereRaw("LOWER(phoneNumber) LIKE ?", ["%" . trim($keywords) . "%"]);
            });
        }

        return $query->paginate($params["limit"]);
    }

    public function save($data = []) {
        return  GoogleSearch::insert($data);
    }

    public function note($request)
    {
        $attributes = $request->only(['google_search_id', 'note']);
        $note = $attributes['note'] ?? null;
        $googleSearchId = $attributes['google_search_id'];
        $userId = \auth('web')->user()->id;
        $data = [
            'user_id' => $userId,
            'google_search_id' => $googleSearchId,
            'note' => $note,
        ];

        return $this->userNoteGoogleSearchRepository->updateOrCreate(['user_id' => $userId, 'google_search_id' => $googleSearchId], $data);
    }

    public function getCitiesAndDistricts() {
        $cities = City::with('districts')->get();
        if ($cities->count()) {
            $data = [];
            foreach ($cities as $city) {
                $temp = [
                    'name' => data_get($city, 'name'),
                    'value' => data_get($city, 'id'),
                ];
                $districtsForInstall = [];
                $districts = data_get($city, 'districts', []);
                if (!empty($districts)) {
                    foreach ($districts as $item) {
                        $districtsForInstall[] = [
                            'name' => data_get($item, 'name'),
                            'value' => data_get($item, 'id')
                        ];
                    }
                    $temp['districts'] = $districtsForInstall;
                } else {
                    $temp['districts'] = [];
                }
                $data[] = $temp;
            }
            return $data;
        }
        return [];
    }

    public function insertUserGgSearch($data)
    {
        UserGoogleSearch::insert($data);
    }

    public function insertGoogleSearch($data, $params)
    {
        $dataSave = [];
        $districtByCity = District::where('city_id', data_get($params, 'provice_id'))->pluck('name', 'id')->toArray();
        $placeIdByCity = GoogleSearch::select('place_id')->where('city_id', data_get($params, 'provice_id'))->whereNotNull('place_id')->get();
        // handle data before insert
        foreach ($data as $item) {
            if (empty(data_get($item, "full_address")) || $placeIdByCity->where('place_id', data_get($item, "place_id"))->count()) {
                continue;
            }
            list($district, $provice) = $this->_getDistrictAndCityFromAddress(data_get($item, "full_address"));
            $districtId = $cityId = null;
            if ($provice == data_get($params, 'location')) {
                $cityId = data_get($params, 'provice_id');
            } else {
                continue;
            }
            list($districtId, $districtName) = $this->_checkDistrictInCity($districtByCity, $district);
            $dataSave[] = [
                'title'  => data_get($item, "name"),
                'address' => data_get($item, "full_address"),
                'latitude' => data_get($item, "latitude"),
                'longitude' => data_get($item, "longitude"),
                'rating' => data_get($item, "rating", data_get($item, "average_rating")),
                'ratingCount' => data_get($item, "review_count", 0),
                'type' => data_get($params, "key", ''),
                'types' => data_get($item, "categories", ''),
                'phoneNumber' => data_get($item, "phone", data_get($item, "phones")),
                'openingHours' => data_get($item, "opening_hours", ''),
                'thumbnailUrl' => data_get($item, "featured_image", ''),
                'place_id' => data_get($item, "place_id"),
                'city_id' => (int)$cityId,
                'district_id' => (int)$districtId,
                'state' => $provice,
                'district' => $districtName
            ];
        }

        if (!empty($dataSave)) {
            GoogleSearch::insert($dataSave);
        }
    }

    private function _getDistrictAndCityFromAddress($address)
    {
        // Tách địa chỉ thành các phần, dùng dấu phẩy làm phân cách
        $parts = explode(',', $address);
        // Trim từng phần để bỏ khoảng trắng thừa
        $parts = array_map('trim', $parts);
        // Xác định phần cuối có phải là Việt Nam không
        $lastPart = end($parts);
        $country = "Việt Nam";
        // Nếu phần cuối là "Việt Nam", lấy phần trước đó làm tỉnh
        if (strpos($lastPart, $country) !== false) {
            array_pop($parts); // Loại bỏ phần "Việt Nam"
            $lastPart = end($parts); // Lấy lại phần cuối sau khi loại bỏ "Việt Nam"
        }
        // Lấy phần tỉnh/thành phố từ phần cuối sau khi loại bỏ "Việt Nam"
        $province = array_pop($parts) ?? '';
        // Gán quận/huyện từ phần liền trước
        $district = array_pop($parts) ?? '';
        return [$district, $province];
    }

    private function _checkDistrictInCity($districtsInCity, $input) {
        // Loại bỏ các từ "quận", "huyện", "thị xã" trong đầu vào
        $normalizedInput = str_ireplace(['quận', 'huyện', 'thị xã', 'thành phố', 'tp.', 'tx.', 'q.', 'h.'], '', $input); // str_ireplace giúp bỏ qua chữ hoa/thường
        // Loại bỏ khoảng trắng thừa để so sánh chính xác
        $normalizedInput = trim($normalizedInput);
        // Kiểm tra nếu giá trị đã qua xử lý có thuộc mảng các quận của thành phố/ tỉnh
        foreach ($districtsInCity as $key => $district) {
            if (str_contains(strtolower($district), strtolower($normalizedInput))) {
                return [$key, $normalizedInput]; // Trả về key của quận nếu trùng khớp
            }
        }

        return [null, ""];
    }
}
