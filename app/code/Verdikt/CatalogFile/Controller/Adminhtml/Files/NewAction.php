<?php

namespace Verdikt\CatalogFile\Controller\Adminhtml\Files;

use Verdikt\CatalogFile\Controller\Adminhtml\Files;

class NewAction extends Files
{
   /**
     *
     * @return void
     */
   public function execute()
   {
      $this->_forward('edit');
   }
}