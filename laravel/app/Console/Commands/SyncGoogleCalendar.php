<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Packages\Command\SyncGoogleCalendar\UseCase\SyncGoogleCalendarService;

class SyncGoogleCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncGoogleCalendar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync google calendar';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        echo "### start ###\n";

        $syncGoogleCalendarService = new SyncGoogleCalendarService();

        $syncGoogleCalendarService->main();

        echo "### completed ###\n";
        return Command::SUCCESS;
    }
}
