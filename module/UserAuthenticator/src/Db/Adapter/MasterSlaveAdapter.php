<?php

namespace ZfcBase\Db\Adapter;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\ResultSet\ResultSetInterface;
use UserAuthenticator\Db\Adapter\MasterSlaveAdapterInterface;

class MasterSlaveAdapter extends Adapter implements MasterSlaveAdapterInterface
{
    /**
     * slave adapter
     *
     * @var Adapter
     */
    protected $slaveAdapter;
    /**
     * @param Adapter $slaveAdapter
     * @param \Laminas\Db\Adapter\Driver\DriverInterface|array $driver
     * @param PlatformInterface $platform
     * @param ResultSetInterface $queryResultPrototype
     */
    public function __construct(
        Adapter $slaveAdapter,
        $driver,
        PlatformInterface $platform = null,
        ResultSetInterface $queryResultPrototype = null
    ) {
        $this->slaveAdapter = $slaveAdapter;
        parent::__construct($driver, $platform, $queryResultPrototype);
    }
    /**
     * get slave adapter
     *
     * @return Adapter
     */
    public function getSlaveAdapter()
    {
        return $this->slaveAdapter;
    }
}
