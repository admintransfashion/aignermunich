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
namespace Trans\Reservation\Controller\Cart;

use Trans\Reservation\Api\Data\ReservationInterface;
use Trans\Reservation\Api\Data\ReservationConfigInterface;
use Trans\Reservation\Api\Data\ReservationItemInterface;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Submit
 */
class Submit extends \Magento\Framework\App\Action\Action
{
	/**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\InventoryApi\Api\SourceItemsSaveInterface
     */
    protected $sourceItemSave;

    /**
     * @var \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory
     */
    protected $sourceItem;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $userFactory;

    /**
     * @var \Trans\Reservation\Api\Data\SourceAttributeInterface
     */
    protected $sourceAttribute;

    /**
     * @var \Trans\Reservation\Model\ReservationItem
     */
    protected $reservationItemModel;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
	 * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
	 */
	protected $reservationItemRepository;

	/**
     * @var \Trans\Reservation\Api\ReservationAttributeRepositoryInterface
     */
    protected $reservationAttribute;

    /**
	 * @var \Trans\Reservation\Helper\Data
	 */
	protected $dataHelper;

	/**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Trans\CatalogMultisource\Helper\SourceItem
     */
    protected $sourceItemHelper;

    /**
     * @var \Trans\Core\Helper\Email
     */
    protected $emailHelper;

    /**
     * @var \Trans\Core\Helper\GoogleCalendarApi
     */
    protected $googleCalendar;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationCheck;

