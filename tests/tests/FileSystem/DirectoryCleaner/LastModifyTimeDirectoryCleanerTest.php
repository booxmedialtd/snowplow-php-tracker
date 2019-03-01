<?php

namespace Snowplow\tests\tests\FileSystem\DirectoryCleaner;

require_once __DIR__ . '/../../BaseIntegrationTest.php';

use Snowplow\tests\tests\BaseIntegrationTest;
use Snowplow\Tracker\FileSystem\DirectoryCleaner\LastModifyTimeDirectoryCleaner;
use Throwable;

/**
 * @see LastModifyTimeDirectoryCleaner
 */
class LastModifyTimeDirectoryCleanerTest extends BaseIntegrationTest
{
    /**
     * @dataProvider provideDeleteIfNotModifiedSecondsAndExpectedDirStatus
     *
     * @param int  $deleteIfNotModifiedSeconds
     * @param bool $isDeleted
     *
     * @return void
     * @throws Throwable
     */
    public function testGetEventsFileThrowCannotOpenDirectoryException(
        int $deleteIfNotModifiedSeconds,
        bool $isDeleted
    ): void {
        $cleaner = new LastModifyTimeDirectoryCleaner($deleteIfNotModifiedSeconds);

        $testRootDir           = $this->createTempDirectory();
        $directoriesToRemove[] = $testRootDir;

        $testDir = $testRootDir . DIRECTORY_SEPARATOR . 'testDir';
        $this->createDirectory($testDir);

        $cleaner->delete($testDir);

        if ($isDeleted) {
            static::assertDirectoryNotExists($testDir);
        } else {
            static::assertDirectoryExists($testDir);
        }
    }

    public function provideDeleteIfNotModifiedSecondsAndExpectedDirStatus(): array
    {
        // deleteIfNotModifiedSeconds, isDeleted
        return [
            [240, false],
            [-1, true],
        ];
    }
}
