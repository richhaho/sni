<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:send {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a Notification';

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
        $user = \App\User::findOrFail(1);
        $message = $this->argument('message');

        event(new \App\Events\NotificationReceived($message, $user));
    }
}
