<?php

namespace App\Models\Bot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelegramBot extends Model
{
    use HasFactory;
    protected $table = 'bot_telegram';
    protected $primaryKey = 'id';
    protected $guarded = [];

    public function telegramBotChat(): HasMany
    {
        return $this->hasMany(TelegramBotChat::class, 'bot_telegram_id', 'id');
    }
}
