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
namespace Pengo\Brand\Controller\Brand;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Pengo\Brand\Model\Layer\Resolver;

class View extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Pengo\Brand\Model\Brand
     */
    protected $_brandModel;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog Layer Resolver
     *
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Pengo\Brand\Helper\Data
     */
    protected $_brandHelper;

    /**
     * @param Context                                             $context              [description]
     * @param \Magento\Store\Model\StoreManager                   $storeManager         [description]
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    [description]
     * @param \Pengo\Brand\Model\Brand                              $brandModel           [description]
     * @param \Magento\Framework\Registry                         $coreRegistry         [description]
     * @param Resolver                                            $layerResolver        [description]
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory [description]
     * @param \Pengo\Brand\Helper\Data                              $brandHelper          [description]
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Pengo\Brand\Model\Brand $brandModel,
        \Magento\Framework\Registry $coreRegistry,
        Resolver $layerResolver,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Pengo\Brand\Helper\Data $brandHelper
        ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_brandModel = $brandModel;
        $this->layerResolver = $layerResolver;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_brandHelper = $brandHelper;
    }

    public function _initBrand()
    {
        $brandId = (int)$this->getRequest()->getParam('brand_id', false);
        if (!$brandId) {
            return false;
        }
        try{
            $brand = $this->_brandModel->load($brandId);
        } catch (NoSuchEntityException $e) {
            return false;
        }
        $this->_coreRegistry->register('current_brand', $brand);
        return $brand;
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if(!$this->_brandHelper->getConfig('general_settings/enable')){
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $brand = $this->_initBrand();
        if ($brand) {
            $this->layerResolver->create('brand');
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $page = $this->resultPageFactory->create();
            // apply custom layout (page) template once the blocks are generated
            if ($brand->getPageLayout()) {
                $page->getConfig()->setPageLayout($brand->getPageLayout());
            }
            $page->addHandle(['type' => 'Pengo_Brand_'.$brand->getId()]);
            if (($layoutUpdate = $brand->getLayoutUpdateXml()) && trim($layoutUpdate)!='') {
                $page->addUpdate($layoutUpdate);
            }

            /*$collectionSize = $brand->getProductCollection()->getSize();
            if($collectionSize){
                $page->addHandle(['type' => 'vesbrand_brand_layered']);
            }*/
            $page->getConfig()->addBodyClass('page-products')
            ->addBodyClass('brand-' . $brand->getUrlKey());
            return $page;
        }elseif (!$this->getResponse()->isRedirect()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
    }
}