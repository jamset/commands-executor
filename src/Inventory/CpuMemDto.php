<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 30.09.15
 * Time: 23:13
 */
namespace CommandsExecutor\Inventory;

use CommandsExecutor\Interfaces\ExecutionDto;

class CpuMemDto implements ExecutionDto
{
    /**
     * @var float
     */
    protected $cpuIdle;

    /**
     * @var int
     */
    protected $memFree;

    /**
     * @return float
     */
    public function getCpuIdle()
    {
        return $this->cpuIdle;
    }

    /**
     * @param float $cpuIdle
     */
    public function setCpuIdle($cpuIdle)
    {
        $this->cpuIdle = $cpuIdle;
    }

    /**
     * @return int
     */
    public function getMemFree()
    {
        return $this->memFree;
    }

    /**
     * @param int $memFree
     */
    public function setMemFree($memFree)
    {
        $this->memFree = $memFree;
    }

}