<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\EventsFileFinder\Date;

use Snowplow\Tracker\FileSystem\DirectoryCleaner\DirectoryCleanerInterface;
use Snowplow\Tracker\FileSystem\EventsFileFinder\Date\Exception\CannotGetEventsFileException;
use Snowplow\Tracker\FileSystem\EventsFileFinder\Date\Exception\CannotOpenDirException;
use Snowplow\Tracker\FileSystem\EventsFileFinder\EventsFileFinderInterface;
use Throwable;

/**
 * @see \Snowplow\tests\tests\FileSystem\EventsFileFinder\Date\DeepFirstAccendingDateEventsFileFinderTest
 */
class DeepFirstAccendingDateEventsFileFinder implements EventsFileFinderInterface
{
    /**
     * @var DirectoryCleanerInterface
     */
    private $directoryCleaner;

    /**
     * @param DirectoryCleanerInterface $directoryCleaner
     */
    public function __construct(DirectoryCleanerInterface $directoryCleaner)
    {
        $this->directoryCleaner = $directoryCleaner;
    }

    /**
     * @param string      $baseDir "/tmp/snowplow/w0/"
     * @param string|null $eventsFileNameIdentifier
     *
     * @return string|null
     * @throws CannotGetEventsFileException
     * @throws CannotOpenDirException
     */
    public function getEventsFile(string $baseDir, ?string $eventsFileNameIdentifier = null): ?string
    {
        if (is_dir($baseDir)) {
            /** @noinspection ReturnNullInspection */
            return $this->getEventsFileFromDir($baseDir, $eventsFileNameIdentifier, true);
        }

        throw CannotOpenDirException::create($baseDir);
    }

    /**
     * @param string      $dir
     * @param string|null $eventsFileNameIdentifier
     * @param bool        $isRootDir
     *
     * @return null|string
     * @throws CannotGetEventsFileException
     * @throws CannotOpenDirException
     */
    private function getEventsFileFromDir(string $dir, ?string $eventsFileNameIdentifier, bool $isRootDir): ?string
    {
        // get all files and directories except "." and ".."
        $files = glob($this->getFilePath($dir, '{,.}[!.,!..]*'), GLOB_BRACE);

        if (false === $files) {
            throw CannotOpenDirException::create($dir);
        }

        $eventsFile = null;
        foreach ($files as $file) {
            if (is_dir($file)) {
                /** @noinspection ReturnNullInspection */
                $eventsFile = $this->getEventsFileFromDir(
                    $file,
                    $eventsFileNameIdentifier,
                    false
                );
            }

            /** @noinspection ReturnFalseInspection */
            if (null === $eventsFile && $this->isEventsFile($file, $eventsFileNameIdentifier)) {
                $eventsFile = $file;
            }

            if (null !== $eventsFile) {
                break;
            }
        }

        if (!$isRootDir && count($files) === 0) {
            try {
                $this->directoryCleaner->delete($dir);
            } catch (Throwable $e) {
                throw CannotGetEventsFileException::create($e);
            }
        }

        return $eventsFile;
    }

    /**
     * @param string $parentDir
     * @param string $file
     *
     * @return string
     */
    private function getFilePath(string $parentDir, string $file): string
    {
        return $parentDir . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * @param string      $filepath
     * @param string|null $eventsFileNameIdentifier
     *
     * @return bool
     */
    private function isEventsFile(string $filepath, ?string $eventsFileNameIdentifier): bool
    {
        /** @noinspection ReturnFalseInspection */
        return null === $eventsFileNameIdentifier
            || false !== mb_strpos(basename($filepath), $eventsFileNameIdentifier);
    }
}
