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
use Magento\Catalog\Model\CategoryFactory;

/**
 * Class Category
 * @package Magenest\ImageGallery\Model\Config\Source
 */
class Category implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Category constructor.
     * @param CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     */
    public function __construct(\Magento\Catalog\Model\CategoryFactory $categoryFactory,
                                \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory)
    {
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $categoryCollection = $this->categoryCollectionFactory->create()->getData();

        foreach ($categoryCollection as $category) {
            $categoryId = $category['entity_id'];
            $categoryName = $this->categoryFactory->create()->load($categoryId)->getData('name');
            $option[] = [
                'label' => $categoryName,
                'value' => $categoryId
            ];
        }
        return $option;
    }
}