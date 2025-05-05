<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->text('logo')->nullable()->after('country_id');
            $table->text('banner')->nullable()->after('logo');
            $table->text('description')->nullable()->after('banner');
        });
    }

    public function down()
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn(['logo', 'banner', 'description']);
        });
    }
};
