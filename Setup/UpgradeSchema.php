<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Magenest\ImageGallery\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory
     */
    protected $galleryCollectionFactory;
    /**
     * @var \Magenest\ImageGallery\Model\GalleryImageFactory
     */
    protected $galleryImageFactory;

    /**
     * UpgradeSchema constructor.
     * @param \Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $galleryCollectionFactory
     * @param \Magenest\ImageGallery\Model\GalleryImageFactory $galleryImageFactory
     */
    public function __construct(\Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory $galleryCollectionFactory,
                                \Magenest\ImageGallery\Model\GalleryImageFactory $galleryImageFactory)
    {
        $this->galleryCollectionFactory = $galleryCollectionFactory;
        $this->galleryImageFactory = $galleryImageFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $setup->startSetup();
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_gallery'),
                'description',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'size' => null,
                    'nullable' => false,
                    'comment' => 'Description']);

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_gallery'),
                'width',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Width']);
            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_gallery'),
                'height',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Height']);

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_gallery'),
                'product_id',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'size' => null,
                    'comment' => 'Product ID']);

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_gallery'),
                'category_id',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'size' => null,
                    'comment' => 'Category ID']);

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_image'),
                'love',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'size' => null,
                    'nullable' => false,
                    'comment' => 'Love']);

            $tableImage = $setup->getConnection()
                ->newTable($setup->getTable('magenest_customer_love_interact'))
                ->addColumn(
                    'interact_id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true]
                )->addColumn(
                    'customer_id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false]
                )->addColumn(
                    'image_id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false,'unsigned' => true]
                )->addColumn(
                    'status',
                    \Magento\Framework\Db\Ddl\Table::TYPE_TEXT,
                    10,
                    ['nullable' => false]
                )->addForeignKey(
                    $setup->getFkName('magenest_customer_love_interact', 'image_id', 'magenest_image_gallery_image', 'image_id'),
                    'image_id',
                    $setup->getTable('magenest_image_gallery_image'),
                    'image_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $setup->getConnection()->createTable($tableImage);

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_gallery'),
                'layout_type',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'size' => null,
                    'nullable' => false,
                    'comment' => 'Layout Type']);

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_gallery'),
                'category_slider_position',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'size' => null,
                    'nullable' => false,
                    'comment' => 'Category slider position']);

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_image'),
                'product_id',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'size' => null,
                    'comment' => 'Product ID']);

            $setup->getConnection()->dropColumn($setup->getTable('magenest_image_gallery_gallery'), 'thumbnail_id');

            $setup->getConnection()->addColumn(
                $setup->getTable('magenest_image_gallery_gallery'),
                'number_image_slider',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'comment' => 'Number of image in slider']);
            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            $setup->startSetup();

            $tableImage = $setup->getConnection()
                ->newTable($setup->getTable('magenest_gallery_image'))
                ->addColumn(
                    'id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true]
                )->addColumn(
                    'gallery_id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false,'unsigned' => true]
                )->addColumn(
                    'image_id',
                    \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false,'unsigned' => true]
                )->addForeignKey(
                    $setup->getFkName('magenest_gallery_image', 'gallery_id', 'magenest_image_gallery_gallery', 'gallery_id'),
                    'gallery_id',
                    $setup->getTable('magenest_image_gallery_gallery'),
                    'gallery_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $setup->getFkName('magenest_gallery_image', 'image_id', 'magenest_image_gallery_image', 'image_id'),
                    'image_id',
                    $setup->getTable('magenest_image_gallery_image'),
                    'image_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                );
            $setup->getConnection()->createTable($tableImage);

            $setup->getConnection()->changeColumn(
                $setup->getTable('magenest_customer_love_interact'),
                'customer_id',
                'customer_id',
                ['type' => \Magento\Framework\Db\Ddl\Table::TYPE_INTEGER, 'nullable' => false, 'unsigned' => true, 'length' => 10],
                'Customer ID'
            );

            $setup->getConnection()->addForeignKey(
                $setup->getFkName('magenest_customer_love_interact', 'customer_id', 'customer_entity', 'entity_id'),
                $setup->getTable('magenest_customer_love_interact'),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            $setup->getConnection()->addIndex(
                'magenest_customer_love_interact',
                $setup->getIdxName('magenest_customer_love_interact','status',\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT),
                [
                    'status'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $setup->getConnection()->addIndex(
                'magenest_image_gallery_gallery',
                $setup->getIdxName('magenest_image_gallery_gallery','title',\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT),
                [
                    'title'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $setup->getConnection()->addIndex(
                'magenest_image_gallery_gallery',
                $setup->getIdxName('magenest_image_gallery_gallery','status',\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT),
                [
                    'status'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $setup->getConnection()->addIndex(
                'magenest_image_gallery_image',
                $setup->getIdxName('magenest_image_gallery_image','title',\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT),
                [
                    'title'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $setup->getConnection()->addIndex(
                'magenest_image_gallery_image',
                $setup->getIdxName('magenest_image_gallery_image','status',\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT),
                [
                    'status'
                ],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );

            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            $setup->startSetup();
            $galleryCollection = $this->galleryCollectionFactory->create();
            foreach ($galleryCollection as $gallery)
            {
                $galleryId = $gallery->getData('gallery_id');
                $imageId = explode(',', $gallery->getData('image_id'));

                if ($imageId[0] != '')
                {
                    foreach ($imageId as $id)
                    {
                        $galleryImage = $this->galleryImageFactory->create();
                        $galleryImage->setData('gallery_id',$galleryId);
                        $galleryImage->setData('image_id',$id);
                        $galleryImage->save();
                    }
                }
            }
            $setup->endSetup();
        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            $setup->startSetup();
            $setup->getConnection()->dropColumn($setup->getTable('magenest_image_gallery_gallery'), 'image_id');
            $setup->endSetup();
        }
    }
}