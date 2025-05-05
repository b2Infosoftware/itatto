<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->enum('media_type', ['1', '2'])
                  ->nullable()
                  ->after('project_id')
                  ->comment('1: Logo, 2: Banner');
        });
    }

    public function down()
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('media_type');
        });
    }
};
