<?php
/**
 * @category Trans
 * @package  Trans_Newsletter
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Newsletter\Observer;

use Magento\Framework\Event\ObserverInterface;
use Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class SubscriberCreatePost
 */
class SubscriberCreatePost implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Trans\Newsletter\Api\NewsletterAdditionalRepositoryInterface
     */
    protected $newsletterAdditionalRepo;

    /**
     * @var \Trans\Newsletter\Api\Data\NewsletterAdditionalInterfaceFactory
     */
    protected $newsletterAdditional;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Trans\Newsletter\Api\NewsletterAdditionalRepositoryInterface $newsletterAdditionalRepo
     * @param \Trans\Newsletter\Api\Data\NewsletterAdditionalInterfaceFactory $newsletterAdditional
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Trans\Newsletter\Api\NewsletterAdditionalRepositoryInterface $newsletterAdditionalRepo,
        \Trans\Newsletter\Api\Data\NewsletterAdditionalInterfaceFactory $newsletterAdditional
    )
    {
        $this->request = $request;
        $this->subscriberFactory = $subscriberFactory;
        $this->newsletterAdditionalRepo = $newsletterAdditionalRepo;
        $this->newsletterAdditional = $newsletterAdditional;
    }

    /**
     * add subscriber additional data
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer(); //Get customer

        $subscriber = $this->subscriberFactory->create()->loadByEmail($customer->getEmail());

        if($subscriber->getId()) {
            $subscriberId = $subscriber->getId();

            try {
                $additional = $this->newsletterAdditionalRepo->getBySubscriberId($subscriberId);
            } catch (NoSuchEntityException $e) {
                $additional = $this->newsletterAdditional->create();
            } catch (\Exception $e) {
                $additional = false;
            }

            if($additional != false) {
                $additional->setSubscriberId($subscriberId);
                $additional->setSubscribeCategory(NewsletterAdditionalInterface::SUBSCRIBE_CATEGORY_ALL);

                try {
                    $this->newsletterAdditionalRepo->save($additional);
                } catch (CouldNotSaveException $e) {

                }
            }
        }
    }
}
