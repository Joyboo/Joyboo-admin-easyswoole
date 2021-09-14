<?php


namespace EasySwoole\Component\Process\Socket;


use EasySwoole\Component\Process\Config;

class UnixProcessConfig extends Config
{
    protected $socketFile;
    protected $asyncCallback = true;
    protected $linger = [ 'l_linger' => 0, 'l_onoff' => 0];

    /**
     * @return int[]
     */
    public function getLinger(): array
    {
        return $this->linger;
    }

    /**
     * @param int[] $linger
     */
    public function setLinger(array $linger): void
    {
        $this->linger = $linger;
    }

    /**
     * @return mixed
     */
    public function getSocketFile()
    {
        return $this->socketFile;
    }

    /**
     * @param mixed $socketFile
     */
    public function setSocketFile($socketFile): void
    {
        $this->socketFile = $socketFile;
    }

    /**
     * @return bool
     */
    public function isAsyncCallback(): bool
    {
        return $this->asyncCallback;
    }

    /**
     * @param bool $asyncCallback
     */
    public function setAsyncCallback(bool $asyncCallback): void
    {
        $this->asyncCallback = $asyncCallback;
    }
}