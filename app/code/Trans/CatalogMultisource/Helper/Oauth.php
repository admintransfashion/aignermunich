<?php
/**
 * @category Trans
 * @package  Trans_CatalogMultisource
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Imam Kusuma <imam.kusuma@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\CatalogMultisource\Helper;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Oauth
 */
class Oauth extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $connection;

    /**
     * @var \Magento\User\Model\ResourceModel\User
     */
    protected $userResourceModel;

    /**
     * @var \Magento\User\Api\Data\UserInterfaceFactory
     */
    protected $userModel;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ResourceConnection $connection
     * @param \Magento\User\Model\ResourceModel\User $userResourceModel
     * @param \Magento\User\Api\Data\UserInterfaceFactory $userModel
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $connection,
        \Magento\User\Model\ResourceModel\User $userResourceModel,
        \Magento\User\Api\Data\UserInterfaceFactory $userModel
    ) {
        $this->connection = $connection;
        $this->userModel = $userModel;
        $this->userResourceModel = $userResourceModel;

        parent::__construct($context);
    }

    /**
     * Get Admin User by token
     * 
     * @param string $token
     * @return array|bool
     */
    public function getAdminIdByToken($token)
    {
        $connection = $this->connection->getConnection();
        $result = $connection->fetchRow(
            $connection
                ->select('admin_id')
                ->from($this->connection->getTableName('oauth_token'))
                ->where('token = :_token')
                ->limit(1),
            [':_token' => $token]
        );

        if(!empty($result)) {
            return $result;
        }

        return false;
    }

    /**
     * Get Admin data by id
     * 
     * @param int $adminId
     * @return Magento\User\Api\Data\UserInterface
     */
    public function getAdminById($adminId)
    {
        $data = $this->userModel->create();
        $this->userResourceModel->load($data, $adminId);
        if (!$data->getId()) {
            throw new NoSuchEntityException(__('Requested Data Response doesn\'t exist'));
        }

        return $data;
    }

    /**
     * Get current user token
     * 
     * @return string
     */
    public function getCurrentToken()
    {
        $bearer = $this->_request->getHeader('Authorization');
        $explode = explode(' ', $bearer);
        if(isset($explode['1'])) {
            $token = $explode['1'];
            return $token;
        }

        return false;
    }
}