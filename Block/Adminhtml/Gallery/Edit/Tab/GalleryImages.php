<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab;

/**
 * Class General
 */
class GalleryImages extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Image\Collection
     */
    protected $_imageCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magenest\ImageGallery\Model\ResourceModel\Image\Collection
     $imageCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magenest\ImageGallery\Model\ResourceModel\Image\Collection
        $imageCollection,
        array $data = []
    ) {
        $this->_imageCollection = $imageCollection;
        parent::__construct($context, $backendHelper, $data);
        $this->setEmptyText(__('No Image Found'));
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('imagesGrid');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_imageCollection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('choose_image', array(
            'header' => __('Choose Image'),
            'align' => 'center',
            'index' => 'choose_image',
            'sortable' => false,
            'filter' => false,
            'renderer' => '\Magenest\ImageGallery\Block\Adminhtml\Gallery\Grid\Renderer\SelectImage',
        ));

        $this->addColumn(
            'image_id',
            [
                'header' => __('Image ID'),
                'index' => 'image_id',
                'type' => 'number'
            ]
        );

        $this->addColumn(
            'image',
            array(
                'header' => __('Image'),
                'index' => 'image',
                'sortable' => false,
                'filter' => false,
                'renderer'  => '\Magenest\ImageGallery\Block\Adminhtml\Gallery\Grid\Renderer\Image',
            )
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
            ]
        );
        $this->addColumn(
            'description',
            [
                'header' => __('Description'),
                'index' => 'description',
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('imagegallery/gallery/imagesgrid', ['_current' => true]);
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Framework\DataObject $item
     * @return string
     */
    public function getRowUrl($item)
    {
        return "";
    }
}
