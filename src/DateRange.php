<?php

declare(strict_types=1);

namespace Yuuan\Date;

use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Carbon\CarbonTimeZone;
use Countable;
use DateTimeInterface;
use Generator;
use IteratorAggregate;
use Yuuan\ReadOnly\HasReadOnlyProperty;

/**
 * @property-read  \Carbon\CarbonImmutable  $start
 * @property-read  \Carbon\CarbonImmutable  $end
 * @implements \IteratorAggregate<\Yuuan\Date\Date>
 */
class DateRange implements IteratorAggregate, Countable
{
    use HasReadOnlyProperty;

    /**
     * The first day.
     */
    private Date $start;

    /**
     * The last day.
     */
    private Date $end;

    /**
     * Create a DateRange instance.
     */
    public function __construct(Date $start, Date $end)
    {
        if ($start->gt($end)) {
            throw new EndDateIsBeforeStartDateException($start, $end);
        }

        if ($start->value->tzName !== $end->value->tzName) {
            throw new DifferentTimeZoneException($start, $end);
        }

        $this->start = $start;
        $this->end = $end;
    }

    /**
     * Get first time of the first day.
     */
    public function startOfDays(): CarbonImmutable
    {
        return $this->start->value;
    }

    /**
     * Get last time of the last day.
     */
    public function endOfDays(): CarbonImmutable
    {
        return $this->end->value->endOfDay();
    }

    /**
     * Get the current time zone.
     */
    public function timezone(): CarbonTimeZone
    {
        return $this->start->timezone();
    }

    /**
     * Determines if this range contains the specified day.
     */
    public function contains(Date $date): bool
    {
        return $this->start->lte($date)
            && $this->end->gte($date);
    }

    /**
     * Determines if this range overlaps with the specified range.
     */
    public function overlapsWith(self $target): bool
    {
        return $this->end->gte($target->start)
            && $target->end->gte($this->start);
    }

    /**
     * Get the overlapping range of the specified range for this range.
     */
    public function getOverlapping(self $target): self
    {
        if (! $this->overlapsWith($target)) {
            throw new RangesDontOverlapException($this, $target);
        }

        return new static(
            $this->start->gte($target->start) ? $this->start : $target->start,
            $this->end->lte($target->end) ? $this->end : $target->end
        );
    }

    /**
     * Get days.
     */
    public function getDays(): int
    {
        return $this->end->value->diffInDays($this->start->value) + 1;
    }

    /**
     * Get days.
     */
    public function count(): int
    {
        return $this->getDays();
    }

    /**
     * Get iterator.
     *
     * @return \Generator<\Yuuan\Date\Date>
     */
    public function getIterator(): Generator
    {
        for ($date = $this->start; $date->lte($this->end); $date = $date->next()) {
            yield $date;
        }
    }

    /**
     * Get daily iterator.
     *
     * @return \Generator<\Yuuan\Date\Date>
     */
    public function perDay(): Generator
    {
        return $this->getIterator();
    }

    /**
     * Get hourly iterator.
     *
     * @return \Generator<\Carbon\CarbonImmutable>
     */
    public function perHour(): Generator
    {
        foreach ($this as $date) {
            foreach (range(0, 23) as $hour) {
                yield $date->value->hour($hour);
            }
        }
    }

    /**
     * Convert to array.
     *
     * @return list<\Yuuan\Date\Date>
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    /**
     * Convert to CarbonPeriod with CarbonImmutable.
     */
    public function toPeriod(): CarbonPeriod
    {
        return CarbonPeriod::create(
            (string) $this->start,
            (string) $this->end
        )->setDateClass(CarbonImmutable::class);
    }

    /**
     * Create an instance by parsing strings.
     */
    public static function parse(string $start, string $end): self
    {
        return new static(
            Date::parse($start),
            Date::parse($end)
        );
    }

    /**
     * Create an instance by DateTime instances.
     */
    public static function fromDateTimes(DateTimeInterface $start, DateTimeInterface $end): self
    {
        return new static(
            new Date($start),
            new Date($end)
        );
    }
}
