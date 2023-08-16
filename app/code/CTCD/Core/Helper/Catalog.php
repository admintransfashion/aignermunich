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

use Magento\Store\Model\ScopeInterface;

class Catalog extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $productAttributeCollectionFactory;

    /**
     * @var \Magento\Eav\Api\Data\AttributeSetInterfaceFactory
     */
    protected $attributeSetFactory;

    /**
     * @var \Magento\Catalog\Api\AttributeSetManagementInterface
     */
    protected $attributeSetManagement;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productModel;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    private $connection;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory
     * @param \Magento\Eav\Api\Data\AttributeSetInterfaceFactory $attributeSetFactory
     * @param \Magento\Catalog\Api\AttributeSetManagementInterface $attributeSetManagement
     * @param \Magento\Catalog\Model\Product $productModel
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magento\Eav\Api\Data\AttributeSetInterfaceFactory $attributeSetFactory,
        \Magento\Catalog\Api\AttributeSetManagementInterface $attributeSetManagement,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeSetManagement = $attributeSetManagement;
        $this->productModel = $productModel;
        $this->productFactory = $productFactory;
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
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function getFilterableAttributes()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributes */
        $productAttributes = $this->productAttributeCollectionFactory->create();
        $productAttributes->addFieldToFilter(
            ['is_filterable', 'is_filterable_in_search'],
            [[1, 2], 1]
        );

        return $productAttributes;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    public function getAttributesMustHaveOptions()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $productAttributes */
        $productAttributes = $this->productAttributeCollectionFactory->create();
        $productAttributes->getSelect()->where(new \Zend_Db_Expr("(`main_table`.`backend_type` = 'int' AND `main_table`.`frontend_input` = 'select') OR (`main_table`.`backend_type` = 'varchar' AND `main_table`.`frontend_input` = 'multiselect')"));

        return $productAttributes;
    }

    /**
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     */
    public function getOptionLabelByValue($attributeCode, $optionId)
    {
        $optionText = '';
        if($attributeCode && $optionId) {
            $product = $this->productFactory->create();
            $isAttributeExist = $product->getResource()->getAttribute($attributeCode);
            if ($isAttributeExist && $isAttributeExist->usesSource()) {
                $optionText = $isAttributeExist->getSource()->getOptionText($optionId);
            }
        }
        return $optionText;
    }

    /**
     * @param string $attributeCode
     * @param string $optionLabel
     * @return int
     */
    public function getOptionIdByLabel($attributeCode, $optionLabel)
    {
        $optionId = 0;
        if($attributeCode && $optionLabel) {
            $product = $this->productFactory->create();
            $isAttributeExist = $product->getResource()->getAttribute($attributeCode);

            if ($isAttributeExist && $isAttributeExist->usesSource()) {
                $optionId = $isAttributeExist->getSource()->getOptionId($optionLabel);
            }
        }
        return $optionId;
    }

    /**
     * Get Option Ids by search label
     *
     * @param string $attributeCode
     * @param string $searchLabel
     * @return array
     */
    public function getOptionIdsByLabel($attributeCode, $searchLabel)
    {
        $optionIds = [];
        if($attributeCode && $searchLabel) {
            $product = $this->productFactory->create();
            $isAttributeExist = $product->getResource()->getAttribute($attributeCode);

            if ($isAttributeExist && $isAttributeExist->usesSource()) {
                $attrOptionTable = $this->connection->getTableName('eav_attribute_option');
                $attrOptionValueTable = $this->connection->getTableName('eav_attribute_option_value');

                $query = $this->connection->select();
                $query->from(
                    ['eao' => $attrOptionTable],
                    ['eao.option_id']
                )->join(
                    ['eaov' => $attrOptionValueTable],
                    "eao.option_id = eaov.option_id AND eaov.store_id = 0 AND eaov.value LIKE '%".$searchLabel."%' ",
                    []
                )->where('eao.attribute_id = ?', $isAttributeExist->getId());

                $result = $this->connection->fetchCol($query);
                if($result){
                    $optionIds = $result;
                }
            }
        }
        return $optionIds;
    }

    /**
     * Get attribute id by name
     *
     * @param $attributeCode
     * @param int $entityTypeId
     * @return int
     */
    public function getAttributeIdByCode($attributeCode, $entityTypeId = 4)
    {
        $attributeId = 0;
        if($attributeCode) {
            $tableName = $this->connection->getTableName('eav_attribute');
            $query = $this->connection->select();
            $query->from($tableName, ['attribute_id'])
                ->where('entity_type_id = ?', $entityTypeId)
                ->where('attribute_code = ?', trim($attributeCode));
            $result = $this->connection->fetchOne($query);
            $attributeId = $result ? (int) $result : 0;
        }
        return $attributeId;
    }

    /**
     * Get attribute code by id
     *
     * @param int $attributeId
     * @param int $entityTypeId
     * @return string
     */
    public function getAttributeCodeById($attributeId, $entityTypeId = 4)
    {
        $attributeCode = '';
        if($attributeId) {
            $tableName = $this->connection->getTableName('eav_attribute');
            $query = $this->connection->select();
            $query->from($tableName, ['attribute_code'])
                ->where('entity_type_id = ?', $entityTypeId)
                ->where('attribute_id = ?', (int) $attributeId);
            $result = $this->connection->fetchOne($query);
            $attributeCode = $result ? (string) $result : '';
        }
        return $attributeCode;
    }

    /**
     * Get frontend input value by attribute id
     *
     * @param int $attributeId
     * @param int $entityTypeId
     * @return int
     */
    public function getFrontendInputByAttributeId($attributeId, $entityTypeId = 4)
    {
        $frontendInput = '';
        if($attributeId) {
            $tableName = $this->connection->getTableName('eav_attribute');
            $query = $this->connection->select();
            $query->from($tableName, ['frontend_input'])
                ->where('entity_type_id = ?', $entityTypeId)
                ->where('attribute_id = ?', (int) $attributeId);
            $result = $this->connection->fetchOne($query);
            $frontendInput = $result ?: '';
        }
        return $frontendInput;
    }

    /**
     * Get attribute set id by name
     *
     * @param string $attributeSetName
     * @return int
     */
    public function getAttributeSetIdByName($attributeSetName)
    {
        $attributeSetId = 0;
        if($attributeSetName) {
            $tableName = $this->connection->getTableName('eav_attribute_set');
            $query = $this->connection->select();
            $query->from($tableName, ['attribute_set_id'])
                ->where('entity_type_id = ?', 4)
                ->where('attribute_set_name = ?', trim($attributeSetName));
            $result = $this->connection->fetchOne($query);
            $attributeSetId = $result ? (int) $result : 0;
        }
        return $attributeSetId;
    }

    /**
     * Get attribute group id by code
     *
     * @param int $attributeSetId
     * @param string $attributeGroupCode
     * @return int
     */
    public function getAttributeGroupIdByGroupCode(int $attributeSetId, string $attributeGroupCode)
    {
        $attributeGroupId = 0;
        if($attributeSetId && $attributeGroupCode) {
            $tableName = $this->connection->getTableName('eav_attribute_group');
            $query = $this->connection->select();
            $query->from($tableName, ['attribute_group_id'])
                ->where('attribute_set_id = ?', $attributeSetId)
                ->where('attribute_group_code = ?', trim($attributeGroupCode));
            $result = $this->connection->fetchOne($query);
            $attributeGroupId = $result ? (int) $result : 0;
        }
        return $attributeGroupId;
    }

    /**
     * @param  string $attributeSetName
     * @return int|null
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createAttributeSet($attributeSetName)
    {
        $attributeSetId = 0;
        $defaultAttributeSetId = $this->productModel->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSet->setAttributeSetName($attributeSetName);
        $result = $this->attributeSetManagement->create($attributeSet, $defaultAttributeSetId);
        if($result) {
            $attributeSetId = $result->getAttributeSetId();
        }
        return $attributeSetId;
    }

    /**
     * Create attribute set and copy data from default attribute set
     *
     * @param string $attributeSetName
     * @param array $additionalData
     * @return int
     */
    public function createAttributeSetAndCopyDataFromDefault(string $attributeSetName, $additionalData = [])
    {
        $result = ['success' => false, 'message' => 'Attribute Set Name = NULL', 'id' => 0];
        if($attributeSetName) {
            $success = true;
            $tableName = $this->connection->getTableName('eav_attribute_set');
            $data = [
                'entity_type_id' => 4,
                'attribute_set_name' => $attributeSetName,
                'sort_order' => 0,
            ];
            $this->connection->insert($tableName, $data);
            $attributeSetId = (int) $this->connection->lastInsertId($tableName);

            if($attributeSetId) {
                if($additionalData) {
                    $assignedAttributeIds = [];
                    if(array_key_exists('attribute_ids', $additionalData)){
                        $assignedAttributeIds = $additionalData['attribute_ids'];
                        unset($additionalData['attribute_ids']);
                    }
                    $additionalData['attribute_set_id'] = $attributeSetId;
                    $customAttributeGroupId = $this->addAttributeGroup($additionalData);
                    if($customAttributeGroupId){
                        if($assignedAttributeIds) {
                            $position = 1;
                            foreach($assignedAttributeIds as $attributeId) {
                                $data = [
                                    'entity_type_id' => 4,
                                    'attribute_set_id' => $attributeSetId,
                                    'attribute_group_id' => $customAttributeGroupId,
                                    'attribute_id' => $attributeId,
                                    'sort_order' => $position
                                ];
                                $entityAttributeId = $this->addAttributeIntoAttributeSet($data);
                                if($entityAttributeId){
                                    $position++;
                                }
                                else{
                                    $success = false;
                                    $result['message'] = __('There was an error when assigning attribute: %1', $attributeId);
                                }
                            }
                        }
                    }
                    else{
                        $success = false;
                        $result['message'] = __('There was an error when adding attribute group: %1', $additionalData['attribute_group_name']);
                    }
                }

                /**
                 * Copy default attribute set groups into new attribute set
                 */
                $defaultAttributeGroups = $this->getDefaultAttributeSetAttributeGroups();
                $newAttributeGroupIds = [];
                if($defaultAttributeGroups) {
                    foreach($defaultAttributeGroups as $group) {
                        $oldAttributeGroupId = $group['attribute_group_id'];
                        unset($group['attribute_group_id']);
                        $group['attribute_set_id'] = $attributeSetId;
                        $attributeGroupId = $this->addAttributeGroup($group);
                        if($attributeGroupId){
                            $newAttributeGroupIds[$oldAttributeGroupId] = $attributeGroupId;
                        }
                        else{
                            $success = false;
                            $result['message'] = __('There was an error when copying attribute group: %1', $group['attribute_group_name']);
                        }
                    }
                }

                if($newAttributeGroupIds) {
                    /**
                     * Copy default attribute set attributes into new attribute set
                     */
                    $defaultAttributes = $this->getDefaultAttributeSetAttributes();
                    foreach($defaultAttributes as $attribute) {
                        if(isset($newAttributeGroupIds[$attribute['attribute_group_id']])) {
                            unset($attribute['entity_attribute_id']);
                            $attribute['attribute_set_id'] = $attributeSetId;
                            $attribute['attribute_group_id'] = $newAttributeGroupIds[$attribute['attribute_group_id']];
                            $entityAttributeId = $this->addAttributeIntoAttributeSet($attribute);
                            if(!$entityAttributeId){
                                $success = false;
                                $result['message'] = __('There was an error when assigning attribute: %1', $attribute['attribute_id']);
                            }
                        }
                        else{
                            $success = false;
                            $result['message'] = __('Attribute group %1 is missing', $attribute['attribute_group_id']);
                        }
                    }
                }
                else{
                    $success = false;
                    $result['message'] = __('There was an error when copying all attribute groups');
                }
            }
            else{
                $success = false;
                $result['message'] = __('There was an error when creating attribute set');
            }

            if($success){
                $result = ['success' => true, 'message' => '', 'id' => $attributeSetId];
            }
        }

        return $result;
    }

    /**
     * Update/Replace the attributes on existing attribute set
     *
     * @param array $data
     * @return array
     */
    public function updateAttributeSetAttributes($data = [])
    {
        $result = ['success' => false, 'message' => 'Parameter is empty'];
        if($data) {
            $assignedAttributeIds = [];
            if(array_key_exists('attribute_ids', $data)){
                $assignedAttributeIds = $data['attribute_ids'];
                unset($data['attribute_ids']);
            }
            $data['entity_type_id'] = 4;
            if($this->unassignAttributeFromAttributeSetByGroup($data) && $assignedAttributeIds){
                $success = true;
                $position = 1;
                foreach($assignedAttributeIds as $attributeId) {
                    $data = [
                        'entity_type_id' => 4,
                        'attribute_set_id' => $data['attribute_set_id'],
                        'attribute_group_id' => $data['attribute_group_id'],
                        'attribute_id' => $attributeId,
                        'sort_order' => $position
                    ];
                    $entityAttributeId = $this->addAttributeIntoAttributeSet($data);
                    if($entityAttributeId){
                        $position++;
                    }
                    else{
                        $success = false;
                        $result['message'] = __('There was an error when assigning attribute: %1', $attributeId);
                    }
                }
                if($success){
                    $result = ['success' => true, 'message' => ''];
                }
            }
        }
        return $result;
    }

    /**
     * Add attribute set by direct query
     *
     * @param string $attributeSetName
     * @return int
     */
    public function addAttributeSet(string $attributeSetName)
    {
        $attributeSetId = 0;
        if($attributeSetName) {
            $tableName = $this->connection->getTableName('eav_attribute_set');

            $data = [
                'entity_type_id' => 4,
                'attribute_set_name' => $attributeSetName,
                'sort_order' => 0,
            ];
            $this->connection->insert($tableName, $data);
            $attributeSetId = (int) $this->connection->lastInsertId($tableName);
        }
        return $attributeSetId;
    }

    /**
     * Add attribute set group into attribute set
     *
     * @param array $data
     * @return int
     */
    public function addAttributeGroup($data = [])
    {
        $attributeGroupId = 0;
        if($data && is_array($data) && !empty($data)) {
            $tableName = $this->connection->getTableName('eav_attribute_group');
            if(isset($data['attribute_group_id'])){
                unset($data['attribute_group_id']);
            }
            $valid = true;
            $allowedColumns = ['attribute_set_id', 'attribute_group_name', 'sort_order', 'default_id', 'attribute_group_code', 'tab_group_code'];
            foreach($allowedColumns as $column) {
                if(!array_key_exists($column, $data)) {
                    $valid = false;
                    break;
                }
            }
            if($valid) {
                $this->connection->insert($tableName, $data);
                $attributeGroupId = (int) $this->connection->lastInsertId($tableName);
            }
        }

        return $attributeGroupId;
    }

    /**
     * Add attribute set group into attribute set
     *
     * @param array $data
     * @return int
     */
    public function addAttributeIntoAttributeSet($data = [])
    {
        $entityAttributeId = 0;
        if($data && is_array($data) && !empty($data)) {
            $tableName = $this->connection->getTableName('eav_entity_attribute');
            if(isset($data['entity_attribute_id'])){
                unset($data['entity_attribute_id']);
            }
            $valid = true;
            $allowedColumns = ['entity_type_id', 'attribute_set_id', 'attribute_group_id', 'attribute_id', 'sort_order'];
            foreach($allowedColumns as $column) {
                if(!array_key_exists($column, $data)) {
                    $valid = false;
                    break;
                }
            }
            if($valid) {
                $this->connection->insert($tableName, $data);
                $entityAttributeId = (int) $this->connection->lastInsertId($tableName);
            }
        }
        return $entityAttributeId;
    }

    /**
     * Unassign attributes from attribute set
     *
     * @param array $data
     * @return bool
     */
    public function unassignAttributeFromAttributeSet($data = [])
    {
        if($data && is_array($data) && !empty($data)) {
            $tableName = $this->connection->getTableName('eav_entity_attribute');
            $valid = true;
            $allowedColumns = ['entity_type_id', 'attribute_set_id', 'attribute_group_id', 'attribute_id'];
            foreach($allowedColumns as $column) {
                if(!array_key_exists($column, $data)) {
                    $valid = false;
                    break;
                }
            }
            if($valid) {
                $condition = [
                    'entity_type_id = ?' => $data['entity_type_id'],
                    'attribute_set_id = ?' => $data['attribute_set_id'],
                    'attribute_group_id = ?' => $data['attribute_group_id'],
                    'attribute_id = ?' => $data['attribute_id']
                ];
                $this->connection->delete($tableName, $condition);
                return true;
            }
        }
        return false;
    }

    /**
     * Unassign attributes from attribute set by attribute group
     *
     * @param array $data
     * @return bool
     */
    public function unassignAttributeFromAttributeSetByGroup($data = [])
    {
        if($data && is_array($data) && !empty($data)) {
            $tableName = $this->connection->getTableName('eav_entity_attribute');
            $valid = true;
            $allowedColumns = ['entity_type_id', 'attribute_set_id', 'attribute_group_id'];
            foreach($allowedColumns as $column) {
                if(!array_key_exists($column, $data)) {
                    $valid = false;
                    break;
                }
            }
            if($valid) {
                $condition = [
                    'entity_type_id = ?' => $data['entity_type_id'],
                    'attribute_set_id = ?' => $data['attribute_set_id'],
                    'attribute_group_id = ?' => $data['attribute_group_id']
                ];
                $this->connection->delete($tableName, $condition);
                return true;
            }
        }
        return false;
    }

    /**
     * Get attributes of default attribute set
     * @return array
     */
    public function getDefaultAttributeSetAttributes()
    {
        $tableName = $this->connection->getTableName('eav_entity_attribute');
        $select = $this->connection->select()->from(
            $tableName,
            ['entity_attribute_id', 'entity_type_id', 'attribute_set_id', 'attribute_group_id', 'attribute_id', 'sort_order']
        )->where(
            'entity_type_id = :entityTypeId AND attribute_set_id = :attributeSetId '
        );
        $bind = ['entityTypeId' => 4, 'attributeSetId' => 4];
        $result = $this->connection->fetchAll($select, $bind);
        return $result ?: [];
    }

    /**
     * Get attribute group of default attribute set
     * @return array
     */
    public function getDefaultAttributeSetAttributeGroups()
    {
        $tableName = $this->connection->getTableName('eav_attribute_group');
        $select = $this->connection->select()->from(
            $tableName,
            ['attribute_group_id', 'attribute_set_id', 'attribute_group_name', 'sort_order', 'default_id', 'attribute_group_code', 'tab_group_code']
        )->where(
            'attribute_set_id = :attributeSetId '
        );
        $bind = ['attributeSetId' => 4];
        $result = $this->connection->fetchAll($select, $bind);
        return $result ?: [];
    }

    /**
     * Get product Id by sku
     * @param $sku
     * @return int
     */
    public function getProductIdBySku($sku)
    {
        $productId = 0;
        if($sku) {
            $tableName = $this->connection->getTableName('catalog_product_entity');
            $select = $this->connection->select()->from(
                $tableName,
                ['entity_id']
            )->where(
                'sku = :sku'
            );
            $bind = ['sku' => $sku];
            $productId = (int) $this->connection->fetchOne($select, $bind);
        }
        return $productId;
    }

    /**
     * Get product Id by sku
     * @param int $productId
     * @return string
     */
    public function getProductSkuById(int $productId)
    {
        $sku = '';
        if($productId) {
            $tableName = $this->connection->getTableName('catalog_product_entity');
            $select = $this->connection->select()->from(
                $tableName,
                ['sku']
            )->where(
                'entity_id = :productId'
            );
            $bind = ['productId' => $productId];
            $sku = $this->connection->fetchOne($select, $bind);
        }
        return $sku;
    }

    /**
     * Get category id by 'code' attribute
     *
     * @param string $categoryCode
     * @return int|null
     */
    public function getCategoryIdByCode(string $categoryCode)
    {
        $categoryId = null;
        if($categoryCode) {
            $attributeId = $this->getAttributeIdByCode('code', 3);
            if($attributeId){
                $connection = $this->resource->getConnection();
                $select = $connection->select()->from(
                    ['e' => $connection->getTableName('catalog_category_entity')],
                    'e.entity_id'
                )->join(
                    ['v' => $connection->getTableName('catalog_category_entity_varchar')],
                    'e.row_id = v.row_id AND v.attribute_id = :attributeId',
                    []
                )->where(
                    'v.value = :categoryCode'
                );
                $bind = [
                    'attributeId' => $attributeId,
                    'categoryCode' => $categoryCode
                ];
                return (int) $connection->fetchOne($select, $bind);
            }
        }
        return $categoryId;
    }

    /**
     * Get category code by id
     *
     * @param int $categoryid
     * @return string
     */
    public function getCategoryCodeById(int $categoryId)
    {
        $categoryCode = '';
        if($categoryId) {
            $attributeId = $this->getAttributeIdByCode('code', 3);
            if($attributeId){
                $connection = $this->resource->getConnection();
                $select = $connection->select()->from(
                    ['e' => $connection->getTableName('catalog_category_entity')],
                    []
                )->join(
                    ['v' => $connection->getTableName('catalog_category_entity_varchar')],
                    'e.row_id = v.row_id AND v.attribute_id = :attributeId',
                    ['code' => 'v.value']
                )->where(
                    'e.entity_id = :categoryId'
                );
                $bind = [
                    'attributeId' => $attributeId,
                    'categoryId' => $categoryId
                ];
                return $connection->fetchOne($select, $bind);
            }
        }
        return $categoryCode;
    }

}
