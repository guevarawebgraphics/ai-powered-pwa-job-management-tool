<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gigs', function (Blueprint $table) {
            $table->longtext('addtl_recommended_repairs')->nullable()->comment('populated with json from technician during submission of report');
        });
    }

    public function down()
    {
        Schema::table('gigs', function (Blueprint $table) {
            $table->dropColumn('addtl_recommended_repairs');
        });
    }
};
