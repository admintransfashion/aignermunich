<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Trans\Customer\Preference\Customer\Block\Widget;

use Magento\Customer\Block\Widget\Gender as MageGender;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\OptionInterface;

/**
 * Block to render customer's gender attribute
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Gender extends MageGender
{
    /**
     * Returns options from gender attribute
     *
     * @return OptionInterface[]
     */
    public function getGenderOptions()
    {
        $opts = [];
        $options = $this->_getAttribute('gender')->getOptions();

        foreach($options as $opt) {
            try {
                if($opt->getValue() == 3) {
                    continue;
                }

                switch ($opt->getValue()) {
                    case 1:
                        $optLabel = 'Mr';
                        break;
                    
                    case 2:
                        $optLabel = 'Ms';
                        break;
                    
                    default:
                        $optLabel = '';
                        break;
                }

                $opt->setLabel($optLabel);
                $opts[] = $opt;
            } catch (\Exception $e) {
                continue;
            }
        }

        return $opts;
    }
}
