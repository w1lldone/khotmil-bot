<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Telegram\Bot\Objects\Update;
use App\Http\Controllers\Controller;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookTelegramController extends Controller
{
    public function store(Request $request, $token)
    {
        if ($token != config('telegram.bots.mybot.token')) {
            return abort(403, 'Unauthorized');
        }

        try {
            /** @var Update */
            $update = Telegram::commandsHandler(true);
            dump($update);
        } catch (\Throwable $th) {
            dump($th);
        }
        return;
    }
}
