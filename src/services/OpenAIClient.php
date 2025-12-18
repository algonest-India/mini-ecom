<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use RuntimeException;

class OpenAIClient {
     private Client $client;
    private string $apiKey;

    public function __construct(string $apiKey) {
        if (empty($apiKey)) throw new RuntimeException('OpenAI key required');
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'verify' => false  // Disable SSL verification for local dev
        ]);
    }
    public function getRecommendation(string $productName): string {
        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a product recommender. Suggest 3 similar items to the given product in 50 words or less.'],
                        ['role' => 'user', 'content' => "Product: $productName"]
                    ],
                    'max_tokens' => 100,
                    'temperature' => 0.7
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'] ?? 'No recommendations available';
        } catch (RequestException $e) {
            error_log("OpenAI Error: " . $e->getMessage());
            return 'AI service unavailable';
        }
    }

    public function chatCompletion(string $prompt, string $model = 'gpt-4o-mini', int $maxTokens = 250): string {
        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'max_tokens' => $maxTokens,
                    'temperature' => 0.7
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'] ?? 'No response';
        } catch (RequestException $e) {
                error_log("OpenAI Error: " . $e->getMessage());
                return 'AI service error: ' . $e->getMessage();
            }
    }
}