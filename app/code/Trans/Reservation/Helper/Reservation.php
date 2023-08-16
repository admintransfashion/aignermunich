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

namespace Trans\Reservation\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;
use Trans\Reservation\Api\Data\ReservationConfigInterface;

/**
 * Class Reservation
 */
class Reservation extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $instances = [];

    /**
     * Constant for product config title
     */
    const DEFAULT_TITLE_BUFFER = 'Product buffer';
    const DEFAULT_TITLE_MAXQTY = 'Max qty';
    const DEFAULT_TITLE_HOURS = 'Reservation hours';
    const DEFAULT_TITLE_DATE = 'Date Config';

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    protected $sourceItem;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    protected $sourceItemSave;

    /**
     * @var \Trans\Reservation\Model\ReservationConfig
     */
    protected $configModel;

    /**
     * @var \Trans\Reservation\Logger\Logger
     */
    protected $logger;

    /**
     * @var \Trans\Reservation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Trans\Reservation\Api\Data\SourceAttributeInterface
     */
    protected $sourceAttribute;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
     */
    protected $reservationItemRepository;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory
     */
    protected $reserveCollection;

    /**
     * @var \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory
     */
    protected $configCollection;

    /**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Trans\CatalogMultisource\Helper\SourceItem
     */
    protected $sourceItemHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var datetime
     */
    protected $datetime;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     * @param \Trans\Reservation\Model\ReservationConfig $configModel
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute
     * @param \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $reserveCollection
     * @param \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $configCollection
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Config $configHelper
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
        \Trans\Reservation\Model\ReservationConfig $configModel,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute,
        \Trans\Reservation\Model\ResourceModel\Reservation\CollectionFactory $reserveCollection,
        \Trans\Reservation\Model\ResourceModel\ReservationConfig\CollectionFactory $configCollection,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Config $configHelper,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
    )
    {
        parent::__construct($context);

        $this->reservationRepository = $reservationRepository;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->configModel = $configModel;
        $this->sourceAttribute = $sourceAttribute;
        $this->sourceItem = $sourceItem;
        $this->sourceItemSave = $sourceItemSave;
        $this->productRepository = $productRepository;
        $this->reserveCollection = $reserveCollection;
        $this->configCollection = $configCollection;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
        $this->sourceItemHelper = $sourceItemHelper;

        $this->timezone = $dataHelper->getTimezone();
        $this->datetime = $dataHelper->getDatetime();
        $this->logger = $dataHelper->getLogger();
    }

     /**
     * Generate product config title
     *
     * @return string
     */
    public function generateTitle(string $config, $qty = 0)
    {
        $collection = $this->configCollection->create();

        $size = $collection->getSize();

        if($config == ReservationConfigInterface::CONFIG_BUFFER) {
            $default = self::DEFAULT_TITLE_BUFFER;
        } else if($config == ReservationConfigInterface::CONFIG_MAXQTY) {
            $default = self::DEFAULT_TITLE_MAXQTY;
        } else if($config == ReservationConfigInterface::CONFIG_HOURS) {
            $default = self::DEFAULT_TITLE_HOURS;
        } else if($config == ReservationConfigInterface::CONFIG_DATE) {
            $default = self::DEFAULT_TITLE_DATE;
        }

        $postTitle = ($size + 1);

        if($qty != 0) {
            $postTitle = $qty;
        }

        $title = $default . ' ' . $postTitle;

        return $title;
    }

    /**
     * get instances data
     *
     * @param string $key
     * @return string|int
     */
    public function getInstances(string $key)
    {
        return isset($this->instances[$key]) ? $this->instances[$key] : false;
    }

    /**
     * get product buffer
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $sourceCode
     * @return int
     */
    public function getProductBuffer(\Magento\Catalog\Api\Data\ProductInterface $product, string $sourceCode)
    {
        $buffer = $this->configModel->getProductBuffer($product, $sourceCode);
        $this->instances[ReservationConfigInterface::CONFIG_BUFFER . $product->getId() . $sourceCode] = $buffer;
        return $buffer;
    }

    /**
     * get reservation store codes
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface $reservationInterface
     * @return array
     */
    public function getReservationStores(\Trans\Reservation\Api\Data\ReservationInterface $reservation)
    {
        $stores = [];
        $items = $reservation->getItems();

        foreach($items as $item) {
            $store = $item->getSourceCode();
            if(!in_array($store, $stores)) {
                $stores[] = $store;
            }
        }

        return $stores;
    }

    /**
     * get reservation business status
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface $reservation
     * @param string $refNumber
     * @return string
     */
    public function getReservationBusinessStatus(\Trans\Reservation\Api\Data\ReservationInterface $reservation, string $refNumber)
    {
        $status = [];
        $item = $this->reservationItemRepository->getByReference($refNumber)->getFirstItem();
        $items = $this->reservationItemRepository->getByReference($refNumber);
        // if($reservation->getFlag() != ReservationInterface::FLAG_FINISH) {
            switch ($reservation->getFlag()) {
                case ReservationInterface::FLAG_FINISH:
                    $status[] = 'Visited';
                    break;

                case ReservationInterface::FLAG_CANCEL:
                    $status[] = 'Cancelled';
                    break;

                case ReservationInterface::FLAG_SUBMIT:
                    $status[] = 'Waiting';
                    break;
            }
        // }

        if(($reservation->getFlag() == ReservationInterface::FLAG_SUBMIT) && $item->getFlag() != ReservationItemInterface::FLAG_SUBMIT) {
            $status = [];
            $status[] = 'Visited';
        }

        if($reservation->getFlag() == ReservationInterface::FLAG_FINISH) {
            foreach($items as $item) {
                $businessStatus = $item->getBusinessStatus();

                if($businessStatus != ReservationItemInterface::BUSINESS_STATUS_RESERVE) {
                    $status[] = $item->getBusinessStatusLabel();
                }
            }
        } else if($item->getFlag() != ReservationItemInterface::FLAG_SUBMIT && $reservation->getFlag() != ReservationItemInterface::FLAG_CANCEL) {
            $businessStatus = $item->getBusinessStatus();

            if($businessStatus != ReservationItemInterface::BUSINESS_STATUS_RESERVE) {
                $status[] = $item->getBusinessStatusLabel();
            }
        }

        $status = array_filter($status);
        $statusLabel = implode(', ', $status);

        return $statusLabel;
    }

    /**
     * is reaching buffer product
     *
     * @param int $productId
     * @param string $sourceCode
     * @param int|float qty of reserved item $qty
     * @return bool
     */
    public function isQtyReachingBuffer($productId, $sourceCode = '', $qty)
    {
        $this->logger->info(__('start run ' . __FUNCTION__ ));
        try {
            $product = $this->productRepository->getById($productId);
            $this->logger->info(__('run ' . __FUNCTION__ . '. $sku = ' . $product->getSku()));
            $buffer = $this->getProductBuffer($product, $sourceCode);
            $sku = $product->getSku();
            $sourceItems = $this->sourceItemHelper->getSourceItem($sku, $sourceCode);

            $newStock = 0;
            foreach($sourceItems as $item) {
                $newStock = $item->getQuantity() - $qty;
            }

            $this->logger->info(__('run ' . __FUNCTION__ . '. $newStock = ' . $newStock));
            $this->logger->info(__('run ' . __FUNCTION__ . '. $buffer = ' . $buffer));

            if($newStock < $buffer) {
                return true;
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->info('check product buffer error. Message = ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->info('check product buffer error. Message = ' . $e->getMessage());
        }
        $this->logger->info(__('end run ' . __FUNCTION__ ));

        return false;
    }

    /**
     * Check reservation limit
     *
     * @param Trans\Reservation\Model\ResouceModel\Reservation\Collection
     * @return bool
     */
    public function checkReservationLimit($collection, $sourceCode = '')
    {
        foreach($collection as $reservation) {
            $productId = $reservation->getProductId();
            try {
                $product = $this->productRepository->getById($productId);
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $check = $this->checkLimitByProduct($collection, $product, $sourceCode);

            if(!$check) {
                return false;
            }
        }

        return true;
    }

    /**
     * get max qty
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $sourceCode
     * @return array
     */
    public function getMaxQty(\Magento\Catalog\Api\Data\ProductInterface $product, $sourceCode = '')
    {
        $productMaxQty = $this->configHelper->getMaxQty();
        if($this->configHelper->isEnableMaxQtyConfig()) {
            try {
                $maxQty = $this->configModel->getMaxQty($product, $sourceCode);
                $productMaxQty = $maxQty->getValue();
                $result['maxQty'] = $maxQty;
                $this->logger->info(__('run ' . __FUNCTION__ . '. max qty id ' . $maxQty->getId()));
            } catch (NoSuchEntityException $e) {
                try {
                    $parentId = $this->dataHelper->getParentProductId($product->getId());
                    if($parentId) {
                        $product = $this->productRepository->getById($parentId);
                        $maxQty = $this->configModel->getMaxQty($product, $sourceCode);
                        $result['maxQty'] = $maxQty;
                        $productMaxQty = $maxQty->getValue();
                        $this->logger->info(__('run ' . __FUNCTION__ . '. get parent product then get max qty. max qty id ' . $maxQty->getId()));
                    } else {
                        $this->logger->info(__('run ' . __FUNCTION__ . '. product doesn\'t have parent id and didnt get any max qty id'));
                    }
                } catch (NoSuchEntityException $e) {
                    $this->logger->info(__('run ' . __FUNCTION__ . '. didnt get any max qty id'));
                }
            }
        }

        $result['productMaxQty'] = $productMaxQty;
        $this->instances[ReservationConfigInterface::CONFIG_MAXQTY . $product->getId() . $sourceCode] = $productMaxQty;
        return $result;
    }

    /**
     * Check reservation limit by product
     *
     * @param Trans\Reservation\Model\ResouceModel\Reservation\Collection
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string $sourceCode
     * @return bool
     */
    public function checkLimitByProduct($collection, \Magento\Catalog\Api\Data\ProductInterface $product, $sourceCode = '')
    {
        $this->logger->info(__('run ' . __FUNCTION__ . '. Product ' . $product->getName()));
        $getMaxQty = $this->getMaxQty($product, $sourceCode);
        $productMaxQty = $getMaxQty['productMaxQty'];
        $maxQty = isset($getMaxQty['maxQty']) ? $getMaxQty['maxQty'] : false;

        $this->logger->info(__('run ' . __FUNCTION__ . '. productMaxQty = ' . $productMaxQty));

        $size = $collection->getSize();

        $this->logger->info(__('run ' . __FUNCTION__ . '. $size = ' . $size));

        // if($size > $productMaxQty) {
        //     return false;
        // } else {
        // }
        $sumqty = 0;
        foreach ($collection as $value) {
            try {
                $product = $this->productRepository->getById($value->getProductId());

                if($maxQty) {
                    if($this->configModel->isProductAssignedToConfig($maxQty, $product, $sourceCode)) {
                        $sumqty += $value->getQty();
                    }
                } else {
                    $sumqty += $value->getQty();
                }
            } catch (NoSuchEntityException $e) {
                continue;
            } catch (\Exception $e) {
                continue;
            }
        }

        $this->logger->info(__('run ' . __FUNCTION__ . '. $sumqty = ' . $sumqty));
        $result = (int)$sumqty > (int)$productMaxQty;
        $this->logger->info(__('run ' . __FUNCTION__ . '. $result = ' . $result));

        if($result) {
            return false;
        }

        return true;
    }

    /**
     * Check reservation qty reaching max
     *
     * @param Trans\Reservation\Model\ResouceModel\Reservation\Collection
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param int $qty
     * @param string $sourceCode
     * @return bool
     */
    public function isQtyReachMax($collection, $qty, \Magento\Catalog\Api\Data\ProductInterface $product, $sourceCode = '')
    {
        $this->logger->info(__('run ' . __FUNCTION__ . '. Product ' . $product->getName()));
        $getMaxQty = $this->getMaxQty($product, $sourceCode);
        $productMaxQty = $getMaxQty['productMaxQty'];
        $maxQty = isset($getMaxQty['maxQty']) ? $getMaxQty['maxQty'] : false;

        $this->logger->info(__('run ' . __FUNCTION__ . '. productMaxQty = ' . $productMaxQty));

        $size = (int)$collection->getSize();
        $this->logger->info(__('run ' . __FUNCTION__ . '. $size = ' . $size));

        // if($size >= $productMaxQty) {
        //     return true;
        // } else {
        // }

        $sumqty = 0;
        foreach ($collection as $value) {
            try {
                $asignedProduct = $this->productRepository->getById($value->getProductId());
                if($maxQty) {
                    if($this->configModel->isProductAssignedToConfig($maxQty, $asignedProduct, $sourceCode)) {
                        $sumqty += $value->getQty();
                    }
                } else {
                    $sumqty += $value->getQty();
                }
            } catch (NoSuchEntityException $e) {
                continue;
            } catch (\Exception $e) {
                continue;
            }
        }

        $sum = (int)$sumqty + (int)$qty;

        $this->logger->info(__('run ' . __FUNCTION__ . '. $sumqty = ' . $sumqty));
        $this->logger->info(__('run ' . __FUNCTION__ . '. $qty = ' . $qty));
        $this->logger->info(__('run ' . __FUNCTION__ . '. $sum = ' . $sum));

        $result = ($sum > (int)$productMaxQty);
        $this->logger->info(__('run ' . __FUNCTION__ . '. $result = ' . $result));

        if($result) {
            return true;
        }

        return false;
    }

    /**
     * check if reservation items product exists
     *
     * @param Trans\Reservation\Model\ResourceModel\ReservationItem\Collection $items
     * @return bool
     */
    public function isReservationItemsExists($items)
    {
        if(count($items) > 0) {
            $count = 0;
            foreach($items as $item) {
                $productId = $item->getProductId();

                try {
                    $this->productRepository->getById($productId);
                    $count++;
                } catch (NoSuchEntityException $e) {
                    continue;
                }
            }

            if($count == 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * is reservation with differenct store
     *
     * @param string $sourceCode
     * @param Trans\Reservation\Model\ResouceModel\Reservation\Collection $collection
     * @return bool
     */
    public function isDifferentStore($sourceCode, $collection)
    {
        $size = $collection->getSize();
        if($size) {
            $diff = 0;
            foreach($collection as $data) {
                if($data->getSourceCode() != $sourceCode) {
                    $diff++;
                }
            }

            if($diff > 0) {
                /** different store */
                return true;
            }
        }

        return false;
    }

    /**
     * is product exists
     *
     * @param string|null $sku
     * @return bool
     */
    public function isProductExists($sku = null)
    {
        if($sku) {
            try {
                $this->productRepository->get($sku);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }

        return true;
    }

    /**
     * generate reservation item end date
     *
     * @param string $sourceCode
     * @param int $productId
     * @return datetime
     */
    public function generateReservationItemEndDate(string $sourceCode, int $productId)
    {
        $result['time'] = $this->sourceAttribute->getCloseTime($sourceCode);
        $restulr['datetime'] = $this->timezone->date()->format('Y-m-d' . ' ' . $this->sourceAttribute->getCloseTime($sourceCode) . ':00');

        try {
            $product = $this->productRepository->getById($productId);
            $reservationHours = $this->configModel->getProductReservationHours($product, $sourceCode);
        } catch (NoSuchEntityException $e) {
            $reservationHours = null;
        }

        $this->logger->info(__('run ' . __FUNCTION__ . '. $reservationHours = ' . $this->json->serialize($reservationHours)));

        if(empty($reservationHours) || !$reservationHours) {
            return $result;
        }

        $now = $this->timezone->date()->format('Y-m-d H:i:s');

        $timestamp = strtotime($this->timezone->date()->format($now)) + ((60*60) * (int)$reservationHours['value']);
        $datetime = date('Y-m-d H:i:s', $timestamp);

        $timestamp = strtotime($this->timezone->date()->format('H:i')) + ((60*60) * (int)$reservationHours['value']);
        $time = date('H:i', $timestamp);

        $result['time'] = (string)$time;
        // $result['datetime'] = $this->timezone->date()->format('Y-m-d' . ' ' . $time . ':00');
        $result['datetime'] = $datetime;

        $this->logger->info(__('run ' . __FUNCTION__ . '. $result = ' . json_encode($result)));

        return $result;
    }

    /**
     * generate reservation end date
     *
     * @param collection $reservationItems
     * @param int $productId
     * @return datetime
     */
    public function generateReservationEndDate(string $sourceCode, $reservationItems)
    {
        $value = false;
        $result['time'] = $this->sourceAttribute->getCloseTime($sourceCode);
        $result['datetime'] = $this->timezone->date()->format('Y-m-d' . ' ' . $this->sourceAttribute->getCloseTime($sourceCode) . ':00');
        $hours = [];
        $hoursPriority = $this->configHelper->getHoursPriority();

        foreach($reservationItems as $item) :

            try {
                $productId = $item->getProductId();
                $product = $this->productRepository->getById($productId);
                $reservationHours = $this->configModel->getProductReservationHours($product, $sourceCode);
                $hours[] = $reservationHours;
            } catch (NoSuchEntityException $e) {
                $reservationHours = null;
            }

        endforeach;

        if($hoursPriority == ReservationConfigInterface::CONFIG_PRIORITY_LONGEST) {
            // $value = max($hours);
            $max = $this->dataHelper->getMaxValueOfArray($hours);
            if($max) {
                $value = $max['value'];
            }
        }

        if($hoursPriority == ReservationConfigInterface::CONFIG_PRIORITY_SHORTEST) {
            // $value = min($hours);
            $min = $this->dataHelper->getMinValueOfArray($hours);
            if($min) {
                $value = $min['value'];
            }
        }

        if($hoursPriority == ReservationConfigInterface::CONFIG_PRIORITY_LATEST) {
            $latest = $this->dataHelper->getLatestOfArray($hours);
            if($latest) {
                $value = $latest['value'];
            }
        }

        $this->logger->info(__('run ' . __FUNCTION__ . '. array $reservationHours = ' . json_encode($reservationHours)));
        $this->logger->info(__('run ' . __FUNCTION__ . '. $value = ' . $value));

        if(!$value) {
            return $result;
        }

        $now = $this->timezone->date()->format('Y-m-d H:i:s');

        $timestamp = strtotime($this->timezone->date()->format($now)) + ((60*60) * (int)$value);
        $datetime = date('Y-m-d H:i:s', $timestamp);

        $timestamp = strtotime($this->timezone->date()->format('H:i')) + ((60*60) * (int)$value);
        $time = date('H:i', $timestamp);

        $result['time'] = (string)$time;
        $result['datetime'] = $datetime;

        $this->logger->info(__('run ' . __FUNCTION__ . '. $result = ' . json_encode($result)));
        return $result;
    }

    /**
     * Release reservation item qty
     *
     * @param ReservationInterface $reservation
     * @return void
     */
    public function releaseReservationItems(ReservationInterface $reservation)
    {
        $reservationItems = $reservation->getItems();
        $sourceCode = $reservation->getSourceCode();

        $items = [];
        foreach($reservationItems as $item)
        {
            if(!$sourceCode) {
                $sourceCode = $item->getSourceCode();
            }
            $this->releaseReservationItemStock($item, $sourceCode);
        }
    }

    /**
     * Release reservation item stock back to inventory
     *
     * @param \Trans\Reservation\Api\Data\ReservationItemInterface $item
     * @return void
     */
    public function releaseReservationItemStock(\Trans\Reservation\Api\Data\ReservationItemInterface $item, $sourceCode = null)
    {
        $productId = $item->getProductId();
        try {
            if($sourceCode == null) {
                $reservation = $this->reservationRepository->getById($item->getReservationId());
                $sourceCode = $reservation->getSourceCode();
                if($sourceCode == null) {
                    $sourceCode = $item->getSourceCode();
                }
            }

            $product = $this->productRepository->getById($productId);
            $sku = $product->getSku();

            $sourceItems = $this->sourceItemHelper->getSourceItem($sku, $sourceCode);

            $qty = 0;
            foreach ($sourceItems as $sourceItem) {
                $qty = (int)($sourceItem->getQuantity() + $item->getQty());
            }

            if($qty >= 0) {
                $sourceItem = $this->sourceItem->create();
                $sourceItem->setSku($sku);
                $sourceItem->setSourceCode($sourceCode);
                $sourceItem->setQuantity($qty);
                $sourceItem->setStatus(1);

                $this->sourceItemSave->execute([$sourceItem]);
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->info('Error release reservation items. Message = ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->info('Error release reservation items. Message = ' . $e->getMessage());
        }
    }

    /**
     * update reservation date data & reservation item date data
     *
     * @param int $reservationId
     * @param string $flag
     * @return void
     */
    public function updateReservationItemsByReservationId(int $reservationId, $flag = null)
    {
        if($flag == null) {
            $flag = ReservationItemInterface::FLAG_SUBMIT;
        }

        $reservation = $this->reservationRepository->getById($reservationId);
        $reservationItems = $reservation->getItems();
        $error = 0;
        $productIdError = [];

        foreach ($reservationItems as $item) {
            $item->setFlag($flag);

            if($flag == ReservationItemInterface::FLAG_CANCEL) {
                $item->setBusinessStatus(ReservationItemInterface::BUSINESS_STATUS_CANCELED);
            }


            try {
                $this->reservationItemRepository->save($item);
            } catch (\Exception $e) {
                $error++;
                $productIdError[] = $item->getProductId();
                continue;
            }
        }

        if($error) {
            $productIdString = implode(', ', $productIdError);
            throw new \Exception("Error Processing Request for product id(s) %1", $productIdString);
        }
    }

    /**
     * update reservation item flag
     *
     * @param int $reservationId
     * @param string $flag
     * @return void
     */
    public function updateReservationItem(string $itemId, $flag = null)
    {
        if($flag == null) {
            $flag = ReservationItemInterface::FLAG_SUBMIT;
        }

        $item = $this->reservationItemRepository->getById($itemId);
        $error = 0;
        $productIdError = [];

        $item->setFlag($flag);

        $businessStatus = ReservationItemInterface::BUSINESS_STATUS_CANCELED;
        if($flag == ReservationItemInterface::FLAG_CONFIRM) {
            $businessStatus = ReservationItemInterface::BUSINESS_STATUS_VISIT;
        }

        $item->setBusinessStatus($businessStatus);

        try {
            $this->reservationItemRepository->save($item);

            $reservation = $this->reservationRepository->getById($item->getReservationId());
            $items = $reservation->getItems();
            $unconfirm = 0;

            foreach($items as $row) {
                if($row->getFlag() != ReservationItemInterface::FLAG_CONFIRM) {
                    $unconfirm++;
                }
            }

            if($unconfirm == 0) {
                $reservation->setFlag(ReservationInterface::FLAG_FINISH);
                $this->reservationRepository->save($reservation);
            }
        } catch (\Exception $e) {
            throw new \Exception("Error Processing Request for product id(s) %1", $item->getProductId());
        }
    }

    /**
     * get collection data reservation today
     *
     * @return Trans\Reservation\Model\ResouceModel\Reservation\Collection
     */
    public function getTodayDataCollection()
    {
        $customerId = $this->dataHelper->getLoggedInCustomerId();
        $collection = $this->reserveCollection->create();

        $now = $this->timezone->date()->format('Y-m-d H:i:s');

        $start = date('Y-m-d' . ' 00:00:00', strtotime($now));
        $timestamp = strtotime($this->timezone->date()->format($start)) + ((60*60) * 7);
        $start = date('Y-m-d H:i:s', $timestamp);

        $end = date('Y-m-d' . ' 23:59:59', strtotime($now));
        $timestamp = strtotime($this->timezone->date()->format($end)) + ((60*60) * 7);
        $end = date('Y-m-d H:i:s', $timestamp);

        $collection->addFieldToFilter('main_table.' . ReservationInterface::FLAG, ['neq' => ReservationInterface::FLAG_CANCEL]);
        $collection->addFieldToFilter(ReservationInterface::CUSTOMER_ID, $customerId);
        $collection->getSelect()->where('
        (
            (reservation_date_submit >= " ' . $start . ' " AND reservation_date_submit <= " ' . $end . ' ")
            OR reservation_date_submit is null
        )');
        $collection->getSelect()->join(array('items' => ReservationItemInterface::TABLE_NAME), 'main_table.id= items.reservation_id');

        return $collection;
    }

    /**
     * generate hours option array
     *
     * @param string $sourceCode
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array
     */
    public function generateHoursOption(string $sourceCode, \Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $this->logger->info(__('-------------- start generateHoursOption -----------'));
        $option = [];
        $hours = $this->configModel->getProductReservationHours($product, $sourceCode);
        $this->logger->info(__('run ' . __FUNCTION__ . '. array $hours = ' . json_encode($hours)));
        $this->logger->info(__('run ' . __FUNCTION__ . '. $sourceCode = ' . $sourceCode));
        $openTime = $this->sourceAttribute->getOpenTime($sourceCode);
        $this->logger->info(__('run ' . __FUNCTION__ . '. $openTime = ' . $openTime));
        $closeTime = $this->sourceAttribute->getCloseTime($sourceCode);
        $this->logger->info(__('run ' . __FUNCTION__ . '. $closeTime = ' . $closeTime));

        $openTimeX = strtotime($openTime);
        $closeTimeX = strtotime($closeTime);

        $interval = $hours['value'] * 60;
        $this->logger->info(__('run ' . __FUNCTION__ . '. $interval = ' . $interval));

        $new = $openTimeX;
        $start = $openTime;
        $x = 1;

        $data['label'] = $start . ' - ' . $closeTime;
        $data['value'] = $start . '-' . $closeTime;
        $option[] = $data;

        if($interval > 0) {
            $option = [];

            while ($x <= 24) {
                $addMinute = strtotime("+" . $interval . " minutes", $new);
                $datetime = date('Y-m-d H:i', $addMinute);
                $time = date('H:i', strtotime($datetime));
                $new = strtotime($datetime);

                if($new > $closeTimeX) {
                    $data['label'] = $start . ' - ' . $closeTime;
                    $data['value'] = $start . '-' . $closeTime;
                    $option[] = $data;
                    break;
                }

                $data['label'] = $start . ' - ' . $time;
                $data['value'] = $start . '-' . $time;
                $option[] = $data;

                $x++;
                $start = $time;
            }
        }

        $this->logger->info(__('run ' . __FUNCTION__ . '. $option = ' . json_encode($option)));

        $this->logger->info(__('-------------- end generateHoursOption -----------'));

        return $option;
    }

    /**
     * generate date config for reservation date interval
     *
     * @param string $sourceCode
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array
     */
    public function generateDateConfig(string $sourceCode, \Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $config = [];
        $days = $this->configModel->getDateConfig($product, $sourceCode);
        $this->logger->info(__('run ' . __FUNCTION__ . '. $days = ' . json_encode($days)));

        $now = date('Y-m-d');
        $max = date('Y-m-d');

        if(!empty($days)) {
            $added = strtotime("+" . $days['value'] . " days", strtotime($now));
            $max = date('Y-m-d', $added);
        }

        $config['min'] = $now;
        $config['max'] = $max;

        $this->logger->info(__('run ' . __FUNCTION__ . '. $config = ' . json_encode($config)));

        return $config;
    }
}
