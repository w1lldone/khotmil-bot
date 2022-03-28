<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SetTelegramWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Telegram Bot Webhook';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $token = config('telegram.bots.mybot.token');
        if (!$token) {
            $this->error('You have not set the telegram bot token!');
            return false;
        }

        Telegram::setWebhook(['url' => route('api.webhooks.telegram.store', $token)]);
        $this->info('Telegram webhook has been successfully set.');
        return true;
    }
}
