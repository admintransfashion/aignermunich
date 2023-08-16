# CTCD Core Module (Magento 2)

## Version 100.2.0

_2021-06-24_

Updates:

-   Add disabled functionality for field on system.xml Block/System/Config/Form/Field/Disable.php
-   Add new helper class Helper/Catalog.php
-   Add new parameter for getImageFile() method on Helper/File.php
-   Update logic for writeLogByDate() and write() on Helper/Logger.php
-   Minor update on model class Model/ImageUploader.php
-   Add new js file for manipulating date - view/base/web/js/datetime-utils.js

## Version 100.1.9

_2021-06-09_

Updates:

-   Add new abstract model class Model/AbstractRepository.php
-   Add new model class Model/ImageUploader.php

## Version 100.1.8

_2021-06-02_

Updates:

-   Add new method 'send()' on Helper/Curl.php
-   Add new configuration for File Logger
-   Add new method writeLogByDate() on Helper/Logger.php    
-   Update logic write() and createDirectory() methods on Helper/Logger.php

## Version 100.1.7

_2021-05-27_

Updates:

-   Add decimal price removal feature (configure through admin's configuration)

## Version 100.1.6

_2021-05-25_

Updates:

-   Add new helper file (Cache.php)
-   Add some new methods on Helper\Customer.php

## Version 100.1.5

_2021-05-24_

Updates:

-   Add new helper file (Customer.php)
-   Add a return method for popupWindow and popupCenterWindow methods on popup-browser-window.js
-   Add new cron group 'ctcd_cron_groups'
-   Refactoring code of HideVersionResponseType.php

## Version 100.1.4

_2021-05-18_

Updates:

-   Add new feature for creating a new popup window browser (base/web/js/popup-browser-window.js)

## Version 100.1.3

_2021-05-11_

Updates:

-   Add new methods on Helper/DateTime.php
-   Add new methods on Helper/File.php

## Version 100.1.2

_2021-04-29_

Updates:

-   Add new methods on Helper/Data.php
-   Add new helper files (Curl.php and SourceItem.php)

## Version 100.1.1

_2021-04-29_

Updates:

-   Fixing issue invalid element on system.xml for developer/default mode

## Version 100.1.0

_2021-03-26_

Updates:

-   Fixing issue 'deletePrompt' method not found
-   Add js/grid/columns/actions.js for action column on Admin Grid

## Version 100.0.9

_2021-03-19_

Updates:

-   Add new helper file (Url.php)

## Version 100.0.8

_2021-03-02_

Updates:

-   Add new helper file (DateTime.php)
-   Add global color styles for status grid column (core.css)

## Version 100.0.7

_2021-02-23_

Updates:

-   Improvement for admin sidebar menu configuration and its ACL
-   Force show the level 1 of submenu group title when the menu only has 1 child

## Version 100.0.6

_2021-02-18_

Updates:

-   Fix issue missing CT Corp logo from admin sidebar menu
-   Add delete prompt functionality for admin form

## Version 100.0.5

_2021-02-11_

Updates:

-   Fixing incorrect ACL configuration
-   Change section id of system.xml from baseconfig into ctcdcore

## Version 100.0.4

_2021-02-08_

Updates:

-   Add feature to hide magento version through admin configuration

## Version 100.0.3

_2021-02-05_

Updates:

-   Fixing admin sidebar logo not shown properly when custom admin sidebar logo removed
-   Fixing wrong wording on alternate & title image logo

## Version 100.0.2

_2021-02-05_

Updates:

-   Add feature to replace Magento logo on admin login page through configuration
-   Add feature to replace Magento logo on admin sidebar logo through configuration
-   Add Powered by CTCD logo on admin login page
-   Change magento copyright statement into Powered by CTCD logo

## Version 100.0.1

_2021-01-28_

Updates:

-   Add CT Corpora logo to the CTCD menu on admin sidebar menu
-   Fix incorrect order when load for css module

## Version 100.0.0

_2021-01-26_

Initial version.
