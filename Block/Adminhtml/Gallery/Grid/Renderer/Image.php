<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

/**
 * Created by PhpStorm.
 */

namespace Magenest\ImageGallery\Block\Adminhtml\Gallery\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Image
 * @package Magenest\ImageGallery\Block\Adminhtml\Gallery\Grid\Renderer
 */
class Image extends AbstractRenderer
{
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        StoreManagerInterface $storemanager,
        array $data = []
    ) {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $imagePath = $this->_getValue($row);
        if ($imagePath=="") {
            $test = $this->getViewFileUrl('Magenest_ImageGallery::images/thumbnail.jpg');
            return "<img src=$test width='75' height='75'/>";
        } else {
            $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );

            return "<img src='".$mediaDirectory.$imagePath."' width='75' height='75'/>";
        }
    }
}
