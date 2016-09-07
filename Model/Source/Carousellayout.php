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
namespace Pengo\Brand\Model\Source;

class Carousellayout implements \Magento\Framework\Option\ArrayInterface
{
    protected  $_group;
    
    /**
     * 
     * @param \Pengo\Brand\Model\Group $group
     */
    public function __construct(
        \Pengo\Brand\Model\Group $group
        ) {
        $this->_group = $group;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {   
        $groupList = array();
        $groupList[] = array(
            'label' => __('Owl Carousel'),
            'value' => 'owl_carousel'
            );
        
        $groupList[] = array(
            'label' => __('Bootstrap Carousel'),
            'value' => 'bootstrap_carousel'
            );
        return $groupList;
    }
}
