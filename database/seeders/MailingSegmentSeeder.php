<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MailingSegment;
use App\Models\Client;

class MailingSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MailingSegment::factory()->count(3)->create()->each(function ($s) {
            $s->clients()->attach(Client::inRandomOrder()->limit(rand(10,100))->get());
        });;
    }
}
