<?php

namespace App\Tests\Service;

use App\Enum\HealthStatus;
use App\Service\GithubService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class GithubServiceTest extends TestCase
{
    /**
     * @dataProvider dinoNameProvider
     */
    public function testGetHealthReportReturnsCorrectHealthStatusForDino(HealthStatus $expectedStatus, string $dinoName): void
    {
        // this allows us to pass in a class or interface and get back a "fake" instance of that class or interface
        $mockLogger = $this->createMock(LoggerInterface::class);
        // Mocking the HttpClient
        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        // Mocking the response
        $mockResponse = $this->createMock(ResponseInterface::class);

        $mockResponse
            ->method('toArray')
            ->willReturn([
                [
                    'title' => 'Daisy',
                    'labels' => [['name' => 'Status: Sick']],
                ],
                [
                    'title' => 'Maverick',
                    'labels' => [['name' => 'Status: Healthy']],
                ],
            ])
        ;

        $mockHttpClient
            ->method('request')
            ->willReturn($mockResponse)
        ;

        $service = new GithubService($mockHttpClient, $mockLogger);
        //$service = new GithubService(HttpClient::create(), $mockLogger);
        self::assertSame($expectedStatus, $service->getHealthReport($dinoName));
    }
    public function dinoNameProvider(): \Generator
    {
        yield 'Sick Dino' => [
            HealthStatus::SICK,
            'Daisy',
        ];
        yield 'Healthy Dino' => [
            HealthStatus::HEALTHY,
            'Maverick',
        ];
    }

}