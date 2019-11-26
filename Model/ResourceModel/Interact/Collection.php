<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Model\ResourceModel\Interact;

/**
 * Class Collection
 * @package Magenest\ImageGallery\Model\ResourceModel\Interact
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     * *
    @return void
     */
    public function _construct()
    {
        $this->_init('Magenest\ImageGallery\Model\Interact', 'Magenest\ImageGallery\Model\ResourceModel\Interact');
    }
}
