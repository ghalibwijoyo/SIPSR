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
        Schema::table('documents', function (Blueprint $table) {
            // Indexes untuk filter & search
            $table->index('nomor_dokumen');
            $table->index('nama_dokumen');
            $table->index('category_id');
            $table->index('uploader_id');
            $table->index('tanggal_dokumen');
            $table->index('created_at');
            $table->index('deleted_at');

            // Composite index untuk soft delete queries
            $table->index(['deleted_at', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('jenis_aktivitas');
            $table->index('created_at');
            $table->index('ip_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['nomor_dokumen']);
            $table->dropIndex(['nama_dokumen']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['uploader_id']);
            $table->dropIndex(['tanggal_dokumen']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['deleted_at', 'created_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['jenis_aktivitas']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['ip_address']);
        });
    }
};
