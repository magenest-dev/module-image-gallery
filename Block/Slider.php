<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Block;

/**
 * Class Slider
 * @package Magenest\ImageGallery\Block
 */
class Slider extends \Magento\Catalog\Block\Category\View
{
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory
     */
    protected $galcollectionFactory;
    /**
     * @var \Magenest\ImageGallery\Model\ImageFactory
     */
    protected $imageFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory
     */
    protected $interactCollectionFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory
     */
    protected $galleryImageCollectionFactory;
    /**
     * @var array
     */
    protected $loadedImage;

    /**
     * Slider constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $galcollectionFactory
     * @param \Magenest\ImageGallery\Model\ImageFactory $imageFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory $interactCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     * @param \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory $galleryImageCollectionFactory
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context,
                                \Magento\Catalog\Model\Layer\Resolver $layerResolver,
                                \Magento\Framework\Registry $registry, \Magento\Catalog\Helper\Category $categoryHelper,
                                \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $galcollectionFactory,
                                \Magenest\ImageGallery\Model\ImageFactory $imageFactory,
                                \Magento\Customer\Model\Session $session,
                                \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory $interactCollectionFactory,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
                                \Magento\Catalog\Model\ProductFactory $productFactory,
                                \Magento\Framework\App\ProductMetadataInterface $productMetadata,
                                \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory $galleryImageCollectionFactory,
                                array $data = [])
    {
        $this->galcollectionFactory = $galcollectionFactory;
        $this->imageFactory = $imageFactory;
        $this->customerSession = $session;
        $this->interactCollectionFactory = $interactCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->productFactory = $productFactory;
        $this->productMetadata = $productMetadata;
        $this->galleryImageCollectionFactory = $galleryImageCollectionFactory;
        $this->loadedImage = [];
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
    }

    /**
     * @param $image_id
     * @return \Magenest\ImageGallery\Model\Image
     */
    public function loadImage($image_id)
    {
        return $this->imageFactory->create()->load($image_id);
    }

    /**
     * @return mixed
     */
    public function getCurrentProductId()
    {
        return $this->_coreRegistry->registry('current_product')->getData('entity_id');
    }

    /**
     * @return array
     */
    public function getListImageProduct()
    {
        $productID = $this->getCurrentProductId();
        $collection = $this->galcollectionFactory->create()
            ->addFieldToFilter('status', 0)
            ->addFieldToFilter('product_id', $productID);

        $listImage = [];
        foreach ($collection as $image) {
            $galleryImageCollection = $this->galleryImageCollectionFactory->create()->addFieldToFilter('gallery_id',$image->getData('gallery_id'));
            foreach ($galleryImageCollection as $item)
                if (!in_array($item->getData('image_id'),$listImage))
                    array_push($listImage, $item->getData('image_id'));
        }
        $idValid = [];
        foreach ($listImage as $id) {
            $imageModel = $this->loadImage($id);
            if ($imageModel->getData('status') == 0)
            {
                array_push($idValid, $id);
                $this->loadedImage[$id] = [
                    'image_id' => $imageModel->getData('image_id'),
                    'image' => $imageModel->getData('image'),
                    'title' => $imageModel->getData('title'),
                    'description' => $imageModel->getData('description'),
                    'sortorder' => $imageModel->getData('sortorder'),
                    'status' => $imageModel->getData('status'),
                    'love' => $imageModel->getData('love'),
                    'product_id' => $imageModel->getData('product_id')
                ];
            }
        }

        for ($i = 0; $i < sizeof($idValid) - 1; $i++)
            for ($j = $i + 1; $j < sizeof($idValid); $j++) {
                if ($this->loadedImage[$idValid[$i]]['sortorder'] > $this->loadedImage[$idValid[$j]]['sortorder']) {
                    $changeOrder = $idValid[$i];
                    $idValid[$i] = $idValid[$j];
                    $idValid[$j] = $changeOrder;
                }
            }
        return $idValid;
    }

    /**
     * @return mixed
     */
    public function getHeightOfProductGallery()
    {
        $currentProductId = $this->getCurrentProductId();
        return $this->galcollectionFactory->create()
            ->addFieldToFilter('product_id', $currentProductId)
            ->getFirstItem()->getData('height');
    }

    /**
     * @return mixed
     */
    public function getWidhtOfProductGallery()
    {
        $currentProductId = $this->getCurrentProductId();
        return $this->galcollectionFactory->create()
            ->addFieldToFilter('product_id', $currentProductId)
            ->getFirstItem()->getData('width');
    }

    // (0,2 => top ; 1,2 => footer)

