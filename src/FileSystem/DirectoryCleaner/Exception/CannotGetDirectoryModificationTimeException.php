<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\DirectoryCleaner\Date\Exception;

use Snowplow\Tracker\FileSystem\DirectoryCleaner\Exception\AbstractDirectoryCleanerException;

final class CannotGetDirectoryModificationTimeException extends AbstractDirectoryCleanerException
{
    private const MESSAGE = 'Cannot get directory "%s" modification time';

    /**
     * @param string $dir
     *
     * @return CannotGetDirectoryModificationTimeException
     */
    public static function create(string $dir): CannotGetDirectoryModificationTimeException
    {
        return new static(
            sprintf(static::MESSAGE, $dir)
        );
    }
}
