<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Snowplow\tests\tests\FileSystem\EventsFileFinder\Date;

require_once __DIR__ . '/../../../BaseIntegrationTest.php';

use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use Snowplow\tests\tests\BaseIntegrationTest;
use Snowplow\Tracker\FileSystem\DirectoryCleaner\DirectoryCleanerInterface;
use Snowplow\Tracker\FileSystem\DirectoryCleaner\LastModifyTimeDirectoryCleaner;
use Snowplow\Tracker\FileSystem\EventsFileFinder\Date\DeepFirstAccendingDateEventsFileFinder;
use Snowplow\Tracker\FileSystem\EventsFileFinder\Date\Exception\CannotGetEventsFileException;
use Snowplow\Tracker\FileSystem\EventsFileFinder\Date\Exception\CannotOpenDirException;

/**
 * @see DeepFirstAccendingDateEventsFileFinder
 */
class DeepFirstAccendingDateEventsFileFinderTest extends BaseIntegrationTest
{
    private const EVENTS_FILE_IDENTIFIER = 'testEvents';

    private const DIR_1 = '01';
    private const DIR_2 = '02';
    private const DIR_3 = '03';

    public function testGetEventsFileThrowCannotOpenDirectoryException(): void
    {
        $cleaner = $this->createMockedDirectoryCleaner();
        $finder  = new DeepFirstAccendingDateEventsFileFinder($cleaner);

        $dir = $this->createTempDirectory();
        $this->deleteDirectory($dir);

        try {
            $finder->getEventsFile($dir, self::EVENTS_FILE_IDENTIFIER);
            static::fail(CannotOpenDirException::class . ' was not thrown');
        } catch (CannotOpenDirException $e) {
            static::assertContains($dir, $e->getMessage());
        }
    }

    public function testGetEventsFileThrowCannotGetEventsFileException(): void
    {
        $expectedException = new Exception('=exception message=');

        $cleaner = $this->createMockedDirectoryCleaner();
        $cleaner->expects(static::once())
            ->method('delete')
            ->willThrowException($expectedException);

        $finder                      = new DeepFirstAccendingDateEventsFileFinder($cleaner);
        $dir                         = $this->createTempDirectory();
        $this->directoriesToRemove[] = $dir;

        $this->createDirectory($dir . '/emptySubdir');

        try {
            $finder->getEventsFile($dir, self::EVENTS_FILE_IDENTIFIER);
            static::fail(CannotGetEventsFileException::class . ' was not thrown');
        } catch (CannotGetEventsFileException $e) {
            static::assertContains($expectedException->getMessage(), $e->getMessage());
        }
    }

    public function testGetEventsFile(): void
    {
        $cleaner = $this->createMockedDirectoryCleaner();
        $finder  = new DeepFirstAccendingDateEventsFileFinder($cleaner);

        $rootDir                     = $this->createTempDirectory();
        $this->directoriesToRemove[] = $rootDir;

        $emptyDir  = $rootDir . DIRECTORY_SEPARATOR . self::DIR_1;
        $emptyDir2 = $rootDir . DIRECTORY_SEPARATOR . self::DIR_2;
        $logDir    = $rootDir . DIRECTORY_SEPARATOR . self::DIR_3;

        $this->createDirectory($emptyDir);
        static::assertDirectoryExists($emptyDir);
        $this->createDirectory($emptyDir2);
        static::assertDirectoryExists($emptyDir2);
        $this->createDirectory($logDir);
        static::assertDirectoryExists($logDir);

        $firstFilePath = $logDir . DIRECTORY_SEPARATOR . self::EVENTS_FILE_IDENTIFIER . '-first.log';
        $this->createFile($firstFilePath);
        $secondFilePath = $logDir . DIRECTORY_SEPARATOR . self::EVENTS_FILE_IDENTIFIER . '-second.log';
        $this->createFile($secondFilePath);

        $file = $finder->getEventsFile($rootDir, self::EVENTS_FILE_IDENTIFIER);
        static::assertSame($firstFilePath, $file);
    }

    public function testGetEventsFileCleanEmptyDirs(): void
    {
        $cleaner = new LastModifyTimeDirectoryCleaner(-1); // clean empty directories immediately
        $finder  = new DeepFirstAccendingDateEventsFileFinder($cleaner);

        $rootDir                     = $this->createTempDirectory();
        $this->directoriesToRemove[] = $rootDir;

        $emptyDir  = $rootDir . DIRECTORY_SEPARATOR . self::DIR_1;
        $emptyDir2 = $rootDir . DIRECTORY_SEPARATOR . self::DIR_2;
        $logDir    = $rootDir . DIRECTORY_SEPARATOR . self::DIR_3;

        $this->createDirectory($emptyDir);
        static::assertDirectoryExists($emptyDir);
        $this->createDirectory($emptyDir2);
        static::assertDirectoryExists($emptyDir2);
        $this->createDirectory($logDir);
        static::assertDirectoryExists($logDir);

        $firstFilePath = $logDir . DIRECTORY_SEPARATOR . self::EVENTS_FILE_IDENTIFIER . '-first.log';
        $this->createFile($firstFilePath);

        $finder->getEventsFile($rootDir, self::EVENTS_FILE_IDENTIFIER);

        static::assertDirectoryNotExists($emptyDir);
        static::assertDirectoryNotExists($emptyDir2);
        static::assertDirectoryExists($logDir);
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|DirectoryCleanerInterface
     */
    private function createMockedDirectoryCleaner()
    {
        return $this->createMock(DirectoryCleanerInterface::class);
    }
}
