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
 * Class ForceAddToCart
 */
class ForceAddToCart extends \Magento\Framework\App\Action\Action
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
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollection;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $configurableModel;

    /**
     * @var \Trans\Reservation\Model\ReservationConfig
     */
    protected $configModel;

    /**
	 * @var \Trans\Reservation\Api\ReservationRepositoryInterface
	 */
	protected $reservationRepository;

	/**
	 * @var \Trans\Reservation\Api\ReservationItemRepositoryInterface
	 */
	protected $reservationItemRepository;

	/**
	 * @var \Trans\Reservation\Helper\Data
	 */
	protected $dataHelper;

	/**
     * @var \Trans\Reservation\Helper\Reservation
     */
    protected $reservationCheck;

    /**
     * @var \Trans\Reservation\Logger\Logger
     */
    protected $logger;

    /**
     * @var int
     */
    protected $qty = 0;

    /**
     * @var string
     */
    protected $sourceCode = '';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Trans\Gtm\Helper\Data
     */
    protected $gtmHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableModel
     * @param \Trans\Reservation\Api\Data\ReservationInterfaceFactory $reservationFactory
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository
     * @param \Trans\Reservation\Helper\Data $dataHelper
     * @param \Trans\Reservation\Helper\Config $configHelper
     * @param \Trans\Reservation\Helper\Reservation $reservationCheck
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Trans\Gtm\Helper\Data $gtmHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableModel,
        \Trans\Reservation\Api\Data\ReservationInterfaceFactory $reservationFactory,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Api\ReservationItemRepositoryInterface $reservationItemRepository,
        \Trans\Reservation\Helper\Data $dataHelper,
        \Trans\Reservation\Helper\Config $configHelper,
        \Trans\Reservation\Helper\Reservation $reservationCheck,
        \Magento\Customer\Model\Session $customerSession,
        \Trans\Gtm\Helper\Data $gtmHelper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->configurableModel = $configurableModel;
        $this->pageFactory = $pageFactory;
        $this->reservationFactory = $reservationFactory;
        $this->reservationRepository = $reservationRepository;
        $this->reservationItemRepository = $reservationItemRepository;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
        $this->reservationCheck = $reservationCheck;
        $this->logger = $this->dataHelper->getLogger();
        $this->customerSession = $customerSession;
        $this->gtmHelper = $gtmHelper;
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

        if(!$this->configHelper->isGuestAddToCart() && !$this->dataHelper->isCustomerLoggedIn()) {
            $return['status'] = 'error';
            $return['redirect'] = 'customer/account/login';
            $return['message'] = __('You have to login/registration first.');

            $result->setData($return);
            return $result;
        }

        $postData = $this->getRequest()->getPost();

        $qty = $this->configHelper->getDefaultQty();

        if(isset($postData['qty'])) {
            $qty = $postData['qty'];
        }

        $this->qty = $qty;

        $optionSelected = $postData['optionSelected'];
        $sourceCode = null;

        if(isset($postData['sourceCode'])) {
            $sourceCode = $postData['sourceCode'];
        }

        $this->sourceCode = $sourceCode;

        $collection = $this->reservationCheck->getTodayDataCollection();

        if(!$this->configHelper->isEnableMultiStore()) {
            if($this->reservationCheck->isDifferentStore($sourceCode, $collection)) {
                $return['status'] = 'error';
                $return['message'] = __('You cannot make reservation for product from different store.');
                $result->setData($return);
                $this->logger->info('==================== End AddToReserveCart ====================');
                return $result;
            }
        }

        try {
            if(isset($postData['childId'])) {
                $product = $this->productRepository->getById($postData['childId']);
            } else {
                $product = $this->productRepository->get($postData['sku']);
            }

            if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                if($optionSelected == null) {
                    $result->setData(['status' => 'error' ,'message' => __('You have to select product varian first.')]);
                    return $result;
                }

                $attributeId = $postData['attributeId'];
                $attributesInfo = array($attributeId => $optionSelected);

                $product = $this->configurableModel->getProductByAttributes($attributesInfo, $product);
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

        if($this->dataHelper->isCustomerLoggedIn() && $limit = $this->reservationCheck->isQtyReachMax($collection, $qty, $product, $sourceCode)) { /** if true */
            $return['status'] = 'error';
            $return['message'] = __('You are only allowed to reserve 2 items per day.');

            $result->setData($return);
            $this->logger->info('==================== End AddToReserveCart ====================');
            return $result;
        }

        $return = $this->processingResult($postData, $product);

        $result->setData($return);
        $this->logger->info('==================== End AddToReserveCart ====================');
        return $result;
    }

    /**
     * processing the result
     *
     * @param \Zend\Stdlib\ParametersInterface|mixed $postRequest
     * @param \Magento\Catalog\Api\ProductInterface $product
     * @return array
     */
    protected function processingResult($postRequest, \Magento\Catalog\Api\Data\ProductInterface $product)
    {
        try {
            $reservation = $this->getReservation($postRequest);
            $this->reservationRepository->save($reservation);
            $reservationId = $reservation->getId();

            $productId = $product->getId();

            $item = $this->reservationItemRepository->get($reservationId, $productId);
            $item->setReservationId($reservationId);
            $item->setFlag(ReservationItemInterface::FLAG_NEW);
            $item->setQty($this->qty);
            $item->setProductId($productId);

            $this->reservationItemRepository->save($item);

            $getCustomerId = $this->gtmHelper->getCurrentCustomerId();

            $return['status'] = 'success';
            $return['redirect'] = 'reservation/cart/index';
            $return['message'] = __('Add product to reservation cart success.');

            $resultPage = $this->pageFactory->create();
            $block = $resultPage->getLayout()
                ->createBlock('Trans\Reservation\Block\Cart\Grid')
                ->setTemplate('Trans_Reservation::product/reservationList.phtml')
                ->toHtml();

            $return['output'] = $block;
            $return['product_name'] = $product->getName();
            $return['product_id'] = $product->getSku();
            $return['price'] = $product->getFinalPrice();
            $return['qty'] = $this->qty;
            $return['size'] = $this->gtmHelper->getProductAttributeValue($product->getId(), 'size');
            $return['color'] = $this->gtmHelper->getProductAttributeValue($product->getId(), 'color');
            $return['id'] = $getCustomerId;
            $return['product_for'] = $this->gtmHelper->getCategoryC0NamesByProduct($product);
            $return['category'] = $this->gtmHelper->getCategoryNamesByProduct($product);

        } catch (NoSuchEntityException $e) {
            $return['status'] = 'error';
            $return['message'] = __('Reservation process failed, a system error occurred.');
            $this->logger->info('Add product to reservat cart error. Message = ' . $e->getMessage());
        } catch (\Exception $e) {
            $return['status'] = 'error';
            $return['message'] = __('Reservation process failed, a system error occurred.');
            $this->logger->info('Add product to reservat cart error. Message = ' . $e->getMessage());
        }

        return $return;
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
            $reservation->setFlag(ReservationInterface::FLAG_NEW);

            if($customerId) {
                $reservation->setCustomerId($customerId);
            }

            $this->reservationRepository->save($reservation);

            $reservationId = $reservation->getId();
            $this->dataHelper->createReservationIdToSession($reservationId);
        } else {
            $reservation = $this->reservationRepository->getById($reservationId);
        }

        return $reservation;
    }

    /**
     * get product categories
     *
     * @param \Magento\Catalog\Api\ProductInterface $product
     * @return array
     */
    protected function getProductCategories(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $categoryIds = $product->getCategoryIds();
        $dataCategory = [];
        $dataParent = [];

        foreach($categoryIds as $entityId) {
            try {
                $category = $this->categoryRepository->get($entityId);
                $dataCategory[] = $category->getName();

                $parent = $category;
                if($category->getLevel() > 2) {
                    $parent = $this->getParentSecondLevel($category);
                }

                if($parent instanceof \Magento\Catalog\Api\Data\CategoryInterface) {
                    $dataParent[] = $parent->getName();
                }
            } catch (NoSuchEntityException $e) {
            }
        }

        $data['category'] = implode(', ', $dataCategory);
        $data['product_for'] = implode(', ', $dataParent);

        return $data;
    }

    /**
     * get category parent second level
     *
     * @param \Magento\Catalog\Api\Data\CategoryInterface $category
     * @return \Magento\Catalog\Api\Data\CategoryInterface
     */
    protected function getParentSecondLevel(\Magento\Catalog\Api\Data\CategoryInterface $category)
    {
        $path = $category->getPath();
        $pathArray = explode('/', $path);

        $parentId = $pathArray[2];
        try {
            $parent = $this->categoryRepository->get($parentId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        return $parent;
    }

}
