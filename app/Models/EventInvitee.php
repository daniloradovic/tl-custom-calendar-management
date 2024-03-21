<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventInvitee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'email',
    ];

    /**
     * Get the event that owns the invitee.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
