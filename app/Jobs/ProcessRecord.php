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
use Throwable;

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
        $activity = ActivityLog::where('entry_id', $this->entry_id)
            ->whereIn('description', [5, 6, 7])
            ->latest('id')
            ->first();
        if (!$activity) {
            Log::channel('records')->warning('Mango recording waits for call history', [
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

        Log::channel('records')->info('Mango recording attached to call history', [
            'entry_id' => $this->entry_id,
            'recording_id' => $this->recording_id,
            'activity_id' => $activity->id,
            'attempt' => $this->attempts(),
        ]);
    }

    public function failed(Throwable $exception): void
    {
        Log::channel('records')->error('Mango recording attachment failed permanently', [
            'entry_id' => $this->entry_id,
            'recording_id' => $this->recording_id,
            'message' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
