<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SetPwd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setpwd {user} {pwd?}';

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
    public function handle()
    {
        $pwd = $this->argument('pwd');
        if (!$pwd)
            $pwd = Str::random(10);
        User::find($this->argument('user'))->fill(['password' => bcrypt($pwd)])->saveQuietly();
        echo $pwd . PHP_EOL;
    }
}
