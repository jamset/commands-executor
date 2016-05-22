<?php
/**
 * Created by PhpStorm.
 * User: ww
 * Date: 02.10.15
 * Time: 23:41
 */
namespace CommandsExecutor\Inventory;

class IncomingDto
{
    /**
     * @var int
     */
    protected $sig;

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


}