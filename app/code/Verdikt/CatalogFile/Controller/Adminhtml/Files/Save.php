<?php

namespace Verdikt\CatalogFile\Controller\Adminhtml\Files;

use Verdikt\CatalogFile\Controller\Adminhtml\Files;

class Save extends Files
{

   /**
     * @return void
     */
   public function execute()
   {
      $isPost = $this->getRequest()->getPost();

      if ($isPost) {
         $filesModel = $this->_filesFactory->create();
         
         if (array_key_exists('id', $this->getRequest()->getParam('files'))) {
            $filesModel->load($this->getRequest()->getParam('files')['id']);
         }

         $formData = $this->getRequest()->getParam('files');
         $filesModel->setData($formData);

         if (isset($_FILES['file']) && isset($_FILES['file']['name']) && strlen($_FILES['file']['name'])) {
            try {
               $base_media_path = 'verdikt/catalogfile/files';
               $uploader = $this->uploader->create(['fileId' => 'file']);
               //$uploader->setAllowedExtensions(['pdf']);
               //$fileAdapter = $this->adapterFactory->create();
               //$uploader->addValidateCallback('file', $fileAdapter, 'validateUploadFile');
               $uploader->setAllowRenameFiles(true);
               $uploader->setFilesDispersion(true);
               $mediaDirectory = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
               $result = $uploader->save($mediaDirectory->getAbsolutePath($base_media_path));
               $filesModel->setFile($base_media_path.$result['file']);
            } catch (\Exception $e) {
               if ($e->getCode() == 0) {
                  $this->messageManager->addError($e->getMessage());
               }
            }
         } else {
            if (isset($formData['file']) && isset($formData['file']['value'])) {
               if (isset($formData['file']['delete'])) {
                  $filesModel->setFile(null);
               } elseif (isset($formData['file']['value'])) {
                  $filesModel->setFile($formData['file']['value']);
               } else {
                  $filesModel->setFile(null);
               }
            }
         }
      
         try {
            $filesModel->save();
            $this->messageManager->addSuccess(__('The file has been saved.'));

            if ($this->getRequest()->getParam('back')) {
               $this->_redirect('*/*/edit', ['id' => $filesModel->getId(), '_current' => true]);
               return;
            }

            $this->_redirect('*/*/');

            return;
            
         } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
         }

         $this->_getSession()->setFormData($formData);
         $this->_redirect('*/*/edit', ['id' => $filesModel->getId()]);
      }
   }
}