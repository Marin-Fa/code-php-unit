<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Dinosaur;
use App\Enum\HealthStatus;
use PHPUnit\Framework\TestCase;

class DinosaurTest extends TestCase
{
    // Equivalent to doing 42 == 42
    public function testItWorks(): void
    {
        self::assertEquals('42',42);
    }

    // Equivalent to doing 42 === 42
    public function testItWorksTheSame(): void
    {
        self::assertSame(42, 42);
    }

    public function testCanGetAndSetData(): void
    {
        $dino = new Dinosaur(
            name: 'Big Eaty',
            genus: 'Tyrannosaurus',
            length: 15,
            enclosure: 'Paddock A',
        );

//        self::assertGreaterThan(
//            $dino->getLength(),
//            10,
//            message: 'Dino is supposed to be bigger than 10 meters!'
//        );

        self::assertSame('Big Eaty', $dino->getName());
        self::assertSame('Tyrannosaurus', $dino->getGenus());
        self::assertSame(15, $dino->getLength());
        self::assertSame('Paddock A', $dino->getEnclosure());
    }

    /**
     * @dataProvider sizeDescriptionProvider
     */
    public function testDino10MetersOrGreaterIsLarge(int $length, string $expectedSize): void
    {
        $dino = new Dinosaur(name: 'Big Eaty', length: $length);

        self::assertSame($expectedSize, $dino->getSizeDescription(), 'This is supposed to be a Large Dinosaur');
    }

//    public function testDinoBetween5And9MetersIsMedium(): void
//    {
//        $dino = new Dinosaur(name: 'Big Eaty', length: 5);
//        self::assertSame('Medium', $dino->getSizeDescription(), 'This is supposed to be a Medium Dinosaur');
//    }
//    public function testDinoUnder5MetersIsSmall(): void
//    {
//        $dino = new Dinosaur(name: 'Big Eaty', length: 4);
//        self::assertSame('Small', $dino->getSizeDescription(), 'This is supposed to be a Small Dinosaur');
//    }

    public function sizeDescriptionProvider(): \Generator
    {
        yield '10 Meter Large Dino' => [10, 'Large'];
        yield '5 Meter Medium Dino' => [5, 'Medium'];
        yield '4 Meter Small Dino' => [4, 'Small'];
    }

    public function testIsAcceptingVisitorsByDefault(): void
    {
        $dino = new Dinosaur('Dennis');

        self::assertTrue($dino->isAcceptingVisitors());
    }

    /**
     * @dataProvider healthStatusProvider
     */
    public function testIsAcceptingVisitorsBasedOnHealthStatus(HealthStatus $healthStatus, bool $expectedVisitorStatus): void
    {
        $dino = new Dinosaur('Bumpy');

        $dino->setHealth($healthStatus);

        self::assertSame($expectedVisitorStatus, $dino->isAcceptingVisitors());
    }

    public function healthStatusProvider(): \Generator
    {
        yield 'Sick dino is not accepting visitors' => [HealthStatus::SICK, false];
        yield 'Hungry dino is accepting visitors' => [HealthStatus::HUNGRY, true];
    }
}