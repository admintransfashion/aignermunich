<?php
/**
 * @category Trans
 * @package  Trans_Catalog
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 *
 * Copyright © 2019 PT Trans Digital. All rights reserved.
 * http://www.ctcorpora.com
 */

namespace Trans\Catalog\Controller\Adminhtml\Season;
 
use Magento\Framework\Exception\LocalizedException;
use Trans\Catalog\Api\Data\SeasonInterface;
use Magento\Framework\Exception\NoSuchEntityException;
 
/**
 * Class Season
 */
abstract class Season extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trans_Catalog::season';
 
    /**
     * @var \Trans\Catalog\Model\ResourceModel\Season\CollectionFactory
     */
    protected $collectionFactory;
 
    /**
     * @var \Trans\Catalog\Model\SeasonFactory
     */
    protected $seasonFactory;
 
    /**
     * @var \Trans\Catalog\Api\SeasonRepositoryInterface
     */
    protected $seasonRepository;
 
    /**
     * @param Context $context
     * @param \Trans\Catalog\Model\ResourceModel\Season\CollectionFactory $collectionFactory
     * @param \Trans\Catalog\Model\SeasonFactory $seasonFactory
     * @param \Trans\Catalog\Api\SeasonRepositoryInterface $seasonRepository
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Trans\Catalog\Model\ResourceModel\Season\CollectionFactory $collectionFactory,
        \Trans\Catalog\Api\Data\SeasonInterfaceFactory $seasonFactory,
        \Trans\Catalog\Api\SeasonRepositoryInterface $seasonRepository
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->seasonFactory = $seasonFactory;
        $this->seasonRepository = $seasonRepository;
        
        parent::__construct($context);
    }

    /**
     * init Season
     * 
     * @return \Trans\Catalog\Api\Data\SeasonInterface
     */
    protected function initSeason()
    {
        $seasonid = $this->getRequest()->getParam('id');

        try {
            $season = $this->seasonRepository->getById($seasonid);
        } catch (NoSuchEntityException $e) {
            $season = $this->seasonFactory->create();
        }

        return $season;
    }
}
