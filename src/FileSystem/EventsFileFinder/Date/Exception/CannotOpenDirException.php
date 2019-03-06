<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\EventsFileFinder\Date\Exception;

use Snowplow\Tracker\FileSystem\EventsFileFinder\Exception\AbstractEventsFileFinderException;

final class CannotOpenDirException extends AbstractEventsFileFinderException
{
    private const MESSAGE = 'Cannot open directory "%s"';

    /**
     * @param string $dir
     *
     * @return CannotOpenDirException
     */
    public static function create(string $dir): CannotOpenDirException
    {
        return new static(
            sprintf(static::MESSAGE, $dir)
        );
    }
}
