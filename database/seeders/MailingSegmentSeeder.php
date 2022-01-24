<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MailingSegment;

class MailingSegmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MailingSegment::factory()->count(10)->create();
    }
}
