<?php

namespace Verdikt\CatalogFile\Controller\Adminhtml\Files;

use Verdikt\CatalogFile\Controller\Adminhtml\Files;

class Edit extends Files
{
   /**
     * @return void
     */
   public function execute()
   {
        $filesId = $this->getRequest()->getParam('id');
        $model = $this->_filesFactory->create();

        if ($filesId) {
            $model->load($filesId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This file no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_session->getFilesData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('catalogfile_files', $model);

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Verdikt_CatalogFile::files');
        $resultPage->getConfig()->getTitle()->prepend(__('Catalog File'));

        return $resultPage;
   }
}