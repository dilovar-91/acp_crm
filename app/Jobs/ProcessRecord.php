<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\OrderRecord;
use Throwable;

class ProcessRecord implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


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
        try {
            if (!empty($activity)) {
                $activity->recording_id = $this->recording_id;
                $activity->save();
                //Log::emergency("record saved(job) : ".$this->entry_id);
            }
            else {
                Log::emergency("(job) record without call: ".$this->entry_id);
            }
        } catch (Throwable $e) {
            Log::emergency(['job reord error:'=>$e]);
        }
    }
}
