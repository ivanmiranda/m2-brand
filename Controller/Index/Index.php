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
namespace Pengo\Brand\Controller\Index;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
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
     * @var \Pengo\Brand\Helper\Data
     */
    protected $_brandHelper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context                                             $context              
     * @param \Magento\Store\Model\StoreManager                   $storeManager         
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory    
     * @param \Pengo\Brand\Helper\Data                              $brandHelper          
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory 
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Pengo\Brand\Helper\Data $brandHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
        ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_brandHelper = $brandHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
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
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $page = $this->resultPageFactory->create();
        return $page;
    }
}