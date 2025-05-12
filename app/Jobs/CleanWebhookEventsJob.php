<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{Log};

class CleanWebhookEventsJob implements ShouldQueue
{
    use Queueable;
    use InteractsWithQueue;
    use SerializesModels;
    use Dispatchable;

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info("CleanWebhookEventsJob iniciado.");

    }
}
