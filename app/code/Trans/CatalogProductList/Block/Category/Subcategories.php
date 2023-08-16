<?php

/**
 * @category Trans
 * @package  Trans_CatalogProductList
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 *
 * @author   Muhammad Randy <muhammad.randy@transdigital.co.id>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

 namespace Trans\CatalogProductList\Block\Category;

 use \Magento\Framework\Controller\Result\Redirect;

/**
 * Class Subcategories
 */
 class Subcategories extends \Magento\Framework\View\Element\Template
 {
     /**
     * @var \Magento\Framework\Registry
     */
     protected $registry;

     /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
     protected $categories;

     /**
     * @var \Psr\Log\LoggerInterface
     */
     protected $logger;

     /**
     * @var \Magento\Customer\Model\Session
     */
     protected $customerSession;

     /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categories,
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Trans\CategoryNavigation\Helper\Catalog $catalogHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
     function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categories,
        \Magento\Customer\Model\Session $customerSession,
        \Trans\CategoryNavigation\Helper\Catalog $catalogHelper,
        array $data = []
     ){
        $this->logger = $logger;
        $this->catalogHelper = $catalogHelper;
        $this->registry = $registry;
        $this->categories = $categories;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
     }

     /**
      * Get categories for PLP sidebar
      *
      * @return array
      */
     public function getCategories()
     {
         $categories = [];
         try
         {
             $currentCategory = $this->registry->registry('current_category');
             if($currentCategory){
                 $currentCategoryId = $currentCategory->getId();
                 $catPath = $currentCategory->getPath(); //get current category
                 $explodedPath = explode("/", $catPath);
                 $parentCategoryId = $explodedPath[2];
                 $parentCategory = $this->categories->get($parentCategoryId);

                 if($currentCategoryId != $parentCategoryId ){
                     $categories[] = [
                         'parent' => true,
                         'active' => false,
                         'category' => $parentCategory
                     ];
                 }

                 $childrenCategories = $parentCategory->getChildrenCategories();
                 if(count($childrenCategories) > 0){
                     foreach($childrenCategories as $subCategory){
                         $categories[] = [
                             'parent' => false,
                             'active' => ($currentCategoryId == $subCategory->getId() ? true : false),
                             'category' => $subCategory
                         ];
                     }
                 }
             }
         } catch (\Exception $e) {
         }

         return $categories;
     }

     /**
      * Get categories for PLP sidebar - mobile view
      * @return array
      */
     public function getMobileCategories()
     {
         $categories = [];
         try
         {
             $currentCategory = $this->registry->registry('current_category');
             if($currentCategory){
                 $currentCategoryId = $currentCategory->getId();
                 $catPath = $currentCategory->getPath(); //get current category
                 $explodedPath = explode("/", $catPath);
                 $parentCategoryId = $explodedPath[2];
                 $parentCategory = $this->categories->get($parentCategoryId);

                 $categories['title'] = $parentCategory->getName();

                 $childrenCategories = $parentCategory->getChildrenCategories();
                 if(count($childrenCategories) > 0){
                     foreach($childrenCategories as $subCategory){
                         $subCategories = $subCategory->getChildrenCategories();
                         $totalSubCategory = count($subCategories);
                         $categories['categories'][] = [
                             'first' => false,
                             'last' => false,
                             'level' => $subCategory->getLevel(),
                             'active' => $currentCategoryId == $subCategory->getId(),
                             'child_count' => $totalSubCategory,
                             'category' => $subCategory
                         ];
                         if($totalSubCategory > 0) {
                             $idx = 1;
                             foreach($subCategories as $subSubCategory){
                                 $categories['categories'][] = [
                                     'first' => $idx == 1,
                                     'last' => $idx == $totalSubCategory,
                                     'level' => $subSubCategory->getLevel(),
                                     'active' => $currentCategoryId == $subSubCategory->getId(),
                                     'child_count' => 0,
                                     'category' => $subSubCategory
                                 ];
                                 $idx++;
                             }
                         }
                     }
                 }
             }
         } catch (\Exception $e) {
         }

         return $categories;
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
     * check category product
     *
     * @param int $categoryId
     * @return bool
     */
    public function checkCatagoryProduct(int $categoryId)
    {
        return $this->catalogHelper->checkCategoryProduct($categoryId);
    }

 }
