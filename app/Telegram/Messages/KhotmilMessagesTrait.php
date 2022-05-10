<?php

namespace App\Telegram\Messages;

use App\Models\Group;
use App\Models\Member;

trait KhotmilMessagesTrait
{
    /**
     * Generate Khotmil Info Message
     *
     * @param Group $group
     * @return string
     */
    public function info(Group $group)
    {
        $members = $group->members;

        $formattedMembers = "";

        foreach ($members as $key => $member) {
            $no = $key + 1;
            $formattedMembers .= "{$no}. $member->name
";
        }

        $message = "
*Khotmil Qur'an {$group->name}*
Durasi: {$group->duration} hari
Timezone: {$group->timezone}

👥 *ANGGOTA*
$formattedMembers
Pengen ikutan? langsung ketik /join aja 🤗
";

        if ($group->started_at == null && $group->members->count() != 0) {
            $message .= "Semua sudah join? Ketik /start untuk memulai khotmil";
        }

        return $message;
    }

    public function progress(Group $group)
    {
        $message = "*Khotmil Quran {$group->name} Putaran {$group->round}*
Periode {$group->started_at->setTimezone($group->timezone)->format('d F Y')} - {$group->deadline->setTimezone($group->timezone)->format('d F Y')}

";

        foreach ($group->schedules()->orderBy('juz')->with('member')->get() as $key => $schedule) {
            $juz = "Juz *{$schedule->juz}* ";
            if ($schedule->started_at) {
                $juz .= "{$schedule->getProgressIcon()} {$schedule->member->name}";
            }

            $message .= $juz . "
";
        }

        $message .= "
{$group->onProgressIcon()} = Progres membaca
{$group->finishedIcon()} = Selesai

Ketik /finish untuk menyelesaikan bacaanmu, ya 😊";

        return $message;
    }

    public function notRegistered()
    {
        return "Ups, Khotmil Quran belum terdaftar. Kamu bisa ketik /new untuk membuat khotmil baru.";
    }

    public function notStarted()
    {
        return "Ups, Khotmil Quran belum dimulai. Ketik /start untuk memulai.";
    }

    public function notAMember()
    {
        return "Ups, Kamu belum terdaftar sebagai member khotmil Quran pada grup ini. Pengen ikutan? ketik /join aja 👍";
    }

    public function noActiveSchedule(Member $member)
    {
        return "Ups! {$member->name} tidak memiliki jadwal khotmil aktif. Tunggu member lain selesai baca, ya 😊";
    }

    public function readingFinished(Member $member)
    {
        return "Barakallah, {$member->name}. Semoga semua hajat Kamu dikabulkan oleh Allah SWT.
";
    }

    /**
     * Undocumented function
     *
     * @param \App\Models\Schedule[] $schedules
     * @return void
     */
    public function remainingSchedules($schedules)
    {
        $members = "";
        foreach ($schedules as $key => $schedule) {
            $members .= "[{$schedule->member->name}](tg://user?id={$schedule->member->telegram_user_id}), ";
        }
        $members .= "Yuk semangat bacanya!";
        return $members;
    }

    public function onlyForAdmin()
    {
        return "Ups, maaf ya ✌️ Perintah ini hanya boleh dijalankan oleh admin grup.";
    }

    public function alreadyStarted()
    {
        return "Khotmil sudah dimulai lho!
Ketik /progress untuk melihat progres.
Ketik /info untuk melihat info khotmil.";
    }

    public function hasNoMember()
    {
        return "Ups! Belum ada member yang bergabung pada khotmil ini. Yuk ketik /join untuk ikutan 🤗";
    }
}
