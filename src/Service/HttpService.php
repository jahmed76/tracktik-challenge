<?php

namespace App\Service;

use App\DTO\ApiResponse;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HttpService
{
    private HttpClientInterface $httpClient;
    private ParameterBagInterface $params;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $params)
    {
        $this->httpClient = $httpClient;
        $this->params = $params;
    }

    private function getUrl($path = ''): string {
        $base_url = $this->params->get('tracktik.base_url');
        return $base_url . $path;
    }

    public function makeRequest(string $method, string $path, array $payload, string $token): ApiResponse
    {
        try {
            // $token = 'asas';
            $headers = ['Content-Type: application/json'];
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }
            // dd($headers);
            $response = $this->httpClient->request($method, $this->getUrl($path), ['body' => $payload, 'headers' =>$headers]);
            $data = $response->toArray(false); 
            if ($response->getStatusCode() != 200) {
                return new ApiResponse($response->getStatusCode(), false, $data['message']);
            }
            return new ApiResponse(200, true, '', $data);

        } catch (Exception $e) {
            return new ApiResponse(500, false, $e->getMessage());
        }
    }
}
