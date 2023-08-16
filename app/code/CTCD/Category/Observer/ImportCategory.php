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

/**
 * Class ImportCategory
 */
class ImportCategory implements \Magento\Framework\Event\ObserverInterface
{
	/**
	 * @var \Magento\Framework\Filesystem
	 */
	protected $filesystem;

	/**
	 * @var \Magento\Framework\File\Csv
	 */
	protected $csv;

	/**
	 * @var \CTCD\Category\Api\Data\CategoryImportDataInterfaceFactory
	 */
	protected $importData;

	/**
	 * @var \CTCD\Category\Api\CategoryImportDataRepositoryInterface
	 */
	protected $importDataRepository;

	/**
	 * @param \Magento\Framework\Filesystem $filesystem
	 * @param \Magento\Framework\File\Csv $csv
	 * @param \CTCD\Category\Api\Data\CategoryImportDataInterfaceFactory $importData
	 * @param \CTCD\Category\Api\CategoryImportDataRepositoryInterface $importDataRepository
	 */
	public function __construct(
		\Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Csv $csv,
		\CTCD\Category\Api\Data\CategoryImportDataInterfaceFactory $importData,
        \CTCD\Category\Api\CategoryImportDataRepositoryInterface $importDataRepository
	)
	{
		$this->csv = $csv;
		$this->filesystem = $filesystem;
		$this->importData = $importData;
		$this->importDataRepository = $importDataRepository;
	}

	/**
	 * Import
	 *
	 * @param \Magento\Framework\Event\Observer $observer
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$importData = $observer->getData('categoryImport');

		$file = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . CategoryImportInterface::FILE_PATH . $importData->getFile();
        $csvData = $this->csv->getData($file);

        $limit = 100;
        $indexGroup = 1;
        $index = 1;
        $array = [];
        $column = is_array($csvData[0]) ? json_encode($csvData[0]) : $csvData[0];

        unset($csvData[0]);

        $totalData = count($csvData);

        if($totalData <= $limit) {
        	$limit = $totalData;
        }

        $arrayKey = array_keys($csvData);
        $lastKey = end($arrayKey);

        foreach ($csvData as $row => $data) {
            $array[$indexGroup][] = $data;
			$countData = count($array[$indexGroup]);

			if(($row == $lastKey) && ($countData <= $limit)) {
				$limit = $countData;
			}

            if ($index == $limit) {
            	$data = $this->importData->create();
                $data->setImportId($importData->getId());
                $data->setJsonData(json_encode($array[$indexGroup]));
                $data->setSequence((int)$indexGroup);
                $data->setColumn($column);
                $data->setStatus(CategoryImportDataInterface::FLAG_PENDING);

                $this->importDataRepository->save($data);

                $indexGroup++;
                $index = 0;
            }

            $index++;
        }

        return $this;
	}
}
