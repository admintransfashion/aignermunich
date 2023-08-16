<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Core
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Core\Helper;

use Magento\Framework\Exception\InputException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	const RANDOM_LOW_CHAR = 'abcdefghijklmnopqrstuvwxyz';

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	protected $datetime;

	/**
	 * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	protected $timezone;

	/**
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $json;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;

	/**
	 * @var \Magento\Framework\Filesystem
	 */
	protected $filesystem;

	/**
	 * @var \Magento\Eav\Model\Entity\Attribute
	 */
	protected $eavAttribute;

	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resource;

	/**
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Framework\Stdlib\DateTime\DateTime $datetime
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
	 * @param \Magento\Framework\Serialize\Serializer\Json $json
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\Filesystem $filesystem
	 * @param \Magento\Eav\Model\Entity\Attribute $eavAttribute
	 * @param \Magento\Framework\App\ResourceConnection $resource
	 */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Stdlib\DateTime\DateTime $datetime,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
		\Magento\Framework\Serialize\Serializer\Json $json,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Filesystem $filesystem,
		\Magento\Eav\Model\Entity\Attribute $eavAttribute,
		\Magento\Framework\App\ResourceConnection $resource
	) {
		parent::__construct($context);

		$this->datetime = $datetime;
		$this->timezone = $timezone;
		$this->json = $json;
		$this->storeManager = $storeManager;
		$this->filesystem = $filesystem;
		$this->eavAttribute = $eavAttribute;
		$this->resource = $resource;
		$this->urlBuilder = $context->getUrlBuilder();
	}

	/**
     * @param string $field
     * @param string|null $scope
     * @param int|null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $scope = null, $storeId = null)
    {
        $scope = !$scope ? ScopeInterface::SCOPE_WEBSITE : $scope;
        return $this->scopeConfig->getValue(
            $field,
            $scope,
            $storeId
        );
    }

    /**
     * Is use custom admin logo
     *
     * @return bool
     */
    public function isUseCustomAdminLogo()
    {
        return (bool) $this->getConfigValue('ctcdcore/company_logo/active');
    }

    /**
     * Get logo title
     *
     * @return string
     */
    public function getCompanyLogoTitle()
    {
        return (string) $this->getConfigValue('ctcdcore/company_logo/logo_title');
    }

    /**
     * Get company logo for admin login page
     *
     * @return string
     */
    public function getCompanyAdminLoginLogo()
    {
        return (string) $this->getConfigValue('ctcdcore/company_logo/admin_login_page');
    }

    /**
     * Get company logo for admin sidebar
     *
     * @return string
     */
    public function getCompanyAdminSidebarLogo()
    {
        return (string) $this->getConfigValue('ctcdcore/company_logo/admin_sidebar');
    }

    /**
     * Is the feature for hide magento version active
     *
     * @return bool
     */
    public function isMagentoVersionBeHidden()
    {
        return (bool) $this->getConfigValue('ctcdcore/magento_version/active');
    }

    /**
     * Get response type of hiding magento version
     *
     * @return int
     */
    public function getResponseTypeOfHidingMagentoVersion()
    {
        return (int) $this->getConfigValue('ctcdcore/magento_version/response_type');
    }

    /**
     * Is decimal price precision enabled?
     *
     * @return bool
     */
    public function isDecimalPriceFeatureEnabled()
    {
        return (bool) $this->getConfigValue('ctcdcore/decimal_price/active');
    }

    /**
     * Is show decimal on price
     *
     * @return bool
     */
    public function isShowDecimalOnPrice()
    {
        return (bool) $this->getConfigValue('ctcdcore/decimal_price/show_decimal');
    }

    /**
     * Get decimal precision on price
     *
     * @return int
     */
    public function getDecimalPricePrecision()
    {
        return (int) $this->getConfigValue('ctcdcore/decimal_price/precision');
    }

    /**
     * Is file logger feature enabled?
     *
     * @return bool
     */
    public function isFileLoggerFeatureEnabled()
    {
        return (bool) $this->getConfigValue('ctcdcore/file_logger/active');
    }

    /**
     * Is it use external image to show on frontend?
     *
     * @return bool
     */
    public function isUsingExternalImage()
    {
        return (bool) $this->getConfigValue('ctcdcore/external_image/active');
    }

    /**
     * Is custom delete confirmation feature enabled?
     *
     * @return bool
     */
    public function isCustomDeleteConfirmationEnabled()
    {
        return (bool) $this->getConfigValue('ctcdcore/delete_prompt/active');
    }

    /**
     * Is website on catalog mode?
     *
     * @return bool
     */
    public function isWebsiteInCatalogMode()
    {
        return (bool) $this->getConfigValue('ctcdcore/catalog_mode/active');
    }

	/**
	 * Get Store Manager
	 *
	 * @return \Magento\Store\Model\StoreManagerInterface
	 */
	public function getStoreManager() {
		return $this->storeManager;
	}

	/**
	 * change date format
	 *
	 * @param datetime $datetime
	 * @return datetime
	 */
	public function changeDateFormat($datetime) {
		return $this->datetime->date('d F Y H:i', $datetime);
	}

	/**
	 * get datetime
	 *
	 * @return \Magento\Framework\Stdlib\DateTime\DateTime
	 */
	public function getDatetime() {
		return $this->datetime;
	}

	/**
	 * get timezone
	 *
	 * @return \Magento\Framework\Stdlib\DateTime\TimezoneInterface
	 */
	public function getTimezone() {
		return $this->timezone;
	}

	/**
	 * get json
	 *
	 * @return \Magento\Framework\Serialize\Serializer\Json
	 */
	public function getJson() {
		return $this->json;
	}

	/**
	 * Get Date Noew
	 * @param string $format
	 * return dateformat $format
	 */
	public function getDateNow($format = '') {
		if (empty($format)) {
			$format = 'Y-m-d H:i:s';
		}
		$this->getTimezone()->date(new \DateTime())->format($format);
	}

	/**
	 * Generate Random Attr Code
	 * @param int $length
	 * @param string $characters
	 * return string $randomString
	 */
	public function genRandAttrCode($length = 10, $characters = "") {
		if (empty($characters)) {
			$characters = self::RANDOM_LOW_CHAR;
		}

		$charactersLength = strlen($characters);
		$randomString     = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	/**
	 * Get Url Builder
	 *
	 * @return \Magento\Framework\UrlInterface
	 */
	public function getUrlBuilder() {
		return $this->urlBuilder;
	}

	/**
	 * Get Media Path
	 *
	 * @return string
	 */
	public function getMediaPath() {
		return $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
	}

	/**
	 * Load attribute data by code
	 *
	 * @param   string $code
	 * @return  \Magento\Eav\Model\Entity\Attribute
	 */
	public function getProductAttributeData($code)
	{
		return $this->eavAttribute->loadByCode(\Magento\Catalog\Model\Product::ENTITY, $code);
	}

	/**
     * generate brand code
     *
     * @param string $name
     * @param string $tableName
     * @param string $column
     * @param int $ordering
     * @return string
     */
    public function generateCodeByName(string $name, string $tableName, string $column = 'code', int $ordering = 0)
    {
        if(!$name || !$tableName || !$column) {
            throw new InputException(__('Parameter can\'t be null'));
        }
        $name = strtoupper(trim($name));
        $connection = $this->resource->getConnection();
        $table = $connection->getTableName($tableName);

        $query = $connection->select();
        $query->from(
            [$table],
            'MAX(CAST(SUBSTR('.$column.',3) AS UNSIGNED)) AS maxnumber'
        );
        $query->where($column . ' like ?', $name[0] . '-%');
        $currentIndex = (int) $connection->fetchOne($query);
        $nextIndex = $currentIndex + 1;
        $nextIndex = ($ordering <= 0) ? $nextIndex : ($nextIndex + $ordering);
        $nextIndexStr = str_pad($nextIndex, 5, '0', STR_PAD_LEFT);

        return strtoupper($name[0]) . '-' . $nextIndexStr;
    }

    /**
     * get number leading with zero
     *
     * @param int $number
     * @param int $length
     * @return string
     */
    public function getNumberLeadingWithZero(int $number, int $length = 5)
    {
        $char = 0;
        $type = 'd';
        $format = "%{$char}{$length}{$type}";

        $result = sprintf($format, $number);

        return $result;
    }

    /**
     * Generate equipment id
     * @return string
     */
    public function generateEquipmentId()
    {
        $osType = PHP_OS;
        $browser = $_SERVER['HTTP_USER_AGENT'];
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        return md5($ipAddress.'___'.$osType.'___'.$browser);
    }
}
