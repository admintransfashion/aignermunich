<?php
/**
 * @category Trans
 * @package  Trans_Gtm
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   ashadi <ashadi.sejati@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */
namespace Trans\Gtm\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Search helper
 * @api
 * @since 100.0.2
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     * @since 100.1.0
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->customerSession = $customerSession;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Get hash of email
     * @param string|null $email
     * @return string|null
     */
    public function getHashEmail($email = null)
    {
        return $email ? hash('sha256', $email) : '';
    }

    /**
     * get customer id
     * @return string
     */
    public function getCustomerIdCustom()
    {
        //return current customer ID
        if ($this->customerSession->getCustomer()->getEmail()) {
            $hashcustomeremail = hash('sha256', $this->customerSession->getCustomer()->getEmail());
            return $hashcustomeremail;
        }
        return false;
    }

    /**
     * get current customer id
     * @return string
     */
    public function getCurrentCustomerId()
    {
        return $this->getCustomerIdCustom() ?: 'Not Login yet';
    }

    /**
     * @param int $productId
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    public function getProductById($productId)
    {
        $product = null;
        if($productId && (int) $productId > 0){
            try {
                $product = $this->productRepository->getById($productId);
            } catch (\Exception $e) {
            }
        }
        return $product;
    }

    /**
     * @param int $productId
     * @param string|null $attributeCode
     * @return string|null
     */
    public function getProductAttributeValue($productId, $attributeCode = null)
    {
        $value = '';
        if($attributeCode && $product = $this->getProductById($productId)){
            $value = $product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product);
        }
        return $value;
    }

    /**
     * Get Category By ID
     *
     * @param int $categoryId
     * @param null $storeId
     * @return \Magento\Catalog\Api\Data\CategoryInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryById($categoryId, $storeId = null)
    {
        $category = null;
        if($categoryId && (int) $categoryId > 0){
            try {
                $category = $this->categoryRepository->get($categoryId, $storeId);
            } catch (\Exception $e) {
            }
        }
        return $category;
    }

    /**
     * Get Category Name By ID
     *
     * @param int $categoryId
     * @return string|null
     */
    public function getCategoryNameById($categoryId)
    {
        $name = '';
        if($category = $this->getCategoryById($categoryId)){
            $name = $category->getName();
        }
        return $name;
    }

    /**
     * Get Category C0 Name (Men, Women, etc)
     * @param string|null $path
     * @return string|null
     */
    public function getCategoryC0NameByPath($path = null)
    {
        $name = '';
        if($path && strpos($path, '/') !== false){
            $categoryIds = explode('/', $path);
            if(isset($categoryIds[2])){
                $name = $this->getCategoryNameById($categoryIds[2]);
            }
        }
        return $name;
    }

    /**
     * Get Category Names by product object
     * @param $product
     * @return string|null
     */
    public function getCategoryNamesByProduct($product)
    {
        $name = '';
        if($product && $product->getId()){
            $categories = $product->getCategoryCollection();
            $names = [];
            foreach($categories as $category){
                $names[] = $this->getCategoryNameById($category->getId());
            }

            $names = array_filter(array_unique($names));
            sort($names);
            $name = !empty($names) ? implode(',', $names) : '';
        }
        return $name;
    }

    /**
     * Get Category C0 Names by product object
     *
     * @param $product
     * @return string|null
     */
    public function getCategoryC0NamesByProduct($product)
    {
        $name = '';
        if($product && $product->getId()){
            $categories = $product->getCategoryCollection();
            $names = [];
            foreach($categories as $category){
                $names[] = $this->getCategoryC0NameByPath($category->getPath());
            }

            $names = array_filter(array_unique($names));
            $name = !empty($names) ? implode(',', $names) : '';
        }
        return $name;
    }


}
