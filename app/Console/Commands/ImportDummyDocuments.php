<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Document;
use App\Models\Category;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportDummyDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipsr:import-dummy {path="D:\GHALIB_WIJOYO\data dummy"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import dummy PDF files from a local directory into the SIPSR database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = $this->argument('path');
        
        // Remove trailing slashes and quotes if any
        $directory = trim($directory, '"\'/\\');

        if (!File::isDirectory($directory)) {
            $this->error("Directory not found: {$directory}");
            return 1;
        }

        $files = File::files($directory);
        $pdfFiles = array_filter($files, function($file) {
            return strtolower($file->getExtension()) === 'pdf';
        });

        if (empty($pdfFiles)) {
            $this->warn("No PDF files found in directory: {$directory}");
            return 0;
        }

        $categories = Category::all();
        $users = User::all();

        if ($categories->isEmpty() || $users->isEmpty()) {
            $this->error("Please make sure you have at least 1 Category and 1 User in the database.");
            return 1;
        }

        $this->info("Found " . count($pdfFiles) . " PDF files. Starting import...");

        $bar = $this->output->createProgressBar(count($pdfFiles));
        $bar->start();

        foreach ($pdfFiles as $file) {
            // 1. Clean the filename
            $originalName = $file->getFilename();
            // Remove .pdf
            $cleanName = preg_replace('/\.pdf$/i', '', $originalName);
            // Remove all occurrences of " - Copy", " (2)", etc.
            // A simple regex to remove any trailing " - Copy" or " (x)"
            $cleanName = preg_replace('/(\s*-\s*Copy\s*(?:\(\d+\))?)+$/i', '', $cleanName);
            $cleanName = preg_replace('/(\s*\(\d+\))+$/i', '', $cleanName);
            $cleanName = trim($cleanName);

            // 2. Parse nama_dokumen and nomor_dokumen
            // Example: "A. RAHMAN_1504041312630000"
            $parts = explode('_', $cleanName, 2);
            $namaDokumen = trim($parts[0]);
            
            if (isset($parts[1])) {
                // Extract only numbers for the document number, just in case
                $nomorDokumen = preg_replace('/[^a-zA-Z0-9.\-]/', '', trim($parts[1]));
            } else {
                // Fallback
                $nomorDokumen = 'DUMMY-' . strtoupper(Str::random(6));
            }
            
            // If namaDokumen is somehow empty
            if (empty($namaDokumen)) {
                $namaDokumen = "Dokumen Tanpa Nama";
            }

            // Pick random user & category
            $user = $users->random();
            $category = $categories->random();

            // 3. Move/Copy File to Laravel Storage
            // Let's copy so we don't destroy the original files
            $date = Carbon::now();
            $year = $date->format('Y');
            $month = $date->format('m');
            $storagePathDir = "uploads/{$year}/{$month}";
            $fullDir = Storage::disk('local')->path($storagePathDir);
            
            if (!File::exists($fullDir)) {
                File::makeDirectory($fullDir, 0755, true);
            }

            // Generate unique filename for storage
            $newFileName = Str::uuid() . '.pdf';
            $fullStoragePath = "{$storagePathDir}/{$newFileName}";
            
            // Perform the copy
            File::copy($file->getPathname(), Storage::disk('local')->path($fullStoragePath));

            // 4. Create Document Record
            $document = Document::create([
                'nomor_dokumen'   => $nomorDokumen,
                'nama_dokumen'    => $namaDokumen,
                'category_id'     => $category->id,
                'tanggal_dokumen' => $date->format('Y-m-d'),
                'deskripsi'       => 'Diimpor otomatis dari data dummy pada ' . $date->format('Y-m-d H:i:s'),
                'file_path'       => $fullStoragePath,
                'file_name'       => $originalName,
                'uploader_id'     => $user->id,
            ]);

            // 5. Create Activity Log
            ActivityLog::create([
                'user_id'         => $user->id,
                'jenis_aktivitas' => 'UPLOAD',
                'deskripsi'       => 'Mengunggah dokumen "' . $document->nama_dokumen . '" via Bulk Import.',
                'ip_address'      => '127.0.0.1',
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully imported " . count($pdfFiles) . " documents!");
        
        return 0;
    }
}
