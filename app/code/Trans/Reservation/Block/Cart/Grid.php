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

namespace Trans\Reservation\Block\Cart;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Grid
 */
class Grid extends \Magento\Framework\View\Element\Template
{
	/**
	 * @var \Trans\Reservation\Model\ReservationItem
	 */
	protected $reservationItem;

	/**
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $productRepository;

	/**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Api\Data\ProductInterface
     */
    protected $productInterface;

    /**
	 * @var \Magento\Catalog\Helper\Image
	 */
	protected $imageHelper;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
	 * @var \Trans\Reservation\Helper\Data
	 */
	protected $dataHelper;

	/**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var \Trans\Reservation\Api\Data\SourceAttributeInterface
     */
    protected $sourceAttribute;

    /**
	 * @var \Trans\Reservation\Api\ReservationRepositoryInterface
	 */
	protected $reservationRepository;

	/**
     * @var \Trans\Reservation\Api\Data\ReservationInterfaceFactory
     */
    protected $reservationFactory;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @var logger
     */
    protected $logger;

     /**
     * @var \Magento\Customer\Model\Session
     * @since 100.1.0
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Grid constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Api\Data\ProductInterface $productInterface
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param \Trans\Reservation\Model\ReservationItem $reservationItem
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\Data\ReservationInterfaceFactory $reservationFactory
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Config $configHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Api\Data\ProductInterfaceFactory $productInterface,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Trans\Reservation\Model\ReservationItem $reservationItem,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\Data\ReservationInterfaceFactory $reservationFactory,
        \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Config $configHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->productInterface = $productInterface;
    	$this->reservationItem = $reservationItem;
        $this->reservationRepository = $reservationRepository;
        $this->reservationFactory = $reservationFactory;
        $this->sourceAttribute = $sourceAttribute;
        $this->configurable = $configurable;
        $this->timezoneInterface = $context->getLocaleDate();
    	$this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
    	$this->reservationHelper = $reservationHelper;
        $this->customerSession = $customerSession;
        $this->categoryCollectionFactory = $categoryCollectionFactory;

    	$this->storeManager = $dataHelper->getStoreManager();
        $this->datetime = $dataHelper->getDatetime();
        $this->logger = $dataHelper->getLogger();

        parent::__construct(
            $context,
            $data
        );
    }

    public function getReservation()
    {
        $reserveId = $this->dataHelper->getSessionReservationId();

        if(!$reserveId) {
            $reserveId = $this->request->getParam('reservation_id');
        }

        try {
            return $this->reservationRepository->getById($reserveId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * get filtered product sources with buffer
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array
     */
    public function getFilteredProductSources(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $result = [];
        $reservationBlock = $this->getLayout()->createBlock('\Trans\Reservation\Block\Product\View\Reservation');
        $sources = $this->dataHelper->getProductSources($product->getSku());
        foreach($sources as $source) {
            $sourceCode = $source->getSourceCode();
            $dataSource = $reservationBlock->getSourceByCode($sourceCode);
            if($sourceCode == 'default' || !$dataSource->isEnabled()) {
                continue;
            }

            if(!$this->checkProductBuffer($sourceCode, $product)) {
                continue;
            }

            $data['name'] = $dataSource->getName();
            $data['code'] = $source->getSourceCode();
            $data['hours'] = $this->reservationHelper->generateHoursOption($sourceCode, $product);
            $data['days'] = $this->reservationHelper->generateDateConfig($sourceCode, $product);
            $result[$source->getSourceCode()] = $data;
        }

        return $result;
    }

    /**
     * get reservation subtotal with current curency
     *
     * @param int $price
     * @param int $qty
     * @return string
     */
    public function getFormattedSubtotal(int $price = null, int $qty = null, $items = null, $store = null)
    {
        $subtotal = 0;
        if($price != null && $qty != null) {
            $subtotal = $price * $qty;
        }

        if($subtotal == 0) {
            if($items == null) {
                $items = $this->getItems();
            }

            foreach($items as $item) {

                if($store != null) {
                    if($item->getSourceCode() != $store) {
                        continue;
                    }
                }
                try {
                    $product = $this->productRepository->getById($item->getProductId());
                } catch (NoSuchEntityException $e) {
                    continue;
                }
                $price = $product->getFinalPrice();
                $qty = $item->getQty();
                $subtotalItem = $price*$qty;
                $subtotal = $subtotal + $subtotalItem;
            }
        }

        $result = $this->priceHelper->currency($subtotal, true, false);

        return $result;
    }

