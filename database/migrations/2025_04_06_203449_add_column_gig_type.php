<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gigs', function (Blueprint $table) {
            $table->string('gig_type')->default(0)->comment('0=first-time; 1=return-repair;');
        });
    }

    public function down()
    {
        Schema::table('gigs', function (Blueprint $table) {
            $table->dropColumn('gig_type');
        });
    }
};
