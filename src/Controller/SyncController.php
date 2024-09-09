<?php
namespace App\Controller;

use App\DTO\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\DTO\EmployeeProviderA;
use App\DTO\EmployeeProviderB;
use App\Repository\EmployeeRepository;
use App\Service\TrackTikService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api')]
class SyncController extends AbstractController
{
    private TrackTikService $trackTikService;
    private EmployeeRepository $employeeRepository;

    public function __construct(TrackTikService $trackTikService, EmployeeRepository $employeeRepository)
    {
        $this->trackTikService = $trackTikService;
        $this->employeeRepository = $employeeRepository;
    }

    #[Route('/sync/{provider}', name: 'sync', methods:['POST', 'GET'])]
    public function sync(Request $request): Response
    {
        $params = $request->attributes->get('_route_params');
        $provider = $params['provider'] ?? 'provider_a';
        $data = json_decode($request->getContent(), true);
        // $data  = ['email' => 'est@gmail.com', 'gender' => 'Male', 'first_name' => 'Test', 'last_name' => 'User'];

        if ($provider === 'provider_a') {
            $employee = new EmployeeProviderA($data);
        } elseif ($provider === 'provider_b') {
            $employee = new EmployeeProviderB($data);
        } else {
            return new JsonResponse(['error' => 'Invalid provider'], 400);
        }

        $error = $employee->validate();
        if ($error) {
            return new JsonResponse(['error' => $error], 400);
        }

        $result = $this->trackTikService->createEmployee($employee);
        if (!$result->isSuccess()) {
            return new JsonResponse($result->toArray(), $result->getStatusCode());
        }
        
        return new JsonResponse($result->getData(), $result->getStatusCode());
    }
}