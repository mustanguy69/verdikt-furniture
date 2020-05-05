<?php
namespace Verdikt\ProductFilter\Block;

use Magento\Framework\Session\Generic as Session;


class Helper extends AbstractHelper
{
    /**
     * @var Session
     */  
    private $userSession;
 
    /**
     * @param Session $userSession
     */ 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Session $userSession
    ) {
        parent::__construct($context);
        $this->session = $userSession;
    }

    /**
     * @return string
     */
    public function getUserSessionId(){
      $userSessionId = $this->session->getSessionId();
      return $userSessionId;
    }
}