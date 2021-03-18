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
 * @method  bool  eq(\Yuuan\Date\Month $month)
 * @method  bool  ne(\Yuuan\Date\Month $month)
 * @method  bool  lt(\Yuuan\Date\Month $month)
 * @method  bool  lte(\Yuuan\Date\Month $month)
 * @method  bool  gt(\Yuuan\Date\Month $month)
 * @method  bool  gte(\Yuuan\Date\Month $month)
 *
 * @method  bool  isCurrentMonth()
 * @method  bool  isNextMonth()
 * @method  bool  isLastMonth()
 *
 * @method  \Yuuan\Date\Month  addMonth(int $number = 1)
 * @method  \Yuuan\Date\Month  addMonths(int $number = 1)
 * @method  \Yuuan\Date\Month  subMonth(int $number = 1)
 * @method  \Yuuan\Date\Month  subMonths(int $number = 1)
 * @method  \Yuuan\Date\Month  addQuarter(int $number = 1)
 * @method  \Yuuan\Date\Month  addQuarters(int $number = 1)
 * @method  \Yuuan\Date\Month  subQuarter(int $number = 1)
 * @method  \Yuuan\Date\Month  subQuarters(int $number = 1)
 * @method  \Yuuan\Date\Month  addYear(int $number = 1)
 * @method  \Yuuan\Date\Month  addYears(int $number = 1)
 * @method  \Yuuan\Date\Month  subYear(int $number = 1)
 * @method  \Yuuan\Date\Month  subYears(int $number = 1)
 * @method  \Yuuan\Date\Month  addCentury(int $number = 1)
 * @method  \Yuuan\Date\Month  addCenturies(int $number = 1)
 * @method  \Yuuan\Date\Month  subCentury(int $number = 1)
 * @method  \Yuuan\Date\Month  subCenturies(int $number = 1)
 */
class Month
{
    use HasReadOnlyProperty;

    /**
     * The month as CarbonImmutable.
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
        'isCurrentMonth',
        'isNextMonth',
        'isLastMonth',
    ];

    /**
     * Addition and Subtraction methods.
     *
     * @var list<string>
     */
    protected array $addition = [
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
     * Create a Month instance.
     */
    public function __construct(DateTimeInterface $date)
    {
        $this->value = CarbonImmutable::instance($date)->startOfMonth();
    }

    /**
     * Get the next month.
     */
    public function next(): self
    {
        return new static($this->value->addMonth());
    }

    /**
     * Get the previous month.
     */
    public function prev(): self
    {
        return new static($this->value->subMonth());
    }

    /**
     * Determines if this instance is in the past.
     */
    public function isPast(): bool
    {
        return $this->value->lt(
            $this->value->nowWithSameTz()->startOfMonth()
        );
    }

    /**
     * Determines if this instance is in the future.
     */
    public function isFuture(): bool
    {
        return $this->value->gt(
            $this->value->nowWithSameTz()->startOfMonth()
        );
    }

    /**
     * Get the first date of the month.
     */
    public function getFirstDate(): Date
    {
        return new Date($this->startOfMonth());
    }

    /**
     * Get the last date of the month.
     */
    public function getLastDate(): Date
    {
        return new Date($this->endOfMonth());
    }

    /**
     * Get first time of the month.
     */
    public function startOfMonth(): CarbonImmutable
    {
        return $this->value;
    }

    /**
     * Get time just before the next month.
     */
    public function endOfMonth(): CarbonImmutable
    {
        return $this->value->endOfMonth();
    }

    /**
     * Determines if the instance is in the current month.
     */
    public function isCurrentMonth(): bool
    {
        return static::thisMonth()->eq($this);
    }

    /**
     * Convert this month to DateRange.
     */
    public function toDateRange(): DateRange
    {
        return new DateRange($this->getFirstDate(), $this->getLastDate());
    }

    /**
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->value->format('Y-m');
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
     * Compare with the specified month in the specified method.
     */
    protected function compare(string $method, self $month): bool
    {
        return $this->value->$method($month->value);
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
     * Create an instance that represents this month.
     */
    public static function thisMonth(): self
    {
        return new static(CarbonImmutable::today());
    }
}
