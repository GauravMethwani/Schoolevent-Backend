<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'date', 'location', 'organizer',
        'department', 'type', 'description', 'image',
    ];
}
