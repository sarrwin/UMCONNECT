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
        Schema::table('project_files', function (Blueprint $table) {
            $table->integer('version')->default(1);
            $table->string('original_name')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('project_files', function (Blueprint $table) {
            $table->dropColumn('version');
            $table->dropColumn('original_name');
        });
    }
};
