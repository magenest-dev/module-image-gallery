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
 * Class LayoutType
 * @package Magenest\ImageGallery\Model\Config\Source
 */
class LayoutType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = [
            ['label' => '4 Columns', 'value' => '0'],
            ['label' => 'Grid', 'value' => '1']
        ];

        return $option;
    }
}