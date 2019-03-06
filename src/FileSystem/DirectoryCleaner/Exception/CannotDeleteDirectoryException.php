<?php declare(strict_types=1);

namespace Snowplow\Tracker\FileSystem\DirectoryCleaner\Exception;

use Throwable;

final class CannotDeleteDirectoryException extends AbstractDirectoryCleanerException
{
    private const MESSAGE = 'Cannot delete empty directory "%s"';

    /**
     * @param string $dir
     *
     * @return CannotDeleteDirectoryException
     */
    public static function create(string $dir): CannotDeleteDirectoryException
    {
        return new static(
            sprintf(static::MESSAGE, $dir)
        );
    }

    /**
     * @param string    $dir
     * @param Throwable $previous
     *
     * @return CannotDeleteDirectoryException
     */
    public static function createWithException(string $dir, Throwable $previous): CannotDeleteDirectoryException
    {
        return new static(
            sprintf(static::MESSAGE, $dir) . ": {$previous->getMessage()}",
            0,
            $previous
        );
    }
}
