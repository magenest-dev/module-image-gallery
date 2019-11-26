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
 * Class AnimationSpeed
 * @package Magenest\ImageGallery\Model\Config\Source
 */
class AnimationSpeed implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $option = [
            ['label' => 'Slow', 'value' => '0'],
            ['label' => 'Medium', 'value' => '1'],
            ['label' => 'Fast', 'value' => '2']
        ];

        return $option;
    }
}