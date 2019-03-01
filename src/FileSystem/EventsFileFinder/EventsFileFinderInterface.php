<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\EventsFileFinder;

use Snowplow\Tracker\FileSystem\EventsFileFinder\Date\Exception\CannotGetEventsFileException;
use Snowplow\Tracker\FileSystem\EventsFileFinder\Date\Exception\CannotOpenDirException;

interface EventsFileFinderInterface
{
    /**
     * @param string      $baseDir "/tmp/snowplow/w0/"
     * @param string|null $eventsFileNameIdentifier
     *
     * @return string|null
     * @throws CannotGetEventsFileException
     * @throws CannotOpenDirException
     */
    public function getEventsFile(string $baseDir, ?string $eventsFileNameIdentifier = null): ?string;
}
