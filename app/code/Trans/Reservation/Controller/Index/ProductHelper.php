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

namespace Trans\Reservation\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ProductHelper
 */
class ProductHelper extends Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurableModel;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configurableModel = $configurableModel;
        $this->productRepository = $productRepository;

        parent::__construct($context);
    }


    /**
     * Get child product id by product attributes
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $resultPage = $this->resultPageFactory->create();
        $param = $this->getRequest()->getParams();

        $attributeInfo = $this->getRequest()->getParam('attribute');
        $parentId = $this->getRequest()->getParam('parentId');

        $attributeId = $attributeInfo['attributeId'];
        $optionSelected = $attributeInfo['optionSelected'];

        if($parentId) {
            try {
                $parent = $this->productRepository->getById($parentId);

                $product = $this->configurableModel->getProductByAttributes([$attributeId => $optionSelected], $parent);
                $result->setData(['product_id' => $product->getId()]);
            } catch (NoSuchEntityException $e) {

            }
        }

        return $result;
    }

}
