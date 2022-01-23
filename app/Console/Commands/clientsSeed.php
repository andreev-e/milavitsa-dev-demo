<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Console\Command;

class clientsSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Faker $faker)
    {
        $phone = 9003543210;
//        Client::chunk(10000, function ($clients) use ($faker, $phone) {
//            foreach ($clients as $client) {
//                $name = $faker->name;
//                $split = explode(' ', $name);
//                $f = $split[0];
//                $i = $split[1];
//                $o = $split[2];
//                $phone = $phone++;
//                $email = $phone.'@test.test';
//                $client->update(
//                    [
//                        'f' => $f,
//                        'i' => $i,
//                        'o' => $o,
//                        'name' => $name,
//                        'phone' => ['+7'.$phone],
//                        'email' => [$email],
//                    ]
//                );
//                echo 'client '.$client->id."\n";
//            }
//        });


            foreach (User::all() as $user) {
                $name = $faker->name;
                $split = explode(' ', $name);
                $f = $split[0];
                $i = $split[1];
                $o = $split[2];
                $email = $phone++.'@test.test';
                $user->update(
                    [
                        'f' => $f,
                        'i' => $i,
                        'o' => $o,
                        'password' => bcrypt(123),
                        'name' => $name,
                        'email' => $email,
                    ]
                );
                echo 'user '.$user->id."\n";
            }


//        Message::chunk(1000, function ($messages) {
//            foreach ($messages as $message) {
//                $message->update(
//                    [
//                        'text' => $faker->text,
//                    ]
//                );
//                echo 'message ' . $message->id . "\n";
//            }
//        });

        return 0;
    }
}
