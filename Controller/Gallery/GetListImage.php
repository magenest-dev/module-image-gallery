<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Controller\Gallery;
use Magenest\ImageGallery\Model\ResourceModel\Gallery;
use Magento\Framework\App\Action\Context;

/**
 * Class GetListImage
 * @package Magenest\ImageGallery\Controller\Gallery
 */
class GetListImage extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var \Magenest\ImageGallery\Model\GalleryFactory
     */
    protected $galleryFactory;
    /**
     * @var Gallery\CollectionFactory
     */
    protected $galleryCollectionFactory;
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
    protected $interactColFactory;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory
     */
    protected $galleryImageCollectionFactory;
    /**
     * @var array
     */
    protected $loadedImage;

    /**
     * GetListImage constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magenest\ImageGallery\Model\GalleryFactory $galleryFactory
     * @param Gallery\CollectionFactory $galleryCollectionFactory
     * @param \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory $interactCollectionFactory
     * @param \Magenest\ImageGallery\Model\ImageFactory $imageFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory $galleryImageCollectionFactory
     */
    public function __construct(Context $context,
                                \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
                                \Magento\Framework\App\RequestInterface $request,
                                \Magenest\ImageGallery\Model\GalleryFactory $galleryFactory,
                                \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $galleryCollectionFactory,
                                \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory $interactCollectionFactory,
                                \Magenest\ImageGallery\Model\ImageFactory $imageFactory,
                                \Magento\Customer\Model\Session $session,
                                \Magento\Catalog\Model\ProductFactory $productFactory,
                                \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory $galleryImageCollectionFactory)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_request = $request;
        $this->galleryFactory = $galleryFactory;
        $this->galleryCollectionFactory = $galleryCollectionFactory;
        $this->interactColFactory = $interactCollectionFactory;
        $this->imageFactory = $imageFactory;
        $this->customerSession = $session;
        $this->productFactory = $productFactory;
        $this->galleryImageCollectionFactory = $galleryImageCollectionFactory;
        $this->loadedImage = [];
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $gallery_id = $this->_request->getParam('gallery_id');

        //Get image from all galleries
        if ($gallery_id == 0) {
            $list_image_all = [];
            $galleryCollection = $this->galleryCollectionFactory->create()->addFieldToFilter('status',0);
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
                    $interactCollection = $this->interactColFactory->create()
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
            $result = $this->resultJsonFactory->create();
            $result = $result->setData($list_all);
            return $result;
        }

        $list_image = $this->galleryImageCollectionFactory->create()
            ->addFieldToSelect('image_id')
            ->addFieldToFilter('gallery_id',$gallery_id)->getData();
        array_push($list_image,null);

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
                $interactCollection = $this->interactColFactory->create()
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

        $result = $this->resultJsonFactory->create();
        $result = $result->setData($list);
        return $result;
    }
}