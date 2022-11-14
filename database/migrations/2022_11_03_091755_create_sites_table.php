<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string("url");
            $table->string("status")->default("pending");
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->references("id")->on("users");
            $table->dateTime('emailed_at')->nullable();
            $table->dateTime("next_run")->nullable();
            $table->string("verb")->default("GET");
            $table->text('payload')->nullable();
            $table->text('headers')->nullable();
            $table->string("check")->nullable();
            $table->string("name")->nullable();
            $table->integer('timeout')->default(0);
            $table->integer('downtime')->default(1);
            $table->text('sitemaps')->nullable();
            $table->text('robots_preference')->nullable();
            $table->unique(['url', 'name']);
            $table->unique(['url', 'user_id']);
            $table->timestamps();

            $table->index(['url', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
};
