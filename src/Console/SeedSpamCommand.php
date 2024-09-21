<?php

namespace EscaliersSolution\LaravelAdmin\Console;

use Illuminate\Console\Command;

class SeedSpamCommand extends Command
{
    protected $signature = 'seed:spam';

    protected $description = 'Runs Seeders for Settings, Permissions, ApiResources and MenuItems';

    public function handle()
    {
        $continue = true;
        
        if($continue) {
            $this->call('db:seed', ['--class' => 'SpamSeeder']);
            return;
        }
    }
}