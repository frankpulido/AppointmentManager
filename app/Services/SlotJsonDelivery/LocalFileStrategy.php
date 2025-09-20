<?php
declare(strict_types=1);
namespace App\Services\SlotJsonDelivery;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;
use Throwable;

class LocalFileStrategy extends SlotJsonDeliveryStrategy
{
    private string $primaryPath;
    private ?string $backupPath;

    public function __construct()
    {
        // Primary path for development/production API access
        $this->primaryPath = public_path(config('slot_json.local.primary_path', 'api/slots'));
        //$this->primaryPath = public_path('api/slots');
        
        // Backup path (optional) - used in production for redundancy
        $this->backupPath = storage_path(config('slot_json.local.backup_path', 'app/public/slots'));
        //$this->backupPath = config('slot_json.backup_path', storage_path('app/public/slots'));
    }

    /**
     * Deliver JSON data to local file system.
     *
     * @param string $filename
     * @param array $jsonData
     * @throws Exception
     */
    public function deliver(string $filename, array $jsonData): void
    {
        $this->validateFilename($filename);
        
        try {
            // Ensure directories exist
            $this->ensureDirectoryExists($this->primaryPath);
            if ($this->backupPath) {
                $this->ensureDirectoryExists($this->backupPath);
            }

            // Encode JSON
            $jsonContent = $this->encodeJson($jsonData);

            // Write to primary location
            $primaryFile = $this->primaryPath . '/' . $filename;
            $this->writeJsonFile($primaryFile, $jsonContent);

            // Write to backup location (if configured)
            if ($this->backupPath) {
                $backupFile = $this->backupPath . '/' . $filename;
                $this->writeJsonFile($backupFile, $jsonContent);
                
                Log::info('JSON file delivered successfully', [
                    'filename' => $filename,
                    'primary_path' => $primaryFile,
                    'backup_path' => $backupFile,
                    'data_size' => strlen($jsonContent)
                ]);
            } else {
                Log::info('JSON file delivered successfully', [
                    'filename' => $filename,
                    'primary_path' => $primaryFile,
                    'data_size' => strlen($jsonContent)
                ]);
            }

        } catch (Throwable $e) {
            Log::error('Failed to deliver JSON file locally', [
                'filename' => $filename,
                'primary_path' => $this->primaryPath,
                'backup_path' => $this->backupPath,
                'error' => $e->getMessage()
            ]);
            
            throw new Exception("Failed to deliver JSON file locally: {$e->getMessage()}", 0, $e);
        }
    }

    /**
     * Ensure directory exists, create if necessary.
     *
     * @param string $path
     * @throws Exception
     */
    private function ensureDirectoryExists(string $path): void
    {
        if (!File::isDirectory($path)) {
            if (!File::makeDirectory($path, 0755, true)) {
                throw new Exception("Failed to create directory: {$path}");
            }
        }
    }

    /**
     * Write JSON content to file with atomic operation.
     *
     * @param string $filepath
     * @param string $content
     * @throws Exception
     */
    private function writeJsonFile(string $filepath, string $content): void
    {
        // Write to temporary file first, then rename (atomic operation)
        $tempFile = $filepath . '.tmp';
        
        if (file_put_contents($tempFile, $content, LOCK_EX) === false) {
            throw new Exception("Failed to write temporary file: {$tempFile}");
        }
        
        if (!rename($tempFile, $filepath)) {
            // Clean up temp file on failure
            @unlink($tempFile);
            throw new Exception("Failed to rename temporary file to final location: {$filepath}");
        }
    }
}