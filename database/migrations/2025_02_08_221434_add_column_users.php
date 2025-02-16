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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('home_no')->nullable();
            $table->string('professional_title')->nullable();
            $table->longtext('skills')->nullable();
            $table->longtext('current_address')->nullable();
            $table->longtext('service_area')->nullable();
            $table->string('is_notify', 1)->default(0);
            $table->string('is_location', 1)->default(0);
            // name column will be removed as we'll use title instead
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['service_area','profile_photo','mobile_no', 'home_no','professional_title','skills','current_address','is_notify','is_location']);
        });
    }
};
