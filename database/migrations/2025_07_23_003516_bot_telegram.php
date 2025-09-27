<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bot_telegram', function (Blueprint $table) {
            $table->id();
            $table->string('bot_name', 150);
            $table->string('bot_token', 150);
            $table->boolean('bot_default')->default(false);
            $table->timestamps();
        });

        Schema::create('bot_telegram_chat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bot_telegram_id');
            $table->string('chat_title', 150);
            $table->bigInteger('chat_id');
            $table->bigInteger('message_thread_id')->default(0);
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('bot_telegram_id')->references('id')->on('bot_telegram')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_telegram_chat');
        Schema::dropIfExists('bot_telegram');
    }
};
