<?php

namespace App\Http\Controllers\Bot;

use App\Http\Controllers\Controller;
use App\Models\Bot\TelegramBot;
use App\Models\Bot\TelegramBotChat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TelegramBotController extends Controller
{
    public function botTelegramData(): JsonResponse
    {
        $bot = TelegramBot::with('telegramBotChat')->get();

        return response()->json([
            'message'   => 'Success get bot data',
            'data'      => [
                'bot'   => $bot
            ]
        ]);
    }

    public function changeBotDefault(int $id): JsonResponse
    {
        TelegramBot::where('id', '!=', $id)->update(['bot_default' => false]);
        $bot = TelegramBot::find($id);
        $bot->update([
            'bot_default' => !$bot->bot_default
        ]);

        return response()->json([
            'message' => 'Berhasil mengubah bot default.',
        ]);
    }

    public function changeBotChatStatus(Int $id): JsonResponse
    {
        $chat = TelegramBotChat::find($id);
        $chat->update([
            'is_active' => !$chat->is_active
        ]);

        return response()->json([
            'message' => 'Berhasil status chat bot.',
        ]);
    }

    public function addBot(Request $request): JsonResponse
    {
        $data = $request->validate([
            'bot_name'  => 'required|string|max:150',
            'bot_token' => 'required|string|max:200',
        ]);

        TelegramBot::create($data);

        return response()->json([
            'message' => 'Berhasil tambah bot.',
        ], 201);
    }

    public function addChatBot(Request $request): JsonResponse
    {
        $data = $request->validate([
            'bot_telegram_id'   => 'required|numeric',
            'chat_id'           => 'required|numeric',
            'message_thread_id' => 'required|numeric',
            'bot_telegram_id'   => 'required|string|max:150',
            'chat_title'        => 'required|string|max:200',
        ]);

        TelegramBotChat::create($data);

        return response()->json([
            'message' => 'Berhasil tambah chat bot.',
        ], 201);
    }

    public function deleteBot(Int $id): JsonResponse
    {
        $bot = TelegramBot::find($id);
        $bot->delete();
        return response()->json([
            'message' => 'Berhasil hapus bot.',
        ]);
    }

    public function deleteChatBot(Int $id): JsonResponse
    {
        $chat = TelegramBotChat::find($id);
        $chat->delete();

        return response()->json([
            'message' => 'Berhasil hapus chat bot.',
        ]);
    }

}
