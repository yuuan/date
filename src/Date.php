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
    protected array $comparison = ['eq', 'ne', 'lt', 'lte', 'gt', 'gte'];

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
     * Convert to string.
     */
    public function __toString(): string
    {
        return $this->value->toDateString();
    }

    /**
     * Handle dynamic calls to the instance to compare.
     *
     * @return bool
     */
    public function __call(string $method, array $parameters)
    {
        if (in_array($method, $this->comparison, true)) {
            return $this->compare($method, ...$parameters);
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