    /**
     * get reservation subtotal
     *
     * @param int $price
     * @param int $qty
     * @return string
     */
    public function getSubtotal(int $price = null, int $qty = null, $items = null)
    {
        $subtotal = 0;
        if($price != null && $qty != null) {
            $subtotal = $price * $qty;
        }

        if($subtotal == 0) {
            if($items == null) {
                $items = $this->getItems();
            }

            foreach($items as $item) {
                try {
                    $product = $this->productRepository->getById($item->getProductId());
                } catch (NoSuchEntityException $e) {
                    continue;
                }
                $price = $product->getFinalPrice();
                $qty = $item->getQty();
                $subtotalItem = $price*$qty;
                $subtotal = $subtotal + $subtotalItem;
            }
        }

        $result = $subtotal;

        return $result;
    }

    /**
     * is stock reach buffer by source code/store
     *
     * @param string $sourceCode
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool
     */
    public function checkProductBuffer(string $sourceCode, \Magento\Catalog\Api\Data\ProductInterface $product)
    {
        /* return false if product quantity reach buffer number. */
        try {
            return $this->reservationItem->checkProductBuffer($sourceCode, $product);
        } catch (NoSuchEntityException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retrive reservation items
     *
     * @return \Trans\Reservation\Model\ResourceModel\ReservationItem\Collection
     */
    public function getItems()
    {
        $reservation = $this->getReservation();
        if($reservation) {
            $items = $reservation->getItems();
            return $items;
        }

        return [];
    }

    /**
     * get product expired date
     *
     * @param collection $items
     * @param string $sourceCode
     * @return datetime
     */
    public function getExpiredDateFormated($items = null, string $sourceCode = null, $format = null)
    {
        if($format == null) {
            $format = 'Y F d H:i';
        }

        if($items && $sourceCode) {
            $expired = $this->reservationHelper->generateReservationEndDate($sourceCode, $items);
            return $this->formatedDate($expired['datetime'], $format);
        }
        if($this->configHelper->isExpireNextDay()) {
            $date = $this->timezoneInterface->date();
            $date = $date->format($format);
            $date = date($format, strtotime($date. ' + 1 days'));
            $time = date("g:i A", strtotime($this->configHelper->getExpireTime()));
            return $date . ' ' . $time;
        }
    }

    /**
     * formating datetime
     *
     * @param string $datetime
     * @param string|null $format
     * @param datetime
     */
    public function formatedDate(string $datetime, $format = null)
    {
        if($format == null) {
            $format = 'Y F d H:i';
        }

        return $this->datetime->date($format, $datetime);
    }

    /**
     * Get customer data
     *
     * @return Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomerData()
    {
        $block = $this->getLayout()->createBlock('\Trans\Reservation\Block\Cart\Customer');
        return $block->getCustomerData();
    }

    /**
     * check is reservation items product exists
     *
     * @param Trans\Reservation\Model\ResourceModel\ReservationItem\Collection $items
     * @return bool
     */
    public function isItemsProductExists($items = null)
    {
        if($items) {
            return $this->reservationHelper->isReservationItemsExists($items);
        }

        return false;
    }

    /**
     * get product by product id
     *
     * @param int $productId
     * @return Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProductById($productId)
    {
        try {
            return $this->productRepository->getById($productId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getParentProductId($productId)
    {
        $parentConfigObject = $this->configurable->getParentIdsByChild($productId);
        if($parentConfigObject) {
            return $parentConfigObject[0];
        }
        return false;
    }

    /**
     * get product url
     *
     * @param Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    public function getProductUrl($product)
    {
        $productParentId = $this->getParentProductId($product->getId());
        if($productParentId) {
            $product = $this->getProductById($productParentId);
        }

        return $product->getProductUrl();
    }

    /**
     * get delete reserve item url
     *
     * @param int $itemId
     * @return string
     */
    public function getDeleteItem($itemId)
    {
        return $this->getUrl('reservation/cart/delete', ['id' => $itemId]);
    }

    /**
     * Get product image
     *
     * @param Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    public function getImageUrl($product)
    {
        /** \Magento\Catalog\Helper\Image */
    	return $this->imageHelper->init($product, 'product_base_image')->getUrl();
    }

    /**
     * Get product price
     *
     * @param Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    public function getProductPrice($product)
    {
        $abstractBlock = $this->getLayout()->createBlock('\Magento\Catalog\Block\Product\AbstractProduct');
        $finalPrice = $abstractBlock->getProductPrice($product);

        if(empty($finalPrice)) {
            /** \Magento\Framework\Pricing\Helper\Data */
            $finalPrice = $this->priceHelper->currency($product->getFinalPrice(), true, false);
        }

