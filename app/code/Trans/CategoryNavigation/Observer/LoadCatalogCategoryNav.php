<?php
/**
 * @category Trans
 * @package  Trans_CategoryNavigation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT CT Corp Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CategoryNavigation\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Data\Tree\Node;

/**
 * Class LoadCatalogCategoryNav
 */
class LoadCatalogCategoryNav implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Data\Tree\NodeFactory
     */
    protected $nodeFactory;

    /**
     * @var \Magento\Catalog\Model\Category\TreeFactory
     */
    protected $treeFactory;

    /**
     * @var \Trans\CategoryNavigation\Helper\Catalog
     */
    protected $catalogHelper;

    /**
     * @param \Magento\Framework\Data\Tree\NodeFactory
     * @param \Magento\Catalog\Model\Category\TreeFactory
     * @param \Trans\CategoryNavigation\Helper\Catalog $catalogHelper
     */
    public function __construct(
        \Magento\Framework\Data\Tree\NodeFactory $nodeFactory,
        \Magento\Catalog\Model\Category\TreeFactory $treeFactory,
        \Trans\CategoryNavigation\Helper\Catalog $catalogHelper
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
        $this->catalogHelper = $catalogHelper;
    }

    /**
     * Generate top menu navigation
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $menu = $observer->getMenu();
        $defaultNav = $menu->getChildren();
        
        foreach ($defaultNav as $childNode) {
            try {
                if($childNode->getIsCategory()) {
                    $menuId = $childNode->getId();
                    $explMenuId = explode('-', $menuId);
                    $categoryId = (int) end($explMenuId);

                    $check = $this->catalogHelper->checkCategoryProduct($categoryId);

                    if(!$check) {
                        $menu->removeChild($childNode);
                        continue;
                    }

                    $secondLevel = $childNode->getChildren();

                    foreach($secondLevel as $scnd) {
                        try {
                            if($scnd->getIsCategory()) {
                                $menuId = $scnd->getId();
                                $explMenuId = explode('-', $menuId);
                                $categoryId = (int) end($explMenuId);
                                $check = $this->catalogHelper->checkCategoryProduct($categoryId);

                                if(!$check) {
                                    $secondLevel->delete($scnd);
                                }
                            }
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }
            

        return $this;
    }
}
