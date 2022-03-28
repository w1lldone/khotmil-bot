<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $dates = ['started_at'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::created(function (Group $group) {
            $group->generateSchedules();
        });
    }

    public function members()
    {
        return $this->hasMany(\App\Models\Member::class);
    }

    public function schedules()
    {
        return $this->hasMany(\App\Models\Schedule::class);
    }

    public function generateSchedules()
    {
        $schedules = collect(range(1,30))->map(function ($item)
        {
            return ['group_id' => $this->id, 'juz' => $item];
        });

        return $this->schedules()->createMany($schedules->toArray());
    }

    public function assignMemberSchedule(Carbon $startedAt = null)
    {
        if ($startedAt == null) {
            $startedAt = now();
        }

        $this->update(['started_at' => $startedAt, 'deadline' => $startedAt->copy()->addDays($this->duration)]);

        $members = $this->members()->orderByDesc('order')->get();
        $schedules = $this->schedules()->whereNull('started_at')->orderBy('juz')->take($members->count())->get();

        foreach ($schedules as $key => $schedule) {
            $schedule->update(['member_id' => $members[$key]->id, 'started_at' => $this->started_at, 'deadline' => $this->deadline]);
        }
    }

    public function getLastMemberOrder()
    {
        $member = $this->members()->select('order')->orderByDesc('order')->first();

        if (!$member) {
            return 0;
        }

        return $member->order;
    }

    public function increaseRound()
    {
        $this->round = $this->round + 1;
        $this->save();

        return $this->round;
    }


    public function finishedIcon()
    {
        return "🕋";
    }

    public function onProgressIcon()
    {
        return "⭐";
    }
}
