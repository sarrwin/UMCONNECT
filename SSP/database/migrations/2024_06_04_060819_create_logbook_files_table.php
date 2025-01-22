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
        Schema::create('logbook_files', function (Blueprint $table) {
            $table->id();
        $table->foreignId('logbook_entry_id')->constrained()->onDelete('cascade');
        $table->string('file_path');
        $table->string('file_type');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbook_files');
    }
};
