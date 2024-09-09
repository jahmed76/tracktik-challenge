<?php

namespace App\Tests\Controller;

use App\DTO\ApiResponse;
use App\Service\TrackTikService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SyncControllerTest extends WebTestCase
{
    private $trackTikService;

    protected function setUp(): void
    {
        $this->trackTikService = $this->createMock(TrackTikService::class);
    }

    public function testSyncSuccessForProviderA()
    {
        $client = static::createClient();
        $this->trackTikService
            ->method('createEmployee')
            ->willReturn(new ApiResponse(200, true, null, [
                'statusCode' => 200,
                'data' => ['id' => 12345],
            ]));
        self::getContainer()->set(TrackTikService::class, $this->trackTikService, ['createEmployee']);

        $crawler = $client->request('POST', '/api/sync/provider_a', [], [], [], json_encode([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]));
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testSyncSuccessForProviderB()
    {
        $client = static::createClient();
        $this->trackTikService
            ->method('createEmployee')
            ->willReturn(new ApiResponse(200, true, null, [
                'statusCode' => 200,
                'data' => ['id' => 12345],
            ]));
        self::getContainer()->set(TrackTikService::class, $this->trackTikService, ['createEmployee']);

        $crawler = $client->request('POST', '/api/sync/provider_b', [], [], [], json_encode([
            'fname' => 'John',
            'lname' => 'Doe',
            'email' => 'john.doe@example.com',
        ]));
        
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }

    public function testSyncFailureInvalidProvider()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/api/sync/provider_c', [], [], [], json_encode([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
        ]));
        
        $this->assertResponseStatusCodeSame(400);
    }

    public function testSyncFailureApiError()
    {
        $client = static::createClient();
        $this->trackTikService
            ->method('createEmployee')
            ->willReturn(new ApiResponse(400, false, 'The email already exists'));
        self::getContainer()->set(TrackTikService::class, $this->trackTikService, ['createEmployee']);

        $crawler = $client->request('POST', '/api/sync/provider_a', [], [], [], json_encode([
            'fname' => 'John',
            'lname' => 'Doe',
            'email' => 'john.doe@example.com',
        ]));
        
        $this->assertResponseStatusCodeSame(400);
    }
}
