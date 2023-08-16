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
 * Class Sourcedata
 */
class Sourcedata extends Action
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
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Trans\Reservation\Api\ReservationRepositoryInterface
     */
    protected $reservationRepository;

    /**
     * @var \Trans\Reservation\Block\Cart\Grid
     */
    protected $gridBlock;

    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository
     * @param \Trans\Reservation\Block\Cart\Grid $gridBlock
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Trans\Reservation\Api\ReservationRepositoryInterface $reservationRepository,
        \Trans\Reservation\Block\Cart\Grid $gridBlock
    )
    {

        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->productRepository = $productRepository;
        $this->reservationRepository = $reservationRepository;
        $this->gridBlock = $gridBlock;

        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            $result->setData(['output' => __('You have to login or register first.')]);
            return $result;
        }

        $resultPage = $this->resultPageFactory->create();
        $reservationId = $this->getRequest()->getParam('reservationId');

        try {
            $reservation = $this->reservationRepository->getById($reservationId);
        } catch (NoSuchEntityException $e) {
            $data = false;
            $result->setData($data);
            return $result;
        }

        $data = [];
        $items = $reservation->getItems();

        foreach($items as $item) {
            try {
                $product = $this->productRepository->getById($item->getProductId());
            } catch (NoSuchEntityException $e) {
                continue;
            }

            $grid = $this->gridBlock->getFilteredProductSources($product);
            $data[$product->getSku()] = $grid;
        }
        $data['size'] = count($data);

        $result->setData($data);
        return $result;
    }

}
