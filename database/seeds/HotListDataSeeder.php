<?php

use Illuminate\Database\Seeder;

class HotListDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Entity::class, 50)->create(['client_id' => 0])->each(function ($e) {
            $xco = rand(1, 6);
            factory(App\ContactInfo::class)->states('primary')->create(['entity_id' => $e->id]);
            factory(App\ContactInfo::class, $xco)->create(['entity_id' => $e->id]);
        });
    }
}
