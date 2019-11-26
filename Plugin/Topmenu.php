<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Plugin;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\UrlInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

/**
 * Class Topmenu
 */
class Topmenu
{
    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var Http
     */
    protected $_request;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var UrlRewriteCollectionFactory
     */
    protected $_urlRewriteCollectionFactory;

    /**
     * Topmenu constructor.
     * @param Helper $helper
     * @param UrlInterface $url
     * @param Http $_request
     */
    public function __construct(
        UrlInterface $url,
        Http $_request,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory
    ) {
        $this->_request      = $_request;
        $this->_urlInterface = $url;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return array
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '', $childrenWrapClass = '', $limit = 0
    ) {
        if ($this->checkEnableFullGallery()) {
            $menu = $subject->getMenu();
            $tree = $menu->getTree();
            $data = [
                'name'      => 'Image Gallery',
                'url'       => $this->getUrlGallery(),
                'id'        => 'image-gallery',
                'is_active' => $this->isActive()
            ];
            $node = new Node($data, 'id', $tree, $menu);
            $menu->addChild($node);
        }
        return [$outermostClass, $childrenWrapClass, $limit];
    }

    /**
     * @return bool
     */
    private function isActive()
    {
        return $this->_request->getRouteName() == 'gallery' && $this->_request->getControllerName() == 'index';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrlGallery()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $url = $this->_urlRewriteCollectionFactory->create()
            ->addFieldToFilter('target_path', 'imagegallery/gallery/index')
            ->addFieldToFilter('store_id', $storeId)
            ->getFirstItem()->getData();

        if (empty($url))
            return $this->_storeManager->getStore()->getUrl('imagegallery/gallery/index');
        if ( $url['request_path'] == null)
            return $this->_storeManager->getStore()->getUrl('imagegallery/gallery/index');
        else
            return $this->_storeManager->getStore()->getUrl() . $url['request_path'];
    }

    /**
     * @return mixed
     */
    public function checkEnableFullGallery()
    {
        return $this->_scopeConfig->getValue('imagegallery/gallerypage/enablefullgallery',\Magento\Store\Model\ScopeInterface::SCOPE_STORES);
    }
}
