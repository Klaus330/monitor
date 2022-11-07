<?php

use App\Models\Site;
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
        Schema::create('site_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Site::class, 'site_id')->onDelete('cascade');
            $table->string('route', 500);
            $table->string('http_code');
            $table->string('found_on')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'http_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_routes');
    }
};
