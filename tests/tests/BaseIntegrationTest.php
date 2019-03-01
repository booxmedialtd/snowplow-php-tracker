<?php

namespace Snowplow\tests\tests;

use PHPUnit_Framework_TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

abstract class BaseIntegrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string[]
     */
    protected $filesToRemove = [];
    /**
     * @var string[]
     */
    protected $directoriesToRemove = [];

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        foreach ($this->filesToRemove as $fileToRemove) {
            $this->deleteFile($fileToRemove);
        }
        foreach ($this->directoriesToRemove as $directoryToRemove) {
            $this->deleteDirectory($directoryToRemove);
        }
        parent::tearDown();
    }

    /**
     * @param string $filePath
     *
     * @return void
     */
    protected function deleteFile(string $filePath): void
    {
        if (!empty($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * @param string $path
     *
     * @return void
     */
    protected function deleteDirectory(string $path): void
    {
        if (!empty($path)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($path);
        }
    }

    /**
     * @param string $directory
     * @param int    $mode
     *
     * @return void
     */
    protected function createDirectory(string $directory, int $mode = 0777): void
    {
        mkdir($directory, $mode);
    }

    /**
     * @param int $mode
     *
     * @return string
     */
    protected function createTempDirectory(int $mode = 0777): string
    {
        $directory = tempnam('/tmp', 'phpTest');
        $this->deleteFile($directory);
        mkdir($directory, $mode);
        return $directory;
    }

    /**
     * @param string $filePath
     * @param string $content
     *
     * @return void
     */
    protected function createFile(string $filePath, string $content = 'testContent'): void
    {
        /** @noinspection ReturnFalseInspection */
        $isSuccess = file_put_contents($filePath, $content);
        if (false === $isSuccess) {
            static::fail('Unable to create file');
        }
    }
}
