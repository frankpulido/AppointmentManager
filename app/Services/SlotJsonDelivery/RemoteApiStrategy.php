<?php
declare(strict_types=1);
namespace App\Services\SlotJsonDelivery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Exception;
use Throwable;

class RemoteApiStrategy extends SlotJsonDeliveryStrategy
{
    private string $remoteApiUrl;
    private array $headers;
    private int $timeout;
    private int $retries;

    public function __construct()
    {
        $this->remoteApiUrl = config('slot_json.remote_api_url');
        $this->timeout = config('slot_json.remote_timeout', 30);
        $this->retries = config('slot_json.remote_retries', 3);
        
        // Configure authentication headers if needed
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // Add API key if configured
        if ($apiKey = config('slot_json.remote_api_key')) {
            $this->headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        // Validate configuration
        if (empty($this->remoteApiUrl)) {
            throw new InvalidArgumentException('Remote API URL must be configured in slot_json.remote_api_url');
        }
    }

    /**
     * Deliver JSON data to remote API endpoint.
     *
     * @param string $filename
     * @param array $jsonData
     * @throws Exception
     */
    public function deliver(string $filename, array $jsonData): void
    {
        $this->validateFilename($filename);
        
        try {
            // Prepare payload
            $payload = [
                'filename' => $filename,
                'data' => $jsonData,
                'timestamp' => now()->toISOString(),
                'source' => config('app.name', 'Laravel App')
            ];

            // Build endpoint URL
            $endpoint = rtrim($this->remoteApiUrl, '/') . '/' . ltrim($filename, '/');

            // Make HTTP request with retries
            $response = Http::withHeaders($this->headers)
                ->timeout($this->timeout)
                ->retry($this->retries, 1000) // Retry 3 times with 1 second delay
                ->post($endpoint, $payload);

            if (!$response->successful()) {
                throw new Exception(
                    "Remote API returned error status {$response->status()}: {$response->body()}"
                );
            }

            Log::info('JSON file delivered to remote API successfully', [
                'filename' => $filename,
                'endpoint' => $endpoint,
                'status_code' => $response->status(),
                'response_size' => strlen($response->body()),
                'data_size' => strlen(json_encode($jsonData))
            ]);

        } catch (Throwable $e) {
            Log::error('Failed to deliver JSON file to remote API', [
                'filename' => $filename,
                'endpoint' => $this->remoteApiUrl,
                'error' => $e->getMessage(),
                'headers' => array_keys($this->headers) // Log header keys only for security
            ]);
            
            throw new Exception("Failed to deliver JSON file to remote API: {$e->getMessage()}", 0, $e);
        }
    }
}