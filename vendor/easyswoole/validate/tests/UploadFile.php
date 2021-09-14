<?php

namespace EasySwoole\Validate\tests;

use Psr\Http\Message\UploadedFileInterface;

class UploadFile implements UploadedFileInterface
{
    private $tempName;

    private $stream;

    private $size;

    private $error;

    private $clientFileName;

    private $clientMediaType;

    public function __construct($tempName, $size, $errorStatus, $clientFilename = null, $clientMediaType = null)
    {
        $this->tempName = $tempName;
        $this->stream = '';
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
}
