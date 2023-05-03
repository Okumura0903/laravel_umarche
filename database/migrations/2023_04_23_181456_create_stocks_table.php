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
        Schema::create('t_stocks', function (Blueprint $table) {//テーブル名をt_stocksに変える
            $table->id();
            $table->foreignId('product_id') //!!「_id」で終わっている場合はLaravelが自動で判別してくれる（テーブル名不要）
            ->constrained()//外部キー制約
            ->onUpdate('cascade')//外部キーと連動して更新
            ->onDelete('cascade');//外部キーと連動して削除
            $table->tinyInteger('type');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_stocks');//テーブル名をt_stocksに変える
    }
};
