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
        Schema::create('notification', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->longtext('content', 255)->nullable();
            $table->string('user_id');
            $table->string('type')->comment('0=GENERAL;1=GIGS;2=GUILD;3=LEVELUP')->default(0);
            $table->string('icon_type')->comment('fontawesome code: fas fa-tshirt');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification');
    }
};
