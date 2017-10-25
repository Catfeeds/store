<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});
$factory->define(App\Models\Commodity::class,function (Faker\Generator $faker){
   return [
       'user_id'=>1,
       'title'=>$faker->sentence(mt_rand(3, 10)),
       'price'=>$faker->numberBetween(1,10),
       'description'=>$faker->sentence(9),
       'state'=>1,
       'detail'=>$faker->sentence(mt_rand(3,10)),
       'phone'=>$faker->phoneNumber,
       'QQ'=>$faker->randomNumber(),
       'WeChat'=>$faker->sentence(1),
       'latitude'=>$faker->randomFloat(7,23.1200491,24.1200491),
       'longitude'=>$faker->randomFloat(7,113.30764968,114.30764968),
       'pass'=>1,
       'enable'=>1
   ];
});
$factory->define(App\Models\TypeList::class,function (Faker\Generator $faker){
    return [
        'type_id'=>mt_rand(1,2),
        'commodity_id'=>mt_rand(1,200),
    ];
});