    /**
     * @param $position
     * @return array
     */
    public function getListImageCategory($position)
    {
        $categoryID = $this->getCurrentCategory()->getId();
        $collection = $this->galcollectionFactory->create()
            ->addFieldToFilter('status', 0)
            ->addFieldToFilter('category_id', $categoryID)
            ->addFieldToFilter('category_slider_position',array('in' => $position));

        $listImage = [];
        foreach ($collection as $image) {
            $galleryImageCollection = $this->galleryImageCollectionFactory->create()->addFieldToFilter('gallery_id',$image->getData('gallery_id'));
            foreach ($galleryImageCollection as $item)
                if (!in_array($item->getData('image_id'),$listImage))
                    array_push($listImage, $item->getData('image_id'));
        }
        $idValid = [];
        foreach ($listImage as $id) {
            $imageModel = $this->loadImage($id);
            if ($imageModel->getData('status') == 0)
            {
                array_push($idValid, $id);
                $this->loadedImage[$id] = [
                    'image_id' => $imageModel->getData('image_id'),
                    'image' => $imageModel->getData('image'),
                    'title' => $imageModel->getData('title'),
                    'description' => $imageModel->getData('description'),
                    'sortorder' => $imageModel->getData('sortorder'),
                    'status' => $imageModel->getData('status'),
                    'love' => $imageModel->getData('love'),
                    'product_id' => $imageModel->getData('product_id')
                ];
            }
        }

        for ($i = 0; $i < sizeof($idValid) - 1; $i++)
            for ($j = $i + 1; $j < sizeof($idValid); $j++) {
                if ($this->loadedImage[$idValid[$i]]['sortorder'] > $this->loadedImage[$idValid[$j]]['sortorder']) {
                    $changeOrder = $idValid[$i];
                    $idValid[$i] = $idValid[$j];
                    $idValid[$j] = $changeOrder;
                }
            }
        return $idValid;
    }

    //get width of height of slider , $position( 0,2 => top ; 1,2 => footer)

    /**
     * @param $attribute
     * @param $position
     * @return mixed|string
     */
    public function getSizeOfCategorySlider($attribute, $position)
    {
        $currentCategoryId = $this->getCurrentCategory()->getId();
        $galleryCollection = $this->galcollectionFactory->create()
            ->addFieldToFilter('category_id',$currentCategoryId)
            ->addFieldToFilter('category_slider_position',array('in' => $position))
            ->addFieldToSelect($attribute)
            ->getFirstItem();
        if (isset($galleryCollection[$attribute]))
            return $galleryCollection[$attribute];
        return '';
    }

    /**
     * @return int
     */
    public function checkLogin()
    {
        if ($this->customerSession->isLoggedIn())
            return 1;
        else return 0;
    }

    /**
     * @param $position
     * @return array
     */
    public function getListImageCategoryCollection($position)
    {
        switch ($position){
            case "top":
                $listImageCategory = $this->getListImageCategory(array(0,2));
                break;
            case "footer":
                $listImageCategory = $this->getListImageCategory(array(1,2));
                break;
        }

        $listImageCategoryCollection = [];

        $customerId = $this->customerSession->getCustomerId();
        $color = "white";

        foreach ($listImageCategory as $image)
        {
            if (isset($customerId)) {
                $interactCollection = $this->interactCollectionFactory->create()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('image_id', $this->loadedImage[$image]['image_id'])
                    ->getFirstItem()->getData();

                if (!empty($interactCollection)) {
                    if ($interactCollection['status'] == 0)
                        $color = "red";
                    else
                        $color = "white";
                } else
                    $color = "white";

            }

            $productModel = $this->productFactory->create()->load($this->loadedImage[$image]['product_id']);

            $listImageCategoryCollection[] =[
                'image_id' => $this->loadedImage[$image]['image_id'],
                'image' => $this->loadedImage[$image]['image'],
                'title' => $this->loadedImage[$image]['title'],
                'description' => $this->loadedImage[$image]['description'],
                'love' => $this->loadedImage[$image]['love'],
                'color' => $color,
                'product_id' => $this->loadedImage[$image]['product_id'],
                'product_name' => $productModel->getData('name'),
                'product_link' => $productModel->getProductUrl()
            ];
        }
        return $listImageCategoryCollection;
    }

