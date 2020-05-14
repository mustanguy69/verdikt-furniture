<?php

namespace Verdikt\CatalogFile\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Files extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('verdikt_catalog_files', 'id');
    }
}