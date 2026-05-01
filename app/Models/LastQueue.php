<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastQueue extends Model
{
    use HasFactory;

    protected $table = 'last_queue';

    protected $fillable = ['site_id', 'number' ,'percent'];
}
