<?php

namespace Verdikt\CatalogFile\Controller\Adminhtml\Files;

use Verdikt\CatalogFile\Controller\Adminhtml\Files;

class Index extends Files
{
    /**
     * @return void
     */
   public function execute()
   {
      if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Verdikt_CatalogFile::files');
        $resultPage->getConfig()->getTitle()->prepend(__('Catalog Files'));

        return $resultPage;
   }
}