        return $finalPrice;
    }

    /**
     * Get custom product price html
     *
     * @param $product
     * @return string
     */
    public function getCustomProductPriceHtml($product)
    {
        $html = '';

        $basePrice = $product->getPrice();
        $finalPrice = $product->getFinalPrice();
        if($finalPrice < $basePrice){
            $html .= '<span class="item-value old-price">' . $this->priceHelper->currency($basePrice, true, false). '</span>';
            $html .= '<span class="item-value special-price">' . $this->priceHelper->currency($finalPrice, true, false) . '</span>';
        }
        else{
            $html .= '<span class="item-value price">' . $this->priceHelper->currency($finalPrice, true, false) . '</span>';
        }
        return $html;
    }

    /**
     * Get base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
		return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
    }

    /**
     * Get source data url
     *
     * @return string
     */
    public function getSourceDataUrl()
    {
        return $this->getUrl('reservation/index/sourcedata');
    }

    /**
     * get source data
     *
     * @return \Magento\InventoryApi\Api\SourceInterface
     */
    public function getSource()
    {
    	$reserveId = $this->dataHelper->getSessionReservationId();
    	$reservation = $this->reservationRepository->getById($reserveId);
    	$sourceCode = $reservation->getSourceCode();
    	$source = $this->dataHelper->getSourceByCode($sourceCode);

    	return $source;
    }

    /**
     * get store gmap url
     *
     * @param \Magento\InventoryApi\Api\SourceInterface $source
     * @return string
     */
    public function getStoreGmapUrl(\Magento\Inventory\Model\Source $source)
    {
        $storeName = strtolower($source->getName());
        $storeName = preg_replace('/[^a-z0-9]/i', ' ', $storeName); // remove special characters
        $storeName = preg_replace('/\s+/', ' ', $storeName); // remove multiple whitespace
        $query = str_replace(' ', '+', $storeName);

        $url = 'https://www.google.com/maps/search/?api=1&query=' . $query;
        return $url;
    }

    /**
     * get source data by code
     *
     * @param string $code
     * @return \Magento\InventoryApi\Api\SourceInterface
     */
    public function getSourceByCode(string $code)
    {
        $source = $this->dataHelper->getSourceByCode($code);

        return $source;
    }

    /**
     * get store close time
     *
     * @return string
     */
    public function getSourceCloseTime()
    {
        $source = $this->getSource();
        return $this->sourceAttribute->getCloseTime($source->getSourceCode());
    }

    /**
     * get today date
     *
     * @return string
     */
    public function getTodayDate()
    {
        return $this->timezoneInterface->date()->format('d F Y');
    }

     /**
     * get customer id
     * @return string
     */
    public function getCustomerIdCustom()
    {
        //return current customer ID
        if ($this->customerSession->getCustomer()->getEmail()) {
            $hashcustomeremail = hash('sha256', $this->customerSession->getCustomer()->getEmail());
            return $hashcustomeremail;
        }
        return false;
    }

    /**
     * Get category collection
     *
     * @param bool $isActive
     * @param bool|int $level
     * @param bool|string $sortBy
     * @param bool|int $pageSize
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }

        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }

        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }

        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }

        return $collection;
    }
}
