<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        \Illuminate\Database\Eloquent\Model::unguard();
        $this->call('CommoditySeeder');
    }
}
class CommoditySeeder extends Seeder
{
    public function run()
    {
        \App\Models\Commodity::truncate();
        factory(\App\Models\Commodity::class, 200)->create();
    }
}
