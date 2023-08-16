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

namespace Trans\Newsletter\Plugin\Newsletter\Controller\Subscriber;

use Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Newsletter\Controller\Subscriber\NewAction as MageNewAction;

/**
 * Plugin class NewAction
 */
class NewAction
{
	/**
	 * @var \Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

	/**
	 * @var \Magento\Framework\App\Response\RedirectInterface
	 */
	protected $redirect;

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
	 * @param \Magento\Framework\Message\ManagerInterface $messageManager
	 * @param \Magento\Framework\App\Response\RedirectInterface $redirect
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Framework\App\Response\Http $response
	 * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
	 * @param \Trans\Newsletter\Api\NewsletterAdditionalRepositoryInterface $newsletterAdditionalRepo
	 * @param \Trans\Newsletter\Api\Data\NewsletterAdditionalInterfaceFactory $newsletterAdditional
	 */
	public function __construct(
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
		\Trans\Newsletter\Api\NewsletterAdditionalRepositoryInterface $newsletterAdditionalRepo,
		\Trans\Newsletter\Api\Data\NewsletterAdditionalInterfaceFactory $newsletterAdditional
	)
	{
		$this->messageManager = $messageManager;
		$this->redirect = $redirect;
		$this->request = $request;
		$this->subscriberFactory = $subscriberFactory;
		$this->newsletterAdditionalRepo = $newsletterAdditionalRepo;
		$this->newsletterAdditional = $newsletterAdditional;
	}

	/**
	 * @param MageNewAction $subject
	 * @param $result
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	public function afterExecute(MageNewAction $subject, $result)
	{
		$subscriber = $this->subscriberFactory->create()->loadByEmail($this->request->getParam('email'));

		if($subscriber->getId()) {
			$subscriberId = $subscriber->getId();

			try {
				$additional = $this->newsletterAdditionalRepo->getBySubscriberId($subscriberId);
			} catch (NoSuchEntityException $e) {
				$additional = $this->newsletterAdditional->create();
			} catch (\Exception $e) {
				return $result;
			}

			$additional->setSubscriberId($subscriberId);

			$subscribeCategory = $this->request->getParam('subscribe');
			if($subscribeCategory) {
                $additional->setSubscribeCategory($subscribeCategory);
				switch ($subscribeCategory) {
                    case NewsletterAdditionalInterface::SUBSCRIBE_CATEGORY_ALL:
                        $additional->setSubscribeMen(1);
                        $additional->setSubscribeWomen(1);
                        break;

					case NewsletterAdditionalInterface::SUBSCRIBE_CATEGORY_MEN:
                        $additional->setSubscribeMen(1);
                        $additional->setSubscribeWomen(0);
						break;

					case NewsletterAdditionalInterface::SUBSCRIBE_CATEGORY_WOMEN:
                        $additional->setSubscribeMen(0);
                        $additional->setSubscribeWomen(1);
						break;
				}
			}

			try {
				$this->newsletterAdditionalRepo->save($additional);
			} catch (\Exception $e) {
			}

			return $result;
		}
	}
}
