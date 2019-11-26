<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */
namespace Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab;

/**
 * Class General
 */
class AttachProducts extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Image\Collection
     */
    protected $_productCollection;
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory
     */
    protected $galleryCollectionFactory;
    /**
     * @var \Magenest\ImageGallery\Model\GalleryFactory
     */
    protected $galleryFactory;

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
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $galleryCollectionFactory,
        \Magenest\ImageGallery\Model\GalleryFactory $galleryFactory,
        array $data = []
    ) {
        $this->_productCollection = $productCollection;
        $this->galleryCollectionFactory = $galleryCollectionFactory;
        $this->galleryFactory = $galleryFactory;
        parent::__construct($context, $backendHelper, $data);
        $this->setEmptyText(__('No Product Found'));
    }

    /**
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $gallery_id = $this->getRequest()->getParam('id');
        $currentProduct_id = $this->galleryFactory->create()->load($gallery_id)->getData('product_id');

        $productCollection= $this->_productCollection->create()->addAttributeToSelect(['entity_id','name','sku','price']);
        $galleryCollection = $this->galleryCollectionFactory->create()->addFieldToSelect('product_id');
        $galleryArray = [];

        foreach ($galleryCollection as $gallery)
        {
            $galleryArray[] = $gallery['product_id'];
        }

        $validProduct_id = [];
        foreach ($productCollection->getData() as $product)
        {
            if (!in_array($product['entity_id'],$galleryArray))
                array_push($validProduct_id,$product['entity_id']);
            elseif ($product['entity_id'] == $currentProduct_id)
            {
                array_push($validProduct_id,$product['entity_id']);
            }
        }

        $validProductCollection = $this->_productCollection->create()
            ->addAttributeToSelect(['entity_id','name','sku','price'])
            ->addAttributeToFilter('entity_id', array('in' => $validProduct_id));
        $this->setCollection($validProductCollection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('attach_products', array(
            'header' => __('Attach Products'),
            'align' => 'center',
            'index' => 'attach_products',
            'sortable' => false,
            'filter' => false,
            'renderer' => '\Magenest\ImageGallery\Block\Adminhtml\Gallery\Grid\Renderer\AttachProducts',
        ));
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'index' => 'entity_id',
                'type' => 'number'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'index' => 'price',
                'type' => 'currency'
            ]
        );
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('imagegallery/gallery/productsgrid', ['_current' => true]);
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
