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
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->boolean('is_disabled')->default(false)->after('title'); // Adjust the placement as needed
        });
    }
    
    public function down()
    {
        Schema::table('chat_rooms', function (Blueprint $table) {
            $table->dropColumn('is_disabled');
        });
    }
    
};
