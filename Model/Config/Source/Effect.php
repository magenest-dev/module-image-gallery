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
 * Class Effect
 * @package Magenest\ImageGallery\Model\Config\Source
 */
class Effect implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 0, 'label' => __('None')]
            , ['value' => 1, 'label' => __('Fade')]
            ,['value' => 2, 'label' => __('Elastic')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('None'), 1 => __('Fade'),2 => __('Elastic')];
    }
}
