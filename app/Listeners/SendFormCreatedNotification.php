<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\formCreated; // Sesuaikan dengan nama event yang Anda gunakan

class SendFormCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(formCreated $event): void
    {
        // Lakukan sesuatu ketika event terjadi
        // Contoh: Kirim notifikasi ke channel
        broadcast($event);
    }
}
