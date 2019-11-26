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
namespace Magenest\ImageGallery\Block\Adminhtml\Image\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_base_fieldset');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Information'));
    }

    /**
     * prepare tab layout
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'general',
            ['label' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    'Magenest\ImageGallery\Block\Adminhtml\Image\Edit\Tab\Main',
                    'imagegalley.image.tab.general'
                )->toHtml(),
            ]
        );
        $this->addTab(
            'link_product',
            ['label' => __('Link with product'),
                'content' => $this->getLayout()->createBlock(
                    'Magenest\ImageGallery\Block\Adminhtml\Image\Edit\Tab\LinkWithProduct',
                    'imagegalley.image.tab.link_product'
                )->toHtml(),
            ]
        );
    }
}
