<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/24
 * Time: 下午3:20
 */

namespace EasySwoole\Http\Message;


use EasySwoole\Http\Exception\FileException;
use EasySwoole\Utility\File;
use Psr\Http\Message\UploadedFileInterface;

class UploadFile implements UploadedFileInterface
{
    private $tempName;
    private $stream;
    private $size;
    private $error;
    private $clientFileName;
    private $clientMediaType;

    function __construct( $tempName,$size, $errorStatus, $clientFilename = null, $clientMediaType = null)
    {
        $this->tempName = $tempName;
        $this->stream = new Stream(fopen($tempName,"r+"));
        $this->error = $errorStatus;
        $this->size = $size;
        $this->clientFileName = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    public function getTempName()
    {
        return $this->tempName;
    }

    public function getStream()
    {
        return $this->stream;
    }

    public function moveTo($targetPath)
    {
        if (!(is_string($targetPath) && false === empty($targetPath))) {
            throw new FileException('Please provide a valid path');
        }

        if ($this->size <= 0) {
            throw new FileException('Unable to retrieve stream');
        }

        $dir = dirname($targetPath);
        if (!File::createDirectory($dir)) {
            throw new FileException(sprintf('Directory "%s" was not created', $dir));
        }

        $movedSize = file_put_contents($targetPath,$this->stream);
        if (!$movedSize) {
            throw new FileException(sprintf('Uploaded file could not be move to %s', $dir));
        }

        if ($movedSize !== $this->size) {
            throw new FileException(sprintf('File upload specified directory(%s) interrupted', $dir));
        }
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getClientFilename()
    {
        return $this->clientFileName;
    }

    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    public function __destruct()
    {
        $this->stream->close();
    }
}
