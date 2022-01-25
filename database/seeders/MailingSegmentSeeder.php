<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MailingSegment;
use App\Models\User;

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
            $s->users()->attach(User::inRandomOrder()->limit(50)->get());
        });;
    }
}
