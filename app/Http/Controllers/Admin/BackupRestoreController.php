<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class BackupRestoreController extends Controller
{
    protected $backupPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true, true);
        }
    }

    /**
     * Tampilkan halaman utama Backup & Restore
     */
    public function index()
    {
        $files = File::files($this->backupPath);
        $backups = [];

        foreach ($files as $file) {
            $filename = $file->getFilename();
            if ($filename === '.gitignore' || str_starts_with($filename, '.')) {
                continue;
            }

            $size = $file->getSize();
            $time = $file->getMTime();
            $ext = strtolower($file->getExtension());

            $type = 'Database SQL';
            $badgeClass = 'bg-primary';

            if (str_contains($filename, 'backup_full')) {
                $type = 'Paket Sistem Lengkap (Full)';
                $badgeClass = 'bg-success';
            } elseif (str_contains($filename, 'backup_files')) {
                $type = 'Berkas Upload (Storage)';
                $badgeClass = 'bg-info';
            } elseif ($ext === 'zip') {
                $type = 'Arsip Zip';
                $badgeClass = 'bg-warning';
            }

            $backups[] = [
                'filename'   => $filename,
                'path'       => $file->getPathname(),
                'size'       => $this->formatBytes($size),
                'size_bytes' => $size,
                'created_at' => date('d F Y H:i:s', $time),
                'timestamp'  => $time,
                'type'       => $type,
                'badge'      => $badgeClass,
                'extension'  => $ext,
            ];
        }

        // Sort backups by timestamp descending
        usort($backups, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        // Informasi sistem
        $dbName = DB::connection()->getDatabaseName();
        $tables = DB::select('SHOW TABLES');
        $tablesCount = count($tables);

        $storagePublicPath = storage_path('app/public');
        $storageSize = File::exists($storagePublicPath) ? $this->getFolderSize($storagePublicPath) : 0;

        $sysInfo = [
            'db_name'       => $dbName,
            'tables_count'  => $tablesCount,
            'storage_size'  => $this->formatBytes($storageSize),
            'php_version'   => PHP_VERSION,
            'zip_enabled'   => extension_loaded('zip'),
            'pdo_driver'    => DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME),
        ];

        return view('admin.backup-restore.index', compact('backups', 'sysInfo'));
    }

    /**
     * Buat Backup Baru (Database, Files, atau Full)
     */
    public function create(Request $request)
    {
        $request->validate([
            'backup_type' => 'required|in:database,files,full',
        ]);

        $type = $request->backup_type;
        $timestamp = date('Y-m-d_H-i-s');

        try {
            if ($type === 'database') {
                $filename = "backup_db_{$timestamp}.sql";
                $filepath = $this->backupPath . '/' . $filename;
                $this->dumpDatabase($filepath);
                $msg = "Backup Database berhasil dibuat ({$filename}).";
            } elseif ($type === 'files') {
                if (!extension_loaded('zip')) {
                    return back()->with('error', 'Ekstensi PHP ZipArchive tidak aktif pada server.');
                }
                $filename = "backup_files_{$timestamp}.zip";
                $filepath = $this->backupPath . '/' . $filename;
                $this->zipStorageFiles($filepath);
                $msg = "Backup Berkas Media (Storage) berhasil dibuat ({$filename}).";
            } else { // Full
                if (!extension_loaded('zip')) {
                    return back()->with('error', 'Ekstensi PHP ZipArchive tidak aktif pada server.');
                }
                $filename = "backup_full_{$timestamp}.zip";
                $filepath = $this->backupPath . '/' . $filename;
                
                // Buat temporary SQL dump
                $tempSqlPath = $this->backupPath . "/temp_db_{$timestamp}.sql";
                $this->dumpDatabase($tempSqlPath);

                // Package full zip
                $this->zipFullSystem($filepath, $tempSqlPath);

                // Hapus temp sql
                if (File::exists($tempSqlPath)) {
                    File::delete($tempSqlPath);
                }

                $msg = "Backup Sistem Lengkap berhasil dibuat ({$filename}).";
            }

            return response()->download($filepath);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Download Berkas Backup
     */
    public function download($filename)
    {
        $filename = basename($filename);
        $filepath = $this->backupPath . '/' . $filename;

        if (!File::exists($filepath)) {
            return back()->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($filepath);
    }

    /**
     * Restore Data dari Backup (File Terdaftar atau Upload Baru)
     */
    public function restore(Request $request)
    {
        $request->validate([
            'filename'    => 'nullable|string',
            'backup_file' => 'nullable|file|mimes:sql,zip|max:512000', // Maks 500MB upload
        ]);

        $filepath = null;
        $tempUploadPath = null;

        try {
            if ($request->hasFile('backup_file')) {
                $uploadedFile = $request->file('backup_file');
                $originalName = $uploadedFile->getClientOriginalName();
                $tempName = 'upload_restore_' . time() . '_' . $originalName;
                $uploadedFile->move($this->backupPath, $tempName);
                $filepath = $this->backupPath . '/' . $tempName;
                $tempUploadPath = $filepath;
            } elseif ($request->filled('filename')) {
                $filename = basename($request->filename);
                $filepath = $this->backupPath . '/' . $filename;
            } else {
                return back()->with('error', 'Pilih file backup atau unggah berkas backup terlebih dahulu.');
            }

            if (!File::exists($filepath)) {
                return back()->with('error', 'File backup tidak ditemukan.');
            }

            $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

            if ($ext === 'sql') {
                $this->restoreSqlFile($filepath);
                $msg = "Restore Database dari file SQL berhasil dilaksanakan.";
            } elseif ($ext === 'zip') {
                $this->restoreZipFile($filepath);
                $msg = "Restore Berkas & Database dari Paket Zip berhasil dilaksanakan.";
            } else {
                return back()->with('error', 'Format file tidak didukung untuk restore.');
            }

            // Bersihkan cache aplikasi
            Artisan::call('cache:clear');
            Artisan::call('view:clear');

            // Hapus file temp upload jika ada
            if ($tempUploadPath && File::exists($tempUploadPath)) {
                File::delete($tempUploadPath);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            if ($tempUploadPath && File::exists($tempUploadPath)) {
                File::delete($tempUploadPath);
            }
            return back()->with('error', 'Gagal melaksanakan Restore: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Berkas Backup
     */
    public function destroy($filename)
    {
        $filename = basename($filename);
        $filepath = $this->backupPath . '/' . $filename;

        if (File::exists($filepath)) {
            File::delete($filepath);
            return back()->with('success', 'File backup berhasil dihapus.');
        }

        return back()->with('error', 'File backup tidak ditemukan.');
    }

    /**
     * Perbaiki Storage Link - Buat symlink atau copy file ke public/storage
     * Solusi untuk shared hosting yang tidak support symlink
     */
    public function fixStorageLink()
    {
        $storagePublic  = storage_path('app/public');
        $publicStorage  = public_path('storage');
        $methods        = [];
        $success        = false;

        // Pastikan folder storage/app/public ada
        if (!File::exists($storagePublic)) {
            File::makeDirectory($storagePublic, 0755, true, true);
        }

        // Coba 1: Buat symlink via PHP function (jika fungsi symlink tersedia)
        if (function_exists('symlink')) {
            try {
                if (is_link($publicStorage)) {
                    @unlink($publicStorage);
                }
                if (!file_exists($publicStorage)) {
                    @symlink($storagePublic, $publicStorage);
                    if (is_link($publicStorage) || File::exists($publicStorage)) {
                        $methods[] = 'Symlink berhasil dibuat (PHP symlink)';
                        $success = true;
                    }
                }
            } catch (\Throwable $e) {
                $methods[] = 'PHP symlink gagal: ' . $e->getMessage();
            }

            if (!$success) {
                try {
                    Artisan::call('storage:link');
                    $methods[] = 'Artisan storage:link berhasil';
                    $success = true;
                } catch (\Throwable $e) {
                    $methods[] = 'Artisan storage:link gagal: ' . $e->getMessage();
                }
            }
        } else {
            $methods[] = 'Fungsi symlink() dinonaktifkan di server PHP (disable_functions)';
        }

        // Coba 2: Mode Copy (Fallback untuk shared hosting / aaPanel tanpa symlink)
        if (!$success) {
            try {
                if (!File::exists($publicStorage)) {
                    File::makeDirectory($publicStorage, 0755, true, true);
                }
                $this->copyDirectorySafe($storagePublic, $publicStorage);
                $methods[] = 'File berhasil disalin ke public/storage (Mode Copy Fallback)';
                $success = true;
            } catch (\Throwable $e3) {
                $methods[] = 'Copy file gagal: ' . $e3->getMessage();
            }
        }

        $detail = implode(' | ', $methods);

        if ($success) {
            return back()->with('success', 'Storage berhasil diperbaiki! Foto & berkas media sekarang dapat diakses. Detail: ' . $detail);
        }

        return back()->with('error', 'Gagal memperbarui storage link. Detail: ' . $detail);
    }

    // ==========================================
    // PRIVATE HELPER METHODS (DUMP & RESTORE)
    // ==========================================

    /**
     * Dump Database ke File .sql menggunakan PDO
     */
    private function dumpDatabase($outputPath)
    {
        $pdo = DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();

        $handle = fopen($outputPath, 'w');
        if (!$handle) {
            throw new \Exception("Gagal membuka file untuk penulisan SQL.");
        }

        fwrite($handle, "-- ========================================================\n");
        fwrite($handle, "-- PPDB SMK Wisata Indonesia Backup Database Dump\n");
        fwrite($handle, "-- Database: {$dbName}\n");
        fwrite($handle, "-- Tanggal: " . date('d F Y H:i:s') . "\n");
        fwrite($handle, "-- ========================================================\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
        fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n\n");

        $tables = DB::select('SHOW TABLES');
        $tableKey = "Tables_in_" . $dbName;

        foreach ($tables as $tableObj) {
            $table = $tableObj->$tableKey ?? current((array)$tableObj);

            // Skip view jika ada
            $createTableRes = DB::select("SHOW CREATE TABLE `{$table}`");
            if (empty($createTableRes)) {
                continue;
            }

            $createSql = $createTableRes[0]->{'Create Table'} ?? null;

            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "-- Struktur Tabel `{$table}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
            if ($createSql) {
                fwrite($handle, $createSql . ";\n\n");
            }

            // Dump data
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                fwrite($handle, "-- Data untuk Tabel `{$table}`\n");
                foreach ($rows as $row) {
                    $rowArr = (array)$row;
                    $values = array_map(function ($val) use ($pdo) {
                        if (is_null($val)) {
                            return 'NULL';
                        }
                        return $pdo->quote($val);
                    }, array_values($rowArr));

                    $cols = array_map(function ($col) {
                        return "`{$col}`";
                    }, array_keys($rowArr));

                    $sql = "INSERT INTO `{$table}` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $values) . ");\n";
                    fwrite($handle, $sql);
                }
                fwrite($handle, "\n");
            }
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($handle);
    }

    /**
     * Restore database dari file .sql
     */
    private function restoreSqlFile($sqlPath)
    {
        $sql = File::get($sqlPath);
        if (empty($sql)) {
            throw new \Exception("Isi berkas SQL kosong.");
        }

        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
        
        DB::unprepared($sql);

        $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
    }

    /**
     * Zip media files storage
     */
    private function zipStorageFiles($zipPath)
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Gagal membuka file ZipArchive.");
        }

        $storagePublic = storage_path('app/public');
        if (File::exists($storagePublic)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePublic),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // Konversi backslash Windows ke forward slash agar compatible di Linux
                    $relativePath = 'storage_public/' . str_replace('\\', '/', substr($filePath, strlen($storagePublic) + 1));
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $uploadsPublic = public_path('uploads');
        if (File::exists($uploadsPublic)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($uploadsPublic),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // Konversi backslash Windows ke forward slash agar compatible di Linux
                    $relativePath = 'uploads_public/' . str_replace('\\', '/', substr($filePath, strlen($uploadsPublic) + 1));
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $zip->close();
    }

    /**
     * Zip Paket Full (Database + Media Files + Manifest)
     */
    private function zipFullSystem($zipPath, $tempSqlPath)
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Gagal membuat berkas paket Zip.");
        }

        // Tambah database.sql
        if (File::exists($tempSqlPath)) {
            $zip->addFile($tempSqlPath, 'database.sql');
        }

        // Tambah Manifest
        $manifest = [
            'app_name'     => 'PPDB SMK Wisata Indonesia',
            'created_at'   => date('Y-m-d H:i:s'),
            'php_version'  => PHP_VERSION,
            'laravel'      => app()->version(),
        ];
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

        // Tambah Berkas Storage
        $storagePublic = storage_path('app/public');
        if (File::exists($storagePublic)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePublic),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // Konversi backslash Windows ke forward slash agar compatible di Linux
                    $relativePath = 'storage_public/' . str_replace('\\', '/', substr($filePath, strlen($storagePublic) + 1));
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $uploadsPublic = public_path('uploads');
        if (File::exists($uploadsPublic)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($uploadsPublic),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    // Konversi backslash Windows ke forward slash agar compatible di Linux
                    $relativePath = 'uploads_public/' . str_replace('\\', '/', substr($filePath, strlen($uploadsPublic) + 1));
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $zip->close();
    }

    /**
     * Restore Zip file (Database + Storage Media Files)
     */
    private function restoreZipFile($zipPath)
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \Exception("Gagal membaca berkas zip backup.");
        }

        $tempExtractDir = $this->backupPath . '/temp_extract_' . time();
        File::makeDirectory($tempExtractDir, 0755, true, true);

        $zip->extractTo($tempExtractDir);
        $zip->close();

        // Perbaiki file-file hasil ekstraksi jika ada backslash (\) dari Windows
        $this->fixExtractedBackslashes($tempExtractDir);

        // Check if database.sql exists
        $sqlPath = $tempExtractDir . '/database.sql';
        if (File::exists($sqlPath)) {
            $this->restoreSqlFile($sqlPath);
        } else {
            // Search for any .sql file inside extract dir
            $sqlFiles = File::glob($tempExtractDir . '/*.sql');
            if (!empty($sqlFiles)) {
                $this->restoreSqlFile($sqlFiles[0]);
            }
        }

        // Restore storage_public files
        $extractedStorage = $tempExtractDir . '/storage_public';
        if (File::exists($extractedStorage)) {
            $targetPublic = storage_path('app/public');
            $this->copyDirectorySafe($extractedStorage, $targetPublic);

            // Juga salin langsung ke public/storage untuk jaminan tampilan di shared hosting
            $publicStorage = public_path('storage');
            $this->copyDirectorySafe($extractedStorage, $publicStorage);
        }

        // Restore uploads_public files
        $extractedUploads = $tempExtractDir . '/uploads_public';
        if (File::exists($extractedUploads)) {
            $targetUploads = public_path('uploads');
            $this->copyDirectorySafe($extractedUploads, $targetUploads);
        }

        // Coba buat ulang symlink storage jika symlink didukung di server
        if (function_exists('symlink')) {
            try {
                $publicStorage = public_path('storage');
                if (!is_link($publicStorage) && !File::exists($publicStorage)) {
                    Artisan::call('storage:link');
                }
            } catch (\Throwable $e) {
                // Ignore if symlink fails or disabled
            }
        }

        // Hapus direktori temp extract
        File::deleteDirectory($tempExtractDir);
    }

    private function copyDirectorySafe($src, $dst)
    {
        if (!File::exists($src)) return;
        if (!File::exists($dst)) {
            File::makeDirectory($dst, 0755, true, true);
        }

        $dir = @opendir($src);
        if (!$dir) return;

        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') continue;

            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . $file;

            if (is_dir($srcPath)) {
                $this->copyDirectorySafe($srcPath, $dstPath);
            } else {
                if (File::exists($dstPath)) {
                    @chmod($dstPath, 0666);
                    @unlink($dstPath);
                }
                @copy($srcPath, $dstPath);
                @chmod($dstPath, 0644);
            }
        }
        closedir($dir);
    }

    private function fixExtractedBackslashes($dir)
    {
        if (!File::exists($dir)) return;

        try {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                $filename = $file->getFilename();
                if (str_contains($filename, '\\')) {
                    $oldPath = $file->getRealPath();
                    $parentDir = dirname($oldPath);
                    $parts = explode('\\', $filename);
                    $curr = $parentDir;
                    foreach ($parts as $idx => $part) {
                        $curr .= '/' . $part;
                        if ($idx < count($parts) - 1) {
                            if (!File::exists($curr)) {
                                File::makeDirectory($curr, 0755, true, true);
                            }
                        } else {
                            @rename($oldPath, $curr);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore iterator errors
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function getFolderSize($dir)
    {
        $size = 0;
        foreach (File::allFiles($dir) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }

    /**
     * Reset Data Sistem / Inisialisasi Tahun Ajaran Baru
     */
    public function resetData(Request $request)
    {
        $request->validate([
            'admin_password' => 'required',
            'confirmation_text' => 'required',
            'reset_scope' => 'nullable|in:pendaftaran_only,full_reset',
        ], [
            'admin_password.required' => 'Password Superadmin wajib diisi.',
            'confirmation_text.required' => 'Teks konfirmasi wajib diisi.',
        ]);

        // Verifikasi password superadmin
        if (!Hash::check($request->admin_password, auth()->user()->password)) {
            return back()->with('error', 'Password Superadmin salah! Proses reset data dibatalkan.');
        }

        // Verifikasi kata konfirmasi
        if (trim(strtoupper($request->confirmation_text)) !== 'RESET DATA SYSTEM') {
            return back()->with('error', 'Kata konfirmasi tidak sesuai! Ketik "RESET DATA SYSTEM" untuk mengonfirmasi.');
        }

        $resetScope = $request->input('reset_scope', 'pendaftaran_only');
        $autoBackup = $request->has('auto_backup');

        try {
            // 1. Otomatis buat backup database sebelum hapus jika auto_backup dicentang
            $backupFilename = null;
            if ($autoBackup) {
                $timestamp = date('Y-m-d_H-i-s');
                $backupFilename = "auto_backup_before_reset_{$timestamp}.sql";
                $backupFilepath = $this->backupPath . '/' . $backupFilename;
                $this->dumpDatabase($backupFilepath);
            }

            // 2. Matikan foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // 3. Truncate data pendaftaran & transaksi
            DB::table('data_siswa')->truncate();
            DB::table('users_siswa')->truncate();
            DB::table('pembayaran')->truncate();
            DB::table('activity_logs')->truncate();
            DB::table('whatsapp_logs')->truncate();

            if (Schema::hasTable('failed_jobs')) {
                DB::table('failed_jobs')->truncate();
            }
            if (Schema::hasTable('personal_access_tokens')) {
                DB::table('personal_access_tokens')->truncate();
            }
            if (Schema::hasTable('password_reset_tokens')) {
                DB::table('password_reset_tokens')->truncate();
            }

            if ($resetScope === 'full_reset') {
                $masterTables = [
                    'jurusans',
                    'jurusan',
                    'kuota_jurusan',
                    'kuota_jurusans',
                    'tahun_ajaran',
                    'data_smp',
                    'master_biaya',
                    'gelombang_pendaftaran',
                    'visitors',
                ];

                foreach ($masterTables as $mTable) {
                    if (Schema::hasTable($mTable)) {
                        DB::table($mTable)->truncate();
                    }
                }
            }

            // 4. Hidupkan kembali foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            // 5. Hapus berkas pendaftar di storage
            $this->cleanPendaftarStorageFiles();

            $msg = 'Berhasil mereset data pendaftaran & transaksi sistem!';
            if ($backupFilename) {
                $msg .= " Cadangan database otomatis telah dibuat ({$backupFilename}).";
            }

            return back()->with('success', $msg);

        } catch (\Throwable $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return back()->with('error', 'Gagal mereset data sistem: ' . $e->getMessage());
        }
    }

    /**
     * Bersihkan berkas pendaftar di storage (kecuali folder pengaturan logo/branding)
     */
    private function cleanPendaftarStorageFiles()
    {
        $storagePublic = storage_path('app/public');
        if (!File::exists($storagePublic)) return;

        // Clean subdirectories except 'pengaturan'
        $directories = File::directories($storagePublic);
        foreach ($directories as $dir) {
            $dirName = basename($dir);
            if ($dirName !== 'pengaturan') {
                File::cleanDirectory($dir);
            }
        }

        // Clean loose files directly under storage/app/public
        $files = File::files($storagePublic);
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (!str_starts_with($filename, '.')) {
                File::delete($file->getPathname());
            }
        }

        // Clean public/storage mirror copies if they exist (for shared hosting mode copy)
        $publicStorage = public_path('storage');
        if (File::exists($publicStorage) && !is_link($publicStorage)) {
            $publicDirs = File::directories($publicStorage);
            foreach ($publicDirs as $dir) {
                if (basename($dir) !== 'pengaturan') {
                    File::cleanDirectory($dir);
                }
            }
        }
    }
}
