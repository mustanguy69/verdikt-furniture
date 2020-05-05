<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */


namespace Amasty\Acart\Model\ResourceModel\Schedule;

/**
 * @method \Amasty\Acart\Model\Schedule[] getItems()
 * @method \Amasty\Acart\Model\Schedule getFirstItem()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Acart\Model\Schedule', 'Amasty\Acart\Model\ResourceModel\Schedule');
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