    /**
     * @var \Trans\Reservation\Api\UserStoreRepositoryInterface
     */
    protected $userStoreRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $datetime;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave
     * @param \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Validator $formKeyValidator
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\Reservation\Api\ReservationAttributeRepositoryInterface $reservationAttribute
     * @param \Trans\Reservation\Model\ReservationItem $reservationItemModel
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Config $configHelper
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     * @param \Trans\Core\Helper\Email $emailHelper
     * @param \Trans\Core\Helper\GoogleCalendarApi $googleCalendar
     * @param \Trans\Reservation\Helper\Reservation $reservationCheck
     * @param \Trans\Reservation\Api\UserStoreRepositoryInterface $userStoreRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\InventoryApi\Api\SourceItemsSaveInterface $sourceItemSave,
        \Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory $sourceItem,
        \Magento\Customer\Model\Session $customerSession,
        Validator $formKeyValidator,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\User\Model\UserFactory $userFactory,
        \Trans\Reservation\Api\Data\SourceAttributeInterface $sourceAttribute,
        \Trans\Reservation\Api\ReservationAttributeRepositoryInterface $reservationAttribute,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\Reservation\Model\ReservationItem $reservationItemModel,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper,
        \Trans\Core\Helper\Email $emailHelper,
        \Trans\Core\Helper\GoogleCalendarApi $googleCalendar,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Config $configHelper,
        \Trans\Reservation\Helper\Reservation $reservationCheck,
        \Trans\Reservation\Api\UserStoreRepositoryInterface $userStoreRepository
    ) {
        parent::__construct($context);
        $this->productRepository = $productRepository;
        $this->sourceItemSave = $sourceItemSave;
        $this->sourceItem = $sourceItem;
        $this->timezone = $timezone;
        $this->userFactory = $userFactory;
        $this->sourceAttribute = $sourceAttribute;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerSession = $customerSession;
        $this->reservationAttribute = $reservationAttribute;
        $this->reservationRepository = $reservationRepository;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->reservationItemModel = $reservationItemModel;
        $this->sourceItemHelper = $sourceItemHelper;
        $this->reservationCheck = $reservationCheck;
        $this->configHelper = $configHelper;
        $this->dataHelper = $dataHelper;
        $this->emailHelper = $emailHelper;
        $this->userStoreRepository = $userStoreRepository;
        $this->googleCalendar = $googleCalendar;

        $this->logger = $dataHelper->getLogger();
        $this->datetime = $dataHelper->getDatetime();
    }

    /**
     * Add reservation action
     *
     * @return \Trans\Reservation\Api\Data\ReservationInterface
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if(!$this->dataHelper->isCustomerLoggedIn()) {
            $this->messageManager->addErrorMessage(__('You have to login or register first.'));
            return $resultRedirect->setPath('*/*/');
        }

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/');
        }

        try {
            $postData = $this->getRequest()->getPost();
            $reservationId = $this->dataHelper->getSessionReservationId();
            $reservation = $this->reservationRepository->getById($reservationId);

            $sourceCode = null;

            $collection = $this->reservationCheck->getTodayDataCollection();

            if(!$this->configHelper->isEnableMaxQtyConfig()) {
                $size = $collection->getSize();
                $globalMaxQty = (int)$this->configHelper->getMaxQty();

                if($size > $globalMaxQty) {
                    $this->messageManager->addErrorMessage(__('Submit reservation failed. Maximum product item is %1.', $globalMaxQty));
                    $resultRedirect->setPath($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
            }

            if(!$this->reservationCheck->checkReservationLimit($collection, $sourceCode)) {
                $this->messageManager->addErrorMessage(__('You have been reach limit reservation for today. You can make reservation again tomorrow.'));
                $resultRedirect->setPath($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            $reservation->setFlag(ReservationInterface::FLAG_SUBMIT);

            if(!$this->configHelper->isEnableMultiStore()) {
                if($this->reservationCheck->isDifferentStore($sourceCode, $collection)) {
                    $this->messageManager->addErrorMessage(__('You cannot make reservation for product from different store.'));
                    $resultRedirect->setPath($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
            }

            $reservationItems = $reservation->getItems();

            $reservationNumber = $this->generateReservationNumber();

            $items = [];
            $count = 0;
            foreach($reservationItems as $item)
            {
                $productId = $item->getProductId();
                try {
                    $product = $this->productRepository->getById($productId);
                } catch (NoSuchEntityException $e) {
                    continue;
                }

                try {
                    $sku = $product->getSku();
                    $storeCode = $postData['store_code'][$sku];

                    if(isset($postData['reservation_date'][$sku])) {
                        $reservationDate = $postData['reservation_date'][$sku];
                    } else {
                        $reservationDate = $this->getDate(null, 'Y-m-d', null);
                    }

                    $reservationTime = $this->timezone->date()->format('H:i');;
                    $startTime = $reservationTime;
                    $startDate = $reservationDate;

                    $endDate = $reservationDate;
                    $endTime = $this->getDate($startDate, 'H:i');

                    if($this->configHelper->isExpireNextDay()) {
                        $endTime = $this->configHelper->getExpireTime();
                        $endDate = $this->getDate($startDate, 'Y-m-d');
                    }

                    $sourceItems = $this->sourceItemHelper->getSourceItem($sku, $storeCode);

                    $qty = 0;
                    foreach ($sourceItems as $sourceItem) {
                        $qty = (int)($sourceItem->getQuantity() - $item->getQty());
                    }

                    if($this->reservationCheck->isQtyReachingBuffer($productId, $storeCode, $item->getQty())) {
                        $this->messageManager->addErrorMessage(__('Reservation process failed, Qty of product(s) with SKU (%1) is insufficient stock.', $sku));
                        $resultRedirect->setPath($this->_redirect->getRefererUrl());
                        return $resultRedirect;
                    }

                    if($qty >= 0) {
                        $sourceItem = $this->sourceItem->create();
                        $sourceItem->setSku($sku);
                        $sourceItem->setSourceCode($storeCode);
                        $sourceItem->setQuantity($qty);
                        $sourceItem->setStatus(1);

                        $items[] = $sourceItem;
                    }

                    $item->setReservationDateStart($startDate);
                    $item->setReservationTimeStart($startTime);
                    $item->setReservationDateEnd($endDate);
                    $item->setReservationTimeEnd($endTime);

                    $item->setFlag(ReservationItemInterface::FLAG_SUBMIT);
                    $item->setReferenceNumber($this->generateReservationNumber($storeCode, $reservationNumber));
                    $item->setSourceCode($storeCode);
                    $item->setBuffer($this->reservationCheck->getInstances(ReservationConfigInterface::CONFIG_BUFFER . $productId . $storeCode));
                    $item->setMaxqty($this->reservationCheck->getInstances(ReservationConfigInterface::CONFIG_MAXQTY . $productId . $storeCode));

                    $item->setBasePrice($product->getPrice());
                    $item->setFinalPrice($product->getFinalPrice());

                    $this->reservationItemRepository->save($item);
                    $count++;
                } catch (NoSuchEntityException $e) {
                    $this->reservationItemRepository->delete($item);
                    continue;
                } catch (\Exception $e) {
                    continue;
                }
            }

            if(!empty($items)) {
                $this->sourceItemSave->execute($items);
            }

            if($reservationId){
                $reservation->setReservationNumber(\Trans\Reservation\Api\Data\ReservationInterface::PREFIX_STORE_CODE . '-' . $reservationNumber);
                $reservation->setReservationDateSubmit($this->timezone->date());
                $this->reservationRepository->save($reservation);

                $this->sendEmailNotif($reservation);
            }
        } catch (\Exception $e) {
            $this->logger->info('Error submit reservation. Message = ' . $e->getMessage());
            $this->messageManager->addErrorMessage(__('Reservation process failed, a system error occurred.'));
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $this->messageManager->addSuccessMessage(__('Reservation success.'));
        $resultRedirect->setPath('reservation/cart/success');

        return $resultRedirect;
    }

    /**
     * get date
     *
     * @return datetime
     */
    protected function getDate($date = null, $format = 'Y-m-d H:i:s', $additional = 1)
    {
        if($additional) {
            return date($format, strtotime($date. ' + ' . $additional . ' days'));
        }

        $date = $this->timezone->date();
        $date = $date->format($format);

        return $date;
    }

    /**
     * generate reservation number
     *
     * @param string $sourceCode
     * @param string $number
     * @return string
     */
    protected function generateReservationNumber(string $sourceCode = null, $number = null)
    {
        $delimiter = $this->configHelper->getReservationNumberDelimiter();
        if($number != null) {
            $expl = explode($delimiter, $number);
            $number = end($expl);
        } else {
            $number = $this->reservationAttribute->generateReservationNumber();
        }

        if($this->configHelper->isStorecodePrefix()) {
            if($sourceCode) {
                $storeCode = strtoupper($sourceCode);
                $number = $storeCode . $delimiter . $number;
            }
        }

        if($this->configHelper->getReservationNumberCode()) {
            $prefix = $this->configHelper->getReservationNumberCode() . $delimiter;
            if($sourceCode) {
                $countPrefix = strlen($prefix);
                $countCode = strlen($sourceCode);
                $len = $countCode - $countPrefix;

                if($len > 0) {
                    $substr = substr($sourceCode, 0, $countPrefix);
                    if($substr == $prefix) {
                        $prefix = '';
                    }
                }
            }

            $number = $prefix . $number;
        }

        return $number;
    }

    /**
     * Send email notification
     *
     * @param string $emailTo
     * @param array $emptyProduct
     * @return void
     */
    public function sendEmailNotif($reservation)
    {
        try {
            // $source = $this->dataHelper->getSourceRepository()->get($reservation->getSourceCode());
            $customer = $this->customerSession->getCustomer();
            $emailTo = $customer->getEmail();
            $template = 'reservation_notification';
            $var['reservation'] = $reservation;
            $var['placed_date'] = $this->dataHelper->changeDateFormat($reservation->getReservationDateSubmit(), 'F d, Y');
            $var['customer'] = $customer;
            $this->emailHelper->sendEmail($emailTo, $var, $template);

            $this->incomingReservationEmail($reservation);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * send email incoming reservation to store sales assistance
     *
     * @param \Trans\Reservation\Api\Data\ReservationInterface
     * @return void
     */
    public function incomingReservationEmail($reservation)
    {
        try {
            $customer = $this->customerSession->getCustomer();
            $template = 'reservation_sales_notification';
            $var['reservation'] = $reservation;
            $var['placed_date'] = $this->dataHelper->changeDateFormat($reservation->getReservationDateSubmit(), 'F d, Y');
            $var['customer'] = $customer;
            $stores = $this->reservationCheck->getReservationStores($reservation);

            foreach($stores as $store) {
                $storeData = $this->dataHelper->getSourceByCode($store);
                $userStores = $this->userStoreRepository->getByStoreCode($store);
                $var['storeName'] = $storeData->getName();
                $var['is_sales'] = 1;
                $var['sales_store'] = $store;
                foreach($userStores as $userStore) {
                    $userId = $userStore->getUserId();

                    $user = $this->userFactory->create()->load($userId);
                    $emailTo = $user->getEmail();

                    if($emailTo) {
                        $this->emailHelper->sendEmail($emailTo, $var, $template);
                    }
                }
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
