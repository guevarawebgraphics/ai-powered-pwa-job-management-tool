<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->longtext('vector_id')->nullable()->comment('openai created vector_id');
            $table->longtext('vector_files')->nullable()->comment('openai uploaded json formatted of file_id');
        });
    }

    public function down()
    {
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('vector_id','vector_files');
        });
    }
};
