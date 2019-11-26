<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Model\ResourceModel\Image;

/**
 * Image Collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Initialize resource collection
     * *
    @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\ImageGallery\Model\Image', 'Magenest\ImageGallery\Model\ResourceModel\Image');
    }
}
