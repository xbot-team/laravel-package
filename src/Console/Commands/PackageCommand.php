<?php

namespace XBot\Package\Console\Commands;

use Illuminate\Console\Command;

class PackageCommand extends Command
{
    protected $signature = 'package';
    
    protected $description = 'Command description';
    
    public function handle(): int
    {
        $this->comment(':package_name command executed.');
        
        return Command::SUCCESS;
    }
}
