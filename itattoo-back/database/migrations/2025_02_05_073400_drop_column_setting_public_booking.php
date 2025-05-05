<?php

use App\Models\SettingPublicBooking;
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
        Schema::table('setting_public_booking', function (Blueprint $table) {
            $table->dropColumn(['monday', 'monday_open_time', 'monday_close_time', 'tuesday', 'tuesday_open_time', 'tuesday_close_time', 'wednesday', 'wednesday_open_time', 'wednesday_close_time', 'thursday', 'thursday_open_time', 'thursday_close_time', 'friday', 'friday_open_time', 'friday_close_time', 'saturday', 'saturday_open_time', 'saturday_close_time', 'sunday', 'sunday_open_time', 'sunday_close_time']);

            $table->boolean('is_open')->comment('1 = open, 0 = close')->default(0)->after('id');
        });

        Schema::table('media', function(Blueprint $table) {
            $table->foreignIdFor(SettingPublicBooking::class)->nullable()->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setting_public_booking', function (Blueprint $table) {
            $table->dropColumn('is_open');
    
            $table->boolean('monday')->default(0);
            $table->time('monday_open_time')->nullable();
            $table->time('monday_close_time')->nullable();
    
            $table->boolean('tuesday')->default(0);
            $table->time('tuesday_open_time')->nullable();
            $table->time('tuesday_close_time')->nullable();
    
            $table->boolean('wednesday')->default(0);
            $table->time('wednesday_open_time')->nullable();
            $table->time('wednesday_close_time')->nullable();
    
            $table->boolean('thursday')->default(0);
            $table->time('thursday_open_time')->nullable();
            $table->time('thursday_close_time')->nullable();
    
            $table->boolean('friday')->default(0);
            $table->time('friday_open_time')->nullable();
            $table->time('friday_close_time')->nullable();
    
            $table->boolean('saturday')->default(0);
            $table->time('saturday_open_time')->nullable();
            $table->time('saturday_close_time')->nullable();
    
            $table->boolean('sunday')->default(0);
            $table->time('sunday_open_time')->nullable();
            $table->time('sunday_close_time')->nullable();
        });
    
        Schema::table('media', function (Blueprint $table) {
            $table->dropForeign(['setting_public_booking_id']);
            $table->dropColumn('setting_public_booking_id');
        });
    }
};