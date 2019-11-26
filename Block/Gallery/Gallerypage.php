<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Block\Gallery;

use Magento\Framework\View\Element\Template;
use \Magenest\ImageGallery\Model\GalleryFactory;
use \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory as GalleryCollection;
use \Magento\Customer\Model\Session;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magenest\ImageGallery\Model\ImageFactory;
use \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory as InteractCollection;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Catalog\Helper\Image;
use \Magento\Framework\App\ProductMetadataInterface;
use Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory as GalleryImageCollection;

/**
 * Class Gallerypage
 * @package Magenest\ImageGallery\Block\Gallery
 */
class Gallerypage extends Template
{
    /**
     * @var GalleryFactory
     */
    protected $galleryFactory;
    /**
     * @var GalleryCollection
     */
    protected $_galleryCollectionFactory;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var ImageFactory
     */
    protected $imageFactory;
    /**
     * @var InteractCollection
     */
    protected $interactCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var Image
     */
    protected $imageHelper;
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;
    /**
     * @var GalleryImageCollection
     */
    protected $galleryImageCollectionFactory;
    /**
     * @var array
     */
    protected $loadedImage;

    /**
     * Gallerypage constructor.
     * @param Template\Context $context
     * @param GalleryFactory $galleryFactory
     * @param GalleryCollection $galleryCollectionFactory
     * @param Session $session
     * @param ScopeConfigInterface $scopeConfig
     * @param ImageFactory $imageFactory
     * @param InteractCollection $interactCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param ProductFactory $productFactory
     * @param Image $imageHelper
     * @param ProductMetadataInterface $productMetadata
     * @param GalleryImageCollection $galleryImageCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        GalleryFactory $galleryFactory,
        GalleryCollection $galleryCollectionFactory,
        Session $session,
        ScopeConfigInterface $scopeConfig,
        ImageFactory $imageFactory,
        InteractCollection $interactCollectionFactory,
        StoreManagerInterface $storeManager,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        ProductFactory $productFactory,
        Image $imageHelper,
        ProductMetadataInterface $productMetadata,
        GalleryImageCollection $galleryImageCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->galleryFactory = $galleryFactory;
        $this->_galleryCollectionFactory = $galleryCollectionFactory;
        $this->customerSession = $session;
        $this->_scopeConfig = $scopeConfig;
        $this->imageFactory = $imageFactory;
        $this->interactCollectionFactory = $interactCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->productFactory = $productFactory;
        $this->imageHelper = $imageHelper;
        $this->productMetadata = $productMetadata;
        $this->galleryImageCollectionFactory = $galleryImageCollectionFactory;
        $this->loadedImage = [];
    }

    /**
     * @return Template
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareLayout()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

        $title = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metatitle', \Magento\Store\Model\ScopeInterface::SCOPE_STORES,$storeId);
        if($title == null)
            $title = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metatitle', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,$websiteId);
        if($title == null)
            $title = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metatitle', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $meta_keywords = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metakeywords', \Magento\Store\Model\ScopeInterface::SCOPE_STORES,$storeId);
        if($meta_keywords == null)
            $meta_keywords = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metakeywords', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,$websiteId);
        if($meta_keywords == null)
            $meta_keywords = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metakeywords', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


        $meta_description = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metadescription', \Magento\Store\Model\ScopeInterface::SCOPE_STORES,$storeId);
        if($meta_description == null)
            $meta_description = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metadescription', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,$websiteId);
        if($meta_description == null)
            $meta_description = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metadescription', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $this->pageConfig->getTitle()->set(__('Magenest Full Gallery'));
        $this->pageConfig->setMetaTitle(__($title));
        $this->pageConfig->setKeywords($meta_keywords);
        $this->pageConfig->setDescription($meta_description);
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getGalleryCollection()
    {
        return $this->_galleryCollectionFactory->create()->addFieldToFilter('status',0)->getData();
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
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrlKey()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $url = $this->_urlRewriteCollectionFactory->create()
            ->addFieldToFilter('target_path', 'imagegallery/gallery/index')
            ->addFieldToFilter('store_id', $storeId)
            ->getFirstItem()->getData();

        if (empty($url))
            return $this->getUrl('imagegallery/gallery/index');
        if ( $url['request_path'] == null)
            return $this->getUrl('imagegallery/gallery/index');
        else
            return $this->getUrl() . $url['request_path'];
    }

    /**
     * @return array
     */
    public function getAllImageFromGalleries()
    {
        $gallery_id = $this->_request->getParam('gallery_id');

        if ($gallery_id == null || $gallery_id == 0)
        {
            //Get image from all galleries
            $list_image_all = [];
            $galleryCollection = $this->_galleryCollectionFactory->create()->addFieldToFilter('status',0);

            foreach ($galleryCollection as $gallery) {
                $galleryImageCollection = $this->galleryImageCollectionFactory->create()->addFieldToFilter('gallery_id',$gallery->getData('gallery_id'));
                foreach ($galleryImageCollection as $item)
                    if (!in_array($item->getData('image_id'),$list_image_all))
                        array_push($list_image_all, $item->getData('image_id'));
            }

            //select enable image
            $list_disable_image = [];
            foreach ($list_image_all as $key => $value)
            {
                $status = $this->imageFactory->create()->load($value)->getData('status');
                if($status == 1)
                {
                    array_push($list_disable_image,$value);
                }
            }

            $list_image_all = array_merge(array_diff($list_image_all,$list_disable_image));

            for ($i = 0; $i < sizeof($list_image_all); $i++)
            {
                $imageModel = $this->imageFactory->create()->load($list_image_all[$i]);
                $this->loadedImage[$imageModel->getData('image_id')] = [
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

            //filter by sort order
            for ($i = 0; $i < sizeof($list_image_all) - 1; $i++)
                for ($j = $i + 1; $j < sizeof($list_image_all); $j++) {
                    $sortorder1 = $this->loadedImage[$list_image_all[$i]]['sortorder'];
                    $sortorder2 = $this->loadedImage[$list_image_all[$j]]['sortorder'];

                    if ($sortorder1 > $sortorder2) {
                        $change = $list_image_all[$i];
                        $list_image_all[$i] = $list_image_all[$j];
                        $list_image_all[$j] = $change;
                    }
                }

            $customerId = $this->customerSession->getCustomerId();
            $descriptionGallery = "";
            $layout_type = "0";
            $list_all = [];
            $color = "white";
            foreach ($list_image_all as $image) {
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

                $list_all[] = [
                    'image_id' => $this->loadedImage[$image]['image_id'],
                    'image' => $this->loadedImage[$image]['image'],
                    'title' => $this->loadedImage[$image]['title'],
                    'description' => $this->loadedImage[$image]['description'],
                    'gallery_description' => $descriptionGallery,
                    'love' => $this->loadedImage[$image]['love'],
                    'color' => $color,
                    'layout_type' => $layout_type,
                    'product_id' => $this->loadedImage[$image]['product_id'],
                    'product_name' => $productModel->getData('name'),
                    'product_link' => $productModel->getProductUrl()
                ];
            }
            return $list_all;
        }

        $list_image = $this->galleryImageCollectionFactory->create()
            ->addFieldToSelect('image_id')
            ->addFieldToFilter('gallery_id',$gallery_id)->getData();

        //select enable image
        $list_disable_image = [];
        foreach ($list_image as $key => $value)
        {
            $status = $this->imageFactory->create()->load($value)->getData('status');
            if($status == 1)
            {
                array_push($list_disable_image,$value);
            }
        }

        $list_image = array_merge(array_diff($list_image,$list_disable_image));

        for ($i = 0; $i < sizeof($list_image); $i++)
        {
            $imageModel = $this->imageFactory->create()->load($list_image[$i]['image_id']);
            $this->loadedImage[$imageModel->getData('image_id')] = [
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

        // filter by sorder
        for($i = 0; $i < sizeof($list_image) - 1 ; $i++)
            for ($j = $i + 1 ; $j < sizeof($list_image); $j++)
            {
                $sortorder1 = $this->loadedImage[$list_image[$i]['image_id']]['sortorder'];
                $sortorder2 = $this->loadedImage[$list_image[$j]['image_id']]['sortorder'];

                if ($sortorder1 > $sortorder2)
                {
                    $change = $list_image[$i];
                    $list_image[$i] = $list_image[$j];
                    $list_image[$j] = $change;
                }
            }

        $customerId = $this->customerSession->getCustomerId();
        $descriptionGallery = $this->galleryFactory->create()->load($gallery_id)->getData('description');
        $layout_type = $this->galleryFactory->create()->load($gallery_id)->getData('layout_type');
        $list = [];
        $color = "white";
        foreach ($list_image as $image)
        {
            if (isset($customerId))
            {
                $interactCollection = $this->interactCollectionFactory->create()
                    ->addFieldToFilter('customer_id',$customerId)
                    ->addFieldToFilter('image_id',$this->loadedImage[$image['image_id']]['image_id'])
                    ->getFirstItem()->getData();

                if(!empty($interactCollection))
                {
                    if($interactCollection['status'] == 0)
                        $color ="red";
                    else
                        $color ="white";
                }
                else
                    $color = "white";
            }

            $productModel = $this->productFactory->create()->load($this->loadedImage[$image['image_id']]['product_id']);

            $list[] = [
                'image_id' => $this->loadedImage[$image['image_id']]['image_id'],
                'image' => $this->loadedImage[$image['image_id']]['image'],
                'title' => $this->loadedImage[$image['image_id']]['title'],
                'description' => $this->loadedImage[$image['image_id']]['description'],
                'gallery_description' => $descriptionGallery,
                'love' => $this->loadedImage[$image['image_id']]['love'],
                'color' => $color,
                'layout_type' => $layout_type,
                'product_id' => $this->loadedImage[$image['image_id']]['product_id'],
                'product_name' => $productModel->getData('name'),
                'product_link' => $productModel->getProductUrl()
            ];
        }

        return $list;
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
     * @return mixed
     */
    public function getGalleryIdRequest()
    {
        return $this->getRequest()->getParam('gallery_id');
    }

    /**
     * @return mixed
     */
    public function getImageIdRequest()
    {
        return $this->getRequest()->getParam('image_id');
    }

    /**
     * @return string
     */
    public function getCurrentLayoutType()
    {
        $galleryId = $this->_request->getParam('gallery_id');

        if($galleryId == null)
            return '1';
        else
        {
            $layout = $this->galleryFactory->create()->load($galleryId)->getData('layout_type');
            if ($layout == 0)
                return '1';
            else return 'gallery-grid';
        }

    }

    /**
     * @return mixed|string
     */
    public function getCurrentGalleryDescription()
    {
        $galleryId = $this->getRequest()->getParam('gallery_id');
        if ($galleryId == null)
            return '';
        else
            return $this->galleryFactory->create()->load($galleryId)->getData('description');
    }

    /**
     * @return string
     */
    public function getHoverEffect()
    {
        $animationClass = "mgn_zoom-";
        $animationType = $this->_scopeConfig->getValue('imagegallery/galleryhover/animationtype', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        switch ($animationType) {
            case 0:{
                $animationClass = $animationClass . "in-gallery-";
                break;
            }
            case 1:{
                $animationClass = $animationClass . "out-gallery-";
                break;
            }
        }

        $animationSpeed = $this->_scopeConfig->getValue('imagegallery/galleryhover/speed', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        switch ($animationSpeed) {
            case 0:{
                $animationClass = $animationClass . "slow";
                break;
            }
            case 1:{
                $animationClass = $animationClass . "normal";
                break;
            }
            case 2:{
                $animationClass = $animationClass . "fast";
                break;
            }
        }

        return $animationClass;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMetaTitle()
    {
        $image_id = $this->getRequest()->getParam('image_id');
        if ($image_id != null)
        {
            $metaTitle = $this->imageFactory->create()->load($image_id)->getData('title');
            return $metaTitle;
        }

        $storeId = $this->_storeManager->getStore()->getId();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

        $title = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metatitle', \Magento\Store\Model\ScopeInterface::SCOPE_STORES,$storeId);
        if($title == null)
            $title = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metatitle', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,$websiteId);
        if($title == null)
            $title = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metatitle', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $title;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMetaDescription()
    {
        $image_id = $this->getRequest()->getParam('image_id');
        if ($image_id != null)
        {
            $metaDescription = $this->imageFactory->create()->load($image_id)->getData('description');
            return $metaDescription;
        }

        $storeId = $this->_storeManager->getStore()->getId();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();

        $meta_description = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metadescription', \Magento\Store\Model\ScopeInterface::SCOPE_STORES,$storeId);
        if($meta_description == null)
            $meta_description = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metadescription', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,$websiteId);
        if($meta_description == null)
            $meta_description = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/metadescription', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $meta_description;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImagePreview()
    {
        $image_id = $this->getRequest()->getParam('image_id');
        if ($image_id != null)
        {
            $metaImage= $this->imageFactory->create()->load($image_id)->getData('image');
            return $this->getMediaUrl() . $metaImage;
        }

        //get first image when share all gallery
        $listAllImageGallery = $this->getAllImageFromGalleries();
        if (isset($listAllImageGallery[0]['image_id']))
        {
            return $this->getMediaUrl() . $listAllImageGallery[0]['image'];
        }

        $placeHolderImage = $this->imageHelper->getDefaultPlaceholderUrl('image');
        return $placeHolderImage;
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }
}
