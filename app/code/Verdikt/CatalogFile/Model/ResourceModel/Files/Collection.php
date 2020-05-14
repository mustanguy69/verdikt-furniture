<?php

namespace Verdikt\CatalogFile\Model\ResourceModel\Files;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Verdikt\CatalogFile\Model\Files',
            'Verdikt\CatalogFile\Model\ResourceModel\Files'
        );
    }
}