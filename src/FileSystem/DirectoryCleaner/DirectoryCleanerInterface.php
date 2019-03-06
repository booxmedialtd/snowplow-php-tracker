<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\DirectoryCleaner;


use Snowplow\Tracker\FileSystem\DirectoryCleaner\Exception\CannotDeleteDirectoryException;

interface DirectoryCleanerInterface
{
    /**
     * @param string $dir
     *
     * @return void
     * @throws CannotDeleteDirectoryException
     */
    public function delete(string $dir): void;
}
