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
use Yuuan\Date\DateRange;
use Yuuan\Date\Month;

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

    /** @dataProvider provideDateForIsFirstOfMonth */
    public function testIsFirstOfMonth(Date $date, bool $expected): void
    {
        $this->assertSame($expected, $date->isFirstOfMonth());
    }

    public function provideDateForIsFirstOfMonth(): array
    {
        return [
            'First day of month' => [
                'date' => Date::parse('2020-11-01'),
                'expected' => true,
            ],
            'Second day of month' => [
                'date' => Date::parse('2020-11-02'),
                'expected' => false,
            ],
            'Last day of month' => [
                'date' => Date::parse('2020-11-30'),
                'expected' => false,
            ],
        ];
    }

    /** @dataProvider provideDateForIsPast */
    public function testIsPast(Date $date, bool $expected): void
    {
        CarbonImmutable::setTestNow('2020-11-22 10:20:30');

        $this->assertSame($expected, $date->isPast());
    }

    public function provideDateForIsPast(): array
    {
        return [
            'Yesterday' => [
                'date' => Date::parse('2020-11-21'),
                'expected' => true,
            ],
            'Today' => [
                'date' => Date::parse('2020-11-22'),
                'expected' => false,
            ],
            'Tomorrow' => [
                'date' => Date::parse('2020-11-23'),
                'expected' => false,
            ],
        ];
    }

    /** @dataProvider provideDateForIsFuture */
    public function testIsFuture(Date $date, bool $expected): void
    {
        CarbonImmutable::setTestNow('2020-11-22 10:20:30');

        $this->assertSame($expected, $date->isFuture());
    }

    public function provideDateForIsFuture(): array
    {
        return [
            'Yesterday' => [
                'date' => Date::parse('2020-11-21'),
                'expected' => false,
            ],
            'Today' => [
                'date' => Date::parse('2020-11-22'),
                'expected' => false,
            ],
            'Tomorrow' => [
                'date' => Date::parse('2020-11-23'),
                'expected' => true,
            ],
        ];
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

    public function testMonth(): void
    {
        $instance = new Date(new CarbonImmutable('2020-10-20 10:00:00'));

        $subject = $instance->month();

        $this->assertInstanceOf(Month::class, $subject);
        $this->assertSame('2020-10', (string) $subject);
    }

    public function testRangeTo(): void
    {
        $instance = new Date(new CarbonImmutable('2020-10-20 10:00:00'));

        $subject = $instance->rangeTo(Date::parse('2020-10-21'));

        $this->assertInstanceOf(DateRange::class, $subject);

        $array = $subject->toArray();

        $this->assertSame('2020-10-20', (string) $array[0]);
        $this->assertSame('2020-10-21', (string) $array[1]);
    }

    public function testRangeFrom(): void
    {
        $instance = new Date(new CarbonImmutable('2020-10-21 10:00:00'));

        $subject = $instance->rangeFrom(Date::parse('2020-10-20'));

        $this->assertInstanceOf(DateRange::class, $subject);

        $array = $subject->toArray();

        $this->assertSame('2020-10-20', (string) $array[0]);
        $this->assertSame('2020-10-21', (string) $array[1]);
    }

    public function testToString(): void
    {
        $instance = new Date(new CarbonImmutable('2020-11-22 10:00:00'));

        $subject = (string) $instance;

        $this->assertTrue(is_string($subject));
        $this->assertSame('2020-11-22', $subject);
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
        $instance = new Date($base);

        $this->assertSame($eq, $instance->eq(new Date($compared)));
        $this->assertSame($ne, $instance->ne(new Date($compared)));
        $this->assertSame($lt, $instance->lt(new Date($compared)));
        $this->assertSame($lte, $instance->lte(new Date($compared)));
        $this->assertSame($gt, $instance->gt(new Date($compared)));
        $this->assertSame($gte, $instance->gte(new Date($compared)));
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

    /** @dataProvider provideDateForDetermine_Weekday */
    public function testDetermine_Weekday(
        Date $date,
        bool $isWeekday,
        bool $isWeekend,
        bool $isMonday,
        bool $isTuesday,
        bool $isWednesday,
        bool $isThursday,
        bool $isFriday,
        bool $isSaturday,
        bool $isSunday
    ): void {
        $this->assertSame($isWeekday, $date->isWeekday());
        $this->assertSame($isWeekend, $date->isWeekend());
        $this->assertSame($isMonday, $date->isMonday());
        $this->assertSame($isTuesday, $date->isTuesday());
        $this->assertSame($isWednesday, $date->isWednesday());
        $this->assertSame($isThursday, $date->isThursday());
        $this->assertSame($isFriday, $date->isFriday());
        $this->assertSame($isSaturday, $date->isSaturday());
        $this->assertSame($isSunday, $date->isSunday());
    }

    public function provideDateForDetermine_Weekday(): array
    {
        return [
            'Monday' => [
                'date' => Date::parse('2020-06-01'),
                'isWeekday' => true,
                'isWeekend' => false,
                'isMonday' => true,
                'isTuesday' => false,
                'isWednesday' => false,
                'isThursday' => false,
                'isFriday' => false,
                'isSaturday' => false,
                'isSunday' => false,
            ],
            'Tuesday' => [
                'date' => Date::parse('2020-06-02'),
                'isWeekday' => true,
                'isWeekend' => false,
                'isMonday' => false,
                'isTuesday' => true,
                'isWednesday' => false,
                'isThursday' => false,
                'isFriday' => false,
                'isSaturday' => false,
                'isSunday' => false,
            ],
            'Wednesday' => [
                'date' => Date::parse('2020-06-03'),
                'isWeekday' => true,
                'isWeekend' => false,
                'isMonday' => false,
                'isTuesday' => false,
                'isWednesday' => true,
                'isThursday' => false,
                'isFriday' => false,
                'isSaturday' => false,
                'isSunday' => false,
            ],
            'Thursday' => [
                'date' => Date::parse('2020-06-04'),
                'isWeekday' => true,
                'isWeekend' => false,
                'isMonday' => false,
                'isTuesday' => false,
                'isWednesday' => false,
                'isThursday' => true,
                'isFriday' => false,
                'isSaturday' => false,
                'isSunday' => false,
            ],
            'Friday' => [
                'date' => Date::parse('2020-06-05'),
                'isWeekday' => true,
                'isWeekend' => false,
                'isMonday' => false,
                'isTuesday' => false,
                'isWednesday' => false,
                'isThursday' => false,
                'isFriday' => true,
                'isSaturday' => false,
                'isSunday' => false,
            ],
            'Saturday' => [
                'date' => Date::parse('2020-06-06'),
                'isWeekday' => false,
                'isWeekend' => true,
                'isMonday' => false,
                'isTuesday' => false,
                'isWednesday' => false,
                'isThursday' => false,
                'isFriday' => false,
                'isSaturday' => true,
                'isSunday' => false,
            ],
            'Sunday' => [
                'date' => Date::parse('2020-06-07'),
                'isWeekday' => false,
                'isWeekend' => true,
                'isMonday' => false,
                'isTuesday' => false,
                'isWednesday' => false,
                'isThursday' => false,
                'isFriday' => false,
                'isSaturday' => false,
                'isSunday' => true,
            ],
        ];
    }

    /** @dataProvider provideDateForDetermine_LastOfMonth */
    public function testDetermine_LastOfMonth(
        Date $date,
        bool $isLastOfMonth
    ): void {
        $this->assertSame($isLastOfMonth, $date->isLastOfMonth());
    }

    public function provideDateForDetermine_LastOfMonth(): array
    {
        return [
            'First day of month' => [
                'date' => Date::parse('2020-01-01'),
                'isLastOfMonth' => false,
            ],
            'Middle day of month' => [
                'date' => Date::parse('2020-06-15'),
                'isLastOfMonth' => false,
            ],
            'Last day of month' => [
                'date' => Date::parse('2020-12-31'),
                'isLastOfMonth' => true,
            ],
        ];
    }

    /** @dataProvider provideDateForDetermine_Relative */
    public function testDetermine_Relative(
        Date $date,
        bool $isYesterday,
        bool $isToday,
        bool $isTomorrow
    ): void {
        CarbonImmutable::setTestNow('2020-11-22 00:00:00');

        $this->assertSame($isYesterday, $date->isYesterday(), 'isYesterday is not expected');
        $this->assertSame($isToday, $date->isToday(), 'isToday is not expected');
        $this->assertSame($isTomorrow, $date->isTomorrow(), 'isTomorrow is not expected');
    }

    public function provideDateForDetermine_Relative(): array
    {
        return [
            'Day before yesterday' => [
                'month' => Date::parse('2020-11-20'),
                'isYesterday' => false,
                'isToday' => false,
                'isTomorrow' => false,
            ],
            'Yesterday' => [
                'month' => Date::parse('2020-11-21'),
                'isYesterday' => true,
                'isToday' => false,
                'isTomorrow' => false,
            ],
            'Today' => [
                'month' => Date::parse('2020-11-22'),
                'isYesterday' => false,
                'isToday' => true,
                'isTomorrow' => false,
            ],
            'Tomorrow' => [
                'month' => Date::parse('2020-11-23'),
                'isYesterday' => false,
                'isToday' => false,
                'isTomorrow' => true,
            ],
            'Day after tomorrow' => [
                'month' => Date::parse('2020-11-24'),
                'isYesterday' => false,
                'isToday' => false,
                'isTomorrow' => false,
            ],
        ];
    }

    /** @dataProvider provideAdditionMethods */
    public function testChange(string $method): void
    {
        $instance = Date::parse('2020-11-22');

        $subject = $instance->$method(2);
        $expected = $instance->value->$method(2);

        $this->assertSame($expected->toW3cString(), $subject->value->toW3cString());
    }

    public function provideAdditionMethods(): array
    {
        return [
            ['method' => 'addDay'],
            ['method' => 'addDays'],
            ['method' => 'subDay'],
            ['method' => 'subDays'],
            ['method' => 'addWeek'],
            ['method' => 'addWeeks'],
            ['method' => 'subWeek'],
            ['method' => 'subWeeks'],
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
