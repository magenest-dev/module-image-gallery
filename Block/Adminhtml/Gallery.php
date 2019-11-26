<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Block\Adminhtml;

/**
 * Class Gallery
 * @package Magenest\ImageGallery\Block\Adminhtml
 */
class Gallery extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_ImageGallery';
        $this->_controller = 'adminhtml_gallery';
        parent::_construct();
    }
}
