<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $dates = ['started_at', 'deadline', 'finished_at'];

    public function group()
    {
        return $this->belongsTo(\App\Models\Group::class);
    }

    public function member()
    {
        return $this->belongsTo(\App\Models\Member::class);
    }

    public function getProgressIcon()
    {
        if ($this->finished_at) {
            return $this->group->finishedIcon();
        }

        return $this->group->onProgressIcon();
    }
}
