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

/**
 * Class Love
 * @package Magenest\ImageGallery\Controller\Gallery
 */
class Love extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magenest\ImageGallery\Model\ImageFactory
     */
    protected $imageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory
     */
    protected $interactColFactory;
    /**
     * @var \Magenest\ImageGallery\Model\InteractFactory
     */
    protected $interactFactory;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Love constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magenest\ImageGallery\Model\ImageFactory $imageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory $interactcollectionFactory
     * @param \Magenest\ImageGallery\Model\InteractFactory $interactFactory
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magenest\ImageGallery\Model\ImageFactory $imageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenest\ImageGallery\Model\ResourceModel\Interact\CollectionFactory $interactcollectionFactory,
        \Magenest\ImageGallery\Model\InteractFactory $interactFactory,
        \Magento\Customer\Model\Session $session

    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->imageFactory = $imageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->interactColFactory = $interactcollectionFactory;
        $this->interactFactory = $interactFactory;
        $this->customerSession = $session;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        $customerId = $this->customerSession->getCustomerId();
        $image_id = $this->getRequest()->getParam('image_id');

        $interactCol = $this->interactColFactory->create()->addFieldToFilter('customer_id', $customerId)->addFieldToFilter('image_id', $image_id);

        if (empty($interactCol->getData())) {
            $interactModel = $this->interactFactory->create();
            $interactModel->setData('customer_id', $customerId);
            $interactModel->setData('image_id', $image_id);
            $interactModel->setData('status', 0);
            $interactModel->save();

            $imageModel = $this->imageFactory->create()->load($image_id);
            $imageModel->setData('love', $imageModel->getData('love') + 1);
            $imageModel->save();
            $image = [
                'number_love' => $imageModel->getData('love'),
                'color' => 'red'
            ];
            $result = $this->resultJsonFactory->create();
            $result = $result->setData($image);

            return $result;
        } else {
            foreach ($interactCol->getData() as $interact) {
                if ($interact['status'] == 1) {
                    $interactCol->setDataToAll('status', 0);
                    $interactCol->save();

                    $imageModel = $this->imageFactory->create()->load($image_id);
                    $imageModel->setData('love', $imageModel->getData('love') + 1);
                    $imageModel->save();
                    $image = [
                        'number_love' => $imageModel->getData('love'),
                        'color' => 'red'
                    ];
                    $result = $this->resultJsonFactory->create();
                    $result = $result->setData($image);

                    return $result;
                } else {
                    $interactCol->setDataToAll('status', 1);
                    $interactCol->save();

                    $imageModel = $this->imageFactory->create()->load($image_id);
                    $imageModel->setData('love', $imageModel->getData('love') - 1);
                    $imageModel->save();
                    $image = [
                        'number_love' => $imageModel->getData('love'),
                        'color' => 'white'
                    ];
                    $result = $this->resultJsonFactory->create();
                    $result = $result->setData($image);

                    return $result;
                }
            }
        }
    }
}
