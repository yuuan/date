<?php

declare(strict_types=1);

namespace Yuuan\Date;

use InvalidArgumentException;

class EndDateIsBeforeStartDateException extends InvalidArgumentException
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
            'The end date `%s` is before the start date `%s`.',
            $start,
            $end
        );
    }

    /**
     * Get the start date.
     */
    public function getStart(): Date
    {
        return $this->start;
    }

    /**
     * Get the end date.
     */
    public function getEnd(): Date
    {
        return $this->end;
    }
}
