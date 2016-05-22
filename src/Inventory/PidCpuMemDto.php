<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 30.09.15
 * Time: 23:14
 */
namespace CommandsExecutor\Inventory;

use CommandsExecutor\Interfaces\ExecutionDto;

class PidCpuMemDto implements ExecutionDto
{
    /**
     * @var float
     */
    protected $pidCpuUsage;

    /**
     * @var int
     */
    protected $pidResidentMemoryUsage;

    /**
     * @var int
     */
    protected $pid;

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param mixed $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * @return mixed
     */
    public function getPidCpuUsage()
    {
        return $this->pidCpuUsage;
    }

    /**
     * @param mixed $pidCpuUsage
     */
    public function setPidCpuUsage($pidCpuUsage)
    {
        $this->pidCpuUsage = $pidCpuUsage;
    }

    /**
     * @return mixed
     */
    public function getPidResidentMemoryUsage()
    {
        return $this->pidResidentMemoryUsage;
    }

    /**
     * @param mixed $pidResidentMemoryUsage
     */
    public function setPidResidentMemoryUsage($pidResidentMemoryUsage)
    {
        $this->pidResidentMemoryUsage = $pidResidentMemoryUsage;
    }


}