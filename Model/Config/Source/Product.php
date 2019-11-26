<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Model\Config\Source;
/**
 * Class Product
 * @package Magenest\ImageGallery\Model\Config\Source
 */
class Product implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * Product constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     */
    public function __construct(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory)
    {
        $this->productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $productCollection = $this->productCollectionFactory->create()->addAttributeToSelect('name')->getItems();

        $option = [];
        foreach ($productCollection as $product) {
            $option[] = [
                'label' => $product->getData('name'),
                'value' => $product->getData('entity_id')
            ];
        }
        return $option;
    }
}