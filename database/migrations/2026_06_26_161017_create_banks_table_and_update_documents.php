<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create banks table
        Schema::create('banks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama');
            $table->timestamps();
        });

        // Replace nama_bank string column with bank_id foreign key
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('nama_bank');
            $table->foreignUuid('bank_id')->nullable()->after('nama_dokumen')->constrained('banks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['bank_id']);
            $table->dropColumn('bank_id');
            $table->string('nama_bank')->nullable()->after('nama_dokumen');
        });

        Schema::dropIfExists('banks');
    }
};
