<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class GallerrySEOConfig
 * @package Magenest\ImageGallery\Observer
 */
class GallerrySEOConfig implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var UrlRewriteFactory
     */
    protected $_urlRewriteFactory;
    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /**
     * GallerrySEOConfig constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlRewriteFactory $urlRewriteFactory
     * @param UrlRewriteCollectionFactory $urlRewriteCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param ManagerInterface $messageManager
     */
    public function __construct(ScopeConfigInterface $scopeConfig,
                                UrlRewriteFactory $urlRewriteFactory,
                                UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
                                StoreManagerInterface $storeManager,
                                RequestInterface $request,
                                ManagerInterface $messageManager)
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->request = $request;
        $this->_messageManager = $messageManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $post = $this->request->getParams();
        $storeManagerDataList = $this->_storeManager->getStores();
        $options = array();

        foreach ($storeManagerDataList as $key => $value) {
            $options[] = ['value' => $key,
                'website' => $value['website_id']];
        }

        if (isset($post['store'])) {
            if (isset($post['groups']['galleryseoconfig']['fields']['urlkey']['inherit'])) {
                $websiteId = $this->_storeManager->getStore($post['store'])->getWebsiteId();
                $urlkey = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/urlkey', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE, $websiteId);
                if ($urlkey == null)
                    $urlkey = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/urlkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


                $urlRewriteCollection = $this->urlRewriteFilter($post['store']);
                if (empty($urlRewriteCollection)) {
                    $urlRewriteModel = $this->urlSetData($post['store'], $urlkey);
                    try {
                        $urlRewriteModel->save();
                    } catch (\Exception $exception) {
                        $this->_messageManager->addErrorMessage($exception->getMessage());
                    }
                } else {
                    $urlRewriteModel = $this->_urlRewriteFactory->create()->load($urlRewriteCollection['url_rewrite_id']);
                    $urlRewriteModel->setRequestPath($urlkey);
                    try {
                        $urlRewriteModel->save();
                    } catch (\Exception $exception) {
                        $this->_messageManager->addErrorMessage($exception->getMessage());
                    }
                }
            } else {

                $urlRewriteCollection = $this->urlRewriteFilter($post['store']);
                $urlkey = $post['groups']['galleryseoconfig']['fields']['urlkey']['value'];

                if (empty($urlRewriteCollection)) {
                    $urlRewriteModel = $this->urlSetData($post['store'], $urlkey);
                    try {
                        $urlRewriteModel->save();
                    } catch (\Exception $exception) {
                        $this->_messageManager->addErrorMessage($exception->getMessage());
                    }
                } else {
                    $urlRewriteModel = $this->_urlRewriteFactory->create()->load($urlRewriteCollection['url_rewrite_id']);
                    $urlRewriteModel->setRequestPath($urlkey);
                    try {
                        $urlRewriteModel->save();
                    } catch (\Exception $exception) {
                        $this->_messageManager->addErrorMessage($exception->getMessage());
                    }
                }

            }
        } elseif (isset($post['website'])) {
            if (isset($post['groups']['galleryseoconfig']['fields']['urlkey']['value'])) {
                foreach ($options as $store) {
                    if ($store['website'] == $post['website']) {
                        $urlRewriteCollection = $this->urlRewriteFilter($store['value']);

                        $urlkey = $post['groups']['galleryseoconfig']['fields']['urlkey']['value'];

                        if (empty($urlRewriteCollection)) {
                            $urlRewriteModel = $this->urlSetData($store['value'], $urlkey);
                            try {
                                $urlRewriteModel->save();
                            } catch (\Exception $exception) {
                                $this->_messageManager->addErrorMessage($exception->getMessage());
                            }
                        } else {
                            $urlkey = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/urlkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $store['value']);
                            $urlRewriteModel = $this->_urlRewriteFactory->create()->load($urlRewriteCollection['url_rewrite_id']);
                            $urlRewriteModel->setRequestPath($urlkey);
                            try {
                                $urlRewriteModel->save();
                            } catch (\Exception $exception) {
                                $this->_messageManager->addErrorMessage($exception->getMessage());
                            }
                        }
                    }
                }
            } else {
                foreach ($options as $store) {
                    if ($store['website'] == $post['website']) {
                        $urlRewriteCollection = $this->urlRewriteFilter($store['value']);
                        $urlkey = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/urlkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $store['value']);
                        if ($urlkey == null)
                            $urlkey = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/urlkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

                        if (empty($urlRewriteCollection)) {
                            $urlRewriteModel = $this->urlSetData($store['value'], $urlkey);
                            try {
                                $urlRewriteModel->save();
                            } catch (\Exception $exception) {
                                $this->_messageManager->addErrorMessage($exception->getMessage());
                            }
                        } else {
                            $urlRewriteModel = $this->_urlRewriteFactory->create()->load($urlRewriteCollection['url_rewrite_id']);
                            $urlRewriteModel->setRequestPath($urlkey);
                            try {
                                $urlRewriteModel->save();
                            } catch (\Exception $exception) {
                                $this->_messageManager->addErrorMessage($exception->getMessage());
                            }
                        }
                    }
                }
            }
        } elseif (!isset($post['website'])) {
            foreach ($options as $store) {

                $urlRewriteCollection = $this->urlRewriteFilter($store['value']);
                $urlkey = $post['groups']['galleryseoconfig']['fields']['urlkey']['value'];
                if (empty($urlRewriteCollection)) {
                    $urlRewriteModel = $this->urlSetData($store['value'], $urlkey);
                    try {
                        $urlRewriteModel->save();
                    } catch (\Exception $exception) {
                        $this->_messageManager->addErrorMessage($exception->getMessage());
                    }
                } else {
                    $urlkey = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/urlkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $store['value']);

                    $urlRewriteModel = $this->_urlRewriteFactory->create()->load($urlRewriteCollection['url_rewrite_id']);
                    $urlRewriteModel->setRequestPath($urlkey);
                    try {
                        $urlRewriteModel->save();
                    } catch (\Exception $exception) {
                        $this->_messageManager->addErrorMessage($exception->getMessage());
                    }
                }
            }
        }
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function urlRewriteFilter($storeId)
    {
        return $this->_urlRewriteCollectionFactory->create()
            ->addFieldToFilter('target_path', 'imagegallery/gallery/index')
            ->addFieldToFilter('store_id', $storeId)
            ->getFirstItem()->getData();
    }

    /**
     * @param $storeId
     * @param $requestPath
     * @return mixed
     */
    public function urlSetData($storeId, $requestPath)
    {
        $urlRewriteModel = $this->_urlRewriteFactory->create();
        $urlRewriteModel->setStoreId($storeId);
        $urlRewriteModel->setRedirectType(0);
        $urlRewriteModel->setRequestPath($requestPath);
        $urlRewriteModel->setTargetPath("imagegallery/gallery/index");
        return $urlRewriteModel;
    }
}