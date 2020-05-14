<?php

namespace Verdikt\CatalogFile\Controller\Adminhtml\Files;

use Verdikt\CatalogFile\Controller\Adminhtml\Files;

class MassDelete extends Files
{
   /**
    * @return void
    */
   public function execute()
   {
      $filesIds = $this->getRequest()->getParam('files');

        foreach ($filesIds as $filesId) {
            try {
                $filesModel = $this->_filesFactory->create();
                $filesModel->load($filesId)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        if (count($filesIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($filesIds))
            );
        }

        $this->_redirect('*/*/index');
   }
}