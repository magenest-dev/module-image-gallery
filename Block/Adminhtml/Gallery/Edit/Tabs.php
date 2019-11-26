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
namespace Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit;

/**
 * Class Tabs
 * @package Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit
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
        $this->setTitle(__('Gallery Information'));
    }

    /**
     *  render layout
     */
    protected function _prepareLayout()
    {
        $this->addTab(
            'general',
            ['label' => __('Gallery'),
                'content' => $this->getLayout()->createBlock(
                    'Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab\Main',
                    'imagegalley.gallery.tab.general'
                )->toHtml(),
            ]
        );
        $this->addTab(
            'galleryimages',
            ['label' => __('Gallery Images'),
                'content' => $this->getLayout()->createBlock(
                    'Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab\GalleryImages',
                    'imagegalley.gallery.tab.galleryimages'
                )->toHtml(),
            ]
        );
        $this->addTab(
            'attachproducts',
            ['label' => __('Attach Products'),
                'content' => $this->getLayout()->createBlock(
                    'Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab\AttachProducts',
                    'imagegalley.gallery.tab.attachproducts'
                )->toHtml(),
            ]
        );
        $this->addTab(
            'attachcategory',
            ['label' => __('Attach Categories'),
                'content' => $this->getLayout()->createBlock(
                    'Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab\AttachCategories',
                    'imagegalley.gallery.tab.attachcategory'
                )->toHtml(),
            ]
        );
    }
}
