<?php

namespace App\Console\Commands;

use App\API;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        DB::transaction(function() {
            $this->info("Setting up system. Remember to run `php artisan migrate´ before");
            $name = $this->ask('Admin user name?');
            $email = $this->ask('Admin user email?');
            $pwd = $this->secret('Admin user password?');

            $client = DB::table('client')->insertGetId([
                'name' => 'System'
            ]);
            $role = DB::table('role')->insertGetId([
                'name' => 'Admin',
                'client_id' => $client,
            ]);
            DB::table('right')->insertGetId([
                'role_id' => $role,
                'client_id' => $client,
                "tables" => "*",
                "columns" => "*",
                "where" => "all",
                "actions" => "CRUD",
            ]);
            DB::table('users')->insertGetId([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($pwd),
                'client_id' => $client,
                'role_id' => $role,
            ]);
        });
    }
}
