<?php

declare(strict_types=1);

namespace Yuuan\Tests\Month;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Yuuan\Date\Date;
use Yuuan\Date\DateRange;
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

    /** @dataProvider provideMonthForIsPast */
    public function testIsPast(Month $month, bool $expected): void
    {
        CarbonImmutable::setTestNow('2020-11-22 10:20:30');

        $this->assertSame($expected, $month->isPast());
    }

    public function provideMonthForIsPast(): array
    {
        return [
            'Previous month' => [
                'month' => Month::parse('2020-10'),
                'expected' => true,
            ],
            'Current month' => [
                'month' => Month::parse('2020-11'),
                'expected' => false,
            ],
            'Next month' => [
                'month' => Month::parse('2020-12'),
                'expected' => false,
            ],
        ];
    }

    /** @dataProvider provideMonthForIsFuture */
    public function testIsFuture(Month $month, bool $expected): void
    {
        CarbonImmutable::setTestNow('2020-11-22 10:20:30');

        $this->assertSame($expected, $month->isFuture());
    }

    public function provideMonthForIsFuture(): array
    {
        return [
            'Previous month' => [
                'month' => Month::parse('2020-10'),
                'expected' => false,
            ],
            'Current month' => [
                'month' => Month::parse('2020-11'),
                'expected' => false,
            ],
            'Next month' => [
                'month' => Month::parse('2020-12'),
                'expected' => true,
            ],
        ];
    }

    public function testGetFirstDate(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->getFirstDate();

        $this->assertInstanceOf(Date::class, $subject);
        $this->assertSame('2020-11-01', (string) $subject);
    }

    public function testGetLastDate(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = $instance->getLastDate();

        $this->assertInstanceOf(Date::class, $subject);
        $this->assertSame('2020-11-30', (string) $subject);
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

    public function testToDateRange(): void
    {
        $instance = Month::parse('2020-11-22');

        $subject = $instance->toDateRange();

        $this->assertInstanceOf(DateRange::class, $subject);
        $this->assertSame('2020-11-01', (string) $subject->start);
        $this->assertSame('2020-11-30', (string) $subject->end);
    }

    public function testToString(): void
    {
        $instance = new Month(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = (string) $instance;

        $this->assertTrue(is_string($subject));
        $this->assertSame('2020-11', $subject);
    }

    /** @dataProvider provideDatesAndExpectedForCompare */
    public function testCompare(
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

        $this->assertSame($eq, $instance->eq(new Month($compared)), 'eq is not expected');
        $this->assertSame($ne, $instance->ne(new Month($compared)), 'ne is not expected');
        $this->assertSame($lt, $instance->lt(new Month($compared)), 'lt is not expected');
        $this->assertSame($lte, $instance->lte(new Month($compared)), 'lte is not expected');
        $this->assertSame($gt, $instance->gt(new Month($compared)), 'gt is not expected');
        $this->assertSame($gte, $instance->gte(new Month($compared)), 'gte is not expected');
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

    /** @dataProvider provideMonthForDetermine_Relative */
    public function testDetermine_Relative(
        Month $month,
        bool $isCurrentMonth,
        bool $isNextMonth,
        bool $isLastMonth
    ): void {
        CarbonImmutable::setTestNow('2020-11-22 10:20:30');

        $this->assertSame($isCurrentMonth, $month->isCurrentMonth(), 'isCurrentMonth is not expected');
        $this->assertSame($isNextMonth, $month->isNextMonth(), 'isNextMonth is not expected');
        $this->assertSame($isLastMonth, $month->isLastMonth(), 'isLastMonth is not expected');
    }

    public function provideMonthForDetermine_Relative(): array
    {
        return [
            'Month before previous' => [
                'month' => Month::parse('2020-09'),
                'isCurrentMonth' => false,
                'isNextMonth' => false,
                'isLastMonth' => false,
            ],
            'Previous month' => [
                'month' => Month::parse('2020-10'),
                'isCurrentMonth' => false,
                'isNextMonth' => false,
                'isLastMonth' => true,
            ],
            'Current month' => [
                'month' => Month::parse('2020-11'),
                'isCurrentMonth' => true,
                'isNextMonth' => false,
                'isLastMonth' => false,
            ],
            'Next month' => [
                'month' => Month::parse('2020-12'),
                'isCurrentMonth' => false,
                'isNextMonth' => true,
                'isLastMonth' => false,
            ],
            'Month after next' => [
                'month' => Month::parse('2021-01'),
                'isCurrentMonth' => false,
                'isNextMonth' => false,
                'isLastMonth' => false,
            ],
        ];
    }

    /** @dataProvider provideAdditionMethods */
    public function testChange(string $method): void
    {
        $instance = Month::parse('2020-11-22');

        $subject = $instance->$method(2);
        $expected = $instance->value->$method(2);

        $this->assertSame($expected->toW3cString(), $subject->value->toW3cString());
    }

    public function provideAdditionMethods(): array
    {
        return [
            ['method' => 'addMonth'],
            ['method' => 'addMonths'],
            ['method' => 'subMonth'],
            ['method' => 'subMonths'],
            ['method' => 'addQuarter'],
            ['method' => 'addQuarters'],
            ['method' => 'subQuarter'],
            ['method' => 'subQuarters'],
            ['method' => 'addYear'],
            ['method' => 'addYears'],
            ['method' => 'subYear'],
            ['method' => 'subYears'],
            ['method' => 'addCentury'],
            ['method' => 'addCenturies'],
            ['method' => 'subCentury'],
            ['method' => 'subCenturies'],
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
            'Year and month' => ['2020-11', '2020-11'],
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
