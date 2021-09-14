<?php


namespace EasySwoole\Component\Process;

use EasySwoole\Component\Singleton;
use Swoole\Process;
use Swoole\Server;
use Swoole\Table;

class Manager
{
    use Singleton;

    /** @var AbstractProcess[] $processList */
    protected $processList = [];
    protected $autoRegister = [];
    protected $table;


    public function __construct()
    {
        $this->table = new Table(2048);
        $this->table->column('pid', Table::TYPE_INT, 8);
        $this->table->column('name', Table::TYPE_STRING, 50);
        $this->table->column('group', Table::TYPE_STRING, 50);
        $this->table->column('memoryUsage', Table::TYPE_INT, 8);
        $this->table->column('memoryPeakUsage', Table::TYPE_INT, 8);
        $this->table->column('startUpTime', Table::TYPE_INT, 8);
        $this->table->column('hash', Table::TYPE_INT, 32);
        $this->table->create();
    }

    public function getProcessTable(): Table
    {
        return $this->table;
    }

    public function getProcessByPid(int $pid): ?AbstractProcess
    {
        $info = $this->table->get($pid);
        if($info){
            $hash = $info['hash'];
            if(isset($this->processList[$hash])){
                return $this->processList[$hash];
            }
        }
        return null;
    }

    public function getProcessByName(string $name): array
    {
        $ret = [];
        foreach ($this->processList as $process) {
            if ($process->getProcessName() === $name) {
                $ret[] = $process;
            }
        }

        return $ret;
    }

    public function getProcessByGroup(string $group): array
    {
        $ret = [];
        foreach ($this->processList as $process) {
            if ($process->getConfig()->getProcessGroup() === $group) {
                $ret[] = $process;
            }
        }

        return $ret;
    }

    public function kill($pidOrGroupName, $sig = SIGTERM): array
    {
        $list = [];
        if (is_numeric($pidOrGroupName)) {
            $info = $this->table->get($pidOrGroupName);
            if ($info) {
                $list[$pidOrGroupName] = $pidOrGroupName;
            }
        } else {
            foreach ($this->table as $key => $value) {
                if ($value['group'] == $pidOrGroupName) {
                    $list[$key] = $value;
                }
            }
        }
        $this->clearPid($list);
        foreach ($list as $pid => $value) {
            Process::kill($pid, $sig);
        }
        return $list;
    }

    public function info($pidOrGroupName = null)
    {
        $list = [];
        if ($pidOrGroupName == null) {
            foreach ($this->table as $pid => $value) {
                $list[$pid] = $value;
            }
        } else if (is_numeric($pidOrGroupName)) {
            $info = $this->table->get($pidOrGroupName);
            if ($info) {
                $list[$pidOrGroupName] = $info;
            }
        } else {
            foreach ($this->table as $key => $value) {
                if ($value['group'] == $pidOrGroupName) {
                    $list[$key] = $value;
                }
            }
        }

        $sort = array_column($list, 'group');
        array_multisort($sort, SORT_DESC, $list);
        foreach ($list as $key => $value) {
            unset($list[$key]);
            $list[$value['pid']] = $value;
        }
        return $this->clearPid($list);
    }

    public function addProcess(AbstractProcess $process, bool $autoRegister = true): Manager
    {
        $hash = spl_object_hash($process->getProcess());
        $this->autoRegister[$hash] = $autoRegister;
        $this->processList[$hash] = $process;
        return $this;
    }

    public function attachToServer(Server $server)
    {
        foreach ($this->processList as $hash => $process) {
            if ($this->autoRegister[$hash] === true) {
                $server->addProcess($process->getProcess());
            }
        }
    }

    public function pidExist(int $pid)
    {
        return Process::kill($pid, 0);
    }

    protected function clearPid(array $list)
    {
        foreach ($list as $pid => $value) {
            if (!$this->pidExist($pid)) {
                $this->table->del($pid);
                unset($list[$pid]);
            }
        }
        return $list;
    }
}