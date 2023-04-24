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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('information');
            $table->unsignedInteger('price');
            $table->boolean('is_selling');
            $table->integer('sort_order')->nullable();
            $table->foreignId('shop_id') //!!「_id」で終わっている場合はLaravelが自動で判別してくれる（テーブル名不要）
            ->constrained()//外部キー制約
            ->onUpdate('cascade')//外部キーと連動して更新
            ->onDelete('cascade');//外部キーと連動して削除

            $table->foreignId('secondary_category_id') //!!「_id」で終わっている場合はLaravelが自動で判別してくれる（テーブル名不要）
            ->constrained();//外部キー制約

            $table->foreignId('image1') //!!「_id」で終わっていない項目を指定
            ->nullable()
            ->constrained('images');//外部キー制約はテーブル名も指定する（自動でないので）
            $table->foreignId('image2') //!!「_id」で終わっていない項目を指定
            ->nullable()
            ->constrained('images');//外部キー制約はテーブル名も指定する（自動でないので
            $table->foreignId('image3') //!!「_id」で終わっていない項目を指定
            ->nullable()
            ->constrained('images');//外部キー制約はテーブル名も指定する（自動でないので
            $table->foreignId('image4') //!!「_id」で終わっていない項目を指定
            ->nullable()
            ->constrained('images');//外部キー制約はテーブル名も指定する（自動でないので
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
