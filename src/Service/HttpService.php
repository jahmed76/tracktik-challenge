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
            $headers = ['Content-Type: application/json'];
            if ($token) {
                $headers[] = 'Authorization: Bearer ' . $token;
            }

            // if (strpos($path, 'access_token') !== false && false) {
            //     return new ApiResponse(200, true, '', [
            //         'access_token' => 'test',
            //         'refresh_token' => 'testrefresh',
            //         'expires_in' => 3600
            //     ]);
            // } else if (strpos($path, 'employee') !== false) {
            //     $employeeData = [
            //         "jobTitle" => "Assistant",
            //         "region" => 123,
            //         "employmentProfile" => 123,
            //         "gender" => "M",
            //         "age" => 123,
            //         "birthday" => "2019-08-24",
            //         "id" => 12345,
            //         "customId" => "C123-A",
            //         "firstName" => "John",
            //         "lastName" => "Smith",
            //         "name" => "Sample Name",
            //         "primaryPhone" => "555-555-1234",
            //         "secondaryPhone" => "555-555-4321",
            //         "username" => "string",
            //         "email" => "john.smith@myemail.com",
            //         "tags" => [
            //             "string"
            //         ]
            //     ];
            //     return new ApiResponse(200, true, '', $employeeData);
            // }

            $response = $this->httpClient->request($method, $this->getUrl($path), ['body' => $payload, 'headers' =>$headers]);
            $data = $response->toArray(false); 

            if ($response->getStatusCode() != 200) {
                $error = $data['message'] ?? null;
                if ($error == null) {
                    $error = $data['error'] ?? null;
                }
                return new ApiResponse($response->getStatusCode(), false, $error);
            }
            return new ApiResponse(200, true, '', $data);

        } catch (Exception $e) {
            return new ApiResponse(500, false, $e->getMessage());
        }
    }
}
