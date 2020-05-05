<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Acart
 */


namespace Amasty\Acart\Block\Adminhtml\System\Config;

use Amasty\Base\Helper\Module;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template;

class Smtp extends Field
{
    /**
     * @var Module
     */
    private $moduleHelper;

    public function __construct(
        Template\Context $context,
        Module $moduleHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if ($this->getModuleManager() && $this->getModuleManager()->isEnabled('Amasty_Smtp')) {
            $element->setValue(__('Installed'));
            $element->setHtmlId('amasty_is_instaled');
            $url = $this->getUrl('adminhtml/system_config/edit/section/amsmtp');
            $element->setComment(__("Specify SMTP settings properly. See more details "
                . "<a target='_blank' href='%1'>here</a>.", $url));
        } else {
            $url = "https://amasty.com/magento-smtp-email-settings.html"
            ."?utm_source=extension&utm_medium=link&utm_campaign=acart-smtp-m2";
            if ($this->moduleHelper->isOriginMarketplace()) {
                $url = "https://marketplace.magento.com/amasty-smtp.html";
            }
            $element->setValue(__('Not Installed'));
            $element->setHtmlId('amasty_not_instaled');
            $element->setComment(__("For more options to customize your smtp settings, consider using our "
                . "<a target='_blank' href='%1'>SMTP extension</a>.", $url));
        }

        return parent::render($element);
    }
}
