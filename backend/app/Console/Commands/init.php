<?php

namespace App\Console\Commands;

use App\API;
use Illuminate\Console\Command;

class init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize system client and admin user';

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
     * @return mixed
     */
    public function handle()
    {
        $this->info("Setting up system. Remember to run `php artisan migrate´ before");
        $name = $this->ask('Admin user name?');
        $email = $this->ask('Admin user email?');
        $pwd = $this->secret('Admin user password?');
        $client = API::create('client', [
            'name' => 'System'
        ]);
        $role = API::create('role', [
            'name' => 'Admin',
            'client_id' => $client,
        ]);
        API::create('users', [
            'name' => $name,
            'email' => $email,
            'password' => $pwd,
            'client_id' => $client,
            'role_id' => $role,
        ]);
    }
}
