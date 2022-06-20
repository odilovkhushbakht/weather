<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cityList = [];
        $data = file_get_contents("current.city.list.json");
        $cityList = json_decode($data, true);
        foreach ($cityList as $value) {
            if (isset($value['langs'])) {
                foreach ($value['langs'] as $subItem) {
                    if (isset($subItem['ru'])) {
                        DB::table('cities')->insert([
                            'number_id' => $value['id'],
                            'name' => $subItem['ru'],
                            'country' => $value['country'],
                            'lat' => $value['coord']['lat'],
                            'lon' => $value['coord']['lon'],
                        ]);
                    }
                }
            }
        }
    }
}
