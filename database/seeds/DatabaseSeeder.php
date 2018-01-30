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

        DB::table('users')->delete();

        $this->call(ProfessionTableSeeder::class);
        $this->call(AreaTableSeeder::class);
        $this->call(EndUserTableSeeder::class);
        $this->call(MastoriTableSeeder::class);

        Model::reguard();
    }
}

class EndUserTableSeeder extends Seeder {

    public function run()
    {
        DB::table('end_users')->delete();

        factory(App\User::class, 'enduser', 500)->create()->each(function($u) {
            $u->addresses()->save(factory(App\Address::class)->make());
        });
    }

}


class MastoriTableSeeder extends Seeder {

    public function run()
    {
        DB::table('mastoria')->delete();

        factory(App\User::class, 'mastori', 500)->create()->each(function($u) {
            $randProfessions = App\Profession::all()->pluck('id')->random(2)->toArray();
            $randAreas = App\Area::all()->pluck('id')->random(2)->toArray();
            $u->userable->professions()->sync($randProfessions);
            $u->userable->areas()->sync($randAreas);
            $u->addresses()->save(factory(App\Address::class)->make());
        });
    }

}

class ProfessionTableSeeder extends Seeder {

    public function run()
    {
        DB::table('mastoria_professions')->delete();
        DB::table('professions')->delete();

        $json = File::get("database/seeds/data/professions.json");
        $data = json_decode($json);
        foreach ($data as $profession) {
            App\Profession::create(array(
                'tag' => $profession->tag,
                'title' => $profession->title
            ));
        }
    }
}

class AreaTableSeeder extends Seeder {

    public function run()
    {

        DB::table('mastoria_areas')->delete();
        DB::table('areas')->delete();

        $json = File::get("database/seeds/data/greece-prefectures/prefectures.json");
        $data = json_decode($json);
        foreach ($data->features as $obj) {
            if (!$obj->geometry) {
                continue;
            }
            App\Area::create(array(
                'id' => $obj->properties->ID_2,
                'name' => $obj->properties->NAME_2,
                'polygon' => $obj->geometry->coordinates
                // 'geo_json' => $obj->geometry
            ));
        }

        $json = File::get("database/seeds/data/greece-prefectures/areas.json");
        $data = json_decode($json);
        foreach ($data->features as $obj) {
            if (!$obj->geometry) {
                continue;
            }
            App\Area::create(array(
                'name' => $obj->properties->NAME_3,
                'parent_id' => $obj->properties->ID_2,
                'polygon' => $obj->geometry->coordinates
                // 'geo_json' => $obj->geometry
            ));
        }
    }
}
