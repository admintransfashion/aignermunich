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

namespace Trans\Reservation\Observer\User\Edit\Tabs;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;


class Store implements ObserverInterface
{
    /**
      * @param EventObserver $observer
      * @return $this
      */
    public function execute(EventObserver $observer)
    {
       $block = $observer->getEvent()->getBlock();
       if($block instanceof \Magento\User\Block\User\Edit\Tabs){
          $block->addTab(
              'store_tabs',
              [
                  'label' => __('Store (Leave blank to granted permission for all store.)'),
                  'title' => __('Store'),
                  // 'after' => __('roles_section'),
                  'content' => $block->getLayout()->createBlock(
                      \Trans\Reservation\Block\Adminhtml\User\Edit\Tab\Stores::class,
                      'user.store.grid'
                  )->toHtml()
              ]
          );
        }
        return $this;
   }
}
