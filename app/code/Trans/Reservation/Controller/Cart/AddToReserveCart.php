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
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AddToReserveCart
 */
class AddToReserveCart extends \Magento\Framework\App\Action\Action
{
	/**
	 * @var \Trans\Reservation\Api\Data\ReservationInterfaceFactory
	 */
	protected $reservationFactory;

	/**
	 * @var \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;

	/**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Trans\CatalogMultisource\Helper\SourceItem
     */
    protected $sourceItemHelper;

    /**
     * @var \Trans\Reservation\Model\ReservationConfig
     */
    protected $configModel;

    /**
	 * @var \Trans\Reservation\Api\ReservationRepositoryInterface
	 */
	protected $reservationRepository;

	/**
	 * @var \Trans\Reservation\Api\Data\ReservationItemInterfaceFactory
	 */
	protected $reservationItemFactory;

	/**
	 * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
	 */
	protected $reservationItemRepository;

	/**
	 * @var \Trans\Reservation\Helper\Data
	 */
	protected $dataHelper;

	/**
     * @var \Trans\Reservation\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationCheck;

    /**
     * @var \Trans\Reservation\Logger\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Trans\Reservation\Model\ReservationConfig $configModel
     * @param \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper
     * @param \Trans\Reservation\Api\Data\ReservationInterfaceFactory $reservationFactory
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\Data\ReservationItemInterfaceFactory $reservationItemFactory
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Config $configHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationCheck
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Trans\CatalogMultisource\Helper\SourceItem $sourceItemHelper,
        \Trans\Reservation\Model\ReservationConfig $configModel,
        \Trans\Reservation\Api\Data\ReservationInterfaceFactory $reservationFactory,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\Data\ReservationItemInterfaceFactory $reservationItemFactory,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Config $configHelper,
        \Trans\Reservation\Helper\Reservation $reservationCheck
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productModel = $productModel;
        $this->productRepository = $productRepository;
        $this->timezone = $timezone;
        $this->configModel = $configModel;
        $this->sourceItemHelper = $sourceItemHelper;
        $this->reservationFactory = $reservationFactory;
        $this->reservationRepository = $reservationRepository;
        $this->reservationItemFactory = $reservationItemFactory;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
        $this->reservationCheck = $reservationCheck;
        $this->logger = $this->dataHelper->getLogger();
    }

    /**
     * Add reservation action
     *
     * @return \Trans\Reservation\Api\Data\ReservationInterface
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function execute()
    {
        $this->logger->info('==================== Start AddToReserveCart ====================');
        $result = $this->resultJsonFactory->create();
    	if(!$this->dataHelper->isCustomerLoggedIn()) {
            $return['status'] = 'error';
            $return['redirect'] = 'customer/account/login';
            $return['message'] = __('You have to login/registration first.');

            $result->setData($return);
            return $result;
    	}

    	$postData = $this->getRequest()->getPost();
        $sourceCode = $postData['sourceCode'];
        $collection = $this->reservationCheck->getTodayDataCollection();

        if($this->reservationCheck->isDifferentStore($sourceCode, $collection)) {
            $return['status'] = 'error';
            $return['message'] = __('You cannot make reservation for product from different store.');
            $result->setData($return);
            $this->logger->info('==================== End AddToReserveCart ====================');
            return $result;
        }

        try {
            if(isset($postData['childId'])) {
                $product = $this->productRepository->getById($postData['childId']);
                $postData['sku'] = $product->getSku();
            } else {
                $product = $this->productRepository->get($postData['sku']);
            }
        } catch (NoSuchEntityException $e) {
            $return['status'] = 'error';
            $return['message'] = __('Reservation process failed, a system error occurred.');
            $this->logger->info('Add product to reservat cart error. Message = ' . $e->getMessage());

            $result->setData($return);
            $this->logger->info('==================== End AddToReserveCart ====================');
            return $result;
        } catch (\Exception $e) {
            $return['status'] = 'error';
            $return['message'] = __('Reservation process failed, a system error occurred.');
            $this->logger->info('Add product to reservat cart error. Message = ' . $e->getMessage());

            $result->setData($return);
            $this->logger->info('==================== End AddToReserveCart ====================');
            return $result;
        }

        // $qty = $postData['qty'];
        $qty = 1;

        if($this->reservationCheck->isQtyReachMax($collection, $qty, $product, $sourceCode)) { /** if true */
            $return['status'] = 'error';
            $return['message'] = __('Add product to reservation cart failed. You have been reach limit for product %1.', $product->getName());

