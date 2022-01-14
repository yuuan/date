<?php

declare(strict_types=1);

namespace Yuuan\Date;

use RuntimeException;
use Yuuan\ReadOnly\HasReadOnlyProperty;

class RangesDontOverlapException extends RuntimeException
{
    use HasReadOnlyProperty;

    /**
     * The first date range.
     */
    private DateRange $first;

    /**
     * The second date range.
     */
    private DateRange $second;

    /**
     * Create an exception instance.
     */
    public function __construct(DateRange $first, DateRange $second)
    {
        $this->first = $first;
        $this->second = $second;

        $this->message = sprintf(
            'Date range `%s - %s` and date range `%s - %s` do not overlap.',
            $first->start,
            $first->end,
            $second->start,
            $second->end
        );
    }

    /**
     * Get the first date range.
     */
    public function getFirst(): DateRange
    {
        return $this->first;
    }

    /**
     * Get the second date range.
     */
    public function getSecond(): DateRange
    {
        return $this->second;
    }
}
