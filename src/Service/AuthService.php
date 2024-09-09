<?php

namespace App\Service;

use App\DTO\ApiResponse;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OAuthToken;

class AuthService
{
    private HttpService $httpService;
    private ParameterBagInterface $params;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, HttpService $httpService, ParameterBagInterface $params)
    {
        $this->httpService = $httpService;
        $this->params = $params;
        $this->entityManager = $entityManager;
    }

    public function getAccessToken(): ApiResponse
    {
        $token = $this->entityManager->getRepository(OAuthToken::class)->findOneBy([]);
        $accessToken = '';
        if (!$token || $token->isExpired()) {
            $resp = $this->refreshToken($token);
            if (!$resp->isSuccess()) {
                return $resp;
            }
            $newTokenData = $resp->getData();
            $this->saveToken($newTokenData);
            $accessToken = $newTokenData['access_token'];
        } else {
            $accessToken = $token->getAccessToken();
        }

        return new ApiResponse(200, true, null, ['access_token' => $accessToken]);
    }

    private function refreshToken(OAuthToken $token = null): ApiResponse
    {
        $oauth_url = $this->params->get('tracktik.oauth_token_url');
        $refreshToken = $token ? $token->getRefreshToken() : $this->params->get('tracktik.refresh_token');
        $payload = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->params->get('tracktik.client_id'),
            'client_secret' => $this->params->get('tracktik.client_secret'),
            'refresh_token' => $refreshToken,
        ];

        $resp = $this->httpService->makeRequest('POST', $oauth_url, $payload, 'valid_token');
        if (!$resp->isSuccess()) {
            return $resp;
        }

        return $resp;
    }

    private function saveToken(array $tokenData): void
    {
        $oauthToken = new OAuthToken();
        $oauthToken->setAccessToken($tokenData['access_token']);
        $oauthToken->setRefreshToken($tokenData['refresh_token']);
        $oauthToken->setExpiresAt((new \DateTime())->modify('+' . $tokenData['expires_in'] . ' seconds'));

        $this->entityManager->persist($oauthToken);
        $this->entityManager->flush();
    }
}