            $result->setData($return);
            $this->logger->info('==================== End AddToReserveCart ====================');
            return $result;
        }

        // if(!$this->reservationCheck->checkLimitByProduct($collection, $product)) { /** if false */
        //     $return['status'] = 'error';
        //     $return['message'] = __('You have been reach limit reservation for today. You can make reservation again tomorrow.');

        //     $result->setData($return);
        //     $this->logger->info('==================== End AddToReserveCart ====================');
        //     return $result;
        // }

        try {
            $reservation = $this->getReservation($postData);
            $reservationId = $reservation->getId();

            if($sourceCode != $reservation->getSourceCode()) {
                if (count($reservation->getItems())) {
                    $error = false; /* error for reserve from different store */
                    if(count($reservation->getItems()) == 1) {
                        foreach($reservation->getItems() as $item) {
                            $productId = $item->getProductId();
                            try {
                                $this->productRepository->getById($productId);
                                $error = true;
                            } catch (NoSuchEntityException $e) {
                                $error = false;
                            }
                        }
                    }

                    if($error) {
                        $return['status'] = 'error';
                        $return['message'] = __('You cannot make reservation for product from different store.');
                        $result->setData($return);
                        $this->logger->info('==================== End AddToReserveCart ====================');
                        return $result;
                    }
                }

                $reservation->setSourceCode($sourceCode);
                $this->reservationRepository->save($reservation);
            }

            $productId = $this->productModel->getIdBySku($postData['sku']);

            // if($this->reservationItemRepository->isItemAddable($reservationId, $sourceCode, $productId, $qty)) {
            if(!$this->reservationCheck->isQtyReachingBuffer($productId, $sourceCode, $qty)) {
                /** if reaching buffer */
                $item = $this->reservationItemRepository->get($reservationId, $productId);
                $item->setReservationId($reservationId);
                $item->setFlag(ReservationItemInterface::FLAG_NEW);
                $item->setQty($qty);
                $item->setProductId($productId);
                $item->setBuffer($this->reservationCheck->getInstances(ReservationConfigInterface::CONFIG_BUFFER . $productId . $sourceCode));
                $item->setMaxqty($this->reservationCheck->getInstances(ReservationConfigInterface::CONFIG_MAXQTY . $productId . $sourceCode));

                $this->reservationItemRepository->save($item);

                $return['status'] = 'success';
                $return['redirect'] = 'reservation/cart/index';
                $return['message'] = __('Add product to reservation cart success.');

            } else {
                /* error product buffer */
                $return['status'] = 'error';
                $return['message'] = __('Reservation process failed. Insufficient number of product.');
            }
        } catch (NoSuchEntityException $e) {
            $return['status'] = 'error';
            $return['message'] = __('Reservation process failed, a system error occurred.');
            $this->logger->info('Add product to reservat cart error. Message = ' . $e->getMessage());
        } catch (\Exception $e) {
            $return['status'] = 'error';
            $return['message'] = __('Reservation process failed, a system error occurred.');
            $this->logger->info('Add product to reservat cart error. Message = ' . $e->getMessage());
        }

        $result->setData($return);
        $this->logger->info('==================== End AddToReserveCart ====================');
        return $result;

    }

    /**
     * Get reservation id
     *
     * @param array $postData
     * @return int
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    protected function getReservation($postData)
    {
    	$customerId = $this->dataHelper->getLoggedInCustomerId();
    	$reservationId = $this->dataHelper->getSessionReservationId();

        /** if session reservation id empty, create new reservation */
    	if($reservationId === null) {
    		$reservation = $this->reservationFactory->create();
    		$reservation->setSourceCode($postData['sourceCode']);
    		$reservation->setCustomerId($customerId);

            $this->reservationRepository->save($reservation);

    		$reservationId = $reservation->getId();
			$this->dataHelper->createReservationIdToSession($reservationId);
    	} else {
            $reservation = $this->reservationRepository->getById($reservationId);
        }

    	return $reservation;
    }
}
