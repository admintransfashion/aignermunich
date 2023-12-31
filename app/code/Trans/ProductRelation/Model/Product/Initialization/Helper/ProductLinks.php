<?php
/**
 * @category Trans
 * @package  Trans_ProductRelation
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\ProductRelation\Model\Product\Initialization\Helper;

use Trans\ProductRelation\Model\Catalog\Product\Link;
use Magento\Catalog\Api\Data\ProductLinkExtensionFactory;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Class ProductLinks
 */
class ProductLinks
{
    /**
     * String name for link type
     */
    const TYPE_NAME = 'combinations';
    /**
     * @var ProductLinkInterfaceFactory
     */
    protected $productLinkFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ProductLinkExtensionFactory
     */
    protected $productLinkExtensionFactory;

    /**
     * Init
     *
     * @param ProductLinkInterfaceFactory $productLinkFactory
     * @param ProductRepositoryInterface $productRepository
     * @param ProductLinkExtensionFactory $productLinkExtensionFactory
     */
    public function __construct(
        ProductLinkInterfaceFactory $productLinkFactory,
        ProductRepositoryInterface $productRepository,
        ProductLinkExtensionFactory $productLinkExtensionFactory
    ) {
        $this->productLinkFactory = $productLinkFactory;
        $this->productRepository = $productRepository;
        $this->productLinkExtensionFactory = $productLinkExtensionFactory;
    }

    public function beforeInitializeLinks(
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $subject,
        \Magento\Catalog\Model\Product $product,
        array $links
    )
    {
        if(isset($links[self::TYPE_NAME]) && !$product->getCombinationsReadonly()) {

            $links = (isset($links[self::TYPE_NAME])) ? $links[self::TYPE_NAME] : $product->getCombinationsLinkData();
            if (!is_array($links)) {
                $links = [];
            }

            if ($product->getCombinationsLinkData()) {
                $links = array_merge($links, $product->getCombinationsLinkData());
            }
            $newLinks = [];
            $existingLinks = $product->getProductLinks();
            foreach ($links as $linkRaw) {
                /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
                $productLink = $this->productLinkFactory->create();
                if (!isset($linkRaw['id'])) {
                    continue;
                }
                $productId = $linkRaw['id'];
                if (!isset($linkRaw['qty'])) {
                    $linkRaw['qty'] = 0;
                }
                $linkedProduct = $this->productRepository->getById($productId);

                $productLink->setSku($product->getSku())
                    ->setLinkType(self::TYPE_NAME)
                    ->setLinkedProductSku($linkedProduct->getSku())
                    ->setLinkedProductType($linkedProduct->getTypeId())
                    ->setPosition($linkRaw['position'])
                    ->getExtensionAttributes()
                    ->setQty($linkRaw['qty']);

                $newLinks[] = $productLink;
            }

            $existingLinks = $this->removeUnExistingLinks($existingLinks, $newLinks);
            $product->setProductLinks(array_merge($existingLinks, $newLinks));
        }
    }

    /**
     * Removes unexisting links
     *
     * @param array $existingLinks
     * @param array $newLinks
     * @return array
     */
    private function removeUnExistingLinks($existingLinks, $newLinks)
    {
        $result = [];
        foreach ($existingLinks as $key => $link) {
            $result[$key] = $link;
            if ($link->getLinkType() == self::TYPE_NAME) {
                $exists = false;
                foreach ($newLinks as $newLink) {
                    if ($link->getLinkedProductSku() == $newLink->getLinkedProductSku()) {
                        $exists = true;
                    }
                }
                if (!$exists) {
                    unset($result[$key]);
                }
            }
        }
        return $result;
    }

}