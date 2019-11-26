<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Block\Adminhtml\Image\Grid\Renderer;

/**
 * Class Radio
 * @package Magenest\Webform\Block\Adminhtml\Fieldset\Renderer
 */
class LinkWithProduct extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magenest\ImageGallery\Model\ImageFactory
     */
    protected $imageFactory;
    /**
     * SelectThumbnail constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magenest\ImageGallery\Model\ImageFactory $imageFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magenest\ImageGallery\Model\ImageFactory $imageFactory,
        array $data = []
    ) {
        $this->imageFactory = $imageFactory;
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
            $model = $this->imageFactory->create()->load($para['id']);
            $products = $model->getData('product_id');
            $products = explode(',',$products);
            if(in_array($productId,$products))
                $checked = "checked";
        }

        $radio ='<div class="radio" >'
            .'<label><input type="radio" class="link_product" name="link_product" '
            .$checked
            . ' id="link_'.$productId.'" '
            . ' value="'.$productId.'"'
            . ' ></label>'
            .'</div>';
        return $radio;

    }
}
