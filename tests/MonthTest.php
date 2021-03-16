<?php

declare(strict_types=1);

namespace Yuuan\Tests\Month;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Yuuan\Date\Month;

class MonthTest extends TestCase
{
    /** @dataProvider provideDateForConstruct */
    public function testConstract(DateTimeInterface $date, string $expected): void
    {
        $subject = new Month($date);

        $this->assertInstanceOf(Month::class, $subject);
        $this->assertSame($expected, (string) $subject);
    }

    public function provideDateForConstruct(): array
    {
        return [
            'DateTime' => [new DateTime('2020-11-22 10:00:00'), '2020-11'],
            'DateTimeImmutable' => [new DateTimeImmutable('2020-11-22 10:00:00'), '2020-11'],
            'Carbon' => [new Carbon('2020-11-22 10:00:00'), '2020-11'],
            'CarbonImmutable' => [new CarbonImmutable('2020-11-22 10:00:00'), '2020-11'],
        ];
    }

    public function testValue(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->value;

        $this->assertInstanceOf(CarbonImmutable::class, $subject);
        $this->assertSame('2020-11-01 00:00:00', $subject->toDateTimeString());
    }

    public function testNext(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->next();

        $this->assertInstanceOf(Month::class, $subject);
        $this->assertSame('2020-12', (string) $subject);
        $this->assertSame('2020-12-01 00:00:00', $subject->value->toDateTimeString());
    }

    public function testPrev(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->prev();

        $this->assertInstanceOf(Month::class, $subject);
        $this->assertSame('2020-10', (string) $subject);
        $this->assertSame('2020-10-01 00:00:00', $subject->value->toDateTimeString());
    }

    public function testStartOfMonth(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->startOfMonth();

        $this->assertInstanceOf(CarbonImmutable::class, $subject);
        $this->assertSame('2020-11-01 00:00:00', $subject->toDateTimeString());
    }

    public function testEndOfMonth(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->endOfMonth();

        $this->assertInstanceOf(CarbonImmutable::class, $subject);
        $this->assertSame('2020-11-30 23:59:59', $subject->toDateTimeString());
    }

    /** @dataProvider provideDateForIsCurrentMonth */
    public function testIsCurrentMonth(CarbonImmutable $date, bool $expected): void
    {
        CarbonImmutable::setTestNow('2020-11-22 10:20:30');

        $instance = new Month($date);

        $subject = $instance->isCurrentMonth();

        $this->assertSame($expected, $subject);
    }

    public function provideDateForIsCurrentMonth(): array
    {
        return [
            'Previous month' => [
                'date' => new CarbonImmutable('2020-10-01'),
                'expected' => false,
            ],
            'This month' => [
                'date' => new CarbonImmutable('2020-11-01'),
                'expected' => true,
            ],
            'Next month' => [
                'date' => new CarbonImmutable('2020-12-01'),
                'expected' => false,
            ],
        ];
    }

    public function testToString(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = (string) $instance;

        $this->assertTrue(is_string($subject));
        $this->assertSame('2020-11', $subject);
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
        $instance = new Month($base);

        $subject = $instance->eq(new Month($compared));

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
        $instance = new Month($base);

        $subject = $instance->ne(new Month($compared));

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
        $instance = new Month($base);

        $subject = $instance->lt(new Month($compared));

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
        $instance = new Month($base);

        $subject = $instance->lte(new Month($compared));

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
        $instance = new Month($base);

        $subject = $instance->gt(new Month($compared));

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
        $instance = new Month($base);

        $subject = $instance->gte(new Month($compared));

        $this->assertSame($gte, $subject);
    }

    public function provideDatesAndExpectedForCompare(): array
    {
        return [
            'The compared date is earlier than the base date' => [
                'base' => new CarbonImmutable('2020-01-01'),
                'compared' => new CarbonImmutable('2020-02-01'),
                'eq' => false,
                'ne' => true,
                'lt' => true,
                'lte' => true,
                'gt' => false,
                'gte' => false,
            ],
            'The compared date is letter than the base date' => [
                'base' => new CarbonImmutable('2020-02-01'),
                'compared' => new CarbonImmutable('2020-01-01'),
                'eq' => false,
                'ne' => true,
                'lt' => false,
                'lte' => false,
                'gt' => true,
                'gte' => true,
            ],
            'The compared date is the same as the base date' => [
                'base' => new CarbonImmutable('2020-01-01'),
                'compared' => new CarbonImmutable('2020-01-01'),
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
        $subject = Month::parse($date);

        $this->assertInstanceOf(Month::class, $subject);
        $this->assertSame($expected, (string) $subject);
    }

    public function provideDateForParse(): array
    {
        return [
            'Date only' => ['2020-11-22', '2020-11'],
            'Slash separated date' => ['2020/11/22', '2020-11'],
            'Date and time' => ['2020-11-22 10:00:00', '2020-11'],
            'W3C' => ['2020-11-22T10:00:00+09:00', '2020-11'],
        ];
    }

    public function testThisMonth(): void
    {
        CarbonImmutable::setTestNow('2020-11-22 10:20:30');

        $subject = Month::thisMonth();

        $this->assertInstanceOf(Month::class, $subject);
        $this->assertSame('2020-11', (string) $subject);
    }
}
