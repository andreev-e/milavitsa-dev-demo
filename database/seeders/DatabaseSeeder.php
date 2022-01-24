<?php

use Illuminate\Database\Seeder;
use Database\Seeders\MailingSegmentSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MailingSegmentSeeder::class);
    }
}
