<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\MangoCallRecording;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ProcessRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 10;
    public $backoff = 60;

    protected $recording_id;
    protected $entry_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($entry_id, $recording_id)
    {
        $this->recording_id = $recording_id;
        $this->entry_id = $entry_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $activity = ActivityLog::where('entry_id', $this->entry_id)->latest('created_at')->first();
        if (!$activity) {
            Log::warning('Mango recording waits for call history', [
                'entry_id' => $this->entry_id,
                'recording_id' => $this->recording_id,
                'attempt' => $this->attempts(),
            ]);
            throw new RuntimeException('Call history is not ready yet');
        }

        $activity->recording_id = $this->recording_id;
        $activity->save();

        MangoCallRecording::where('recording_id', $this->recording_id)
            ->update(['attached_at' => now()]);
    }
}
