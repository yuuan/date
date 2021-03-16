<?php

declare(strict_types=1);

namespace Yuuan\Tests\Date;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Yuuan\Date\Date;

class DateTest extends TestCase
{
    /** @dataProvider provideDateForConstruct */
    public function testConstract(DateTimeInterface $date, string $expected): void
    {
        $subject = new Date($date);

        $this->assertInstanceOf(Date::class, $subject);
        $this->assertSame($expected, (string) $subject);
    }

    public function provideDateForConstruct(): array
    {
        return [
            'DateTime' => [new DateTime('2020-11-22 10:00:00'), '2020-11-22'],
            'DateTimeImmutable' => [new DateTimeImmutable('2020-11-22 10:00:00'), '2020-11-22'],
            'Carbon' => [new Carbon('2020-11-22 10:00:00'), '2020-11-22'],
            'CarbonImmutable' => [new CarbonImmutable('2020-11-22 10:00:00'), '2020-11-22'],
        ];
    }

    public function testValue(): void
    {
        $instance = new Date(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->value;

        $this->assertInstanceOf(CarbonImmutable::class, $subject);
        $this->assertSame('2020-11-22 00:00:00', $subject->toDateTimeString());
    }

    public function testNext(): void
    {
        $instance = new Date(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->next();

        $this->assertInstanceOf(Date::class, $subject);
        $this->assertSame('2020-11-23', (string) $subject);
        $this->assertSame('2020-11-23 00:00:00', $subject->value->toDateTimeString());
    }

    public function testPrev(): void
    {
        $instance = new Date(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->prev();

        $this->assertInstanceOf(Date::class, $subject);
        $this->assertSame('2020-11-21', (string) $subject);
        $this->assertSame('2020-11-21 00:00:00', $subject->value->toDateTimeString());
    }

    public function testStartOfDay(): void
    {
        $instance = new Date(new CarbonImmutable('2020-10-20 10:00:00'));

        $subject = $instance->startOfDay();

        $this->assertInstanceOf(CarbonImmutable::class, $subject);
        $this->assertSame('2020-10-20 00:00:00', $subject->toDateTimeString());
    }

    public function testEndOfDay(): void
    {
        $instance = new Date(new CarbonImmutable('2020-10-20 10:00:00'));

        $subject = $instance->endOfDay();

        $this->assertInstanceOf(CarbonImmutable::class, $subject);
        $this->assertSame('2020-10-20 23:59:59', $subject->toDateTimeString());
    }

    public function testToString(): void
    {
        $instance = new Date(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = (string) $instance;

        $this->assertTrue(is_string($subject));
        $this->assertSame('2020-11-22', $subject);
    }

    /** @dataProvider provideDatesAndExpectedForCompare */
    public function testEq(
        CarbonImmutable $base,
        CarbonImmutable $compared,
        bool $eq,
        bool $ne,
        bool $lt,
        bool $lte,
        bool $gt,
        bool $gte
    ): void {
        $instance = new Date($base);

        $subject = $instance->eq(new Date($compared));

        $this->assertSame($expected = $eq, $subject);
    }

    /** @dataProvider provideDatesAndExpectedForCompare */
    public function testNe(
        CarbonImmutable $base,
        CarbonImmutable $compared,
        bool $eq,
        bool $ne,
        bool $lt,
        bool $lte,
        bool $gt,
        bool $gte
    ): void {
        $instance = new Date($base);

        $subject = $instance->ne(new Date($compared));

        $this->assertSame($expected = $ne, $subject);
    }

    /** @dataProvider provideDatesAndExpectedForCompare */
    public function testLt(
        CarbonImmutable $base,
        CarbonImmutable $compared,
        bool $eq,
        bool $ne,
        bool $lt,
        bool $lte,
        bool $gt,
        bool $gte
    ): void {
        $instance = new Date($base);

        $subject = $instance->lt(new Date($compared));

        $this->assertSame($expected = $lt, $subject);
    }

    /** @dataProvider provideDatesAndExpectedForCompare */
    public function testLte(
        CarbonImmutable $base,
        CarbonImmutable $compared,
        bool $eq,
        bool $ne,
        bool $lt,
        bool $lte,
        bool $gt,
        bool $gte
    ): void {
        $instance = new Date($base);

        $subject = $instance->lte(new Date($compared));

        $this->assertSame($expected = $lte, $subject);
    }

    /** @dataProvider provideDatesAndExpectedForCompare */
    public function testGt(
        CarbonImmutable $base,
        CarbonImmutable $compared,
        bool $eq,
        bool $ne,
        bool $lt,
        bool $lte,
        bool $gt,
        bool $gte
    ): void {
        $instance = new Date($base);

        $subject = $instance->gt(new Date($compared));

        $this->assertSame($expected = $gt, $subject);
    }

    /** @dataProvider provideDatesAndExpectedForCompare */
    public function testGte(
        CarbonImmutable $base,
        CarbonImmutable $compared,
        bool $eq,
        bool $ne,
        bool $lt,
        bool $lte,
        bool $gt,
        bool $gte
    ): void {
        $instance = new Date($base);

        $subject = $instance->gte(new Date($compared));

        $this->assertSame($expected = $gte, $subject);
    }

    public function provideDatesAndExpectedForCompare(): array
    {
        return [
            'The compared date is earlier than the base date' => [
                'base' => new CarbonImmutable('2020-11-10'),
                'compared' => new CarbonImmutable('2020-11-20'),
                'eq' => false,
                'ne' => true,
                'lt' => true,
                'lte' => true,
                'gt' => false,
                'gte' => false,
            ],
            'The compared date is letter than the base date' => [
                'base' => new CarbonImmutable('2020-11-20'),
                'compared' => new CarbonImmutable('2020-11-10'),
                'eq' => false,
                'ne' => true,
                'lt' => false,
                'lte' => false,
                'gt' => true,
                'gte' => true,
            ],
            'The compared date is the same as the base date' => [
                'base' => new CarbonImmutable('2020-11-15'),
                'compared' => new CarbonImmutable('2020-11-15'),
                'eq' => true,
                'ne' => false,
                'lt' => false,
                'lte' => true,
                'gt' => false,
                'gte' => true,
            ],
        ];
    }

    /** @dataProvider provideDateForParse */
    public function testParse(string $date, string $expected): void
    {
        $subject = Date::parse($date);

        $this->assertInstanceOf(Date::class, $subject);
        $this->assertSame($expected, (string) $subject);
    }

    public function provideDateForParse(): array
    {
        return [
            'Date only' => ['2020-10-20', '2020-10-20'],
            'Slash separated date' => ['2020/10/20', '2020-10-20'],
            'Date and time' => ['2020-10-20 10:00:00', '2020-10-20'],
            'W3C' => ['2020-10-20T10:00:00+09:00', '2020-10-20'],
        ];
    }

    public function testToday(): void
    {
        CarbonImmutable::setTestNow('2020-10-20 10:20:30');

        $subject = Date::today();

        $this->assertInstanceOf(Date::class, $subject);
        $this->assertSame('2020-10-20', (string) $subject);
    }
}
