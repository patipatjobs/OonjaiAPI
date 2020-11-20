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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

$factory->define(App\Car::class, function (Faker\Generator $faker) {
    return [
        'car_id' => $faker->car_id,
        'car_license' => $faker->car_license,
        'car_title' => $faker->car_title,
        'users_username' => $faker->users_username
    ];
});

