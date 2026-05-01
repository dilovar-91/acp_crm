<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActualProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id', // Add 'site_id' to the fillable array
        'showroom_id',
        'type_id',
        'manager_id',
        'system_id',
        'status_id',
        'comment',
    ];

    public function showroom()
    {
        return $this->belongsTo('App\Models\Showroom');
    }
    public function site()
    {
        return $this->belongsTo('App\Models\Site', 'site_id');
    }
    public function type()
    {
        return $this->belongsTo('App\Models\ProjectType', 'type_id');
    }
    public function status()
    {
        return $this->belongsTo('App\Models\ProjectStatus', 'status_id');
    }
    public function manager()
    {
        return $this->belongsTo('App\Models\ProjectManager');
    }
    public function system()
    {
        return $this->belongsTo('App\Models\ProjectSystem');
    }

    public function scopeSortByShowroom($query, array $showroomIds)
    {
        $condition = implode(',', $showroomIds);
        return $query->orderByRaw("FIELD(showroom_id, $condition)");
    }
}
