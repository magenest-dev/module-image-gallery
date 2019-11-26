<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */
namespace Magenest\ImageGallery\Controller\Adminhtml\Image;

use Magenest\ImageGallery\Controller\Adminhtml\Image as ImageController;
use Magenest\ImageGallery\Model\ResourceModel\Image\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory as GalleryImageCollection;

/**
 * Class MassDelete
 * @package Magenest\ImageGallery\Controller\Adminhtml\Gallery
 */
class MassDelete extends ImageController
{
    /**
     * @var GalleryImageCollection
     */
    protected $galleryImageCollectionFactory;

    /**
     * MassDelete constructor.
     * @param Action\Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CollectionFactory $collectionFactory
     * @param GalleryImageCollection $galleryImageCollectionFactory
     */
    public function __construct(Action\Context $context, Registry $coreRegistry, PageFactory $resultPageFactory, CollectionFactory $collectionFactory, GalleryImageCollection $galleryImageCollectionFactory)
    {
        $this->galleryImageCollectionFactory = $galleryImageCollectionFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $collectionFactory);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->getRequest()->getParams();
        $model = \Magento\Framework\App\ObjectManager::getInstance()->create('Magenest\ImageGallery\Model\Image');

        //not chose all
        if (isset($collection['selected'])) {
            $cols = $collection['selected'];
        } // chose all image
        else {
            $cols = $model->getCollection()->getAllIds();
        }

        //check image exist in gallery
        $galleryImageCollection = $this->galleryImageCollectionFactory->create()->addFieldToSelect('image_id');

        $idValid = [];
        $idInValid = [];
        foreach ($cols as $idChose) {
            $checkExistInGallery = 0;

            foreach ($galleryImageCollection as $item)
                if ($idChose == $item->getData('image_id'))
                {
                    $checkExistInGallery++;
                    break;
                }

            if ($checkExistInGallery > 0)
                array_push($idInValid, $idChose);
//                $this->messageManager->addErrorMessage(__('Image ID '.$idChose.' exist in gallery. Remove it in gallery before delete.'));
            else
                array_push($idValid, $idChose);
        }
        if (!empty($idInValid))
            $this->messageManager->addErrorMessage(__('Image ID '. implode(',',$idInValid) .' exist in gallery. Remove it in gallery before delete.'));

            $totals = 0;
        try {
            foreach ($idValid as $item) {
                /** @var \Magenest\ImageGallery\Model\Image $item */
                $model ->load($item);
                $model->delete();
                $totals++;
            }

            if($totals!=0)
                $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deteled.', $totals));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while delete the post(s).'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
