<?php


namespace EasySwoole\Utility\Tests;


use EasySwoole\Utility\FileSystem;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * Class FileSystemTest
 * @package EasySwoole\Utility\Tests
 * reference link https://github.com/laravel/framework/blob/8.x/tests/Filesystem/FilesystemTest.php
 */
class FileSystemTest extends TestCase
{

    protected static $tempDir;

    public static function setUpBeforeClass(): void
    {
        static::$tempDir = __DIR__ . '/Temp';
        $files = new FileSystem();
        $files->makeDirectory(static::$tempDir);
    }

    public static function tearDownAfterClass(): void
    {
        $files = new FileSystem();
        $files->deleteDirectory(static::$tempDir);
    }

    protected function tearDown(): void
    {
        $files = new FileSystem();
        $files->deleteDirectory(static::$tempDir, true);
    }

    public function testGet()
    {
        file_put_contents(self::$tempDir . '/file.txt', 'Hello World');
        $files = new Filesystem;
        $this->assertEquals('Hello World', $files->get(self::$tempDir . '/file.txt'));
    }

    public function testPut()
    {
        $files = new Filesystem;
        $files->put(self::$tempDir . '/file.txt', 'Hello World');
        $this->assertStringEqualsFile(self::$tempDir . '/file.txt', 'Hello World');
    }

    public function testReplace()
    {
        $tempFile = self::$tempDir . '/file.txt';
        $filesystem = new Filesystem;
        $filesystem->replace($tempFile, 'Hello World');
        $this->assertStringEqualsFile($tempFile, 'Hello World');
    }

    public function testSetChmod()
    {
        file_put_contents(self::$tempDir . '/file.txt', 'Hello World');
        $files = new Filesystem;
        $files->chmod(self::$tempDir . '/file.txt', 0755);
        $filePermission = substr(sprintf('%o', fileperms(self::$tempDir . '/file.txt')), -4);
        $expectedPermissions = DIRECTORY_SEPARATOR == '\\' ? '0666' : '0755';
        $this->assertEquals($expectedPermissions, $filePermission);
    }

    public function testGetChmod()
    {
        file_put_contents(self::$tempDir . '/file.txt', 'Hello World');
        chmod(self::$tempDir . '/file.txt', 0755);
        $files = new Filesystem;
        $filePermission = $files->chmod(self::$tempDir . '/file.txt');
        $expectedPermissions = DIRECTORY_SEPARATOR == '\\' ? '0666' : '0755';
        $this->assertEquals($expectedPermissions, $filePermission);
    }

    public function testDelete()
    {
        file_put_contents(self::$tempDir . '/file1.txt', 'Hello World');
        file_put_contents(self::$tempDir . '/file2.txt', 'Hello World');
        file_put_contents(self::$tempDir . '/file3.txt', 'Hello World');

        $files = new Filesystem;
        $files->delete(self::$tempDir . '/file1.txt');
        Assert::assertFileDoesNotExist(self::$tempDir . '/file1.txt');

        $files->delete([self::$tempDir . '/file2.txt', self::$tempDir . '/file3.txt']);
        Assert::assertFileDoesNotExist(self::$tempDir . '/file2.txt');
        Assert::assertFileDoesNotExist(self::$tempDir . '/file3.txt');
    }

    public function testPrependExistingFiles()
    {
        $files = new Filesystem;
        $files->put(self::$tempDir . '/file.txt', 'World');
        $files->prepend(self::$tempDir . '/file.txt', 'Hello ');
        $this->assertStringEqualsFile(self::$tempDir . '/file.txt', 'Hello World');
    }

    public function testPrependNewFiles()
    {
        $files = new Filesystem;
        $files->prepend(self::$tempDir . '/file.txt', 'Hello World');
        $this->assertStringEqualsFile(self::$tempDir . '/file.txt', 'Hello World');
    }

