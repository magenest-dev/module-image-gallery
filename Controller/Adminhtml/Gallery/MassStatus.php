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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magenest\ImageGallery\Controller\Adminhtml\Gallery as GalleryController;

/**
 * Class MassStatus
 * @package Magenest\ImageGallery\Controller\Adminhtml\Gallery
 */
class MassStatus extends GalleryController
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {

        $collection = $this->getRequest()->getParams();
        $model = \Magento\Framework\App\ObjectManager::getInstance()->create('Magenest\ImageGallery\Model\Gallery');
        //not chose all
        if (isset($collection['selected'])) {
            $cols = $collection['selected'];
        } // chose all image
        else {
            $cols=$model->getCollection()->getAllIds();
        }
        $status = $collection['status'];
        $totals = 0;
        try {
            foreach ($cols as $item) {
                /** @var \Magenest\ImageGallery\Model\Gallery $item */
                $model->load($item);
                $model->setStatus($status)->save();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $totals));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->_getSession()->addException($e, __('Something went wrong while updating the product(s) status.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
