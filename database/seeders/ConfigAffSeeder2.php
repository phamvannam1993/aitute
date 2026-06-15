<?php

namespace Database\Seeders;

use App\Models\AIPricing;
use App\Models\ConfigAff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigAffSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=ConfigAffSeeder2
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $configAff = new ConfigAff();
            $configAff->name = "Trial Account";
            $configAff->code = "aff_trial";
            $configAff->credit = 300000;
            $configAff->price = 1000000;
            $configAff->month = 0;
            $configAff->day = 14;
            $configAff->save();

            $accountConfigAff = ConfigAff::where('code', 'aff_account')->first();
            $accountConfigAff->price = 25000000;
            $accountConfigAff->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
