<?php 

/**
 * @category Trans
 * @package  Trans_ThirdPartyExt
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Dwi Septha Kurniawan <septha.kurniawan@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\ThirdPartyExt\Plugin\Productslider\Block;

/**
 *  Class AbstractSlider
 */
class AbstractSlider
{
	/**
	 * override template default
	 * 
	 * @param \Mageplaza\Productslider\Block\AbstractSlider $subject
	 *
	 */
	public function beforeToHtml(\Mageplaza\Productslider\Block\AbstractSlider $subject)
    {
        $subject->setTemplate('Trans_ThirdPartyExt::productslider.phtml');
    }
}
