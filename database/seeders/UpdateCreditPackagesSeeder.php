<?php

namespace Database\Seeders;

use App\Models\CreditPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCreditPackagesSeeder  extends Seeder
{
    /**
     *  php artisan db:seed --class=UpdateCreditPackagesSeeder 
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            $creditPackage = CreditPackage::find(1);

            if ($creditPackage) {
                $creditPackage->template = "Trial";
                $creditPackage->name = "Gói trải nghiệm";
                $creditPackage->title = "Được sử dụng <strong> toàn bộ các tính năng </strong> trên AIWOW";
                $creditPackage->credit = 150000;
                $creditPackage->content = 'Khi sử dụng hết 150.000 điểm  có thể nâng hạng để gói có thời hạn sử dụng 12 tháng và giá thành rẻ hơn';
                $creditPackage->save();
            }

            $creditPackage = CreditPackage::find(2);

            if ($creditPackage) {
                $creditPackage->template = "Standard";
                $creditPackage->name = "Gói tiêu chuẩn";
                $creditPackage->title = "Được sử dụng <strong>toàn bộ các tính năng</strong> trên AIWOW";
                $creditPackage->credit = 300000;
                $creditPackage->save();
            }


            $creditPackage = CreditPackage::find(3);

            if ($creditPackage) {
                $creditPackage->template = "Premium";
                $creditPackage->name = "Gói cao cấp";
                $creditPackage->title = "Được sử dụng <strong> toàn bộ tính năng </strong> của gói <strong>TIÊU CHUẨN</strong> <strong> + thêm các tính năng:<strong>";
                $creditPackage->credit = 500000;
                $creditPackage->save();
            }


            $creditPackage = CreditPackage::find(4);

            if ($creditPackage) {
                $creditPackage->template = "Vip";
                $creditPackage->name = "Gói VIP";
                $creditPackage->title = "Được sử dụng <strong> toàn bộ tính năng </strong> của gói <strong>TIÊU CHUẨN</strong> và gói <strong> CAO CẤP + thêm các tính năng:<strong>";
                $creditPackage->credit = 1000000;
                $creditPackage->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
