<?php
namespace Trans\Customer\Plugin\Customer\Controller\Account;
use Magento\Customer\Controller\Account\LoginPost as MageLoginPost;
/**
 * Class LoginPost
 */
class LoginPost
{
	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $request;
	/**
	 * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
	 */
	protected $customerFactory;
	/**
	 * @var \Magento\Customer\Model\Session
	 */
	protected $customerSession;
	/**
	 * @var \Trans\Customer\Helper\Config
	 */
	protected $configHelper;
	/**
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
	 * @param \Magento\Customer\Model\Session $customerSession
	 * @param \Trans\Customer\Helper\Config $configHelper
	 */
	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
		\Magento\Customer\Model\Session $customerSession,
		\Trans\Customer\Helper\Config $configHelper
	)
	{
		$this->request = $request;
		$this->customerFactory = $customerFactory;
		$this->customerSession = $customerSession;
		$this->configHelper = $configHelper;
	}
	/**
	 * phone number login process
	 *
	 * @param MageLoginPost $subject
	 * @param callable $proceed
	 * @return mixed
	 */
	public function aroundExecute(MageLoginPost $subject, callable $proceed)
	{
		if($this->configHelper->isPhoneLoginEnabled())
		{
			$login = $this->request->getParam('login');
			if(!strpos($login['username'], '@') !== false )
			{
	            /* Get email id based on mobile number and login*/
	            $customereCollection = $this->customerFactory->create();
	            $customereCollection->addFieldToFilter("telephone", $login['username']);
	            if($customereCollection->getSize() > 0) {
		            foreach($customereCollection as $customerdata){
		                $login['username'] = $customerdata['email'];
		            }
		            $this->request->setPostValue('login', $login);
	            }
	        }
		}
		$result = $proceed();
		$this->customerSession->setFlagAfterLogin(true);
		return $result;
	}
}
