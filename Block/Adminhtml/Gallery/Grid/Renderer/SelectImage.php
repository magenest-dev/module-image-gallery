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
class SelectImage extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory
     */
    protected $galleryImageCollectionFactory;
    /**
     * SelectImage constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory $galleryImageCollectionFactory,
        array $data = []
    ) {
        $this->galleryImageCollectionFactory = $galleryImageCollectionFactory;
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
        $imageId = $row->getImageId();
        $para = $this->getRequest()->getParams();
        if(array_key_exists('id',$para)){
            $galleryImageCollection = $this->galleryImageCollectionFactory->create()->addFieldToFilter('gallery_id',$para['id']);
            foreach ($galleryImageCollection as $item)
                if($item->getData('image_id') == $imageId)
                    $checked = "checked";
        }
        $radio ='<div class="checkbox" >'
            .'<label><input type="checkbox" class="checkbox_image" name="checkbox_image" '
            .$checked
            . ' id="choose_image_'.$imageId.'" '
            . ' value="'.$imageId.'"'
            . ' ></label>'
            .'</div>';

        return $radio;
    }
}