    /**
     * @return array
     */
    public function getListImageProductCollection()
    {
        $listImageProduct = $this->getListImageProduct();
        $listImageProductCollection = [];

        $customerId = $this->customerSession->getCustomerId();
        $color = "white";

        foreach ($listImageProduct as $image)
        {
            if (isset($customerId)) {
                $interactCollection = $this->interactCollectionFactory->create()
                    ->addFieldToFilter('customer_id', $customerId)
                    ->addFieldToFilter('image_id', $this->loadedImage[$image]['image_id'])
                    ->getFirstItem()->getData();

                if (!empty($interactCollection)) {
                    if ($interactCollection['status'] == 0)
                        $color = "red";
                    else
                        $color = "white";
                } else
                    $color = "white";

            }

            $productModel = $this->productFactory->create()->load($this->loadedImage[$image]['product_id']);

            $listImageProductCollection[] =[
                'image_id' => $this->loadedImage[$image]['image_id'],
                'image' => $this->loadedImage[$image]['image'],
                'title' => $this->loadedImage[$image]['title'],
                'description' => $this->loadedImage[$image]['description'],
                'love' => $this->loadedImage[$image]['love'],
                'color' => $color,
                'product_id' => $this->loadedImage[$image]['product_id'],
                'product_name' => $productModel->getData('name'),
                'product_link' => $productModel->getProductUrl()
            ];
        }
        return $listImageProductCollection;
    }

    // (0,2 => top ; 1,2 => footer)

    /**
     * @param $position
     * @return mixed|string
     */
    public function getGalleryIdCategory($position)
    {
        $currentCategoryId = $this->getCurrentCategory()->getId();
        $galleryCollection = $this->galcollectionFactory->create()
            ->addFieldToFilter('category_id',$currentCategoryId)
            ->addFieldToFilter('category_slider_position',array('in' => $position))
            ->addFieldToSelect('gallery_id')
            ->getFirstItem();
        if (isset($galleryCollection['gallery_id']))
            return $galleryCollection['gallery_id'];
        return '';
    }

    // (0,2 => top ; 1,2 => footer)

    /**
     * @param $position
     * @return mixed|string
     */
    public function getGalleryTitleCategory($position)
    {
        $currentCategoryId = $this->getCurrentCategory()->getId();
        $galleryCollection = $this->galcollectionFactory->create()
            ->addFieldToFilter('category_id', $currentCategoryId)
            ->addFieldToFilter('category_slider_position', array('in' => $position))
            ->addFieldToSelect('title')
            ->getFirstItem();
        if (isset($galleryCollection['title']))
            return $galleryCollection['title'];
        return '';
    }

    /**
     * @return mixed
     */
    public function getGalleryTitleProduct()
    {
        $currentProductId = $this->getCurrentProductId();
        $galleryCollection = $this->galcollectionFactory->create()->addFieldToFilter('product_id',$currentProductId)
            ->addFieldToSelect('title')
            ->getFirstItem();
        return $galleryCollection['title'];
    }

    // (0,2 => top ; 1,2 => footer)

    /**
     * @param $position
     * @return int|mixed
     */
    public function getNumberImageSliderCategory($position)
    {
        $currentCategoryId = $this->getCurrentCategory()->getId();
        $galleryCollection = $this->galcollectionFactory->create()
            ->addFieldToFilter('category_id',$currentCategoryId)
            ->addFieldToFilter('category_slider_position',array('in' => $position))
            ->addFieldToSelect('number_image_slider')
            ->getFirstItem();
        if (isset($galleryCollection['number_image_slider']))
            return $galleryCollection['number_image_slider'];
        return 0;
    }

    /**
     * @return int|mixed
     */
    public function getNumberImageSliderProduct()
    {
        $currentProductId = $this->getCurrentProductId();
        $galleryCollection = $this->galcollectionFactory->create()->addFieldToFilter('product_id',$currentProductId)
            ->addFieldToSelect('number_image_slider')
            ->getFirstItem();
        if (isset($galleryCollection['number_image_slider']))
            return $galleryCollection['number_image_slider'];
        return 0;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        $mediaUrl = $this ->_storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        return $mediaUrl;
    }

    /**
     * @return mixed|string
     */
    public function getGalleryIdProduct()
    {
        $currentProductId = $this->getCurrentProductId();
        $galleryCollection = $this->galcollectionFactory->create()->addFieldToFilter('product_id',$currentProductId)
            ->addFieldToSelect('gallery_id')
            ->getFirstItem();
        if (isset($galleryCollection['gallery_id']))
            return $galleryCollection['gallery_id'];
        return '';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrlKey()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $url = $this->_urlRewriteCollectionFactory->create()
            ->addFieldToFilter('target_path', 'imagegallery/gallery/index')
            ->addFieldToFilter('store_id', $storeId)
            ->getFirstItem();

        if (empty($url))
            return $this->getUrl('imagegallery/gallery/index');
        if ( $url['request_path'] == null)
            return $this->getUrl('imagegallery/gallery/index');
        else
            return $this->getUrl() . $url['request_path'];
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }
}