<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\User;
use App\EndUser;
use App\Mastori;
use App\Address;
use App\Profession;

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

        $this->call(EndUserTableSeeder::class);
        $this->call(MastoriTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(AddressTableSeeder::class);
        $this->call(ProfessionTableSeeder::class);

        Model::reguard();
    }
}

class EndUserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('end_users')->delete();

        EndUser::create([
            'id' => 1,
            'name' => 'John Doe',
            'phone' => '(+30)6932451840',
            'photo' => null
        ]);
    }

}

class MastoriTableSeeder extends Seeder {

    public function run()
    {
        DB::table('mastoria')->delete();

        Mastori::create([
            'id' => 1,
            'first_name' => 'Σταθης',
            'last_name' => 'Doe',
            'paratsoukli' => 'κυρ Στάθης',
            'description' => 'Στο επάγγελμα απο το 1975.',
            'photo' => '',
            'phone' => '+30 6938 000 000',
            'avg_rating' => null,
            'avg_response_time' => null,
            'active' => 1
        ]);
    }

}

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        User::create([
            'userable_id' => '1',
            'userable_type' => 'App\EndUser',
        	'username' => 'user',
        	'email' => 'user@foobar.com',
        	'password' => bcrypt('password')
      	]);

        User::create([
            'userable_id' => '1',
            'userable_type' => 'App\Mastori',
            'username' => 'mastorr',
            'email' => 'mastorr@foobar.com',
            'password' => bcrypt('password')
        ]);
    }

}


class AddressTableSeeder extends Seeder {

    public function run()
    {
        DB::table('addresses')->delete();

        Address::create([
    		'user_id' => 1,
    		'city' => 'Thessaloniki',
    		'country' => 'Greece',
    		'address' => 'Armenopoulou 25',
            'friendly_name' => 'Home',
            'lat' => 40.626469288832304,
            'lng' => 22.948430559277313
        ]);

        Address::create([
    		'user_id' => 1,
    		'city' => 'Thessaloniki',
    		'country' => 'Greece',
    		'address' => 'Karamanlh 25',
            'friendly_name' => 'Home',
            'lat' => 40.626469288832304,
            'lng' => 22.948430559277313
        ]);

        Address::create([
    		'user_id' => 2,
    		'city' => 'Thessaloniki',
    		'country' => 'Greece',
    		'address' => 'Gounari 25',
            'friendly_name' => '',
            'lat' => 40.626469288832304,
            'lng' => 22.948430559277313
        ]);

        Address::create([
    		'user_id' => 2,
    		'city' => 'Thessaloniki',
    		'country' => 'Greece',
    		'address' => 'Armenopoulou 30',
            'friendly_name' => '',
            'lat' => 40.6321039178281,
            'lng' => 22.951563379406707
        ]);

    }

}

class ProfessionTableSeeder extends Seeder {

    public function run()
    {
        DB::table('professions')->delete();

        Profession::create([
            'tag' => 'ilektrologos',
            'title' => 'Hlektrologos'
        ]);
    }
}
