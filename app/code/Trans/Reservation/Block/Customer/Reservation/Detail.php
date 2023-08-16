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

namespace Trans\Reservation\Block\Customer\Reservation;

use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;
use Magento\Framework\Pricing\Helper\Data as PriceData;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Detail
 */
class Detail extends AbstractBlock
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var PriceData
     */
    protected $priceHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @var \Trans\Reservation\Api\Data\SourceAttributeInterface
     */
    protected $sourceAttribute;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\Framework\Registry $coreRegistry
     * @param PriceData $priceHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $collectionFactory
     * @param \Trans\Reservation\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        PriceData $priceHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $collectionFactory,
        \Trans\Reservation\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->currentCustomer = $currentCustomer;
        $this->datetime = $datetime;
        $this->sourceAttribute = $sourceAttribute;
        $this->productRepository = $productRepository;
        $this->coreRegistry = $coreRegistry;
        $this->configurable = $configurable;
        $this->priceHelper = $priceHelper;
        $this->imageHelper = $imageHelper;
        $this->reservationRepository = $reservationRepository;
        $this->collectionFactory = $collectionFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * get reservation data
     *
     * @return Trans\Reservation\APi\Data\ReservationInterface
     */
    public function getReservationData()
    {
        return $this->coreRegistry->registry('reservation');;
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
     * Format price
     *
     * @param float $price
     * @return float|string
     */
    public function formatPrice(float $price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * get reservation status
     *
     * @param $item
     * @return string
     */
    public function getStatus($item)
    {
        $statusItemLabel = '';
        $statusItem = $item->getBusinessStatus();
        if($statusItem == ReservationItemInterface::BUSINESS_STATUS_RESERVE) {
            $statusItemLabel = ReservationItemInterface::BUSINESS_STATUS_RESERVE_LABEL;
        }
        elseif ($statusItem == ReservationItemInterface::BUSINESS_STATUS_VISIT) {
            $statusItemLabel = ReservationItemInterface::BUSINESS_STATUS_VISIT_LABEL;
        }
        elseif ($statusItem == ReservationItemInterface::BUSINESS_STATUS_PURCHASE) {
            $statusItemLabel = ReservationItemInterface::BUSINESS_STATUS_PURCHASE_LABEL;
        }
        elseif ($statusItem == ReservationItemInterface::BUSINESS_STATUS_CHANGE) {
            $statusItemLabel = ReservationItemInterface::BUSINESS_STATUS_CHANGE_LABEL;
        }
        elseif ($statusItem == ReservationItemInterface::BUSINESS_STATUS_CANCELED) {
            $statusItemLabel = ReservationItemInterface::BUSINESS_STATUS_CANCELED_LABEL;
        }
        elseif ($statusItem == ReservationItemInterface::BUSINESS_STATUS_VISIT_CANCELED) {
            $statusItemLabel = 'Visited, Canceled';
        }
        else {
            $statusItemLabel = 'Cancel';
        }
        return $statusItemLabel;
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
     * get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('reservation/customer/history');
    }

    /**
     * change date format
     *
     * @param datetime $datetime
     * @return datetime
     */
    public function changeDateFormat($datetime, $format = 'd F Y H:i')
    {
        return $this->dataHelper->changeDateFormat($datetime, $format);
    }

    /**
     * get datetime
     *
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDateTime()
    {
        return $this->datetime;
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

        return $this->getDateTime()->date($format, $datetime);
    }

    /**
     * get source data by code
     */
    public function getSourceData($code)
    {
        try {
            return $this->dataHelper->getSourceByCode($code);
        } catch (NoSuchEntityException $e) {
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
        $data[] = $source->getStreet() ? $source->getStreet() : '';
        $data[] = $source->getRegion() ? $source->getRegion() : '';
        $data[] = $source->getCity() ? $source->getCity() : '';
        $data[] = $source->getPostcode() ? $source->getPostcode() : '';

        $string = implode(', ', $data);

        return $string;
    }

    /**
     * get source open hour
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return string
     */
    public function getStoreOpenHour($source)
    {
        return $this->sourceAttribute->getOpenTime($source->getSourceCode());
    }

    /**
     * get source close hour
     *
     * @param \Magento\InventoryApi\Api\Data\SourceInterface
     * @return string
     */
    public function getStoreCloseHour($source)
    {
        return $this->sourceAttribute->getCloseTime($source->getSourceCode());
    }

    /**
     * is reservation cancelable
     *
     * @param object $reservation
     * @return bool
     */
    public function isCancelable($reservationItem)
    {
        // if($reservationItem->getFlag() != ReservationItemInterface::FLAG_CANCEL && $reservationItem->getFlag() != ReservationItemInterface::FLAG_CONFIRM) {
        if($reservationItem->getFlag() != ReservationInterface::FLAG_CANCEL && $reservationItem->getFlag() != ReservationInterface::FLAG_FINISH) {
            return true;
        }

        return false;
    }

    /**
     * get cancel url
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface | \Trans\Reservation\Api\Data\ReservationItemInterface $reserve
     * @return url
     */
    public function getCancelItemUrl($reserve)
    {
        return $this->getUrl('reservation/customer/cancelitem/', ['id' => $reserve->getId()]);
    }

    /**
     * get subtotal of reservation
     *
     * @return string
     */
    public function getFormattedSubtotal()
    {
        $reservation = $this->getReservationData();
        $items = $reservation->getItems();
        $result = '';
        if($items && $items->getSize() > 0){
            $subtotal = 0;
            foreach($items as $item){
                if($item->getFinalPrice() > 0){
                    $subtotal += $item->getFinalPrice();
                }
            }
            $result = $this->formatPrice($subtotal);
        }

        return $result;
    }
}
