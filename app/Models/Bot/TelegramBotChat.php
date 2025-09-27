<?php

namespace App\Models\Bot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TelegramBotChat extends Model
{
    use HasFactory;
    protected $table = 'bot_telegram_chat';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function telegramBot()
    {
        return $this->hasOne(TelegramBot::class, 'bot_telegram_id', 'id');
    }
}
