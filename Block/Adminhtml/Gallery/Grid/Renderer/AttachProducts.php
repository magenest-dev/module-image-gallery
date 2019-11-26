<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Block\Adminhtml\Gallery\Grid\Renderer;

/**
 * Class Radio
 * @package Magenest\Webform\Block\Adminhtml\Fieldset\Renderer
 */
class AttachProducts extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * SelectThumbnail constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Render the grid cell value
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $checked = "";
        $productId = $row->getData('entity_id');
        $para = $this->getRequest()->getParams();
        if(array_key_exists('id',$para)){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $model = $objectManager->create('Magenest\ImageGallery\Model\Gallery');
            $model->load($para['id']);
            $products = $model->getData('product_id');
            $products = explode(',',$products);
            if(in_array($productId,$products))
                $checked = "checked";
        }

        $radio ='<div class="radio" >'
            .'<label><input type="radio" class="attach_products" name="attach_products" '
            .$checked
            . ' id="thumbnail_'.$productId.'" '
            . ' value="'.$productId.'"'
            . '></label>'
            .'</div>';
        return $radio;

    }
}
