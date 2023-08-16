<?php

/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category CTCD
 * @package  CTCD_Inspiration
 * @license  Proprietary
 *
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace CTCD\Inspiration\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory
     */
    protected $inspirationCollectionFactory;

    /**
     * @var \CTCD\Inspiration\Helper\Data
     */
    protected $inspirationHelper;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory $inspirationCollectionFactory
     * @param \CTCD\Inspiration\Helper\Data $inspirationHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $response,
        \CTCD\Inspiration\Model\ResourceModel\Inspiration\CollectionFactory $inspirationCollectionFactory,
        \CTCD\Inspiration\Helper\Data $inspirationHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->inspirationCollectionFactory = $inspirationCollectionFactory;
        $this->inspirationHelper = $inspirationHelper;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if($this->inspirationHelper->isModuleEnabled()){
            $inspirations = $this->inspirationCollectionFactory->create();
            $inspirations->addFieldToFilter('is_active', ['eq' => 1]);
            if($inspirations && $inspirations->getSize() <= 0){
                return false;
            }

            $identifier = trim($request->getPathInfo(), '/');
            if($identifier == 'inspiration/list.html'){
                $request->setModuleName('inspiration')->setControllerName('post')-> setActionName('index');
                return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
            } elseif(strpos($identifier, 'inspiration/') !== false) {
                $identifiers = explode('/', $identifier);
                if(strpos(end($identifiers), '.html') !== false){
                    $urlKeys = explode('.', end($identifiers));
                    $key = isset($urlKeys[0]) ? $urlKeys[0] : null;
                    if($key){
                        try {
                            $inspirations->addFieldToFilter('url_key', ['eq' => $key]);
                            $inspirations->getSelect()->limit(1);
                            if($inspirations && $inspirations->getSize() > 0) {
                                foreach($inspirations as $inspiration) {
                                    $request->setModuleName('inspiration')->setControllerName('post')-> setActionName('view')->setParam('id', $inspiration->getId());
                                    return $this->actionFactory->create(\Magento\Framework\App\Action\Forward::class);
                                }
                            }
                        } catch (\Exception $exception) {
                        }
                    }
                }
            }
        }

        return false;
    }
}
