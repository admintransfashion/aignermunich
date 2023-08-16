<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Customer
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Customer\Controller\Account;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Escaper;
use Magento\Customer\Api\AccountManagementInterface;
use Trans\Customer\Helper\Config as ConfigHelper;

class ChangePasswordPost extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customerModel;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param Escaper|null $escaper
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        ?Escaper $escaper = null,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        ConfigHelper $configHelper
    ) {
        $this->request = $request;
        $this->formKeyValidator = $formKeyValidator;
        $this->escaper = $escaper ?: ObjectManager::getInstance()->get(Escaper::class);
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerSession = $customerSession;
        $this->customerModel = $customerModel;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        \Magento\Framework\App\RequestInterface $request
    ): ?InvalidRequestException {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/account/changepassword');

        return new InvalidRequestException(
            $resultRedirect,
            [new Phrase('Invalid Form Key. Please refresh the page.')]
        );
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(\Magento\Framework\App\RequestInterface $request): ?bool
    {
        return null;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if(! $this->customerSession->isLoggedIn()){
            $this->messageManager->addErrorMessage(__('You have to login to change your pasword'));
            return $resultRedirect->setPath('customer/account/login');
        }

        $validFormKey = $this->formKeyValidator->validate($this->request);
        if ($validFormKey && $this->request->isPost()) {
            $customer = $this->customerSession->getCustomer();
            if($customer && $customer->getEmail()){
                try {
                    $this->changeCustomerPassword($customer->getEmail());
                    $this->messageManager->addSuccessMessage(__('Your password has been updated successfully. Please login to continue.'));
                    return $resultRedirect->setPath('customer/account');
                } catch (InvalidEmailOrPasswordException $e) {
                    $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
                } catch (InputException $e) {
                    $this->messageManager->addErrorMessage($this->escaper->escapeHtml($e->getMessage()));
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('We can\'t save the customer.'));
                }
            }
            else{
                $this->customerSession->logout();
                $this->customerSession->start();
                $this->messageManager->addErrorMessage(__('You have to login to change your pasword'));
                return $resultRedirect->setPath('customer/account/login');
            }
        }

        return $resultRedirect->setPath('customer/account/changepassword');
    }

    /**
     * Change customer password
     *
     * @param string $email
     * @return boolean
     * @throws InvalidEmailOrPasswordException|InputException
     */
    protected function changeCustomerPassword($email)
    {
        $currPass = $this->request->getPost('current_password');
        $newPass = $this->request->getPost('password');
        $confPass = $this->request->getPost('password_confirmation');
        if ($newPass != $confPass) {
            throw new InputException(__('Password confirmation doesn\'t match entered password.'));
        }

        return $this->customerAccountManagement->changePassword($email, $currPass, $newPass);
    }
}
