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

namespace Trans\Reservation\Block\Adminhtml\Grid;

use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;
use Magento\Framework\Pricing\Helper\Data as PriceData;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class View.
 */
class View extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurable;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @var PriceData
     */
    protected $priceHelper;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Trans\Reservation\Api\Data\SourceAttributeInterface
     */
    protected $sourceAttribute;

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Trans\Reservation\Api\UserStoreRepositoryInterface
     */
    protected $userStoreRepository;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationHelper;

    /**
     * @var array
     */
    protected $userStore = [];

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param PriceData $priceHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationHelper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $itemRepository
     * @param \Trans\Reservation\Api\UserStoreRepositoryInterface $userStoreRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $datetime,
        PriceData $priceHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $itemRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Reservation $reservationHelper,
        \Trans\Reservation\Api\UserStoreRepositoryInterface $userStoreRepository,
        array $data = []
    )
    {
        $this->customerRepository = $customerRepository;
        $this->authSession = $authSession;
        $this->datetime = $datetime;
        $this->sourceAttribute = $sourceAttribute;
        $this->productRepository = $productRepository;
        $this->itemRepository = $itemRepository;
        $this->priceHelper = $priceHelper;
        $this->imageHelper = $imageHelper;
        $this->configurable = $configurable;
        $this->dataHelper = $dataHelper;
        $this->userStoreRepository = $userStoreRepository;
        $this->reservationHelper = $reservationHelper;
        $this->urlBuilder = $context->getUrlBuilder();

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * get reservation data
     *
     * @return Trans\Reservation\APi\Data\ReservationInterface
     */
    public function getReservationData()
    {
        return $this->_coreRegistry->registry('reservation');;
    }

    /**
     * get reservation item data
     *
     * @return Trans\Reservation\APi\Data\ReservationItemInterface
     */
    public function getReservationItemData()
    {
        return $this->_coreRegistry->registry('reservationItem');;
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
     * get customer data by id
     *
     * @param int $customerId
     * @return Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomerById($customerId)
    {
        return $this->customerRepository->getById($customerId);
    }

    /**
     * get customer name
     *
     * @param Magento\Customer\Api\Data\CustomerInterface
     * @return string
     */
    public function getCustomerName($customer)
    {
        $name = $customer->getFirstname() . ' ' . $customer->getLastname();

        return $name;
    }

    /**
     * get customer telephone number
     *
     * @param Magento\Customer\Api\Data\CustomerInterface
     * @return string
     */
    public function getCustomerTelephone($customer)
    {
        return $customer->getCustomAttribute('telephone')->getValue();
    }

    /**
     * get customer email
     *
     * @param Magento\Customer\Api\Data\CustomerInterface
     * @return string
     */
    public function getCustomerEmail($customer)
    {
        return $customer->getEmail();
    }

    /**
     * get reservation business status
     *
     * @param string $refNumber
     * @return string
     */
    public function getBusinessStatus(string $refNumber)
    {
        $reservation = $this->getReservationData();

        return $this->reservationHelper->getReservationBusinessStatus($reservation, $refNumber);
    }

    /**
     * get reservation status
     *
     * @param $item
     * @return string
     */
    public function getItemStatus($item)
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
     * get reservation subtotal with current curency
     *
     * @param int $price
     * @param int $qty
     * @param string $orderId
     * @return string
     */
    public function getFormattedSubtotal(int $price = null, int $qty = null, $orderId = null)
    {
        $subtotal = 0;
        if($price != null && $qty != null) {
            $subtotal = $price * $qty;
        }

        if($subtotal == 0) {
            $reservation = $this->getReservationData();
            $items = $reservation->getItems();

            foreach($items as $item) {
                if($orderId != null) {
                    if($item->getReferenceNumber() != $orderId) {
                        continue;
                    }
                }
//                try {
//                    $product = $this->productRepository->getById($item->getProductId());
//                } catch (NoSuchEntityException $e) {
//                    continue;
//                }
//                $price = $product->getFinalPrice();
                $price = $item->getFinalPrice();
                $qty = $item->getQty();
                $subtotalItem = $price*$qty;
                $subtotal = $subtotal + $subtotalItem;
            }
        }

        $result = $this->priceHelper->currency($subtotal, true, false);

        return $result;
    }

    /**
     * get current user
     *
     * @return Magento\User\Model\User
     */
    protected function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * generate user store to array
     *
     * @return void
     */
    protected function getUserStore()
    {
        $user = $this->getCurrentUser();

        $userStore = $this->userStoreRepository->getByUserId($user->getId());

        foreach($userStore as $store) {
            $this->userStore[] = $store->getStoreCode();
        }
    }

    /**
     * prepare raw body
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface $reservation
     * @return array
     */
    public function prepareData(\Trans\Reservation\Api\Data\ReservationInterface $reservation)
    {
        $this->getUserStore();
        $items = $reservation->getItems();
        $dataItem = [];

        foreach($items as $item) {
            if(count($this->userStore) != 0 && !in_array($item->getSourceCode(), $this->userStore)) {
                continue;
            }

            $totalWeight[$item->getReferenceNumber()] = 0;
            $totalPrice[$item->getReferenceNumber()] = 0;
        }

        foreach($items as $item) {
            if(count($this->userStore) != 0 && !in_array($item->getSourceCode(), $this->userStore)) {
                continue;
            }

            try {
                $product = $this->productRepository->getById($item->getProductId());
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $weight = (int)$product->getWeight() * 1000;

            if($product->getWeight()) {
                $totalWeight[$item->getReferenceNumber()] += $weight;
            }

            if($product->getFinalPrice()) {
                $totalPrice[$item->getReferenceNumber()] += $weight;
            }

            if(isset($dataItem[$item->getReferenceNumber()])) {
                $dataItem[$item->getReferenceNumber()]['items'][] = $item;
                $dataItem[$item->getReferenceNumber()]['total_weight'] = $totalWeight[$item->getReferenceNumber()];
                $dataItem[$item->getReferenceNumber()]['grand_total'] = $totalPrice[$item->getReferenceNumber()];
            } else {
                $data[$item->getReferenceNumber()]['order_id'] = $item->getReferenceNumber();
                $data[$item->getReferenceNumber()]['flag'] = $item->getFlag();
                $data[$item->getReferenceNumber()]['grand_total'] = $totalPrice[$item->getReferenceNumber()];
                $data[$item->getReferenceNumber()]['store_code'] = $item->getSourceCode();
                $data[$item->getReferenceNumber()]['end_date'] = $item->getReservationDateEnd();
                $data[$item->getReferenceNumber()]['end_time'] = $item->getReservationTimeEnd();
                $data[$item->getReferenceNumber()]['items'][] = $item;

                $dataItem = $data;
            }
        }

        return $dataItem;
    }

    /**
     * get parent product id
     *
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
     * get mass action option array
     *
     * @return array
     */
    public function getMassActionOption()
    {
        $data = array(
            ['label' => 'Made Purchase', 'value' => 'purchase'],
            ['label' => 'Change Product', 'value' => 'change'],
            ['label' => 'Cancel', 'value' => 'cancel_business']
        );

        return $data;
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
     * Get product image
     *
     * @param Magento\Catalog\Api\Data\ProductInterface $product
     * @return string
     */
    public function getImageUrl($product)
    {
        $url = false;
        if ($product->getImage()) {
            $url = $this->urlBuilder->getBaseUrl(
                ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
            ) . 'catalog/product/' . $product->getImage();
        }
        return $url;
    }

    /**
     * get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('reservation/reservation/index');
    }

    /**
     * get release url
     *
     * @return string
     */
    public function getReleaseUrl($orderid)
    {
        return $this->getUrl('reservation/reservation/releaseItem', ['id' => $orderid]);
    }

    /**
     * get mass action url
     *
     * @param string $refNumber
     * @return string
     */
    public function getMassActionUrl(string $refNumber)
    {
        return $this->getUrl('reservation/reservation/massActionItem', ['ref' => $refNumber]);
    }

    /**
     * change date format
     *
     * @param datetime $datetime
     * @return datetime
     */
    public function changeDateFormat($datetime)
    {
        return $this->dataHelper->changeDateFormat($datetime);
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
     * get source data by code
     */
    public function getSourceData($code)
    {
        return $this->dataHelper->getSourceByCode($code);
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
     * is reservation released
     *
     * @return bool
     */
    public function isReleased()
    {
        $reservation = $this->getReservationData();

        if($reservation->getFlag() === ReservationInterface::FLAG_SUBMIT) {
            return true;
        }

        return false;
    }

    /**
     * is reservation item released
     *
     * @param \Trans\Reservation\Api\Data\ReservationItemInterface $item
     * @return bool
     */
    public function isItemReleased(ReservationItemInterface $item)
    {
        if($item->getFlag() === ReservationItemInterface::FLAG_CONFIRM) {
            return true;
        }

        return false;
    }

    /**
     * is order released
     *
     * @param string $orderId
     * @return bool
     */
    public function isOrderReleased(string $orderId)
    {
        $items = $this->itemRepository->getByReference($orderId);
        $unreleased = 0;
        foreach($items as $item) {
            if($item->getFlag() !== ReservationItemInterface::FLAG_CONFIRM && $item->getFlag() !== ReservationItemInterface::FLAG_CANCEL) {
                $unreleased++;
            }
        }

        if($unreleased == 0) {
            return true;
        }

        return false;
    }
}
