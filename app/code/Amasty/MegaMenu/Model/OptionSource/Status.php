<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Model\OptionSource;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    const DISABLED = 0;

    const ENABLED = 1;

    const DESKTOP = 2;

    const MOBILE = 3;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ENABLED,
                'label' => __('Enable For Both Desktop and Mobile')
            ],
            [
                'value' => self::DESKTOP,
                'label' => __('Enable for Desktop Only')
            ],
            [
                'value' => self::MOBILE,
                'label' => __('Enable for Mobile Only')
            ],
            [
                'value' => self::DISABLED,
                'label' => __('Disable')
            ]
        ];
    }
}
