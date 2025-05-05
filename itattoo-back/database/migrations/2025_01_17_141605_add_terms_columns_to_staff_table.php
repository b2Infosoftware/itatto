<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->boolean('accept_privacy_terms')->default(false)
                ->comment('0 for disagree, 1 for agree')
                ->after('preselected_staff_id');
            $table->boolean('accept_terms_conditions')->default(false)
                ->comment('0 for disagree, 1 for agree')
                ->after('accept_privacy_terms'); 
        });
    }

    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn(['accept_privacy_terms', 'accept_terms_conditions']);
        });
    }
};
