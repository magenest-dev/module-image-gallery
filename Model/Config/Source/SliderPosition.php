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
 * Class SliderPosition
 * @package Magenest\ImageGallery\Model\Config\Source
 */
class SliderPosition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = [
            ['label' => 'Top', 'value' => '0'],
            ['label' => 'Footer', 'value' => '1'],
            ['label' => 'Top & Footer', 'value' => '2']
        ];

        return $option;
    }
}