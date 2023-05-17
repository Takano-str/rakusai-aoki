<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stores')->insert([
            // [
            //     'id' => 333645, 
            //     'user_id' => 1, 
            //     'store_name' => '久喜支店', 
            //     'store_tel' => '07013934433', 
            //     'google_account' => 'media4mymc@gmail.com', 
            //     'guest_account' => '[]', 
            //     'result_url' => '', 
            //     'created_at' => now(), 
            //     'updated_at' => now()
            // ],
            [
                'id' => 69698, 
                'user_id' => 1, 
                'store_name' => '川越営業所', 
                'store_tel' => '07013934433', 
                'google_account' => 'kawagoe-saiyo@careerroad.co.jp', 
                'guest_account' => '[]', 
                'result_url' => '', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'id' => 396174, 
                'user_id' => 1, 
                'store_name' => 'アマゾン狭山日高', 
                'store_tel' => '07013934433', 
                'google_account' => 'kawagoe-saiyo@careerroad.co.jp', 
                'guest_account' => '[]', 
                'result_url' => '', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ]);
    }
}
