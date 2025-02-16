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
        Schema::create('schedules_per_user', function (Blueprint $table) {
            $table->id();
            $table->string('open')->nullable();
            $table->string('close')->nullable();
            $table->boolean('is_close')->default(false);
            $table->string('day')->nullable()->comment('1=Monday; 2=Tuesday; 3=Wednesday; 4=Thursday; 5=Friday; 6=Saturday; 7=Sunday;');
            $table->string('user_id')->nullable();
            $table->timestamps();
            
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules_per_user');
    }
};
