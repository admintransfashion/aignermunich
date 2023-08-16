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

namespace CTCD\Inspiration\Api;

use CTCD\Inspiration\Api\Data\InspirationInterface;

/**
 * @api
 *
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
interface InspirationRepositoryInterface
{
    /**
     * Save data
     *
     * @param \CTCD\Inspiration\Api\Data\InspirationInterface $parameters
     * @return \CTCD\Inspiration\Api\Data\InspirationInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(InspirationInterface $parameters);

    /**
     * Get data
     *
     * @param string $key
     * @param bool $forceReload
     * @return \CTCD\Inspiration\Api\Data\InspirationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($key, $forceReload = false);

    /**
     * Delete data
     *
     * @param string $key
     * @return \CTCD\Inspiration\Api\Data\InspirationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete($key);

}