    public function testMissingFile()
    {
        $files = new Filesystem;
        $this->assertTrue($files->missing(self::$tempDir . '/file.txt'));
    }

    public function testDeleteDirectory()
    {
        mkdir(self::$tempDir . '/foo');
        file_put_contents(self::$tempDir . '/foo/file.txt', 'Hello World');
        $files = new Filesystem;
        $files->deleteDirectory(self::$tempDir . '/foo');
        Assert::assertDirectoryDoesNotExist(self::$tempDir . '/foo');
        Assert::assertFileDoesNotExist(self::$tempDir . '/foo/file.txt');
    }

    public function testDeleteDirectoryReturnFalseWhenNotADirectory()
    {
        mkdir(self::$tempDir . '/bar');
        file_put_contents(self::$tempDir . '/bar/file.txt', 'Hello World');
        $files = new Filesystem;
        $this->assertFalse($files->deleteDirectory(self::$tempDir . '/bar/file.txt'));
    }

    public function testCleanDirectory()
    {
        mkdir(self::$tempDir . '/baz');
        file_put_contents(self::$tempDir . '/baz/file.txt', 'Hello World');
        $files = new Filesystem;
        $files->cleanDirectory(self::$tempDir . '/baz');
        $this->assertDirectoryExists(self::$tempDir . '/baz');
        Assert::assertFileDoesNotExist(self::$tempDir . '/baz/file.txt');
    }

    public function testCopyDirectoryReturnsFalse()
    {
        $files = new Filesystem;
        $this->assertFalse($files->copyDirectory(self::$tempDir . '/breeze/boom/foo/bar/baz', self::$tempDir));
    }

    public function testCopyDirectoryMovesEntireDirectory()
    {
        mkdir(self::$tempDir . '/tmp', 0777, true);
        file_put_contents(self::$tempDir . '/tmp/foo.txt', '');
        file_put_contents(self::$tempDir . '/tmp/bar.txt', '');
        mkdir(self::$tempDir . '/tmp/nested', 0777, true);
        file_put_contents(self::$tempDir . '/tmp/nested/baz.txt', '');

        $files = new Filesystem;
        $files->copyDirectory(self::$tempDir . '/tmp', self::$tempDir . '/tmp2');
        $this->assertDirectoryExists(self::$tempDir . '/tmp2');
        $this->assertFileExists(self::$tempDir . '/tmp2/foo.txt');
        $this->assertFileExists(self::$tempDir . '/tmp2/bar.txt');
        $this->assertDirectoryExists(self::$tempDir . '/tmp2/nested');
        $this->assertFileExists(self::$tempDir . '/tmp2/nested/baz.txt');
    }

    public function testMoveDirectoryMovesEntireDirectory()
    {
        mkdir(self::$tempDir . '/tmp2', 0777, true);
        file_put_contents(self::$tempDir . '/tmp2/foo.txt', '');
        file_put_contents(self::$tempDir . '/tmp2/bar.txt', '');
        mkdir(self::$tempDir . '/tmp2/nested', 0777, true);
        file_put_contents(self::$tempDir . '/tmp2/nested/baz.txt', '');

        $files = new Filesystem;
        $files->moveDirectory(self::$tempDir . '/tmp2', self::$tempDir . '/tmp3');
        $this->assertDirectoryExists(self::$tempDir . '/tmp3');
        $this->assertFileExists(self::$tempDir . '/tmp3/foo.txt');
        $this->assertFileExists(self::$tempDir . '/tmp3/bar.txt');
        $this->assertDirectoryExists(self::$tempDir . '/tmp3/nested');
        $this->assertFileExists(self::$tempDir . '/tmp3/nested/baz.txt');
        Assert::assertDirectoryDoesNotExist(self::$tempDir . '/tmp2');
    }

