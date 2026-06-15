<?php

namespace Database\Seeders;

use App\Models\AIPricing;
use App\Models\ConfigAff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigAffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=ConfigAffSeeder
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $configAff = new ConfigAff();
            $configAff->name = "Register Account";
            $configAff->code = "aff_account";
            $configAff->credit = 1000000;
            $configAff->month = 12;
            $configAff->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }
    }
}
