<?php

namespace Verdikt\CatalogFile\Model;

use Magento\Framework\Model\AbstractModel;

class Files extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Verdikt\CatalogFile\Model\ResourceModel\Files');
    }
}