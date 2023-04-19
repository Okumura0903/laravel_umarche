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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')
            ->constrained()//外部キー制約
            ->onUpdate('cascade')//外部キーと連動して更新
            ->onDelete('cascade');//外部キーと連動して削除
            $table->string('name');
            $table->text('information');
            $table->string('filename');
            $table->boolean('is_selling');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
