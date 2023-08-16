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

namespace Trans\Reservation\Block\Product\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Block\Product\Context;

/**
 * Reservation
 */
class Reservation extends \Trans\Reservation\Block\Product\AbstractProduct
{
	/**
	 * @var \Magento\InventoryApi\Api\SourceRepositoryInterface
	 */
	protected $sourceRepository;

	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Model\ReservationItem
     */
    protected $reservationItem;

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $reservationItemRepository;

    /**
     * @var \Trans\CatalogMultisource\Helper\SourceItem
     */
    protected $sourceItemHelper;

    /**
     * @var logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
	 * @param \Magento\Catalog\Block\Product\Context $context
	 * @param \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Model\ReservationItem $reservationItem
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
	 */
    public function __construct(
        Context $context,
        \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Model\ReservationItem $reservationItem,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper,
        array $data = []
    ) {
    	$this->productRepository = $productRepository;
        $this->customerSession = $customerSession;
        $this->sourceRepository = $sourceRepository;
        $this->dataHelper = $dataHelper;
        $this->reservationItem = $reservationItem;
        $this->reservationRepository = $reservationRepository;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->sourceItemHelper = $sourceItemHelper;

        $this->logger = $dataHelper->getLogger();
        $this->formKey = $dataHelper->getFormKey();

        parent::__construct($context, $data);
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function getProduct()
    {
        $product = parent::getProduct();
        if(!$product) {
            $sku = $this->getData('sku');
            $optionSelected = $this->getData('optionSelected');
            if($optionSelected != null) {
                $product = $this->productRepository->getById($optionSelected);
            } else {
                $product = $this->productRepository->get($sku);
            }
        }

        return $product;
    }

    /**
     * Get super attribute product
     *
     * @return array
     */
    public function getProductSuperAttribute()
    {
        return $this->dataHelper->getSuperAttributeData($this->getProduct());
    }

    /**
     * Get source each product by SKU
     *
     * @return \Magento\InventoryApi\Api\Data\GetSourceItemsBySkuInterface
     */
    public function getProductSources()
    {
        $product = $this->getProduct();

    	$sku = $product->getSku();
        $reservationId = $this->dataHelper->getSessionReservationId();

        $sources[$product->getId()] = $this->dataHelper->getProductSources($sku);

        if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $children = $product->getTypeInstance()->getUsedProducts($product);

            foreach($children as $child) {
                $sources[$child->getId()] = $this->dataHelper->getProductSources($child->getSku());
            }
        }

    	return $sources;
    }

    /**
     * Get childs products
     *
     * @return json
     */
    public function getSourceKeys()
    {
        $childs = [];
        $product = $this->getProduct();

        if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $children = $product->getTypeInstance()->getUsedProducts($product);

            foreach($children as $child) {
                $data['id'] = $child->getId();
                $data['sku'] = $child->getSku();
                $childs[] = $data;
            }
        }

        $data['id'] = $product->getId();
        $data['sku'] = $product->getSku();
        $childs[] = $data;

        return $childs;
    }

    /**
     * get product source array
     *
     * @return array
     */
    public function getProductSourcesArray()
    {
        $result = [];
        $sourceKey = $this->getSourceKeys();
        $productSources = $this->getProductSources();

        foreach($sourceKey as $key) {
            if(!isset($productSources[$key['id']])) :
                continue;
            endif;
            foreach($productSources[$key['id']] as $data) :
                $source = $this->getSourceByCode($data->getSourceCode());
                if(!$source->isEnabled()) { continue; }

                $checkBuffer = $this->checkProductBuffer($data->getSourceCode(), $key['sku']);
                if($checkBuffer) {
                    $row['source'] = $source;
                    $row['product_id'] = $key['id'];
                    $row['sku'] = $key['sku'];
                    $row['source_address'] = $this->getSourceAddress($source);

                    $result[] = $row;
                }
            endforeach;
        }

        return $result;
    }

    /**
     * Get source data by source code
     *
     * @param string $sourceCode
     * @return \Magento\InventoryApi\Api\Data\SourceInterface
     */
    public function getSourceByCode($code)
    {
    	try {
    		return $this->sourceRepository->get($code);
    	} catch (NoSuchEntityException $exception) {
    		return false;
    	}
    }

    /**
     * get source address
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return string
     */
    public function getSourceAddress($source)
    {
        return $this->dataHelper->getSourceAddress($source);
    }

    /**
     * Check is customer logged in or not
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        if($this->customerSession->isLoggedIn()) {
           return true;
        }

        return false;
    }

    /**
     * is item already reserved
     *
     * @param int $productId
     * @return bool
     */
    public function isItemAlreadyReserved($productId)
    {
        $reservationId = $this->dataHelper->getSessionReservationId();
        if(!empty($reservationId)) {
            try {
                $item = $this->reservationItemRepository->get($reservationId, $productId);
                if($item->getProductId()) {
                    return true;
                }
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * check product buffer
     *
     * @param string $sku
     * @param string $sourceCode
     * @return bool
     */
    public function checkProductBuffer($sourceCode, $sku = null)
    {
        /* return false if product quantity reach buffer number. */
        try {
            $product = $this->getProduct();
            if(!empty($sku)) {
                $product = $this->productRepository->get($sku);
            }

            return $this->reservationItem->checkProductBuffer($sourceCode, $product);
        } catch (NoSuchEntityException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get add to cart url
     *
     * @return string
     */
    public function getAddToCartUrl()
    {
        return $this->getUrl('reservation/cart/addtoreservecart', ['form_key' => $this->formKey->getFormKey()]);
    }

    /**
     * Get add to cart url
     *
     * @return string
     */
    public function getProductHelperUrl()
    {
        return $this->getUrl('reservation/index/producthelper');
    }
}
