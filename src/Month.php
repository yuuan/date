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
     */
    public function __call(string $method, array $parameters)
    {
        if (in_array($method, $this->comparison, true)) {
            return $this->compare($method, ...$parameters);
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
