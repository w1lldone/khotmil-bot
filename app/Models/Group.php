<?php

namespace App\Models;

use App\Events\GroupScheduleUpdated;
use App\Events\ScheduleUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $dates = ['started_at', 'deadline'];
    protected static $defaultTimezone = "Asia/Jakarta";

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

        $startedAt->setTimezone($this->timezone)->setTime(0,0)->setTimezone(config('app.timezone'));

        $this->update(['started_at' => $startedAt, 'deadline' => $startedAt->copy()->addDays($this->duration-1)]);

        event(new GroupScheduleUpdated($this));
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

    public function resetMemberOrder()
    {
        $members = $this->members()->orderBy('order')->get();

        foreach ($members as $key => $member) {
            $member->order += 1;
            if ($member->order > $members->count()) {
                $member->order -= $members->count();
            }
            $member->save();
        }
    }

    public function finishedIcon()
    {
        return "🕋";
    }

    public function onProgressIcon()
    {
        return "⭐";
    }

    public static function getDefaultTimezone()
    {
        return self::$defaultTimezone;
    }
}
