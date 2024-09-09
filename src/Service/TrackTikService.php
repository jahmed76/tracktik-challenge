<?php

namespace App\Service;

use App\DTO\Employee;
use App\DTO\ApiResponse;
use App\Entity\Employee as EmployeeEntity;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;

class TrackTikService
{
    private HttpService $httpService;
    private AuthService $authService;
    private ParameterBagInterface $params;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, HttpService $httpService, AuthService $authService, ParameterBagInterface $params)
    {
        $this->httpService = $httpService;
        $this->authService = $authService;
        $this->params = $params;
        $this->entityManager = $entityManager;
    }

    public function createEmployee(Employee $employee): ApiResponse
    {
        $error = $employee->validate();
        if ( $error != "") {
            return new ApiResponse(400, false, $error);
        }

        // $employeeData = [
        //     "jobTitle" => "Assistant",
        //     "region" => 123,
        //     "employmentProfile" => 123,
        //     "gender" => "M",
        //     "age" => 123,
        //     "birthday" => "2019-08-24",
        //     "id" => 12345,
        //     "customId" => "C123-A",
        //     "firstName" => "John",
        //     "lastName" => "Smith",
        //     "name" => "Sample Name",
        //     "primaryPhone" => "555-555-1234",
        //     "secondaryPhone" => "555-555-4321",
        //     "username" => "string",
        //     "email" => "john.smith@myemail.com",
        //     "tags" => [
        //         "string"
        //     ]

        // ];
        $url = '/rest/v1/employees';
        $tokenResp = $this->authService->getAccessToken();
        if (!$tokenResp->isSuccess()) {
            return $tokenResp;
        }

        $data = $tokenResp->getData();
        $token = $data['access_token'];

        $data = $employee->prepare();

        // return new ApiResponse(200, true, '', $employeeData);
        
        $response = $this->httpService->makeRequest('POST', $url, $data, $token);
        $this->saveEmployee($employee->getProvider(), $response->getData());

        return $response;
    }

    private function saveEmployee(string $provider, array $employeeData): void
    {
        $employee = new EmployeeEntity();
        $employee->setProvider($provider);
        $employee->setTracktikEmployeeId($employeeData['id']);
        $employee->setUpdatedAt((new \DateTime()));
        $employee->setCreatedAt((new \DateTime()));

        $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }
}
