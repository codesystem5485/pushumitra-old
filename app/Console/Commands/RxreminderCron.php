<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Notifications;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;

class RxreminderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rxreminder:cron';

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
		$insertArray = array(
				'message' =>"testing Cron",
				'scheduled_date' => date("Y-m-d"),
				'sender_user_id' => 32,
				'rx_reminder_id' => 6666,
				'title' => "test cron",
			);
			
		$response = Notifications::create($insertArray);
		exit;
        //return 0;
    }
}
