<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Category
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Category\Observer;

use Magento\Framework\App\Filesystem\DirectoryList;
use CTCD\Category\Api\Data\CategoryImportInterface;
use CTCD\Category\Api\Data\CategoryImportDataInterface;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MapCategory
 */
class MapCategory implements \Magento\Framework\Event\ObserverInterface
{
	/**
	 * @var \CTCD\Category\Api\CategoryImportMapRepositoryInterface
	 */
	protected $importMapRepository;

	/**
	 * @param \CTCD\Category\Api\CategoryImportMapRepositoryInterface $importMapRepository
	 */
	public function __construct(
		\CTCD\Category\Api\CategoryImportMapRepositoryInterface $importMapRepository
	)
	{
		$this->importMapRepository = $importMapRepository;
	}

	/**
	 * Import
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$importData = $observer->getData('category');

		$categoryId = $importData->getId();

		try {
			$mapCategory = $this->importMapRepository->getByCategoryId($categoryId);
			$this->importMapRepository->delete($mapCategory);
		} catch (NoSuchEntityException $e) {
		} catch (ValidatorException $e) {
		} catch (\Exception $e) {
		}

        return $this;
	}
}
