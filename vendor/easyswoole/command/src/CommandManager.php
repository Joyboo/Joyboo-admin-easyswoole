<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */

namespace EasySwoole\Command;

use EasySwoole\Command\AbstractInterface\CallerInterface;
use EasySwoole\Command\AbstractInterface\CommandInterface;
use EasySwoole\Component\Singleton;

class CommandManager
{
    use Singleton;

    /**
     * desc
     * @var string
     */
    private $desc = 'Welcome To EasySwoole Command Console!';

    /**
     * a b framework=easyswoole
     * @var array
     */
    private $args = [];

    /**
     * --config=dev.php -d
     * @var array
     */
    private $opts = [];

    /**
     * 当前执行的command
     * @var string
     */
    private $command = '';

    /**
     * 脚本
     * @var string
     */
    private $script = '';

    /**
     * add commands
     * @var array
     */
    private $commands = [];

    /**
     * @var int
     */
    private $width = 1;

    /**
     * @var array
     */
    private $originArgv = [];

    /**
     * @param CallerInterface $caller
     * @return string
     */
    public function run(CallerInterface $caller): ?string
    {
        $argv = $this->originArgv = $caller->getParams();

        // remove script command
        array_shift($argv);
        array_shift($argv);

        // script
        $this->script = $caller->getScript();

        // command
        $this->command = $caller->getCommand();

        $this->parseArgv(array_values($argv));

        if (!($command = $this->command)) {
            return $this->displayHelp();
        }

        if ($command == '--help' || $command == '-h') {
            return $this->displayHelp();
        }

        if ($this->issetOpt('h') || $this->issetOpt('help')) {
            return $this->displayCommandHelp($command);
        }

        if (!array_key_exists($command, $this->commands)) {
            return $this->displayAlternativesHelp($command);
        }

        /** @var CommandInterface $handler */
        $handler = $this->commands[$command];

        return $handler->exec();
    }

    /**
     * @return array
     */
    public function getOriginArgv(): array
    {
        return $this->originArgv;
    }

    /**
     * @param array $params
     */
    private function parseArgv(array $params)
    {
        while (false !== ($param = current($params))) {
            next($params);
            if (strpos($param, '-') === 0) {
                $option = ltrim($param, '-');
                $value  = null;
                if (strpos($option, '=') !== false) {
                    [$option, $value] = explode('=', $option, 2);
                }
                if ($option) $this->opts[$option] = $value;
            } else if (strpos($param, '=') !== false) {
                [$name, $value] = explode('=', $param, 2);
                if ($name) $this->args[$name] = $value;
            } else {
                $this->args[] = $param;
            }
        }
    }

    public function addCommand(CommandInterface $handler)
    {
        $command = $handler->commandName();

        $this->commands[$command] = $handler;

        if (($len = strlen($command)) > $this->width) {
            $this->width = $len;
        }

    }

    public function displayAlternativesHelp($command): string
    {
        $text         = "The command '{$command}' is not exists!\n";
        $commandNames = array_keys($this->commands);
        $alternatives = [];
        foreach ($commandNames as $commandName) {
            $lev = levenshtein($command, $commandName);
            if ($lev <= strlen($command) / 3 || false !== strpos($commandName, $command)) {
                $alternatives[$commandName] = $lev;
            }
        }
        $threshold    = 1e3;
        $alternatives = array_filter($alternatives, function ($lev) use ($threshold) {
            return $lev < 2 * $threshold;
        });
        ksort($alternatives);

        if ($alternatives) {
            $text .= "Did you mean one of these?\n";
            foreach (array_keys($alternatives) as $alternative) {
                $text .= "$alternative\n";
            }
        } else {
            $text .= $this->displayHelp();
        }

        return Color::danger($text);
    }

    public function displayCommandHelp($command)
    {
        /** @var CommandInterface $handler */
        $handler = $this->commands[$command] ?? '';
        if (!$handler) {
            $result = Color::danger("The command '{$command}' is not exists!\n");
            $result .= $this->displayHelp();
            return $result;
        }

        $fullCmd = $this->script . " " . $handler->commandName();

        $desc  = $handler->desc() ? ucfirst($handler->desc()) : 'No description for the command';
        $desc  = "<brown>$desc</brown>";
        $usage = "<cyan>$fullCmd ACTION</cyan> [--opts ...]";

        $nodes = [
            $desc,
            "<brown>Usage:</brown>" . "\n  $usage\n",
        ];

        $helpMsg = implode("\n", $nodes);

        /**-----------------CommandHelp--------------------------------*/

        /** @var CommandHelp $commandHelp */
        $commandHelp = $handler->help(new CommandHelp());

        $helpMsg .= "<brown>Actions:</brown>\n";

        $actions     = $commandHelp->getActions();
        $actionWidth = $commandHelp->getActionWidth();

        if (empty($actions)) $helpMsg .= "\n";
        foreach ($actions as $name => $desc) {
            $name    = str_pad($name, $actionWidth, ' ');
            $helpMsg .= "  <green>$name</green>  $desc\n";
        }

        $helpMsg .= "<brown>Options:</brown>\n";

        $opts     = $commandHelp->getOpts();
        $optWidth = $commandHelp->getOptWidth();

        foreach ($opts as $name => $desc) {
            $name    = str_pad($name, $optWidth, ' ');
            $helpMsg .= "  <green>$name</green>  $desc\n";
        }

        /**-----------------CommandHelp--------------------------------*/

        return Color::render($helpMsg);
    }

    public function displayHelp()
    {
        // help
        $desc  = ucfirst($this->desc) . "\n";
        $usage = "<cyan>{$this->script} COMMAND -h</cyan>";

        $help = "<brown>{$desc}Usage:</brown>" . " $usage\n<brown>Commands:</brown>\n";
        $data = $this->commands;
        ksort($data);

        /**
         * @var string $command
         * @var CommandInterface $handler
         */
        foreach ($data as $command => $handler) {
            $command = str_pad($command, $this->width, ' ');
            $desc    = $handler->desc() ? ucfirst($handler->desc()) : 'No description for the command';
            $help    .= "  <green>$command</green>  $desc\n";
        }

        $help .= "\nFor command usage please run: $usage\n";

        return Color::render($help);
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    /**
     * @return array
     */
    public function getOpts(): array
    {
        return $this->opts;
    }

    /**
     * @param array $opts
     */
    public function setOpts(array $opts): void
    {
        $this->opts = $opts;
    }

    /**
     * @param string|int $name
     * @param mixed $default
     *
     * @return mixed|null
     */
    public function getArg($name, $default = null)
    {
        return $this->args[$name] ?? $default;
    }

    /**
     * @param string|int $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getOpt($name, $default = null)
    {
        return $this->opts[$name] ?? $default;
    }

    /**
     * @param string|int $name
     * @return bool
     */
    public function issetArg($name)
    {
        return isset($this->args[$name]);
    }


    /**
     * @param string|int $name
     * @return bool
     */
    public function issetOpt($name)
    {
        return array_key_exists($name, $this->opts);
    }

    /**
     * @return string
     */
    public function getDesc(): string
    {
        return $this->desc;
    }

    /**
     * @param string $desc
     */
    public function setDesc(string $desc): void
    {
        $this->desc = $desc;
    }
}
