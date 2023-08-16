<?php
/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_TCastSMS
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\TCastSMS\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;
use CTCD\TCastSMS\Api\Data\LogOtpInterface;
use CTCD\TCastSMS\Api\LogOtpRepositoryInterface;
use CTCD\TCastSMS\Api\Data\LogOtpSearchResultsInterfaceFactory;
use CTCD\TCastSMS\Model\LogOtpFactory;
use CTCD\TCastSMS\Model\ResourceModel\LogOtp\CollectionFactory as LogOtpCollectionFactory;
use CTCD\TCastSMS\Model\ResourceModel\LogOtp as ResourceModel;

class LogOtpRepository extends AbstractRepository implements LogOtpRepositoryInterface
{

    /**
     * Constants defined for cache key
     */
    const PARENT_KEY = 'tcastotplog';
    const GROUP_KEY = 'key';

    /**
     * @var LogOtpInterface[]
     */
    protected $instances = [];

    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @var LogOtpFactory
     */
    protected $logOtpFactory;

    /**
     * @var LogOtpCollectionFactory
     */
    protected $logOtpCollectionFactory;

    /**
     * @var LogOtpSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @param ResourceModel $resourceModel
     * @param LogOtpFactory $logOtpFactory
     * @param LogOtpCollectionFactory $logOtpCollectionFactory
     * @param LogOtpSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        ResourceModel $resourceModel,
        LogOtpFactory $logOtpFactory,
        LogOtpCollectionFactory $logOtpCollectionFactory,
        LogOtpSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->logOtpFactory = $logOtpFactory;
        $this->logOtpCollectionFactory  = $logOtpCollectionFactory;
        $this->searchResultFactory  = $searchResultFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCollection()
    {
        return $this->logOtpCollectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSearchResultFactory()
    {
        return $this->searchResultFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function save(LogOtpInterface $parameters)
    {
        $logOtp = $parameters;
        try {
            $this->resourceModel->save($logOtp);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                new Phrase(__('Could not save the OTP log: %1', $e->getMessage()))
            );
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($verificationId, $forceReload = false)
    {
        $cacheKey = $this->getCacheKey([self::PARENT_KEY]);
        if (!isset($this->instances[$cacheKey][self::GROUP_KEY][$verificationId]) || $forceReload) {
            $logId = $this->resourceModel->getIdByKey($verificationId);
            $logOtp = $this->getById($logId);
            if (! $logOtp->getId()) {
                throw new NoSuchEntityException(new Phrase(__('Requested OTP log doesn\'t exist')));
            }
            $this->instances[$cacheKey][self::GROUP_KEY][$verificationId] = $logOtp;
        }

        return $this->instances[$cacheKey][self::GROUP_KEY][$verificationId];
    }

    /**
     * Get log by ID
     *
     * @param int $logId
     * @return \CTCD\TCastSMS\Api\Data\LogOtpInterface
     */
    public function getById($logId)
    {
        $logOtp = $this->logOtpFactory->create();
        $logOtp->getResource()->load($logOtp, $logId);
        return $logOtp;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($logId)
    {
        try {
            $existingData = $this->get($logId, true);
            $this->resourceModel->delete($existingData);
        } catch (\Exception $e) {
            throw new StateException(
                new Phrase(__('Unable to delete OTP log: %1', $e->getMessage()))
            );
        }

        $cacheKey = $this->getCacheKey([self::PARENT_KEY]);
        unset($this->instances[$cacheKey][self::GROUP_KEY][$logId]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function countTelephoneByToday($mobileNumber)
    {
        return $this->resourceModel->countTelephoneByToday($mobileNumber);
    }
}
