<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:import-dummy-documents')]
#[Description('Command description')]
class ImportDummyDocuments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
