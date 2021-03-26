<?php

declare(strict_types=1);

namespace Yuuan\Tests\Date;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yuuan\Date\Date;
use Yuuan\Date\DateRange;
use Yuuan\Date\DifferentTimeZoneException;
use Yuuan\Date\EndDateIsBeforeStartDateException;
use Yuuan\Date\RangesDontOverlapException;

class DateRangeTest extends TestCase
{
    public function testConstract(): void
    {
        $subject = new DateRange(Date::parse('2020-01-01'), Date::parse('2020-12-31'));

        $this->assertInstanceOf(DateRange::class, $subject);
        $this->assertSame('2020-01-01', (string) $subject->start);
        $this->assertSame('2020-12-31', (string) $subject->end);
    }

    public function testConstruct_WhenEndDateIsBeforeStartDate(): void
    {
        $this->expectException(EndDateIsBeforeStartDateException::class);

        $subject = new DateRange(Date::parse('2020-12-31'), Date::parse('2020-01-01'));
    }

    public function testConstruct_WhenStartDateTimeZoneAndEndDateTimeZoneAreDifferent(): void
    {
        $this->expectException(DifferentTimeZoneException::class);

        $subject = new DateRange(
            Date::parse('2020-01-01T00:00:00+00:00'),
            Date::parse('2020-01-02T00:00:00+09:00')
        );
    }

    public function testStartOfDays(): void
    {
        $range = new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-02'));

        $subject = $range->startOfDays();

        $this->assertInstanceOf(CarbonImmutable::class, $subject);
        $this->assertSame('2020-01-01 00:00:00', $subject->toDateTimeString());
    }

    public function testEndOfDays(): void
    {
        $range = new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-02'));

        $subject = $range->endOfDays();

        $this->assertInstanceOf(CarbonImmutable::class, $subject);
        $this->assertSame('2020-01-02 23:59:59', $subject->toDateTimeString());
    }

    /** @dataProvider provideDatesForContains */
    public function testContaints(Date $start, Date $end, Date $target, bool $expected): void
    {
        $range = new DateRange($start, $end);

        $subject = $range->contains($target);

        $this->assertSame($expected, $subject);
    }

    public function provideDatesForContains(): array
    {
        return [
            'Target date is before the range' => [
                'start' => Date::parse('2020-01-10'),
                'end' => Date::parse('2020-01-20'),
                'target' => Date::parse('2020-01-01'),
                'expected' => false,
            ],
            'Target date is after the range' => [
                'start' => Date::parse('2020-01-10'),
                'end' => Date::parse('2020-01-20'),
                'target' => Date::parse('2020-01-30'),
                'expected' => false,
            ],
            'Target date is the same as the start date' => [
                'start' => Date::parse('2020-01-10'),
                'end' => Date::parse('2020-01-20'),
                'target' => Date::parse('2020-01-10'),
                'expected' => true,
            ],
            'Target date is the same as the end date' => [
                'start' => Date::parse('2020-01-10'),
                'end' => Date::parse('2020-01-20'),
                'target' => Date::parse('2020-01-20'),
                'expected' => true,
            ],
            'Target date is within the range' => [
                'start' => Date::parse('2020-01-10'),
                'end' => Date::parse('2020-01-20'),
                'target' => Date::parse('2020-01-15'),
                'expected' => true,
            ],
        ];
    }

    /** @dataProvider provideDatesForOverlapsWith */
    public function testOverlapsWith(DateRange $first, DateRange $second, bool $expected): void
    {
        $subject = $first->overlapsWith($second);

        $this->assertSame($expected, $subject);
    }

