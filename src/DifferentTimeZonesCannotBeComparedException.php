<?php

declare(strict_types=1);

namespace Yuuan\Date;

use InvalidArgumentException;

class DifferentTimeZonesCannotBeComparedException extends InvalidArgumentException
{
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
            'Cannot be compared because TimeZone `%s` and TimeZone `%s` do not match.',
            $first->timezone()->getName(),
            $second->timezone()->getName()
        );
    }
}
