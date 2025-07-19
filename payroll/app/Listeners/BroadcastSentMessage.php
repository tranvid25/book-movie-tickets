<?php

namespace App\Listeners;

use App\Events\UserOnline;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BroadcastSentMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserOnline $event): void
    {
        broadcast(new UserOnline($event->user,$event->message));
    }
}
