<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */
namespace Magenest\ImageGallery\Block\Adminhtml\Image\Edit\Tab;

/**
 * Class General
 */
class LinkWithProduct extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Image\Collection
     */
    protected $_productCollection;
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Image\CollectionFactory
     */
    protected $imageCollectionFactory;
    /**
     * @var \Magenest\ImageGallery\Model\ImageFactory
     */
    protected $imageFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magenest\ImageGallery\Model\ResourceModel\Image\CollectionFactory $imageCollectionFactory
     * @param \Magenest\ImageGallery\Model\ImageFactory $imageFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magenest\ImageGallery\Model\ResourceModel\Image\CollectionFactory $imageCollectionFactory,
        \Magenest\ImageGallery\Model\ImageFactory $imageFactory,
        array $data = []
    ) {
        $this->_productCollection = $productCollection;
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->imageFactory = $imageFactory;
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
        $image_id = $this->getRequest()->getParam('id');
        $currentProduct_id = $this->imageFactory->create()->load($image_id)->getData('product_id');

        $productCollection= $this->_productCollection->create()->addAttributeToSelect(['entity_id','name','sku','price']);
        $imageCollection = $this->imageCollectionFactory->create()->addFieldToSelect('product_id');
        $imageArray = [];

        foreach ($imageCollection as $image)
        {
            $imageArray[] = $image['product_id'];
        }

        $validProduct_id = [];
        foreach ($productCollection->getData() as $product)
        {
            if (!in_array($product['entity_id'],$imageArray))
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
        $this->addColumn('link_product', array(
            'header' => __('Link With Product'),
            'align' => 'center',
            'index' => 'link_product',
            'sortable' => false,
            'filter' => false,
            'renderer' => '\Magenest\ImageGallery\Block\Adminhtml\Image\Grid\Renderer\LinkWithProduct',
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
        return $this->getUrl('imagegallery/image/productsgrid', ['_current' => true]);
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
