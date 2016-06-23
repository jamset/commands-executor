<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 30.09.15
 * Time: 23:38
 */
namespace CommandsExecutor;

use CommandsExecutor\Inventory\CommandsConstants;
use CommandsExecutor\Inventory\CommandsExecutionDto;
use CommandsExecutor\Inventory\Exceptions\CommandsExecutionException;
use CommandsExecutor\Inventory\Exceptions\CommandsExecutionInvalidArgument;
use CommandsExecutor\Inventory\CpuMemDto;
use CommandsExecutor\Inventory\PidCpuMemDto;
use CommandsExecutor\Inventory\PidDto;
use React\ChildProcess\Process;

class LinuxCommands
{

    /**
     * @param $pid
     * @return null|string
     */
    public static function trySendSigTerm($pid)
    {
        $resultInfo = null;

        $sendSigTermResult = self::sendSig($pid, SIGTERM);

        if (!$sendSigTermResult) {

            $resultInfo = "SigTerm for PID " . $pid . " wasn't success.";

            $sendSigKillResult = self::sendSig($pid, SIGKILL);

            if (!$sendSigKillResult) {
                $resultInfo .= " Child process with PID $pid can't be sigKilled.";
            } else {
                $resultInfo .= " SendSigKillResult = " . serialize($sendSigKillResult);
            }
        }

        return $resultInfo;
    }

    /**
     * @param Process $process
     * @return null
     * @throws CommandsExecutionException
     */
    public static function tryTerminateProcess(Process $process)
    {
        try {

            $terminationResult = $process->terminate();

            if (!$terminationResult) {
                throw new CommandsExecutionException("Process termination for PID " . $process->getPid() . " wasn't success");
            }

        } catch (\Exception $e) {

            try {

                self::sendSig($process->getPid(), SIGKILL);

            } catch (\Exception $e) {

                throw new CommandsExecutionException("SIGKILL for PID " . $process->getPid() . " wasn't success");

            }
        }

        return null;
    }


    /**
     * @param $commandName
     * @param null $pid
     * @return CpuMemDto|PidCpuMemDto|PidDto|null
     * @throws CommandsExecutionException
     */
    //public static function getCommandResult($commandName, $pid = null, $sig = null, $processName = null)
    public static function getCommandResult(CommandsExecutionDto $commandsDto)
    {
        $result = null;

        switch ($commandsDto->getCommandName()):
            case(CommandsConstants::GET_PID_LOAD_INFO):
                $result = self::getPidLoadInfo($commandsDto->getPid());
                break;
            case(CommandsConstants::GET_MEM_FREE):
                $result = self::getMemFree();
                break;
            case(CommandsConstants::GET_CPU_IDLE):
                $result = self::getCpuIdle();
                break;
            case(CommandsConstants::GET_PID_BY_PPID):
                $result = self::getPidByPpid($commandsDto->getPid());
                break;
            case(CommandsConstants::SEND_SIG):
                $result = self::sendSig($commandsDto->getPid(), $commandsDto->getSig());
                break;
            case(CommandsConstants::GET_CORE_NUMBER):
                $result = self::getCoreNumber();
                break;
            case(CommandsConstants::IS_PROCESS_NAME_RUNNING):
                $result = self::isProcessNameRunning($commandsDto->getProcessName());
                break;
            default:
                throw new CommandsExecutionInvalidArgument("Unknown command name.");
        endswitch;

        return $result;
    }

    protected static function isProcessNameRunning($processName)
    {
        $cmdResult = `ps aux | grep '$processName' | awk '{print $2}'`;

        preg_match_all("/[\d]+/", $cmdResult, $matches);
        $result = array_pop($matches);

        array_walk($result, function (&$item) {
            $item = (int)$item;
        });

        if (($key = array_search(posix_getpid(), $result)) !== false) {
            unset($result[$key]);
        }

        return (count($result) >= 3) ? true : false;
    }

    protected static function getCoreNumber()
    {
        $coreNumber = null;

        $cmdResult = `cat /proc/cpuinfo | grep processor | wc -l`;
        $coreNumber = (int)$cmdResult;

        return $coreNumber;
    }

    protected static function getPidLoadInfo($pid)
    {
        $pidCpuMemDto = new PidCpuMemDto();

        if (!is_int($pid)) {
            throw new CommandsExecutionInvalidArgument("PID for " . CommandsConstants::GET_PID_LOAD_INFO . " command "
                . "isn't an integer: " . serialize($pid));
        }

        $cmdResult = `top -bn 1 -p $pid | grep 'RES' -A 1 | awk '{printf("%-8s  %-8s", $6,$9)}'`;
        preg_match_all("/[\d]+/", $cmdResult, $matches);
        $result = array_pop($matches);

        if (isset($result[0]) === false) {
            throw new CommandsExecutionException("There is no process with PID " . serialize($pid) . " | cmdResult: "
                . serialize($cmdResult) . " | result: " . serialize($result));
        }

        $pidCpuMemDto->setPid((int)$pid);

        $memUsage = $result[0];
        $lastSymbol = $memUsage{strlen($memUsage) - 1};

        if (!(is_numeric($lastSymbol))) {
            if ($lastSymbol === 'g') {
                $memUsage = ((int)(str_replace(",", '', $cmdResult))) * 1024;
            } else {
                throw new CommandsExecutionException("Unknown letter in top during checking memory: " . serialize($lastSymbol));
            }
        } else {
            $memUsage = (int)$memUsage;
        }

        $pidCpuMemDto->setPidResidentMemoryUsage($memUsage);
        $pidCpuMemDto->setPidCpuUsage((float)($result[1] . "." . $result[2]));

        return $pidCpuMemDto;
    }

    protected static function getMemFree()
    {
        $cpuMemDto = new CpuMemDto();
        $cpuMemDto->setMemFree((int)(`grep 'MemFree' /proc/meminfo | awk '{printf("%-8s",$2)}'`));

        return $cpuMemDto;
    }

    protected static function getCpuIdle()
    {
        $cpuMemDto = new CpuMemDto();

        $cmdResult = `top -bn2 | grep 'id,' | awk '{ printf("%-8s", $8); }'`;

        $splitResult = preg_split('/\s+/', $cmdResult);

        $idleArr = [];

        foreach ($splitResult as $item) {
            $item = (str_replace(",", ".", trim($item)));
            if (is_numeric($item)) {
                $idleArr[] = (float)$item;
            }
        }

        $res = array_pop($idleArr);

        $cpuMemDto->setCpuIdle($res);

        return $cpuMemDto;
    }

    protected static function getPidByPpid($pid)
    {
        $pidDto = new PidDto();
        $cmdResult = `ps -o pid --no-heading --ppid $pid`;

        if (!$cmdResult) {
            throw new CommandsExecutionException("PID with such PPID ($pid) doesn't exist.");
        }

        $splitResult = preg_split('/\s+/', $cmdResult);

        foreach ($splitResult as $item) {
            if ((int)$item > 0) {
                $childPid = (int)$item;
                break;
            }
        }

        $pidDto->setPid($childPid);
        $pidDto->setPpid((int)$pid);

        return $pidDto;
    }

    protected static function sendSig($pid, $sig)
    {
        $killRes = posix_kill($pid, $sig);
        return $killRes;
    }

}
