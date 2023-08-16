<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;
use CTCD\Inspiration\Helper\Data as DataHelper;
use CTCD\Inspiration\Api\Data\InspirationInterface;
use CTCD\Inspiration\Api\InspirationRepositoryInterface;
use CTCD\Inspiration\Api\Data\InspirationSearchResultsInterfaceFactory;
use CTCD\Inspiration\Model\InspirationFactory;
use CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory as InspirationCollectionFactory;
use CTCD\Inspiration\Model\ResourceModel\Inspiration as ResourceModel;

class InspirationRepository extends AbstractRepository implements InspirationRepositoryInterface
{

    /**
     * Constants defined for cache key
     */
    const PARENT_KEY = 'ctcd_inspiration';
    const GROUP_KEY = 'key';

    /**
     * @var InspirationInterface[]
     */
    protected $instances = [];

    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @var InspirationFactory
     */
    protected $inspirationFactory;

    /**
     * @var InspirationCollectionFactory
     */
    protected $inspirationCollectionFactory;

    /**
     * @var InspirationSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
    * @var DataHelper
    */
    protected $dataHelper;


    public function __construct(
        ResourceModel $resourceModel,
        InspirationFactory $inspirationFactory,
        InspirationCollectionFactory $inspirationCollectionFactory,
        InspirationSearchResultsInterfaceFactory $searchResultFactory,
        DataHelper $dataHelper
    ) {
        $this->resourceModel = $resourceModel;
        $this->inspirationFactory = $inspirationFactory;
        $this->inspirationCollectionFactory  = $inspirationCollectionFactory;
        $this->searchResultFactory  = $searchResultFactory;
        $this->dataHelper  = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function getCollection()
    {
        return $this->inspirationCollectionFactory->create();
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
    public function save(InspirationInterface $parameters)
    {
        $inspiration = $parameters;

        $requiredParams = [
            InspirationInterface::URL_KEY
        ];

        foreach ($requiredParams as $param) {
            $value = $inspiration->getData($param);
            if (array_key_exists($param, $inspiration->getData())) {
                if (! $value) {
                    throw new ValidatorException(new Phrase(__('Parameter %1 can\'t be empty', $param)));
                }
            } else {
                throw new NoSuchEntityException(new Phrase(__('Could not found the parameter : %1', $param)));
            }
        }

        try {
            $this->resourceModel->save($inspiration);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                new Phrase(__('Could not save the inspiration: %1', $exception->getMessage()))
            );
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $forceReload = false)
    {
        if (!$key) {
            throw new InputException(new Phrase(__("Parameter '%1' is a required field.", InspirationInterface::CODE)));
        }

        $cacheKey = $this->getCacheKey([self::PARENT_KEY]);
        if (!isset($this->instances[$cacheKey][self::GROUP_KEY][$key]) || $forceReload) {
            $inspirationId = $this->resourceModel->getIdByKey($key);
            $inspiration = $this->getById($inspirationId);

            if (! $inspiration->getId()) {
                throw new NoSuchEntityException(new Phrase(__('Requested template class doesn\'t exist')));
            }

            $this->instances[$cacheKey][self::GROUP_KEY][$key] = $inspiration;
        }

        return $this->instances[$cacheKey][self::GROUP_KEY][$key];
    }

    /**
     * Get template class data by id
     *
     * @param int $inspirationId
     * @return \CTCD\Inspiration\Api\Data\InspirationInterface
     */
    public function getById($inspirationId)
    {
        $inspiration = $this->inspirationFactory->create();
        $inspiration->getResource()->load($inspiration, $inspirationId);

        return $inspiration;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        if (!$key) {
            throw new InputException(new Phrase(__("Parameter '%1' is a required field.", InspirationInterface::URL_KEY)));
        }

        try {
            $existingData = $this->get($key, true);
            $this->resourceModel->delete($existingData);
        } catch (\Exception $e) {
            throw new StateException(
                new Phrase(__('Unable to delete template class: %1', $e->getMessage()))
            );
        }

        $cacheKey = $this->getCacheKey([self::PARENT_KEY]);
        unset($this->instances[$cacheKey][self::GROUP_KEY][$key]);

        return true;
    }

}
