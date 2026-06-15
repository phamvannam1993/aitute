<?php

namespace Database\Seeders;

use App\Models\VoiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoiceTypesSeeder2 extends Seeder
{
    /**
     * php artisan db:seed --class=VoiceTypesSeeder2
     */
    public function run(): void
    {
        $listVoicesNeedUpdate = [
            [ 'name'=>'Ngọc Lan', 'alter_name'=>'Ngọc Lan - Hà Nội'],
            [ 'name'=>'Hương Giang', 'alter_name'=>'Hương Giang - Huế'],
            [ 'name'=>'Thảo Trinh', 'alter_name'=>'Thảo Trinh - Sài Gòn'],
            [ 'name'=>'Mạnh Dũng', 'alter_name'=>'Mạnh Dũng - Hà Nội'],
            [ 'name'=>'Trung Kiên', 'alter_name'=>'Trung Kiên - Sài Gòn'],
            [ 'name'=>'Duy Phương', 'alter_name'=>'Duy Phương - Huế'],
        ];
        foreach ($listVoicesNeedUpdate as $voice) {
            DB::table('voice_types')
                ->where('name', '=', $voice['name'])
                ->update(['name' => $voice['alter_name']]);
        }
    }
}
