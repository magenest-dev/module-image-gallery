<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */
namespace Magenest\ImageGallery\Controller\Adminhtml\Gallery;

use Magenest\ImageGallery\Controller\Adminhtml\Gallery as GalleryController;
use Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory as GalleryImageCollection;

/**
 * Class MassDelete
 * @package Magenest\ImageGallery\Controller\Adminhtml\Gallery
 */

class MassDelete extends GalleryController
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
    public function __construct(Action\Context $context, Registry $coreRegistry, PageFactory $resultPageFactory, CollectionFactory $collectionFactory, GalleryImageCollection $galleryImageCollectionFactory )
    {
        $this->galleryImageCollectionFactory = $galleryImageCollectionFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $collectionFactory);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $collection = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        $model = \Magento\Framework\App\ObjectManager::getInstance()->create('Magenest\ImageGallery\Model\Gallery');
        if (isset($collection['selected'])) {
            $cols = $collection['selected'];
        } else {
            foreach ($model->getCollection() as $item) {
                $model->load($item->getGalleryId())->delete();

                $galleryImageCollection = $this->galleryImageCollectionFactory->create()->addFieldToFilter('gallery_id',$item->getGalleryId());
                foreach ($galleryImageCollection as $item2)
                    $item2->delete();
            }
            $this->messageManager->addSuccessMessage(__('All records have been deteled.'));
            return $resultRedirect->setPath('*/*/');
        }
        $totals = 0;

        try {
            foreach ($cols as $item) {
                /** @var \Magenest\ImageGallery\Model\Gallery $item */
                $model ->load($item);
                $model->delete();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deteled.', $totals));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while delete the post(s).'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
