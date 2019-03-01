<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\DirectoryCleaner;

class DummyDirectoryCleaner implements DirectoryCleanerInterface
{
    /**
     * @param string $dir
     *
     * @return void
     */
    public function delete(string $dir): void
    {
        // do nothing
    }
}