    public function testMoveDirectoryMovesEntireDirectoryAndOverwrites()
    {
        mkdir(self::$tempDir . '/tmp4', 0777, true);
        file_put_contents(self::$tempDir . '/tmp4/foo.txt', '');
        file_put_contents(self::$tempDir . '/tmp4/bar.txt', '');
        mkdir(self::$tempDir . '/tmp4/nested', 0777, true);
        file_put_contents(self::$tempDir . '/tmp4/nested/baz.txt', '');
        mkdir(self::$tempDir . '/tmp5', 0777, true);
        file_put_contents(self::$tempDir . '/tmp5/foo2.txt', '');
        file_put_contents(self::$tempDir . '/tmp5/bar2.txt', '');

        $files = new Filesystem;
        $files->moveDirectory(self::$tempDir . '/tmp4', self::$tempDir . '/tmp5', true);
        $this->assertDirectoryExists(self::$tempDir . '/tmp5');
        $this->assertFileExists(self::$tempDir . '/tmp5/foo.txt');
        $this->assertFileExists(self::$tempDir . '/tmp5/bar.txt');
        $this->assertDirectoryExists(self::$tempDir . '/tmp5/nested');
        $this->assertFileExists(self::$tempDir . '/tmp5/nested/baz.txt');
        Assert::assertFileDoesNotExist(self::$tempDir . '/tmp5/foo2.txt');
        Assert::assertFileDoesNotExist(self::$tempDir . '/tmp5/bar2.txt');
        Assert::assertDirectoryDoesNotExist(self::$tempDir . '/tmp4');
    }

    public function testMoveDirectoryReturns()
    {
        mkdir(self::$tempDir . '/tmp6', 0777, true);
        file_put_contents(self::$tempDir . '/tmp6/foo.txt', '');
        mkdir(self::$tempDir . '/tmp7', 0777, true);

        $files = new FileSystem();
        $this->assertTrue($files->moveDirectory(self::$tempDir . '/tmp6', self::$tempDir . '/tmp7', true));
    }

    public function testGetThrowsException()
    {
        $this->expectException(\Exception::class);

        $files = new Filesystem;
        $files->get(self::$tempDir . '/unknown-file.txt');
    }

    public function testAppendAddsDataToFile()
    {
        file_put_contents(self::$tempDir . '/file.txt', 'foo');
        $files = new Filesystem;
        $bytesWritten = $files->append(self::$tempDir . '/file.txt', 'bar');
        $this->assertEquals(mb_strlen('bar', '8bit'), $bytesWritten);
        $this->assertFileExists(self::$tempDir . '/file.txt');
        $this->assertStringEqualsFile(self::$tempDir . '/file.txt', 'foobar');
    }

