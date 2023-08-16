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

namespace Trans\Reservation\Plugin\User\Controller\Adminhtml\User;

use Magento\User\Controller\Adminhtml\User\Save as UserSave;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Plugin class Save
 */
class Save
{
	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;

	/**
	 * @var \Magento\User\Api\Data\UserInterface
	 */
	protected $userInterface;

	/**
	 * @var \Magento\Framework\Controller\Result\RedirectFactory
	 */
	protected $resultRedirectFactory;

	/**
	 * @var \Trans\Reservation\Helper\Config
	 */
	protected $configHelper;

	/**
	 * @var \Trans\Reservation\Api\UserStoreRepositoryInterface
	 */
	protected $userStoreRepository;

	/**
	 * @var \Trans\Reservation\Api\Data\UserStoreInterfaceFactory
	 */
	protected $userStore;

	/**
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\User\Api\Data\UserInterface $userInterface
	 * @param \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
	 * @param \Trans\Reservation\Helper\Config $configHelper
	 * @param \Trans\Reservation\Api\UserStoreRepositoryInterface $userStoreRepository
	 * @param \Trans\Reservation\Api\Data\UserStoreInterfaceFactory $userStore
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
		\Magento\User\Api\Data\UserInterface $userInterface,
		\Trans\Reservation\Helper\Config $configHelper,
		\Trans\Reservation\Api\UserStoreRepositoryInterface $userStoreRepository,
		\Trans\Reservation\Api\Data\UserStoreInterfaceFactory $userStore
	)
	{
		$this->request = $request;
		$this->userInterface = $userInterface;
		$this->resultRedirectFactory = $resultRedirectFactory;
		$this->configHelper = $configHelper;
		$this->userStoreRepository = $userStoreRepository;
		$this->userStore = $userStore;
	}

	/**
	 * Checking is sales module disable
	 *
	 * @param UserSave $subject
	 * @param callable $proceed
	 * @return bool
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	// public function afterExecute(UserSave $subject, $result)
	public function aroundExecute(UserSave $subject, callable $proceed)
	{
		$params = $this->request->getParams();

		$result = $proceed();

		if(isset($params['user_id'])) {
			$userId = $params['user_id'];
		} else {
			$user = $this->userInterface->loadByUsername($params['username']);
			$userId = $user->getId();
		}

		try {
			$userStores = $this->userStoreRepository->getByUserId($userId);

			foreach($userStores as $userStore) {
				$this->userStoreRepository->delete($userStore);
			}

		} catch (NoSuchEntityException $e) {

		}

		if(isset($params['stores']) && !empty($params['stores'])) {
			$stores = $params['stores'];
			$this->saveStores($stores, $userId);
		}

		return $result;
	}

	/**
	 * Save user store
	 *
	 * @param array $stores
	 * @param $userId
	 * @return bool
	 */
	protected function saveStores(array $stores, $userId)
	{
		try {
			foreach($stores as $store) {
				$userStore = $this->userStore->create();
				$userStore->setUserId($userId);
				$userStore->setStoreCode($store);

				$this->userStoreRepository->save($userStore);
			}

			return true;
		} catch (\Exception $e) {
			return false;
		}
	}
}
