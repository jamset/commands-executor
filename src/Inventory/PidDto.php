<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 01.10.15
 * Time: 22:32
 */
namespace CommandsExecutor\Inventory;

use CommandsExecutor\Interfaces\ExecutionDto;

class PidDto implements ExecutionDto
{
    /**
     * @var int
     */
    protected $pid;

    /**
     * @var int
     */
    protected $ppid;

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
    public function getPpid()
    {
        return $this->ppid;
    }

    /**
     * @param int $ppid
     */
    public function setPpid($ppid)
    {
        $this->ppid = $ppid;
    }


}