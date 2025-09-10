<?php
declare(strict_types=1);
namespace App\Services\SlotJsonDelivery;

use Exception;
use InvalidArgumentException;

abstract class SlotJsonDeliveryStrategy
{
    /**
     * Deliver JSON data to the configured destination.
     *
     * @param string $filename The filename for the JSON file
     * @param array $jsonData The data to be JSON encoded and delivered
     * @throws Exception If delivery fails
     */
    abstract public function deliver(string $filename, array $jsonData): void;

    /**
     * Helper method to encode JSON with consistent formatting.
     *
     * @param array $data
     * @return string
     */
    protected function encodeJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Helper method to validate filename.
     *
     * @param string $filename
     * @throws InvalidArgumentException
     */
    protected function validateFilename(string $filename): void
    {
        if (empty($filename)) {
            throw new InvalidArgumentException('Filename cannot be empty');
        }

        if (!str_ends_with($filename, '.json')) {
            throw new InvalidArgumentException('Filename must have .json extension');
        }
    }
}