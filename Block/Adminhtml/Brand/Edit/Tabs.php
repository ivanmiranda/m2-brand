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
namespace Pengo\Brand\Block\Adminhtml\Brand\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Brand Information'));
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        $this->addTab(
                'general',
                [
                    'label' => __('Brand Information'),
                    'content' => $this->getLayout()->createBlock('Pengo\Brand\Block\Adminhtml\Brand\Edit\Tab\Main')->toHtml()
                ]
            );

        $this->addTab(
                'products',
                [
                    'label' => __('Products'),
                    'url' => $this->getUrl('vesbrand/*/products', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );

        $this->addTab(
                'design',
                [
                    'label' => __('Design'),
                    'content' => $this->getLayout()->createBlock('Pengo\Brand\Block\Adminhtml\Brand\Edit\Tab\Design')->toHtml()
                ]
            );

        $this->addTab(
                'meta',
                [
                    'label' => __('Meta Data'),
                    'content' => $this->getLayout()->createBlock('Pengo\Brand\Block\Adminhtml\Brand\Edit\Tab\Meta')->toHtml()
                ]
            );
    }
}
