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

define(
   [
     'jquery',
     'Magento_Catalog/js/product/view/product-ids-resolver',
     'mage/url',
     'Magento_Ui/js/model/messageList',
     'Magento_Customer/js/customer-data'
   ],
   function($, productIdResolver, url, messageList, customerData) {
      "use strict";

      return function (param) {
          var sourceCode = param.sourceCode, qty, form = $('#product_addtocart_form'), sku, postData;
          $('#reserve-' + sourceCode).on('click', function(){
              qty = $('input[id="qty"]').val();
              sku = form.data().productSku;

              postData = {qty: qty, sku: sku, sourceCode: sourceCode};
              sendData(postData);
          });

          function sendData(postData) {
              var redirectUrl = url.build('reservation/cart/addtoreservecart');

              $.ajax({
                  type: 'post',
                  data: postData,
                  showLoader: true,
                  url: redirectUrl,
                  cache: false,
                  success: function(data) {
                      var status = data.status;
                      // if(status == 'success') {
                      //     var sprintDirect = data.redirectURL;
                      //     window.location.replace(sprintDirect);
                      // }

                      customerData.set('messages', {
                        messages: [{
                            type: status,
                            text: data.message
                        }]
                      });

                      $("#popup").modal("closeModal");
                  }//end of ajax success
              });
          }
      };
   }
);
