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
 * Class AnimationType
 * @package Magenest\ImageGallery\Model\Config\Source
 */
class AnimationType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = [
            ['label' => 'Zoom-in', 'value' => '0'],
            ['label' => 'Zoom-out', 'value' => '1']
        ];

        return $option;
    }
}