<?php

namespace Verdikt\CatalogFile\Block\Adminhtml\Files\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('files_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('File Content'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'file_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Verdikt\CatalogFile\Block\Adminhtml\Files\Edit\Tab\Content'
                )->toHtml(),
                'active' => true
            ]
        );

        return parent::_beforeToHtml();
    }
}