<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(ProfessionTableSeeder::class);
        $this->call(EndUserTableSeeder::class);
        $this->call(MastoriTableSeeder::class);

        Model::reguard();
    }
}

class EndUserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();
        DB::table('end_users')->delete();

        factory(App\User::class, 'enduser', 500)->create()->each(function($u) {
            $u->addresses()->save(factory(App\Address::class)->make());
        });
    }

}


class MastoriTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();
        DB::table('mastoria')->delete();

        factory(App\User::class, 'mastori', 500)->create()->each(function($u) {
            $u->userable->professions()->sync([1]);
            $u->addresses()->save(factory(App\Address::class)->make());
        });
    }

}

class ProfessionTableSeeder extends Seeder {

    public function run()
    {
        // DB::table('professions')->delete();

        App\Profession::create([
            'tag' => 'ilektrologos',
            'title' => 'Hlektrologos'
        ]);
    }
}