<?php

namespace Verdikt\CatalogFile\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Files extends Container
{
   /**
     * Constructor
     *
     * @return void
     */
   protected function _construct()
    {
        $this->_controller = 'adminhtml_files';
        $this->_blockGroup = 'Verdikt_CatalogFile';
        $this->_headerText = __('Manage Files');
        $this->_addButtonLabel = __('Add File');
        parent::_construct();
    }
}