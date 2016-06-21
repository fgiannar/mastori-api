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
        'username' => $faker->unique()->userName,
        'email' => $faker->unique()->email,
        'password' => bcrypt('password'),
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(App\User::class, 'enduser', function (Faker\Generator $faker) use ($factory) {

    $user = $factory->raw(App\User::class);

    return array_merge($user, [
        'userable_type' => 'App\EndUser',
        'userable_id' => function () {
            return factory(App\EndUser::class)->create()->id;
        }
    ]);
});


$factory->defineAs(App\User::class, 'mastori', function (Faker\Generator $faker) use ($factory) {

    $user = $factory->raw(App\User::class);

    return array_merge($user, [
        'userable_type' => 'App\Mastori',
        'userable_id' => function () {
            return factory(App\Mastori::class)->create()->id;
        }
    ]);
});


$factory->define(App\EndUser::class, function ($faker) {
    return [
        'name' => $faker->name,
        'phone' => $faker->phoneNumber
    ];
});

$factory->define(App\Mastori::class, function ($faker) {

    return [
        'last_name' => $faker->lastName,
        'first_name' => $faker->firstName,
        'phone' => $faker->unique()->phoneNumber,
        'description' => $faker->text(),
        'offers' => rand(0, 1) == 1 ? $faker->text() : null,
        'avg_rating' => rand(1, 5),
        'active' => true
    ];
});

$factory->define(App\Address::class, function ($faker) {
    return [
        'lat' => $faker->latitude,
        'lng' => $faker->longitude,
        'address' => $faker->address,
        'city' => $faker->city,
        'country' => $faker->country
    ];
});