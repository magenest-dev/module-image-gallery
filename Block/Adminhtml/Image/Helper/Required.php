<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Block\Adminhtml\Image\Helper;

/**
 * Class Required
 * @package Magenest\ImageGallery\Block\Adminhtml\Image\Helper
 */
class Required extends \Magento\Framework\Data\Form\Element\Image
{
    /**
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        return '';
    }
}