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

namespace Trans\Reservation\Model;

use Trans\Reservation\Api\Data\ReservationItemInterface;
use Trans\Reservation\Model\ResourceModel\ReservationItem as ResourceModel;

/**
 * Class ReservationItem
 */
class ReservationItem extends \Magento\Framework\Model\AbstractModel implements ReservationItemInterface
{

    /**
     * cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'trans_reservation_item';

    /**
     * cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'trans_reservation_item';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'trans_reservation_item';

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Reservation\Model\ReservationConfig
     */
    protected $configModel;

    /**
     * @var \Trans\CatalogMultisource\Helper\SourceItem
     */
    protected $sourceItemHelper;

    /**
     * @var logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $itemRepository
     * @param \Trans\Reservation\Model\ReservationConfig $configModel
     * @param \Trans\Reservation\Helper\Config $configHelper
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $itemRepository,
        \Trans\Reservation\Model\ReservationConfig $configModel,
        \Trans\Reservation\Helper\Config $configHelper,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper,
        array $data = []
    ) {
        $this->itemRepository = $itemRepository;
        $this->configModel = $configModel;
        $this->sourceItemHelper = $sourceItemHelper;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;

        $this->logger = $dataHelper->getLogger();

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationId()
    {
        return (int) $this->getData(ReservationItemInterface::RESERVATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationId($reservationId)
    {
        $this->setData(ReservationItemInterface::RESERVATION_ID, $reservationId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceNumber()
    {
        return $this->getData(ReservationItemInterface::REFERENCE_NUMBER);
    }

    /**
     * {@inheritdoc}
     */
    public function setReferenceNumber($referenceNumber)
    {
        $this->setData(ReservationItemInterface::REFERENCE_NUMBER, $referenceNumber);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSourceCode()
    {
        return $this->getData(ReservationItemInterface::SOURCE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSourceCode($source)
    {
        $this->setData(ReservationItemInterface::SOURCE_CODE, $source);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(ReservationItemInterface::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($productId)
    {
        $this->setData(ReservationItemInterface::PRODUCT_ID, $productId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBasePrice()
    {
        return $this->getData(ReservationItemInterface::BASE_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBasePrice($price)
    {
        $this->setData(ReservationItemInterface::BASE_PRICE, $price);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalPrice()
    {
        return $this->getData(ReservationItemInterface::FINAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFinalPrice($price)
    {
        $this->setData(ReservationItemInterface::FINAL_PRICE, $price);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData(ReservationItemInterface::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        $this->setData(ReservationItemInterface::QTY, $qty);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuffer()
    {
        return $this->getData(ReservationItemInterface::BUFFER);
    }

    /**
     * {@inheritdoc}
     */
    public function setBuffer($buffer)
    {
        $this->setData(ReservationItemInterface::BUFFER, $buffer);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxqty()
    {
        return $this->getData(ReservationItemInterface::MAXQTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxqty($maxqty)
    {
        $this->setData(ReservationItemInterface::MAXQTY, $maxqty);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationDateStart()
    {
        return $this->getData(ReservationItemInterface::START_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationDateStart($date)
    {
        $this->setData(ReservationItemInterface::START_DATE, $date);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationTimeStart()
    {
        return $this->getData(ReservationItemInterface::START_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationTimeStart($time)
    {
        $this->setData(ReservationItemInterface::START_TIME, $time);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationDateEnd()
    {
        return $this->getData(ReservationItemInterface::END_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationDateEnd($date)
    {
        $this->setData(ReservationItemInterface::END_DATE, $date);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReservationTimeEnd()
    {
        return $this->getData(ReservationItemInterface::END_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setReservationTimeEnd($time)
    {
        $this->setData(ReservationItemInterface::END_TIME, $time);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFlag()
    {
        return $this->getData(ReservationItemInterface::FLAG);
    }

    /**
     * {@inheritdoc}
     */
    public function setFlag($flag)
    {
        $this->setData(ReservationItemInterface::FLAG, $flag);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBusinessStatus()
    {
        return $this->getData(ReservationItemInterface::BUSINESS_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setBusinessStatus($status)
    {
        $this->setData(ReservationItemInterface::BUSINESS_STATUS, $status);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReminderEmail()
    {
        return (int) $this->getData(ReservationItemInterface::REMINDER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setReminderEmail($status)
    {
        $this->setData(ReservationItemInterface::REMINDER_EMAIL, $status);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAutocancelEmail()
    {
        return (int) $this->getData(ReservationItemInterface::AUTOCANCEL_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setAutocancelEmail($status)
    {
        $this->setData(ReservationItemInterface::AUTOCANCEL_EMAIL, $status);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(ReservationItemInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($datetime)
    {
        $this->setData(ReservationItemInterface::CREATED_AT, $datetime);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt() {
        return $this->getData(ReservationItemInterface::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($datetime)
    {
        $this->setData(ReservationItemInterface::UPDATED_AT, $datetime);
        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessStatusLabel()
    {
        $status = $this->getBusinessStatus();

        switch ($status) {
            case ReservationItemInterface::BUSINESS_STATUS_RESERVE:
                $businessStatusLabel = ReservationItemInterface::BUSINESS_STATUS_RESERVE_LABEL;
                break;

            case ReservationItemInterface::BUSINESS_STATUS_VISIT:
                // return ReservationItemInterface::BUSINESS_STATUS_VISIT_LABEL;
                $businessStatusLabel = '';
                break;

            case ReservationItemInterface::BUSINESS_STATUS_PURCHASE:
                $businessStatusLabel = ReservationItemInterface::BUSINESS_STATUS_PURCHASE_LABEL;
                break;

            case ReservationItemInterface::BUSINESS_STATUS_CHANGE:
                $businessStatusLabel = ReservationItemInterface::BUSINESS_STATUS_CHANGE_LABEL;
                break;

            default:
                $businessStatusLabel = ReservationItemInterface::BUSINESS_STATUS_CANCELED_LABEL;
                break;
        }

        return $businessStatusLabel;
    }

    /**
     * merge reservation items to reservation data
     *
     * @param \Trans\Reservation\Model\ResourceModel\ReservationItem\Collection $items
     * @param int $reservationId destination
     * @return void
     */
    public function mergeItems(\Trans\Reservation\Model\ResourceModel\ReservationItem\Collection $items, int $reservationId)
    {
        if($items->getSize()) {
            try {
                foreach($items as $item) {
                    $existingItem = $this->itemRepository->get($reservationId, $item->getProductId());
                    if($existingItem->getId()) {
                        // $qty = $item->getQty() + $existingItem->getQty();
                        $qty = 1;
                        $existingItem->setQty($qty);
                        $data = $existingItem;
                        $this->itemRepository->delete($item);
                    } else {
                        $item->setReservationId($reservationId);
                        $data = $item;
                    }

                    $this->itemRepository->save($data);
                }
            } catch (\Exception $e) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * check product buffer
     *
     * @param string $sourceCode
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return bool
     */
    public function checkProductBuffer($sourceCode, \Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $this->logger->info(__('start run ' . __FUNCTION__));

        $sku = $product->getSku();
        $this->logger->info(__('run ' . __FUNCTION__ . '. $sku = ' . $sku));
        $sourceItems = $this->sourceItemHelper->getSourceItem($sku, $sourceCode);

        $buffer = $this->configModel->getProductBuffer($product, $sourceCode);
        $this->logger->info(__('run ' . __FUNCTION__ . '. $buffer = ' . $buffer));

        $currentStock = 0;
        foreach($sourceItems as $item) {
            $currentStock = $item->getQuantity();
        }

        $this->logger->info(__('run ' . __FUNCTION__ . '. $currentStock = ' . $currentStock));

        $this->logger->info(__('end run ' . __FUNCTION__));
        try {
            if($currentStock <= $buffer) {
                return false;
            }
        } catch (\Exception $e) {
            throw new Exception("Error while getting content.", 1);
        }

        return true;
    }
}
