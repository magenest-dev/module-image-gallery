<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Block\Widget;

/**
 * Class ImageGallery
 * @package Magenest\ImageGallery\Block\Widget
 */
class ImageGallery extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('landingspage.phtml');
    }
}
