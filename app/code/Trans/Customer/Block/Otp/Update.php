<?php

/**
 * Copyright © 2023 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Customer
 * @license  Proprietary
 *
 * @author   hadi <ashadi.sejati@transdigital.co.id>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Customer\Block\Otp;

class Update extends \Trans\Customer\Block\Otp\Index
{
    /**
     * generate form fields
     *
     * @return mixed|string
     */
    public function generateFormFields()
    {
        $post = $this->registry->registry('formPost');
        $field = '';
        foreach($post as $key => $row) {
            if($key == 'form_key') {
                continue;
            }
            $field .= "<input type='hidden' name='" . $key . "' value='" . $row . "'>";
        }

        return $field;
    }
}