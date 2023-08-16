<?php 

/**
 * @category Trans
 * @package  Trans_CategoryImage
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\ThirdPartyExt\Model\Mageplaza\Core\Model\Config\Structure;

/**
 * Class Data
 */
class Data extends \Mageplaza\Core\Model\Config\Structure\Data 
{
	protected function getDynamicConfigGroups($moduleName, $sectionName)
	{
		$defaultFieldOptions = [
		    'type'          => 'text',
		    'showInDefault' => '1',
		    'showInWebsite' => '0',
		    'showInStore'   => '0',
		    'sortOrder'     => 1,
		    'module_name'   => $moduleName,
		    'module_type'   => $this->_helper->getModuleType($moduleName),
		    'validate'      => 'required-entry',
		    '_elementType'  => 'field',
		    'path'          => $sectionName . '/module'
		];

		$fields = [];
		foreach ($this->getFieldList() as $id => $option) {
		    $fields[$id] = array_merge($defaultFieldOptions, ['id' => $id], $option);
		}

		$dynamicConfigGroups['module'] = [
		    'id'            => 'module',
		    'label'         => __('Module Information'),
		    'showInDefault' => '0', //default 1
		    'showInWebsite' => '0',
		    'showInStore'   => '0',
		    'sortOrder'     => 1000,
		    "_elementType"  => "group",
		    'path'          => $sectionName,
		    'children'      => $fields
		];

		return $dynamicConfigGroups;
	}
}

 ?>