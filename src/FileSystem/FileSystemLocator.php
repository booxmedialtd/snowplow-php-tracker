<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem;

use Snowplow\Tracker\FileSystem\DirectoryCleaner\DirectoryCleanerInterface;
use Snowplow\Tracker\FileSystem\DirectoryCleaner\LastModifyTimeDirectoryCleaner;
use Snowplow\Tracker\FileSystem\EventsFileFinder\Date\DeepFirstAccendingDateEventsFileFinder;
use Snowplow\Tracker\FileSystem\EventsFileFinder\EventsFileFinderInterface;

final class FileSystemLocator
{
    private const HOUR = 60 * 60;

    /**
     * @var \Snowplow\Tracker\FileSystem\FileSystemLocator
     */
    private static $instance;

    /**
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * @return FileSystemLocator
     */
    public static function getInstance(): FileSystemLocator
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @var DeepFirstAccendingDateEventsFileFinder
     */
    private $accendingDateEventsFileFinder;

    /**
     * @return EventsFileFinderInterface
     */
    public function getAccendingDateEventsFileFinder(): EventsFileFinderInterface
    {
        if (null === $this->accendingDateEventsFileFinder) {
            $this->accendingDateEventsFileFinder = new DeepFirstAccendingDateEventsFileFinder(
                $this->createDirectoryCleaner()
            );
        }
        return $this->accendingDateEventsFileFinder;
    }

    /**
     * @param EventsFileFinderInterface|null $finder
     *
     * @return void
     */
    public function setAccendingDateEventsFileFinder(?EventsFileFinderInterface $finder = null): void
    {
        $this->accendingDateEventsFileFinder = $finder;
    }

    /**
     * @return DirectoryCleanerInterface
     */
    private function createDirectoryCleaner(): DirectoryCleanerInterface
    {
        return new LastModifyTimeDirectoryCleaner(self::HOUR * 2);
    }
}
