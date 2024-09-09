<?php

namespace App\Tests\Service;

use App\DTO\ApiResponse;
use App\DTO\EmployeeProviderA;
use App\DTO\EmployeeProviderB;
use App\Service\HttpService;
use App\Service\TrackTikService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TracktikServiceTest extends KernelTestCase
{
    private $httpService;
    // private $httpService;
    private $trackTikService;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->httpService = $this->createMock(HttpService::class);
    }

    private function setupMockCallback($url, $statusCode = 200, $success = true): ApiResponse {
        if (!$success) {
            return new ApiResponse($statusCode, false, 'Error');
        }

        if (strpos($url, 'access_token') !== false) {
            $data = [
                "id_token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI3ZDBiZWJlMTliMDA1ZWViN2NjM2NmZGIiLCJpc3MiOiJodHRwczovL3Ntb2tlLnN0YWZmci5uZXQiLCJpYXQiOjE3MjU3ODgxNzYsImV4cCI6MTcyNTc5MTc3Niwic3ViIjoiMzM5MSIsIm5hbWUiOiJ0ZXN0IGVtcGxveWVlIiwiZ2l2ZW5fbmFtZSI6InRlc3QiLCJmYW1pbHlfbmFtZSI6ImVtcGxveWVlIiwibWlkZGxlX25hbWUiOm51bGwsInByZWZlcnJlZF91c2VybmFtZSI6InRlc3R0cmFja3RpayIsInByb2ZpbGUiOiIjL2VtcGxveWVlL2RlZmF1bHQvdmlldy9pZC8ybTcvay85ZTI0MDNlMzU2YzVlMDY1MzZhYzhhY2IwN2NiNmNiODNhNDJhMmRkIiwicGljdHVyZSI6ImRhdGE6aW1hZ2Uvc3ZnK3htbDtiYXNlNjQsUEhOMlp5QjJaWEp6YVc5dVBTY3hMakVuSUhodGJHNXpQU2RvZEhSd09pOHZkM2QzTG5jekxtOXlaeTh5TURBd0wzTjJaeWNnZDJsa2RHZzlKelV3SnlCb1pXbG5hSFE5SnpVd0p6NDhjbVZqZENCNFBTY3dKeUI1UFNjd0p5QjNhV1IwYUQwbk5UQW5JR2hsYVdkb2REMG5OVEFuSUdacGJHdzlKeU13WWpObE1URW5Qand2Y21WamRENDhkR1Y0ZENCNFBTY3lOU2NnZVQwbk16SW5JSFJsZUhRdFlXNWphRzl5UFNkdGFXUmtiR1VuSUdadmJuUXRjMmw2WlQwbk1qSXVOU2NnWm1sc2JEMG5JMlptWm1abVppY2dabTl1ZEMxbVlXMXBiSGs5SjNOaGJuTXRjMlZ5YVdZblBsUkZQQzkwWlhoMFBqd3vjM1puUGc9PSIsIndlYnNpdGUiOm51bGwsImJpcnRoZGF0ZSI6bnVsbCwibG9jYWxlIjoiRU4iLCJ6b25laW5mbyI6IkFtZXJpY2EvTW9udHJlYWwiLCJ1cGRhdGVkX2F0IjoxNzI1Nzg4MTc2fQ.Ekv_li-L95CVpcwEysNz2fgwfs5ok_NnXfLDgeDeGZJiHuyV2Rh3THyUlFIGqgMQkQjwyHbY8S8_IkM86Owo-WjQmQ_HLqoO28zcwGEufT-ML1csy0jrJ4oalzV19w8z_EVFsjznhDeUsIJK1_u_OZb2_iqmX3OcHDObReUI974tYQ1x-IDH8jWzsl0EffOWZEQVnZCg3pAlTwxNBzc5ls08CyUVzjp800I6JlqFpo3A4pjZToT1XAzVIyV2yLzCGUbSjS3QxGJmSsFV0E5aj6S_jQjlVr2DWVQkzfJF8WagtkgRzCftap01hCLu373IsoRqFoXy0aUZ3sGlPRLc6MmyzQU3-A_W3XyGRbxjDzuMITYCiILNomgFHLIEqdtyZASvRtpwH1knmYGnrinwpAWn97eiMk6weB8KnI_FdH7Hm5B3awB7KZh3yBS6E0kZH12LW0RCq_xlRIxfC16Q2kp0LVyIH80AK-3Px5QYSTLriBSWu3z4ELneqfSn_kjg4AdnW9-SN5itVNUG5lRowkaPZDd43K7lDeGKTuJWeYuy3eSGtH-a1hJqC5zJdw3LsIPx7lcn0Pek5k9kfei324xkNwf4PE19h9OJZeEt3CtBYt9yElNqZF3uyjT4yDu7oQT1QsVB9g2KGZd-LCwbgxqno66o_J9uq-4_6-hoRZ0",
                "media_token" => "8b81372b091627e341827049268d259e017d211ffda1feb97ce04af282d83b19",
                "token_type" => "Bearer",
                "expires_in" => 3600,
                "access_token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6IlJTMjU2LWN1cnJlbnQifQ.eyJhdWQiOiI3ZDBiZWJlMTliMDA1ZWViN2NjM2NmZGIiLCJqdGkiOiIyY2EyMzViMzMwOWMxNDkxNmJmNjM3NzhmMzQyNDA5YzZjZjE4NTJmYjBlMzlmMDc3ZTIzODcyNGY3MGJkMmQ1ZGEwMjQwZDYxNzQ1MDYwZiIsImlhdCI6MTcyNTc4ODE3Ni4zNTY5MDIsImlzcyI6InNtb2tlLnN0YWZmci5uZXQiLCJuYmYiOjE3MjU3ODgxNzYuMzU2OTEzLCJleHAiOjE3MjU3OTE3NzYsInN1YiI6IjMzOTEiLCJzY29wZXMiOlsib3BlbmlkIiwicHJvZmlsZSIsImNvcmU6ZW50aXRpZXM6ZW1wbG95ZWVzOnJlYWQiLCJjb3JlOmVudGl0aWVzOmVtcGxveWVlczp3cml0ZSJdfQ.R2n2XkSNogIt0rKUP5JaKsc16s-9gMpR_ER8vVpCPmJxgkVDy3BVa8a7tPMYwaY-J8ol-YyEIeUaKh1hQ0sfPqo2gqlruTUyK3XbnRkgBobqBgKXZbgG-taJCk8K65UNDVB2dzYV39eaZRO4do8d-jO6GHgc5qqWfSw3QjPwWBXTGJqKo-VTuwMWu3Tl4CG_e5ttqhMQoWKA-tYI7VpkjMVRLkEHXjIOt9Z85Psf-u8KiPUhWuBJSy4UlRuyF5h-sy1JLMlncXBAw7b9Pz4tH8BOw3EUqHSGaBbTw9XmZRrnDfwzn92der7AJXCJ317CrbYiVgIEuh1DwsGoLOaYCedfPxPgs-44AltdvHHsnjulD1MKSwkdaE2O2Md-r3luOq7uzUtrMKA-0PX6hIafWx2OA-o9RgBhZW0s440tHrxZIduTyFU-Vb4vgT44755-Eqf9S09DBDN1aFYetS8DPcxdIFcYDlz8il0TG-6BAtWdfkD6_ypIoL6ElHb7ynrRn-4i3eshmQXhTDT0ER5b-7WXoqWzNYHKWMMJnQCC2P_3P17Utx1Y0MPqAs2vMu4JNXVwuIUEkpqFt-TxEW4pQinjsiu7N0UEHAGItYMEWqy08HhieTWQsCsOP",
                "refresh_token" => "def50200cd7c1ad19a873b421f28806a83b911083cd0b73b4f48c81491f1d172c70547825361a27f46b18fa0fbadb53973b334e85af6152403c7338cb54fdf70c7ed4a32637c62763fa4e63fde224ef613014172d0e67851a7064ac0dbc39425318680d1472581e6fda6619846294183d4487b4dec4d93058c987065981f49c3ebc9837cf1c6ab61bc88afe8920f9310a940f8df665ddf5d7267c3db76ad05357598286abe1197e9cf4d3cb875b4d743076e78a0b21ed61f52a8ced9e6119052c39d9612f8e108c32ef9255eae3eb114e4b0a4194d16e76fec7c6466449db1a93f21d9f13bea1d238c33be2d2ecf7fa6c873f6731af3901af4d4cdf61bd6d329fa489006184495146f8f97f014592be32370934dfe4372e9d547156ed579d43e3bead0456e799d889c9e10bc4a84f4089e94080816ef07eec1afac769364ba3de8b1ef86ce5bf71a8395cb1971e7304a4260996cc28295e4c87a48a75ac2a269ece271a0b8b10eb7e4a2b8258fbbf8ba948cbd47751e11c046a31cf2b5513c4d997a5c2cef761d34262361f740adea39fb61e6c5251a7c495c9d0538da34fb76c75058125a669bdc0c209d3ad4d817b363545fd513ecbc75f1e5c85fe0d896b1b7b3bba40c081b1e94ba17143055"    
            ];
            return new ApiResponse(200, true, '', $data);
        } else {
            $employeeData = [
                "jobTitle" => "Assistant",
                "region" => 123,
                "employmentProfile" => 123,
                "gender" => "M",
                "age" => 123,
                "birthday" => "2019-08-24",
                "id" => 12345,
                "customId" => "C123-A",
                "firstName" => "John",
                "lastName" => "Smith",
                "name" => "Sample Name",
                "primaryPhone" => "555-555-1234",
                "secondaryPhone" => "555-555-4321",
                "username" => "string",
                "email" => "john.smith@myemail.com",
                "tags" => [
                    "string"
                ]
            ];
            return new ApiResponse(200, true, '', $employeeData);
        }
    }

    public function testCreateEmployeeSuccess()
    {

        $container = static::getContainer();
        $self = $this;
        $this->httpService->expects($this->atLeastOnce())
            ->method('makeRequest')
            ->willReturnCallback(function ($method, $url) use ($self) {
                return $self->setupMockCallback($url, 200, true);
            });

        self::getContainer()->set(HttpService::class, $this->httpService, ['makeRequest']);

        // Test for provider A
        $data  = ['email' => 'est@gmail.com', 'gender' => 'Male', 'first_name' => 'Test', 'last_name' => 'User'];
        $employee = new EmployeeProviderA($data);

        $this->trackTikService = $container->get(TrackTikService::class);
        $result = $this->trackTikService->createEmployee($employee);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(12345, $result->getData()['id']);
        $this->assertEquals(true, $result->isSuccess());

         // Test for provider B
        $data  = ['contact' => ['email' => 'est@gmail.com'], 'sex' => 'other', 'fname' => 'Test2', 'lname' => 'User2'];
        $employee = new EmployeeProviderB($data);

        $result = $this->trackTikService->createEmployee($employee);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals(12345, $result->getData()['id']);
        $this->assertEquals(true, $result->isSuccess());
    }

    public function testCreateEmployeeFailureInvalidData()
    {
        $container = static::getContainer();
        $self = $this;
        $this->httpService->expects($this->never())
            ->method('makeRequest')
            ->willReturnCallback(function ($method, $url) use ($self) {
                return $self->setupMockCallback($url, 200, true);
            });

        self::getContainer()->set(HttpService::class, $this->httpService, ['makeRequest']);

        // Test for provider A
        $data  = ['email' => 'test@gmail.com', 'gender' => 'Male', 'last_name' => 'User'];
        $employee = new EmployeeProviderA($data);
        $this->trackTikService = $container->get(TrackTikService::class);
        $result = $this->trackTikService->createEmployee($employee);

        $this->assertEquals(400, $result->getStatusCode());
        $this->assertEquals(false, $result->isSuccess());

        // Test for provider B
        $data  = ['email' => 'test@gmail.com', 'gender' => 'Male', 'fname' => 'Test'];
        $employee = new EmployeeProviderB($data);
        $result = $this->trackTikService->createEmployee($employee);

        $this->assertEquals(400, $result->getStatusCode());
        $this->assertEquals(false, $result->isSuccess());
    }

    public function testCreateEmployeeFailureApiData()
    {        
        $container = static::getContainer();
        $self = $this;
        $this->httpService->expects($this->atLeastOnce())
            ->method('makeRequest')
            ->willReturnCallback(function ($method, $url) use ($self) {
                return $self->setupMockCallback($url, 400, false);
            });
        
        self::getContainer()->set(HttpService::class, $this->httpService, ['makeRequest']);

        // Test for provider A
        $data  = ['email' => 'test@gmail.com', 'gender' => 'Male', 'first_name' => 'Test', 'last_name' => 'User'];
        $employee = new EmployeeProviderA($data);
        $this->trackTikService = $container->get(TrackTikService::class);
        $result = $this->trackTikService->createEmployee($employee);

        $this->assertEquals(400, $result->getStatusCode());
        $this->assertEquals(false, $result->isSuccess());

        // Test for provider B
        $data  = ['contact' => ['email' => 'test@gmail.com'], 'sex' => 'other', 'fname' => 'Test2', 'lname' => 'User2'];
        $employee = new EmployeeProviderB($data);
        $result = $this->trackTikService->createEmployee($employee);

        $this->assertEquals(400, $result->getStatusCode());
        $this->assertEquals(false, $result->isSuccess());
    }
}
