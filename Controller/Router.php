<?php
/**
 * Pengo
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Pengo.com license that is
 * available through the world-wide-web at this URL:
 * 
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Pengo
 * @package    Pengo_Brand
 * @copyright  Copyright (c) 2016 Pengo (http://www.pengo.mx/)
 * @license    
 */
namespace Pengo\Brand\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Url;

class Router implements RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Response
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * @var bool
     */
    protected $dispatched;

    /**
     * Brand Factory
     *
     * @var \Pengo\Brand\Model\Brand $brandCollection
     */
    protected $_brandCollection;

    /**
     * Brand Factory
     *
     * @var \Pengo\Brand\Model\Group $groupCollection
     */
    protected $_groupCollection;

    /**
     * Brand Helper
     */
    protected $_brandHelper;

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ActionFactory          $actionFactory   
     * @param ResponseInterface      $response        
     * @param ManagerInterface       $eventManager    
     * @param \Pengo\Brand\Model\Brand $brandCollection 
     * @param \Pengo\Brand\Model\Group $groupCollection 
     * @param \Pengo\Brand\Helper\Data $brandHelper     
     * @param StoreManagerInterface  $storeManager    
     */
    public function __construct(
    	ActionFactory $actionFactory,
    	ResponseInterface $response,
        ManagerInterface $eventManager,
        \Pengo\Brand\Model\Brand $brandCollection,
        \Pengo\Brand\Model\Group $groupCollection,
        \Pengo\Brand\Helper\Data $brandHelper,
        StoreManagerInterface $storeManager
        )
    {
    	$this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->response = $response;
        $this->_brandHelper = $brandHelper;
        $this->_brandCollection = $brandCollection;
        $this->_groupCollection = $groupCollection;
        $this->storeManager = $storeManager;
    }
    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface
     */
    public function match(RequestInterface $request)
    {
        $_brandHelper = $this->_brandHelper;
        if (!$this->dispatched) {
            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;
            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'Pengo_Brand_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
                );
            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                    );
            }
            if (!$condition->getContinue()) {
                return null;
            }
            $route = $_brandHelper->getConfig('general_settings/route');
            if( $route !='' && $urlKey == $route )
            {
                $request->setModuleName('vesbrand')
                ->setControllerName('index')
                ->setActionName('index');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                    );
            }
            $url_prefix = $_brandHelper->getConfig('general_settings/url_prefix');
            $url_suffix = $_brandHelper->getConfig('general_settings/url_suffix');

            $identifiers = explode('/',$urlKey);
            //Check Group Url
            if( (count($identifiers) == 2 && $identifiers[0] == $url_prefix && strpos($identifiers[1], $url_suffix)) || (trim($url_prefix) == '' && count($identifiers) == 1)){
                $brandUrl = '';
                if(trim($url_prefix) == '' && count($identifiers) == 1){
                    $brandUrl = str_replace($url_suffix, '', $identifiers[0]);
                }
                if(count($identifiers) == 2){
                    $brandUrl = str_replace($url_suffix, '', $identifiers[1]);
                }
                $group = $this->_groupCollection->getCollection()
                ->addFieldToFilter('status', array('eq' => 1))
                ->addFieldToFilter('url_key', array('eq' => $brandUrl))
                ->getFirstItem();

                if($group && $group->getId()){
                    $request->setModuleName('vesbrand')
                    ->setControllerName('group')
                    ->setActionName('view')
                    ->setParam('group_id', $group->getId());
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                    $request->setDispatched(true);
                    $this->dispatched = true;
                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                        );
                }
            }

            // Check Brand Url Key
            if( (count($identifiers) == 2 && $identifiers[0] == $url_prefix && strpos($identifiers[1], $url_suffix)) || (trim($url_prefix) == '' && count($identifiers) == 1)){
                if(count($identifiers) == 2){
                    $brandUrl = str_replace($url_suffix, '', $identifiers[1]);
                }
                if(trim($url_prefix) == '' && count($identifiers) == 1){
                    $brandUrl = str_replace($url_suffix, '', $identifiers[0]);
                }

                $brand = $this->_brandCollection->getCollection()
                ->addFieldToFilter('status', array('eq' => 1))
                ->addFieldToFilter('url_key', array('eq' => $brandUrl))
                ->getFirstItem();

                if($brand && $brand->getId() && (in_array($this->storeManager->getStore()->getId(), $brand->getStoreId()) || in_array(0,$brand->getStoreId())) ){
                    $request->setModuleName('vesbrand')
                    ->setControllerName('brand')
                    ->setActionName('view')
                    ->setParam('brand_id', $brand->getId());
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
                    $request->setDispatched(true);
                    $this->dispatched = true;
                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                        );
                }
            }
        }
    }
}