    public function testMoveMovesFiles()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $files = new Filesystem;
        $files->move(self::$tempDir . '/foo.txt', self::$tempDir . '/bar.txt');
        $this->assertFileExists(self::$tempDir . '/bar.txt');
        Assert::assertFileDoesNotExist(self::$tempDir . '/foo.txt');
    }

    public function testNameReturnsName()
    {
        file_put_contents(self::$tempDir . '/foobar.txt', 'foo');
        $filesystem = new Filesystem;
        $this->assertSame('foobar', $filesystem->name(self::$tempDir . '/foobar.txt'));
    }

    public function testExtensionReturnsExtension()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertSame('txt', $files->extension(self::$tempDir . '/foo.txt'));
    }

    public function testBasenameReturnsBasename()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertSame('foo.txt', $files->basename(self::$tempDir . '/foo.txt'));
    }

    public function testDirnameReturnsDirectory()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals(self::$tempDir, $files->dirname(self::$tempDir . '/foo.txt'));
    }

    public function testTypeIdentifiesFile()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertSame('file', $files->type(self::$tempDir . '/foo.txt'));
    }

    public function testTypeIdentifiesDirectory()
    {
        mkdir(self::$tempDir . '/foo-dir');
        $files = new Filesystem;
        $this->assertSame('dir', $files->type(self::$tempDir . '/foo-dir'));
    }

    public function testSizeOutputsSize()
    {
        $size = file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $files = new Filesystem;
        $this->assertEquals($size, $files->size(self::$tempDir . '/foo.txt'));
    }

    public function testIsWritable()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $files = new Filesystem;
        @chmod(self::$tempDir . '/foo.txt', 0444);
        $this->assertFalse($files->isWritable(self::$tempDir . '/foo.txt'));
        @chmod(self::$tempDir . '/foo.txt', 0777);
        $this->assertTrue($files->isWritable(self::$tempDir . '/foo.txt'));
    }

    public function testIsReadable()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $files = new Filesystem;
        // chmod is noneffective on Windows
        if (DIRECTORY_SEPARATOR === '\\') {
            $this->assertTrue($files->isReadable(self::$tempDir . '/foo.txt'));
        } else {
            @chmod(self::$tempDir . '/foo.txt', 0000);
            $this->assertFalse($files->isReadable(self::$tempDir . '/foo.txt'));
            @chmod(self::$tempDir . '/foo.txt', 0777);
            $this->assertTrue($files->isReadable(self::$tempDir . '/foo.txt'));
        }
        $this->assertFalse($files->isReadable(self::$tempDir . '/doesnotexist.txt'));
    }

    public function testGlobFindsFiles()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        file_put_contents(self::$tempDir . '/bar.txt', 'bar');
        $files = new Filesystem;
        $glob = $files->glob(self::$tempDir . '/*.txt');
        $this->assertContains(self::$tempDir . '/foo.txt', $glob);
        $this->assertContains(self::$tempDir . '/bar.txt', $glob);
    }

    public function testMakeDirectory()
    {
        $files = new Filesystem;
        $this->assertTrue($files->makeDirectory(self::$tempDir . '/created'));
        $this->assertFileExists(self::$tempDir . '/created');
    }

    /**
     * @requires extension pcntl
     * @requires function pcntl_fork
     */
    public function testSharedGet()
    {
        if (PHP_OS == 'Darwin') {
            $this->markTestSkipped('The operating system is MacOS.');
        }

        $content = str_repeat('123456', 1000000);
        $result = 1;

        posix_setpgid(0, 0);

        for ($i = 1; $i <= 20; $i++) {
            $pid = pcntl_fork();

            if (!$pid) {
                $files = new Filesystem;
                $files->put(self::$tempDir . '/file.txt', $content, true);
                $read = $files->get(self::$tempDir . '/file.txt', true);

                exit(strlen($read) === strlen($content) ? 1 : 0);
            }
        }

        while (pcntl_waitpid(0, $status) != -1) {
            $status = pcntl_wexitstatus($status);
            $result *= $status;
        }

        $this->assertSame(1, $result);
    }

    public function testCopyCopiesFileProperly()
    {
        $filesystem = new Filesystem;
        $data = 'contents';
        mkdir(self::$tempDir . '/text');
        file_put_contents(self::$tempDir . '/text/foo.txt', $data);
        $filesystem->copy(self::$tempDir . '/text/foo.txt', self::$tempDir . '/text/foo2.txt');
        $this->assertFileExists(self::$tempDir . '/text/foo2.txt');
        $this->assertEquals($data, file_get_contents(self::$tempDir . '/text/foo2.txt'));
    }

    public function testIsFileChecksFilesProperly()
    {
        $filesystem = new Filesystem;
        mkdir(self::$tempDir . '/help');
        file_put_contents(self::$tempDir . '/help/foo.txt', 'contents');
        $this->assertTrue($filesystem->isFile(self::$tempDir . '/help/foo.txt'));
        $this->assertFalse($filesystem->isFile(self::$tempDir . './help'));
    }

    public function testHash()
    {
        file_put_contents(self::$tempDir . '/foo.txt', 'foo');
        $filesystem = new Filesystem;
        $this->assertSame('acbd18db4cc2f85cedef654fccc4a4d8', $filesystem->hash(self::$tempDir . '/foo.txt'));
    }

}
