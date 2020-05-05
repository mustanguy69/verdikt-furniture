<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Checkout
 */


namespace Amasty\Checkout\Model\Optimization;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Command class.
 * Delete bundle file from all themes and languages in static directory.
 * @api
 * @since 3.0.0
 */
class DeleteCheckoutBundles
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    public function __construct(\Magento\Framework\Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Delete all bundle files created by amasty checkout.
     *
     * @return void
     */
    public function execute()
    {
        $staticDir = $this->filesystem->getDirectoryWrite(DirectoryList::STATIC_VIEW);
        if (!$staticDir->isDirectory('frontend')) {
            return;
        }
        foreach ($staticDir->read('frontend') as $vendorDir) {
            if (!$staticDir->isDirectory($vendorDir)) {
                continue;
            }
            foreach ($staticDir->read($vendorDir) as $themeDir) {
                if (!$staticDir->isDirectory($themeDir)) {
                    continue;
                }
                foreach ($staticDir->read($themeDir) as $languageDir) {
                    if (!$staticDir->isDirectory($languageDir)) {
                        continue;
                    }
                    $staticDir->delete(
                        $languageDir . '/' . \Amasty\Checkout\Model\Optimization\Bundle::BUNDLE_JS_DIR . '/'
                        . \Amasty\Checkout\Model\Optimization\Bundle::BUNDLE_SUB_DIR . '/'
                        . \Amasty\Checkout\Model\Optimization\Bundle::BUNDLE_JS_FILE
                    );
                }
            }
        }
    }
}
