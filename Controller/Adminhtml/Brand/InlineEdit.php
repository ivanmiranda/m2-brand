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
namespace Pengo\Brand\Controller\Adminhtml\Brand;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Api\PageRepositoryInterface as PageRepository;

use Pengo\Brand\Model\Brand as BrandModel;

class InlineEdit extends \Magento\Backend\App\Action
{

    /** @var PageRepository  */
    protected $brandRepository;

    /** @var JsonFactory  */
    protected $jsonFactory;

    /** @var brandModel */
    protected $brandModel;

    /**
     * @param Context $context
     * @param PageRepository $brandRepository
     * @param JsonFactory $jsonFactory
     * @param Pengo\Brand\Model\Brand $brandModel
     */
    public function __construct(
        Context $context,
        PageRepository $brandRepository,
        JsonFactory $jsonFactory,
        BrandModel $brandModel
        ) {
        parent::__construct($context);
        $this->pageRepository = $brandRepository;
        $this->jsonFactory = $jsonFactory;
        $this->brandModel = $brandModel;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
                ]);
        }

        foreach (array_keys($postItems) as $brandId) {
            /** @var \Pengo\Brand\Model\Group $brand */
            $brand = $this->_objectManager->create('Pengo\Brand\Model\Brand');
            $brandData = $postItems[$brandId];

            try {
                $brand->load($brandId);
                $brand->setData(array_merge($brand->getData(), $brandData));
                $brand->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithgroupId($brand, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithgroupId($brand, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPageId(
                    $page,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => 'abc',
            'error' => 'def'
            ]);
    }

    /**
     * Add page title to error message
     *
     * @param PageInterface $brand
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithgroupId($brand, $errorText)
    {
        return '[Page ID: ' . $brand->getId() . '] ' . $errorText;
    }
}