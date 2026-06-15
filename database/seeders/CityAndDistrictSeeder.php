<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CityAndDistrictSeeder extends Seeder
{
    CONST PROVINCES = [
        "thanh_pho_ha_noi" => ["latitude" => 21.028511, "longitude" => 105.804817],
        "thanh_pho_ho_chi_minh" => ["latitude" => 10.776889, "longitude" => 106.700806],
        "thanh_pho_da_nang" => ["latitude" => 16.047079, "longitude" => 108.206230],
        "thanh_pho_hai_phong" => ["latitude" => 20.844912, "longitude" => 106.688084],
        "thanh_pho_can_tho" => ["latitude" => 10.045162, "longitude" => 105.746857],
        "tinh_bac_giang" => ["latitude" => 21.273539, "longitude" => 106.194598],
        "tinh_bac_kan" => ["latitude" => 22.144371, "longitude" => 105.834098],
        "tinh_bac_lieu" => ["latitude" => 9.251556, "longitude" => 105.513649],
        "tinh_bac_ninh" => ["latitude" => 21.186082, "longitude" => 106.076429],
        "tinh_ben_tre" => ["latitude" => 10.243355, "longitude" => 106.375551],
        "tinh_binh_dinh" => ["latitude" => 13.782289, "longitude" => 109.219663],
        "tinh_binh_duong" => ["latitude" => 11.325402, "longitude" => 106.477017],
        "tinh_binh_phuoc" => ["latitude" => 11.751189, "longitude" => 106.723463],
        "tinh_binh_thuan" => ["latitude" => 11.090370, "longitude" => 108.072079],
        "tinh_ca_mau" => ["latitude" => 9.152672, "longitude" => 105.196079],
        "tinh_cao_bang" => ["latitude" => 22.665709, "longitude" => 106.257845],
        "tinh_dak_lak" => ["latitude" => 12.710011, "longitude" => 108.237751],
        "tinh_dak_nong" => ["latitude" => 12.264647, "longitude" => 107.609806],
        "tinh_dien_bien" => ["latitude" => 21.386000, "longitude" => 103.023010],
        "tinh_dong_nai" => ["latitude" => 10.945274, "longitude" => 106.824305],
        "tinh_dong_thap" => ["latitude" => 10.535339, "longitude" => 105.634949],
        "tinh_gia_lai" => ["latitude" => 13.807894, "longitude" => 108.109375],
        "tinh_ha_giang" => ["latitude" => 22.823333, "longitude" => 104.983571],
        "tinh_ha_nam" => ["latitude" => 20.583519, "longitude" => 105.922990],
        "tinh_ha_tinh" => ["latitude" => 18.355930, "longitude" => 105.887749],
        "tinh_hai_duong" => ["latitude" => 20.937341, "longitude" => 106.314678],
        "tinh_hau_giang" => ["latitude" => 9.757898, "longitude" => 105.641252],
        "tinh_hoa_binh" => ["latitude" => 20.853889, "longitude" => 105.337593],
        "tinh_hung_yen" => ["latitude" => 20.646000, "longitude" => 106.051000],
        "tinh_khanh_hoa" => ["latitude" => 12.258509, "longitude" => 109.052607],
        "tinh_kien_giang" => ["latitude" => 10.028680, "longitude" => 105.217903],
        "tinh_kon_tum" => ["latitude" => 14.349377, "longitude" => 108.000000],
        "tinh_lai_chau" => ["latitude" => 22.386222, "longitude" => 103.470000],
        "tinh_lam_dong" => ["latitude" => 11.575279, "longitude" => 108.142866],
        "tinh_lang_son" => ["latitude" => 21.852640, "longitude" => 106.761520],
        "tinh_lao_cai" => ["latitude" => 22.480943, "longitude" => 103.975548],
        "tinh_long_an" => ["latitude" => 10.535000, "longitude" => 106.413000],
        "tinh_nam_dinh" => ["latitude" => 20.438822, "longitude" => 106.162105],
        "tinh_nghe_an" => ["latitude" => 19.234249, "longitude" => 104.920036],
        "tinh_ninh_binh" => ["latitude" => 20.250000, "longitude" => 105.833333],
        "tinh_ninh_thuan" => ["latitude" => 11.680290, "longitude" => 108.986039],
        "tinh_phu_tho" => ["latitude" => 21.345636, "longitude" => 105.198279],
        "tinh_phu_yen" => ["latitude" => 13.088186, "longitude" => 109.091373],
        "tinh_quang_binh" => ["latitude" => 17.468902, "longitude" => 106.622307],
        "tinh_quang_nam" => ["latitude" => 15.539353, "longitude" => 108.019107],
        "tinh_quang_ngai" => ["latitude" => 15.120152, "longitude" => 108.792260],
        "tinh_quang_ninh" => ["latitude" => 21.006382, "longitude" => 107.292514],
        "tinh_quang_tri" => ["latitude" => 16.745382, "longitude" => 107.185467],
        "tinh_soc_trang" => ["latitude" => 9.603012, "longitude" => 105.971931],
        "tinh_son_la" => ["latitude" => 21.325905, "longitude" => 103.918274],
        "tinh_tay_ninh" => ["latitude" => 11.365980, "longitude" => 106.098146],
        "tinh_thai_binh" => ["latitude" => 20.446346, "longitude" => 106.336060],
        "tinh_thai_nguyen" => ["latitude" => 21.592781, "longitude" => 105.844160],
        "tinh_thanh_hoa" => ["latitude" => 19.806692, "longitude" => 105.785181],
        "thanh_pho_hue" => ["latitude" => 16.463713, "longitude" => 107.590866],
        "tinh_tien_giang" => ["latitude" => 10.449332, "longitude" => 106.342050],
        "tinh_tra_vinh" => ["latitude" => 9.812741, "longitude" => 106.299291],
        "tinh_tuyen_quang" => ["latitude" => 21.823789, "longitude" => 105.217430],
        "tinh_vinh_long" => ["latitude" => 10.250000, "longitude" => 105.958805],
        "tinh_vinh_phuc" => ["latitude" => 21.355577, "longitude" => 105.547437],
        "tinh_yen_bai" => ["latitude" => 21.700977, "longitude" => 104.870790],
        "tinh_hau_giang" => ["latitude" => 9.757898, "longitude" => 105.641252],
        "tinh_an_giang" => ["latitude" => 10.521583, "longitude" => 105.125895],
        "tinh_ba_ria_vung_tau" => ["latitude" => 10.541739, "longitude" => 107.242998],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $latLongByProvinces = $this::PROVINCES;
        $cityTable = (new City())->getTable();
        $districtTable = (new District())->getTable();
        $url = "https://provinces.open-api.vn/api/?depth=2";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        if (!empty($data)) {
            $cityData = [];
            $districtData = [];
            foreach ($data as $city) {
                $cityData[] = [
                    'id' => data_get($city, 'code'),
                    'code' => data_get($city, 'code'),
                    'name' => str_replace(['Tỉnh ', 'Thành phố '], '', data_get($city, 'name')),
                    'code_name' => data_get($city, 'codename'),
                    'latitude' => data_get($latLongByProvinces, data_get($city, 'codename') . '.latitude'),
                    'longitude' => data_get($latLongByProvinces, data_get($city, 'codename') . '.longitude')
                ];
                foreach (data_get($city, 'districts') as $district) {
                    $districtData[] = [
                        'city_id' => data_get($city, 'code'),
                        'code' => data_get($district, 'code'),
                        'name' => data_get($district, 'name'),
                        'code_name' => data_get($district, 'codename')
                    ];
                }
            }

            if (!empty($cityData) && !empty($districtData)) {
                // truncate table before insert data
                DB::table($districtTable)->truncate();
                DB::table($cityTable)->truncate();
                // insert
                City::insert($cityData);
                District::insert($districtData);
            }
        }
    }
}
