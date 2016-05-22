<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 30.09.15
 * Time: 23:12
 */
namespace CommandsExecutor;

use CommandsExecutor\Inventory\CommandsConstants;
use CommandsExecutor\Inventory\CommandsExecutionDto;
use CommandsExecutor\Inventory\Exceptions\CommandsExecutionException;
use CommandsExecutor\Inventory\CpuMemDto;
use CommandsExecutor\Inventory\PidCpuMemDto;

class CommandsManager
{
    protected $osType;

    //TODO: FUT: automatic OS type identification
    public function __construct($osType = NULL)
    {
        $this->osType = ($osType) ?: CommandsConstants::LINUX;
    }

    public function isProcessNameRunning($processName)
    {
        $commandsDto = new CommandsExecutionDto();

        $commandsDto->setCommandName(CommandsConstants::IS_PROCESS_NAME_RUNNING);
        $commandsDto->setProcessName($processName);

        return $this->getCommandExecutionResult($commandsDto);
    }

    /**Return PID's cpu usage and resident (physical) memory usage
     * @param int $pid
     * @return PidCpuMemDto
     */
    public function getPidLoadInfo($pid)
    {
        $commandsDto = new CommandsExecutionDto();
        $commandsDto->setCommandName(CommandsConstants::GET_PID_LOAD_INFO);
        $commandsDto->setPid($pid);

        return $this->getCommandExecutionResult($commandsDto);
    }

    /**Get current cpu idle and memory free
     * @return CpuMemDto
     */
    public function getSystemLoadInfo()
    {
        $cpuMemDto = new CpuMemDto();

        $cpuMemDto = $this->getMemFree($cpuMemDto);
        $cpuMemDto = $this->getCpuIdle($cpuMemDto);

        return $cpuMemDto;
    }

    public function getMemFree(CpuMemDto $cpuMemDto)
    {
        $commandsDto = new CommandsExecutionDto();
        $commandsDto->setCommandName(CommandsConstants::GET_MEM_FREE);

        /**
         * @var CpuMemDto $memoryInfo
         */
        $memoryInfo = $this->getCommandExecutionResult($commandsDto);
        $cpuMemDto->setMemFree($memoryInfo->getMemFree());

        return $cpuMemDto;
    }

    public function getCpuIdle(CpuMemDto $cpuMemDto)
    {
        $commandsDto = new CommandsExecutionDto();
        $commandsDto->setCommandName(CommandsConstants::GET_CPU_IDLE);

        /**
         * @var CpuMemDto $cpuInfo
         **/
        $cpuInfo = $this->getCommandExecutionResult($commandsDto);
        $cpuMemDto->setCpuIdle($cpuInfo->getCpuIdle());

        return $cpuMemDto;
    }

    /**Return PID of the child's process by parent PID
     * @param $pid
     */
    public function getPidByPpid($ppid)
    {
        $commandsDto = new CommandsExecutionDto();
        $commandsDto->setCommandName(CommandsConstants::GET_PID_BY_PPID);
        $commandsDto->setPid($ppid);

        return $this->getCommandExecutionResult($commandsDto);
    }

    public function sendSig($pid, $sig)
    {
        $commandsDto = new CommandsExecutionDto();
        $commandsDto->setCommandName(CommandsConstants::SEND_SIG);
        $commandsDto->setPid($pid);
        $commandsDto->setSig($sig);

        return $this->getCommandExecutionResult($commandsDto);
    }

    public function getCoreNumber()
    {
        $commandsDto = new CommandsExecutionDto();
        $commandsDto->setCommandName(CommandsConstants::GET_CORE_NUMBER);

        return $this->getCommandExecutionResult($commandsDto);
    }

    //protected function getCommandExecutionResult($commandName, $pid = null, $sig = null)
    protected function getCommandExecutionResult(CommandsExecutionDto $commandsDto)
    {
        $commandResult = null;

        switch ($this->osType):
            case(CommandsConstants::LINUX):
                $commandResult = LinuxCommands::getCommandResult($commandsDto);
                break;
            default:
                throw new CommandsExecutionException('Unknown OS type.');
        endswitch;

        return $commandResult;
    }


}