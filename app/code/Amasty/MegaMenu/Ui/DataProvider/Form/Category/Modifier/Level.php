<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Block\Html\Topmenu;
use Magento\Catalog\Model\Category;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\App\RequestInterface;

class Level implements ModifierInterface
{
    /**
     * @var Category
     */
    private $entity;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        RequestInterface $request
    ) {
        $this->storeId = (int)$request->getParam('store', 0);
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->entity && $this->entity->getLevel() == 2) {
            $itemsToHide = ['category_level_error'];
        } else {
            $itemsToHide = ['width', 'width_value', 'column_count', 'label', 'label_group', 'content', 'label_group'];
        }

        foreach ($itemsToHide as $item) {
            $meta['am_mega_menu_fieldset']['children'][$item]['arguments']['data']['config']['visible'] = false;
        }

        if ($this->moduleManager->isEnabled('Magento_PageBuilder')) {
            if ($this->moduleManager->isEnabled('Amasty_MegaMenuPageBuilder')) {
                $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default']
                    = Topmenu::CHILD_CATEGORIES_PAGE_BUILDER;
                $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice']
                    = __('You can use the menu item Add Content for showing child categories.');
            } else {
                $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default']
                    = Topmenu::CHILD_CATEGORIES;
            }
        } else {
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['default']
                = Topmenu::CHILD_CATEGORIES;
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['component']
                = 'Amasty_MegaMenu/js/form/element/wysiwyg';
            $meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config']['notice']
                = __('You can use the variable: {{child_categories_content}} for showing child categories.');
        }

        return $meta;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->entity = $category;
        return $this;
    }
}
