<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgrammaticSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday',
        'sunday', 'comment'];


    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class,  'user_id');
    }
}
