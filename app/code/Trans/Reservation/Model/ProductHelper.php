<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Reservation
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Customer\Model;

use Trans\Reservation\Api\ProductHelperInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @api
 */
class ProductHelper implements ProductHelperInterface
{
	/**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurableModel;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->request = $request;
        $this->json = $json;
        $this->configurableModel = $configurableModel;
        $this->productRepository = $productRepository;
    }

    /**
     * {inheritdoc}
     */
    public function getChildProductIdByAttribute(array $attributeInfo, int $parentId)
    {
        try {
            $product = $this->productRepository->getById($parentId);
            $product = $this->configurableModel->getProductByAttributes($attributeInfo, $product);

            return $product->getId();
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
