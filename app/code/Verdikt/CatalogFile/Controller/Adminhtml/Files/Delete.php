<?php

namespace Verdikt\CatalogFile\Controller\Adminhtml\Files;

use Verdikt\CatalogFile\Controller\Adminhtml\Files;

class Delete extends Files
{
   /**
    * @return void
    */
   public function execute()
   {
      $filesId = (int) $this->getRequest()->getParam('id');

      if ($filesId) {
         $filesModel = $this->_filesFactory->create();
         $filesModel->load($filesId);

         if (!$filesModel->getId()) {
            $this->messageManager->addError(__('This file no longer exists.'));
         } else {
               try {
                  $filesModel->delete();
                  $this->messageManager->addSuccess(__('The file has been deleted.'));

                  $this->_redirect('*/*/');
                  return;
               } catch (\Exception $e) {
                   $this->messageManager->addError($e->getMessage());
                   $this->_redirect('*/*/edit', ['id' => $filesModel->getId()]);
               }
            }
      }
   }
}