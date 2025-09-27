<?php

namespace App\Http\Controllers\Report;

use Error;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Models\Bot\TelegramBot;

class ReportTelegramController extends Controller
{
    public function sendToBotTelegram()
    {
        $now = now()->format('Y-m-d');

        $report = new ReportController();
        $reportSales = $report->hitungDataLaporan($now, $now);
        dd($reportSales);
    }

    function sendToBotTelegramTest()
    {
        $message = "*📢 Info Penting*\nHalo ini pesan dari Laravel!\n" . now();

        $bot = TelegramBot::with('telegramBotChat')
            ->where('bot_default', true)
            ->first();

        if (!$bot) {
            throw new Error("Tidak ada bot aktif", 422);
        }

        if($bot->telegramBotChat->where('is_active', true)->count() === 0) {
            throw new Error("Bot aktif, tidak ada chat aktif", 422);
        }

        $botToken = $bot->bot_token;
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

        $responses = [];

        foreach ($bot->telegramBotChat as $chat) {
            if(!$chat->is_active) continue;

            $requestData = [
                'chat_id' => $chat->chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ];

            if (!empty($chat->message_thread_id)) {
                $requestData['message_thread_id'] = $chat->message_thread_id;
            }

            $response = Http::post($url, $requestData);
            $responses[] = [
                'chat_id' => $chat->chat_id,
                'status' => $response->status(),
                'ok' => $response->ok(),
                'body' => $response->json(),
            ];
        }

        return $responses;
    }
}
