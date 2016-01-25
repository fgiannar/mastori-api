<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

use App\User;
use App\Mastori;
use App\Address;

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

        $this->call(UserTableSeeder::class);
        $this->call(MastoriTableSeeder::class);
        $this->call(AddressTableSeeder::class);

        Model::reguard();
    }
}

class UserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('users')->delete();

        User::create([
        	'name' => 'John Doe',
        	'email' => 'user@foobar.com',
        	'password' => bcrypt('password'),
        	'photo' => null
      	]);
    }

}

class MastoriTableSeeder extends Seeder {

    public function run()
    {
        DB::table('mastoria')->delete();

        Mastori::create([
        		'username' => 'mastorrrr',
        	  'email' => '',
        	  'password' => bcrypt('password'),
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

class AddressTableSeeder extends Seeder {

    public function run()
    {
        DB::table('addresses')->delete();

        Address::create([
        		'user_id' => 1,
        		'city' => 'Thessaloniki',
        		'country' => 'Greece',
        		'address' => 'Armenopoulou 25',
        		'mastori_id' => null,
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
            'mastori_id' => null,
            'lat' => 40.626469288832304,
            'lng' => 22.948430559277313
        ]);

        Address::create([
        		'user_id' => null,
        		'city' => 'Thessaloniki',
        		'country' => 'Greece',
        		'address' => 'Gounari 25',
            'friendly_name' => '',
            'mastori_id' => 1,
            'lat' => 40.626469288832304,
            'lng' => 22.948430559277313
        ]);

        Address::create([
        		'user_id' => null,
        		'city' => 'Thessaloniki',
        		'country' => 'Greece',
        		'address' => 'Armenopoulou 30',
            'friendly_name' => '',
            'mastori_id' => 1,
            'lat' => 40.6321039178281,
            'lng' => 22.951563379406707
        ]);

    }

}
