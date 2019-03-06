<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\EventsFileFinder\Date\Exception;

use Snowplow\Tracker\FileSystem\EventsFileFinder\Exception\AbstractEventsFileFinderException;
use Throwable;

final class CannotGetEventsFileException extends AbstractEventsFileFinderException
{
    private const MESSAGE = 'Cannot get events file: %s';

    /**
     * @param Throwable $previous
     *
     * @return CannotGetEventsFileException
     */
    public static function create(Throwable $previous): CannotGetEventsFileException
    {
        return new static(
            sprintf(static::MESSAGE, $previous->getMessage()),
            0,
            $previous
        );
    }
}
