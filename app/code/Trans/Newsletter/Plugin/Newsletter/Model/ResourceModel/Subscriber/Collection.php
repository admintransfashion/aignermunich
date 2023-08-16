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

namespace Trans\Newsletter\Plugin\Newsletter\Model\ResourceModel\Subscriber;

use Trans\Newsletter\Api\Data\NewsletterAdditionalInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection as NewsletterCollection;

/**
 * Plugin class Collection
 */
class Collection
{
	/**
	 * @param NewsletterCollection $subject
	 * @param $result
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
    public function afterShowCustomerInfo(NewsletterCollection $subject, $result)
    {
        $subject->getSelect()->joinLeft(
            [
                'subscriber_additional' => $subject->getTable('newsletter_subscriber_additional')
            ],
            'main_table.subscriber_id = subscriber_additional.subscriber_id',
            ['subscribe_men', 'subscribe_women', 'subscribe_category']
        );

        return $result;
    }
}
