<?php

namespace UserAuthenticator\Db\Adapter;

interface MasterSlaveAdapterInterface
{
    /**
     * @return \Laminas\Db\Adapter\Adapter
     */
    public function getSlaveAdapter();
}
