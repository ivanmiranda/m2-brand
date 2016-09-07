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
namespace Pengo\Brand\Model;

class Brandlist extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected  $_brand;
    
    /**
     * 
     * @param \Pengo\Brand\Model\Brand $brand
     */
    public function __construct(
        \Pengo\Brand\Model\Brand $brand
        ) {
        $this->_brand = $brand;
    }
    
    
    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableTemplate()
    {
        $brands = $this->_brand->getCollection()
        ->addFieldToFilter('status', '1');
        $listBrand = array();
        foreach ($brands as $brand) {
            $listBrand[] = array('label' => $brand->getName(),
                'value' => $brand->getId());
        }
        return $listBrand;
    }

    /**
     * Get model option as array
     *
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $options = array();
        $options = $this->getAvailableTemplate();

        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => '-- Please Select --',
                ));
        }
        return $options;
    }
}