<?php

declare(strict_types=1);

namespace Yuuan\Date;

use BadMethodCallException;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Yuuan\ReadOnly\HasReadOnlyProperty;

/**
 * @property-read  \Carbon\CarbonImmutable  $value
 *
 * @method  bool  eq(\Yuuan\Date\Date $date)
 * @method  bool  ne(\Yuuan\Date\Date $date)
 * @method  bool  lt(\Yuuan\Date\Date $date)
 * @method  bool  lte(\Yuuan\Date\Date $date)
 * @method  bool  gt(\Yuuan\Date\Date $date)
 * @method  bool  gte(\Yuuan\Date\Date $date)
 *
 * @method  bool  isLastOfMonth()
 * @method  bool  isWeekday()
 * @method  bool  isWeekend()
 * @method  bool  isMonday()
 * @method  bool  isTuesday()
 * @method  bool  isWednesday()
 * @method  bool  isThursday()
 * @method  bool  isFriday()
 * @method  bool  isSaturday()
 * @method  bool  isSunday()
 * @method  bool  isYesterday()
 * @method  bool  isToday()
 * @method  bool  isTomorrow()
 *
 * @method  \Yuuan\Date\Date  addDay(int $number = 1),
 * @method  \Yuuan\Date\Date  addDays(int $number = 1),
 * @method  \Yuuan\Date\Date  subDay(int $number = 1),
 * @method  \Yuuan\Date\Date  subDays(int $number = 1),
 * @method  \Yuuan\Date\Date  addWeek(int $number = 1),
 * @method  \Yuuan\Date\Date  addWeeks(int $number = 1),
 * @method  \Yuuan\Date\Date  subWeek(int $number = 1),
 * @method  \Yuuan\Date\Date  subWeeks(int $number = 1),
 * @method  \Yuuan\Date\Date  addMonth(int $number = 1),
 * @method  \Yuuan\Date\Date  addMonths(int $number = 1),
 * @method  \Yuuan\Date\Date  subMonth(int $number = 1),
 * @method  \Yuuan\Date\Date  subMonths(int $number = 1),
 * @method  \Yuuan\Date\Date  addQuarter(int $number = 1),
 * @method  \Yuuan\Date\Date  addQuarters(int $number = 1),
 * @method  \Yuuan\Date\Date  subQuarter(int $number = 1),
 * @method  \Yuuan\Date\Date  subQuarters(int $number = 1),
 * @method  \Yuuan\Date\Date  addYear(int $number = 1),
 * @method  \Yuuan\Date\Date  addYears(int $number = 1),
 * @method  \Yuuan\Date\Date  subYear(int $number = 1),
 * @method  \Yuuan\Date\Date  subYears(int $number = 1),
 * @method  \Yuuan\Date\Date  addCentury(int $number = 1),
 * @method  \Yuuan\Date\Date  addCenturies(int $number = 1),
 * @method  \Yuuan\Date\Date  subCentury(int $number = 1),
 * @method  \Yuuan\Date\Date  subCenturies(int $number = 1),
 */
class Date
{
    use HasReadOnlyProperty;

    /**
     * The date as CarbonImmutable.
     */
    protected CarbonImmutable $value;

    /**
     * Comparison methods.
     *
     * @var list<string>
     */
    protected array $comparison = [
        'eq',
        'ne',
        'lt',
        'lte',
        'gt',
        'gte',
    ];

    /**
     * Determination methods.
     *
     * @var list<string>
     */
    protected array $determination = [
        'isLastOfMonth',
        'isWeekday',
        'isWeekend',
        'isMonday',
        'isTuesday',
        'isWednesday',
        'isThursday',
        'isFriday',
        'isSaturday',
        'isSunday',
        'isYesterday',
        'isToday',
        'isTomorrow',
    ];

    /**
     * Addition and Subtraction methods.
     *
     * @var list<string>
     */
    protected array $addition = [
        'addDay',
        'addDays',
        'subDay',
        'subDays',
        'addWeek',
        'addWeeks',
        'subWeek',
        'subWeeks',
        'addMonth',
        'addMonths',
        'subMonth',
        'subMonths',
        'addQuarter',
        'addQuarters',
        'subQuarter',
        'subQuarters',
        'addYear',
        'addYears',
        'subYear',
        'subYears',
        'addCentury',
        'addCenturies',
        'subCentury',
        'subCenturies',
    ];

    /**
     * Create a Date instance.
     */
    public function __construct(DateTimeInterface $date)
    {
        $this->value = CarbonImmutable::instance($date)->startOfDay();
    }

    /**
     * Get the next day.
     */
    public function next(): self
    {
        return new static($this->value->addDay());
    }

    /**
     * Get the previous day.
     */
    public function prev(): self
    {
        return new static($this->value->subDay());
    }

    /**
     * Determines if this instance is the first day of the month.
     */
    public function isFirstOfMonth(): bool
    {
        return $this->value->day === 1;
    }

    /**
     * Determines if this instance is in the past.
     */
    public function isPast(): bool
    {
        return $this->value->lt(
            $this->value->nowWithSameTz()->startOfDay()
        );
    }

    /**
     * Determines if this instance is in the future.
     */
    public function isFuture(): bool
    {
        return $this->value->gt(
            $this->value->nowWithSameTz()->startOfDay()
        );
    }

    /**
     * Get first time of the day.
     */
    public function startOfDay(): CarbonImmutable
    {
        return $this->value;
    }

    /**
     * Get time just before the next day.
     */
    public function endOfDay(): CarbonImmutable
    {
        return $this->value->endOfDay();
    }

    /**
     * Get the month containing this day.
     */
    public function month(): Month
    {
        return new Month($this->value);
    }

    /**
     * Create a DateRange instance from this instance to specified instance.
     */
    public function rangeTo(self $end): DateRange
    {
        return new DateRange($this, $end);
    }

    /**
     * Create a DateRange instance from specified instance to this instance.
     */
    public function rangeFrom(self $start): DateRange
    {
        return new DateRange($start, $this);
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->value->toDateString();
    }

    /**
     * Handle dynamic calls to the instance to compare.
     *
     * @return self|bool
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $parameters)
    {
        if (in_array($method, $this->comparison, true)) {
            return $this->compare($method, ...$parameters);
        }

        if (in_array($method, $this->determination, true)) {
            return $this->determine($method);
        }

        if (in_array($method, $this->addition, true)) {
            return $this->change($method, ...$parameters);
        }

        throw new BadMethodCallException(
            sprintf('Call to undefined method %s::%s()', static::class, $method)
        );
    }

    /**
     * Compare with the specified date in the specified method.
     */
    protected function compare(string $method, self $date): bool
    {
        return $this->value->$method($date->value);
    }

    /**
     * Determine the current instance with the specified method.
     */
    protected function determine(string $method): bool
    {
        return $this->value->$method();
    }

    /**
     * Add using specified method to the current instance.
     */
    protected function change(string $method, int $number = 1): self
    {
        return new static($this->value->$method($number));
    }

    /**
     * Create an instance by parsing a string.
     */
    public static function parse(string $string): self
    {
        return new static(CarbonImmutable::parse($string));
    }

    /**
     * Create an instance that represents today.
     */
    public static function today(): self
    {
        return new static(CarbonImmutable::today());
    }
}
