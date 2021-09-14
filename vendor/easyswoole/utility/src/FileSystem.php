<?php

namespace EasySwoole\Utility;

use \Exception;
use \Throwable;
use \FilesystemIterator;

/**
 * Class FileSystem
 * @package EasySwoole\Utility
 * reference link https://github.com/laravel/framework/blob/8.x/src/Illuminate/Filesystem/Filesystem.php
 */
class FileSystem
{
    /**
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function missing(string $path): bool
    {
        return !$this->exists($path);
    }

    /**
     * @param string $path
     * @param bool $lock
     * @return false|string
     * @throws Exception
     */
    public function get(string $path, bool $lock = false)
    {
        if (!$this->isFile($path)) {
            throw new Exception("File does not exist at path {$path}.");
        }
        return $lock ? $this->sharedGet($path) : file_get_contents($path);
    }

    /**
     * @param string $path
     * @return false|string
     */
    public function sharedGet(string $path)
    {
        $contents = '';
        $handle = fopen($path, 'rb');
        if (!$handle) {
            return $contents;
        }
        try {
            if (!flock($handle, LOCK_SH)) {
                return $contents;
            }
            clearstatcache(true, $path);
            $contents = fread($handle, $this->size($path) ?: 1);
            flock($handle, LOCK_UN);
        } finally {
            fclose($handle);
        }
        return $contents;
    }

    /**
     * @param string $path
     * @return false|string
     */
    public function hash(string $path)
    {
        return md5_file($path);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param bool $lock
     * @return false|int
     */
    public function put(string $path, string $contents, bool $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * @param string $path
     * @param string $content
     */
    public function replace(string $path, string $content)
    {
        clearstatcache(true, $path);
        $path = realpath($path) ?: $path;
        $tempPath = tempnam(dirname($path), basename($path));
        chmod($tempPath, 0777 - umask());
        file_put_contents($tempPath, $content);
        rename($tempPath, $path);
    }

    /**
     * @param string $path
     * @param string $data
     * @return false|int
     * @throws Exception
     */
    public function prepend(string $path, string $data)
    {
        if ($this->exists($path)) {
            return $this->put($path, $data . $this->get($path));
        }
        return $this->put($path, $data);
    }

    /**
     * @param string $path
     * @param string $data
     * @return false|int
     */
    public function append(string $path, string $data)
    {
        return file_put_contents($path, $data, FILE_APPEND);
    }

    /**
     * @param string $path
     * @param int|null $mode
     * @return bool|string
     */
    public function chmod(string $path, ?int $mode = null)
    {
        if ($mode) {
            return chmod($path, $mode);
        }
        return substr(sprintf('%o', fileperms($path)), -4);
    }

    /**
     * @param string|array $paths
     * @return bool
     */
    public function delete($paths)
    {
        $paths = is_array($paths) ? $paths : func_get_args();
        $ret = true;
        foreach ($paths as $path) {
            try {
                if (!unlink($path)) {
                    $ret = false;
                }
            } catch (Throwable $throwable) {
                $ret = false;
            }
        }
        return $ret;
    }

    /**
     * @param string $path
     * @param string $target
     * @return bool
     */
    public function move(string $path, string $target)
    {
        return rename($path, $target);
    }

    /**
     * @param string $path
     * @param string $target
     * @return bool
     */
    public function copy(string $path, string $target)
    {
        return copy($path, $target);
    }

    /**
     * @param string $path
     * @return string
     */
    public function name(string $path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function basename(string $path)
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function dirname(string $path)
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * @param string $path
     * @return string
     */
    public function extension(string $path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * @param string $path
     * @return string
     */
    public function type(string $path)
    {
        return filetype($path);
    }

    /**
     * @param string $path
     * @return false|int
     */
    public function size(string $path)
    {
        return filesize($path);
    }

    /**
     * @param string $path
     * @return int
     */
    public function lastModified(string $path)
    {
        return filemtime($path);
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function isDirectory(string $directory)
    {
        return is_dir($directory);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isReadable(string $path)
    {
        return is_readable($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isWritable(string $path)
    {
        return is_writable($path);
    }

    /**
     * @param string $file
     * @return bool
     */
    public function isFile(string $file)
    {
        return is_file($file);
    }

    /**
     * @param string $pattern
     * @param int $flags
     * @return array|false
     */
    public function glob(string $pattern, int $flags = 0)
    {
        return glob($pattern, $flags);
    }

    /**
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function ensureDirectoryExists(string $path, $mode = 0755, bool $recursive = true)
    {
        if (!$this->isDirectory($path)) {
            return $this->makeDirectory($path, $mode, $recursive);
        }
        return true;
    }

    /**
     * @param string $path
     * @param int $mode
     * @param bool $recursive
     * @param bool $force
     * @return bool
     */
    public function makeDirectory(string $path, $mode = 0755, bool $recursive = false, bool $force = false): bool
    {
        if ($force) {
            return @mkdir($path, $mode, $recursive);
        }
        return mkdir($path, $mode, $recursive);
    }

    /**
     * @param string $from
     * @param string $to
     * @param bool $overwrite
     * @return bool
     */
    public function moveDirectory(string $from, string $to, bool $overwrite = false): bool
    {
        if ($overwrite && $this->isDirectory($to) && !$this->deleteDirectory($to)) {
            return false;
        }
        return @rename($from, $to) === true;
    }

    /**
     * Copy a directory from one location to another.
     *
     * @param string $directory
     * @param string $destination
     * @param int|null $options
     * @return bool
     */
    public function copyDirectory(string $directory, string $destination, ?int $options = null): bool
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }
        $options = $options ?: FilesystemIterator::SKIP_DOTS;
        // If the destination directory does not actually exist, we will go ahead and
        // create it recursively, which just gets the destination prepared to copy
        // the files over. Once we make the directory we'll proceed the copying.
        $this->ensureDirectoryExists($destination, 0777);
        $items = new FilesystemIterator($directory, $options);
        foreach ($items as $item) {
            // As we spin through items, we will check to see if the current file is actually
            // a directory or a file. When it is actually a directory we will need to call
            // back into this function recursively to keep copying these nested folders.
            $target = $destination . '/' . $item->getBasename();
            if ($item->isDir()) {
                $path = $item->getPathname();
                if (!$this->copyDirectory($path, $target, $options)) {
                    return false;
                }
            }
            // If the current items is just a regular file, we will just copy this to the new
            // location and keep looping. If for some reason the copy fails we'll bail out
            // and return false, so the developer is aware that the copy process failed.
            else {
                if (!$this->copy($item->getPathname(), $target)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param string $directory
     * @param bool $preserve
     * @return bool
     */
    public function deleteDirectory(string $directory, bool $preserve = false): bool
    {
        if (!$this->isDirectory($directory)) {
            return false;
        }
        $items = new FilesystemIterator($directory);
        foreach ($items as $item) {
            // If the item is a directory, we can just recurse into the function and
            // delete that sub-directory otherwise we'll just delete the file and
            // keep iterating through each file until the directory is cleaned.
            if ($item->isDir() && !$item->isLink()) {
                $this->deleteDirectory($item->getPathname());
            }
            // If the item is just a file, we can go ahead and delete it since we're
            // just looping through and waxing all of the files in this directory
            // and calling directories recursively, so we delete the real path.
            else {
                $this->delete($item->getPathname());
            }
        }
        if (!$preserve) {
            @rmdir($directory);
        }
        return true;
    }

    /**
     * @param string $directory
     * @return bool
     */
    public function cleanDirectory(string $directory): bool
    {
        return $this->deleteDirectory($directory, true);
    }
}
