<?php

namespace App\Tests\Service;

use App\Enum\HealthStatus;
use App\Service\GithubService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GithubServiceTest extends TestCase
{
    /**
     * @dataProvider dinoNameProvider
     */
    public function testGetHealthReportReturnsCorrectHealthStatusForDino(HealthStatus $expectedStatus, string $dinoName): void
    {
        // this allows us to pass in a class or interface and get back a "fake" instance of that class or interface
        $mockLogger = $this->createMock(LoggerInterface::class);

        $service = new GithubService($mockLogger); // API call
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