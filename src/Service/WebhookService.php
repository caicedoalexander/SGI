<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Http\Client;
use Cake\Log\Log;
use Exception;

class WebhookService
{
    private Client $client;

    /**
     * @param int $timeout Request timeout in seconds.
     */
    public function __construct(int $timeout = 30)
    {
        $this->client = new Client([
            'timeout' => $timeout,
        ]);
    }

    /**
     * POST JSON data to a URL.
     */
    public function sendJson(string $url, array $data, array $headers = []): array
    {
        $headers['Content-Type'] = 'application/json';

        return $this->post($url, json_encode($data), $headers);
    }

    /**
     * POST a file (multipart) to a URL.
     */
    public function sendFile(
        string $url,
        string $filePath,
        string $fieldName = 'file',
        array $extraData = [],
        array $headers = [],
    ): array {
        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'statusCode' => 0,
                'body' => '',
                'error' => "File not found: {$filePath}",
            ];
        }

        try {
            $response = $this->client->post($url, array_merge($extraData, [
                $fieldName => fopen($filePath, 'r'),
            ]), [
                'headers' => $headers,
                'type' => 'multipart/form-data',
            ]);

            return [
                'success' => $response->isOk(),
                'statusCode' => $response->getStatusCode(),
                'body' => (string)$response->getBody(),
                'error' => $response->isOk() ? null : "HTTP {$response->getStatusCode()}",
            ];
        } catch (Exception $e) {
            Log::error("WebhookService::sendFile error: {$e->getMessage()}");

            return [
                'success' => false,
                'statusCode' => 0,
                'body' => '',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generic POST request.
     */
    public function post(string $url, mixed $body, array $headers = []): array
    {
        try {
            $response = $this->client->post($url, (string)$body, [
                'headers' => $headers,
            ]);

            return [
                'success' => $response->isOk(),
                'statusCode' => $response->getStatusCode(),
                'body' => (string)$response->getBody(),
                'error' => $response->isOk() ? null : "HTTP {$response->getStatusCode()}",
            ];
        } catch (Exception $e) {
            Log::error("WebhookService::post error: {$e->getMessage()}");

            return [
                'success' => false,
                'statusCode' => 0,
                'body' => '',
                'error' => $e->getMessage(),
            ];
        }
    }
}
