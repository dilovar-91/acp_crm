<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;


class Dilo extends Model
{
    use HasFactory, Searchable;
    protected $table = 'orders_test';


    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'client_name' => $this->client_name,
            'created_at' => $this->created_at,
        ];
    }
}
