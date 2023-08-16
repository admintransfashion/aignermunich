<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Helper;

use Magento\Framework\App\ScopeInterface;

class DateTime extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ScopeResolverInterface $scopeResolver
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime $dateTime
    ) {
        $this->scopeResolver = $scopeResolver;
        $this->timezone = $timezone;
        $this->dateTime = $dateTime;
        parent::__construct($context);
    }

    /**
     * Get current UTC+0 time
     *
     * @param string $format
     * @return string
     */
    public function getCurrentUTCZeroDateTime($format = 'Y-m-d H:i:s')
    {
        $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        return $dateTime->format($format);
    }

    /**
     * Convert UTC+0 time into current timezone
     *
     * @param string $date
     * @return string
     */
    public function convertUTCZeroToCurrentTimezone($date = null)
    {
        if($date && $this->isValidMySQLDate($date)) {
            $dateTime = new \DateTime($date, new \DateTimeZone('UTC'));
            return $this->timezone->date($dateTime)->format('Y-m-d H:i:s');
        }

        return null;
    }

    /**
     * Check whether the string date has format Y-m-d H:i:s
     *
     * @param string $date
     * @return boolean
     */
    public function isValidMySQLDate($date = null)
    {
        if($date && is_string($date)) {
            $regex = "/^((((19|[2-9]\d)\d{2})\-(0[13578]|1[02])\-(0[1-9]|[12]\d|3[01]))|(((19|[2-9]\d)\d{2})\-(0[13456789]|1[012])\-(0[1-9]|[12]\d|30))|(((19|[2-9]\d)\d{2})\-02\-(0[1-9]|1\d|2[0-8]))|(((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))\-02\-29))(\s([0-1][0-9]|2[0-4]):([0-5][0-9]):([0-5][0-9]))$/";

            return preg_match($regex, $date);
        }

        return false;
    }

    /**
     * Check whether the current Date is within dateFrom and dateTo
     *
     * @param \Magento\Framework\App\ScopeInterface $scope
     * @param string $dateFrom
     * @param string $dateTo
     * @return bool
     */
    public function isScopeDateInInterval($scope, $dateFrom, $dateTo)
    {
        return $this->timezone->isScopeDateInInterval($scope, $dateFrom, $dateTo);
    }

    /**
     * Check whether the current DateTime is within dateFrom and dateTo
     *
     * @param \Magento\Framework\App\ScopeInterface $scope
     * @param string $dateFrom
     * @param string $dateTo
     * @return bool
     */
    public function isScopeDateTimeInInterval($scope, $dateFrom, $dateTo)
    {
        if (!$scope instanceof ScopeInterface) {
            $scope = $this->scopeResolver->getScope($scope);
        }

        $scopeTimeStamp = $this->timezone->scopeTimeStamp($scope);
        $fromTimeStamp = strtotime($dateFrom);
        $toTimeStamp = strtotime($dateTo);

        return !((!$this->dateTime->isEmptyDate($dateFrom) && $scopeTimeStamp < $fromTimeStamp) ||
            (!$this->dateTime->isEmptyDate($dateTo) && $scopeTimeStamp > $toTimeStamp));
    }
}
