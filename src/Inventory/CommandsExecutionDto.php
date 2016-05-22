<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 19.11.15
 * Time: 22:15
 */
namespace CommandsExecutor\Inventory;

class CommandsExecutionDto
{
    /**
     * @var string
     */
    protected $commandName;

    /**
     * @var int
     */
    protected $pid;

    /**
     * @var int
     */
    protected $sig;

    /**
     * @var string
     */
    protected $processName;

    /**
     * @return string
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * @param string $commandName
     */
    public function setCommandName($commandName)
    {
        $this->commandName = $commandName;
    }

    /**
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param int $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return int
     */
    public function getSig()
    {
        return $this->sig;
    }

    /**
     * @param int $sig
     */
    public function setSig($sig)
    {
        $this->sig = $sig;
    }

    /**
     * @return string
     */
    public function getProcessName()
    {
        return $this->processName;
    }

    /**
     * @param string $processName
     */
    public function setProcessName($processName)
    {
        $this->processName = $processName;
    }


}