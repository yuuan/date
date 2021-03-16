<?php

declare(strict_types=1);

namespace Yuuan\Date;

use InvalidArgumentException;

class DifferentTimeZoneException extends InvalidArgumentException
{
    /**
     * The start date.
     */
    private Date $start;

    /**
     * The end date.
     */
    private Date $end;

    /**
     * Create an exception instance.
     */
    public function __construct(Date $start, Date $end)
    {
        $this->start = $start;
        $this->end = $end;

        $this->message = sprintf(
            'The start date TimeZone `%s` and the end date TimeZone `%s` are different.',
            $start->value->tzName,
            $end->value->tzName
        );
    }
}
