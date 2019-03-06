<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\DirectoryCleaner;

use Snowplow\Tracker\FileSystem\DirectoryCleaner\Date\Exception\CannotGetDirectoryModificationTimeException;
use Snowplow\Tracker\FileSystem\DirectoryCleaner\Exception\CannotDeleteDirectoryException;

/**
 * @see \Snowplow\tests\tests\FileSystem\DirectoryCleaner\LastModifyTimeDirectoryCleanerTest
 */
class LastModifyTimeDirectoryCleaner implements DirectoryCleanerInterface
{
    /**
     * @var int
     */
    private $deleteIfNotModifiedSeconds;

    /**
     * @param int $deleteIfNotModifySeconds
     */
    public function __construct(int $deleteIfNotModifySeconds)
    {
        $this->deleteIfNotModifiedSeconds = $deleteIfNotModifySeconds;
    }

    /**
     * @param string $dir
     *
     * @return void
     * @throws CannotDeleteDirectoryException
     */
    public function delete(string $dir): void
    {
        /** @noinspection ReturnFalseInspection */
        $dirModifiedTime = filemtime($dir);

        if (false === $dirModifiedTime) {
            throw CannotDeleteDirectoryException::createWithException(
                $dir,
                CannotGetDirectoryModificationTimeException::create($dir)
            );
        }

        if (($dirModifiedTime < $this->getCurrentTime() - $this->deleteIfNotModifiedSeconds)
            && false === rmdir($dir)
        ) {
            throw CannotDeleteDirectoryException::create($dir);
        }
    }

    /**
     * @return int
     */
    private function getCurrentTime(): int
    {
        return time();
    }

}