    public function provideDatesForOverlapsWith(): array
    {
        return [
            'First is before second (exclusive)' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'second' => new DateRange(Date::parse('2020-02-01'), Date::parse('2020-02-29')),
                'overlapped' => false,
            ],
            'First is after second (exclusive)' => [
                'first' => new DateRange(Date::parse('2020-02-01'), Date::parse('2020-02-29')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'expected' => false,
            ],
            'First is a bit before second' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'second' => new DateRange(Date::parse('2020-01-15'), Date::parse('2020-02-15')),
                'expected' => true,
            ],
            'First is a bit after second' => [
                'first' => new DateRange(Date::parse('2020-01-15'), Date::parse('2020-02-15')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'expected' => true,
            ],
            'First is the same as second' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'expected' => true,
            ],
            'First is the same as second and one day' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-01')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-01')),
                'expected' => true,
            ],
            'First contains second' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'second' => new DateRange(Date::parse('2020-01-10'), Date::parse('2020-01-20')),
                'expected' => true,
            ],
            'Second contains first' => [
                'first' => new DateRange(Date::parse('2020-01-10'), Date::parse('2020-01-20')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'expected' => true,
            ],
        ];
    }

    /** @dataProvider provideOverlappedDates */
    public function testGetOverlapping_WhenOverlapped(DateRange $first, DateRange $second, DateRange $expected): void
    {
        $subject = $first->getOverlapping($second);

        $this->assertInstanceOf(DateRange::class, $subject);
        $this->assertSame((string) $expected->start, (string) $subject->start);
        $this->assertSame((string) $expected->end, (string) $subject->end);
    }

    public function provideOverlappedDates(): array
    {
        return [
            'First is a bit before second' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'second' => new DateRange(Date::parse('2020-01-15'), Date::parse('2020-02-15')),
                'expected' => new DateRange(Date::parse('2020-01-15'), Date::parse('2020-01-31')),
            ],
            'First is a bit after second' => [
                'first' => new DateRange(Date::parse('2020-01-15'), Date::parse('2020-02-15')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'expected' => new DateRange(Date::parse('2020-01-15'), Date::parse('2020-01-31')),
            ],
            'First is the same as second' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'expected' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
            ],
            'First is the same as second and one day' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-01')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-01')),
                'expected' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-01')),
            ],
            'First contains second' => [
                'first' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'second' => new DateRange(Date::parse('2020-01-10'), Date::parse('2020-01-20')),
                'expected' => new DateRange(Date::parse('2020-01-10'), Date::parse('2020-01-20')),
            ],
            'Second contains first' => [
                'first' => new DateRange(Date::parse('2020-01-10'), Date::parse('2020-01-20')),
                'second' => new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                'expected' => new DateRange(Date::parse('2020-01-10'), Date::parse('2020-01-20')),
            ],
        ];
    }

    /** @dataProvider provideNotOverlappedDates */
    public function testGetOverlapping_WhenNotOverlapped(DateRange $first, DateRange $second): void
    {
        $this->expectException(RangesDontOverlapException::class);

        $subject = $first->getOverlapping($second);
    }

    public function provideNotOverlappedDates(): array
    {
        return [
            'First is before second (exclusive)' => [
                new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
                new DateRange(Date::parse('2020-02-01'), Date::parse('2020-02-29')),
            ],
            'First is after second (exclusive)' => [
                new DateRange(Date::parse('2020-02-01'), Date::parse('2020-02-29')),
                new DateRange(Date::parse('2020-01-01'), Date::parse('2020-01-31')),
            ],
        ];
    }

    public function testGetDays(): void
    {
        $instance = new DateRange(Date::parse('2020-10-01'), Date::parse('2020-10-01'));

        $subject = $instance->getDays();

        $this->assertSame(1, $subject);
    }

    public function testCount(): void
    {
        $instance = new DateRange(Date::parse('2020-10-01'), Date::parse('2020-10-03'));

        $subject = count($instance);

        $this->assertSame(3, $subject);
    }

    public function testGetIterator(): void
    {
        $instance = new DateRange(Date::parse('2020-10-01'), Date::parse('2020-10-03'));

        $subject = $instance->getIterator();

        $this->assertIsIterable($subject);

        $array = iterator_to_array($subject);

        $this->assertCount(3, $array);
        $this->assertSame('2020-10-01', (string) $array[0]);
        $this->assertSame('2020-10-02', (string) $array[1]);
        $this->assertSame('2020-10-03', (string) $array[2]);
    }

    public function testPerDay(): void
    {
        $instance = new DateRange(Date::parse('2020-10-01'), Date::parse('2020-10-01'));

        $subject = $instance->perDay();

        $this->assertIsIterable($subject);

        $array = iterator_to_array($subject);

        $this->assertCount(1, $array);
        $this->assertInstanceOf(Date::class, $array[0]);
        $this->assertSame('2020-10-01', (string) $array[0]);
    }

    public function testPerHour(): void
    {
        $instance = new DateRange(Date::parse('2020-10-01'), Date::parse('2020-10-01'));

        $subject = $instance->perHour();

        $this->assertIsIterable($subject);

        $array = iterator_to_array($subject);

        $this->assertCount(24, $array);

        foreach (range(0, 23) as $i => $hour) {
            $this->assertInstanceOf(CarbonImmutable::class, $array[$i]);
            $this->assertSame(sprintf('2020-10-01 %02d:00:00', $hour), $array[$i]->toDateTimeString());
        }
    }

    public function testToArray(): void
    {
        $instance = new DateRange(Date::parse('2020-10-01'), Date::parse('2020-10-01'));

        $subject = $instance->toArray();

        $this->assertIsArray($subject);
        $this->assertCount(1, $subject);
        $this->assertInstanceOf(Date::class, $subject[0]);
        $this->assertSame('2020-10-01', (string) $subject[0]);
    }

    public function testToPeriod(): void
    {
        $instance = new DateRange(Date::parse('2020-10-01'), Date::parse('2020-10-01'));

        $subject = $instance->toPeriod();

        $this->assertInstanceOf(CarbonPeriod::class, $subject);

        $array = iterator_to_array($subject);

        $this->assertCount(1, $array);
        $this->assertInstanceOf(CarbonImmutable::class, $array[0]);
        $this->assertSame('2020-10-01 00:00:00', (string) $array[0]);
    }

    public function testParse(): void
    {
        $subject = DateRange::parse('2020-01-01', '2020-01-03');

        $this->assertInstanceOf(DateRange::class, $subject);
        $this->assertSame('2020-01-01', (string) $subject->start);
        $this->assertSame('2020-01-03', (string) $subject->end);
    }

    public function testFromDateTimes(): void
    {
        $start = new CarbonImmutable('2020-01-01');
        $end = new CarbonImmutable('2020-01-03');

        $subject = DateRange::fromDateTimes($start, $end);

        $this->assertInstanceOf(DateRange::class, $subject);
        $this->assertSame('2020-01-01', (string) $subject->start);
        $this->assertSame('2020-01-03', (string) $subject->end);
    }
}
