<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\everyMinute::class,
        Commands\everyDay::class,
        Commands\FetchOfferData::class,
        Commands\SubscriptionStatusCheck::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //----- will send notification (email and sms) of treatments, events and clips ----
        $schedule->command('email:add')->everyMinute()->onSuccess(function(){
            \Log::channel('custom')->info('Schedule work properly!');
        })
        ->onFailure(function(){
            \Log::channel('custom')->error('Error to run schedule task! email:add');
            Mail::to('tbilal866@gmail.com')->send(new SendMail("Error in Schedule Task.","There is an error to run Schedule task email:add, Please have a look on it.","Pawer System"));
        });

        //----------- Will send free spot emails -----
        // $schedule->command('free:spot')->dailyAt('12:00')->onSuccess(function(){
        //     \Log::channel('custom')->info('Schedule for free spots work properly!');
        // })
        // ->onFailure(function(){
        //     \Log::channel('custom')->error('Error to run schedule task! free:spot');
        //     Mail::to('tbilal866@gmail.com')->send(new SendMail("Error in Schedule Task For free:spot.","There is an error to run Schedule task, Please have a look on it.","Pawer System"));
        // });

        //----------- Will fetch offer data -----
        $schedule->command('fetch:offer')->daily()->onSuccess(function(){
            \Log::channel('custom')->info('Schedule for fetch data task work properly!');
        })
        ->onFailure(function(){
            \Log::channel('custom')->error('Error to run fetch data task. fetch:offer');
            Mail::to('tbilal866@gmail.com')->send(new SendMail("Error to run fetch data task.","There is an error to run Schedule task fetch:offer, Please have a look on it.","Pawer System"));
        });


        //----------- Will check subscription status -----
        $schedule->command('subscription:check')->hourly()->onSuccess(function(){
            \Log::channel('custom')->info('Schedule for subscription check has work!');
        })
        ->onFailure(function(){
            \Log::channel('custom')->error('Error to run subscription check task. subscription:check');
            Mail::to('tbilal866@gmail.com')->send(new SendMail("Error to run subscription check task.","There is an error to run Schedule task subscription:check, Please have a look on it.","pawerbookings.com"));
        });

        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
