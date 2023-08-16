<?php
/**
 * Copyright © 2021 CT Corp Digital. All rights reserved.
 * https://www.ctcorpdigital.com
 *
 * @category Trans
 * @package  Trans_Catalog
 * @license  Proprietary
 *
 * @author   Imam Kusuma <imam.kusuma@ctcorpdigital.com>
 * @author   Edi Suryadi <edi.suryadi@ctcorpdigital.com>
 */

namespace Trans\Catalog\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Api\AttributeOptionManagementInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductAttributeManagementInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Trans\Catalog\Api\Data\SeasonInterfaceFactory;
use Trans\Catalog\Api\SeasonRepositoryInterface;

/**
 * Upgrade Data script
 */

class UpgradeData implements UpgradeDataInterface
{
    const  BU_ATTR_GROUP = 'Aigner Data';

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var AttributeOptionLabelInterfaceFactory
     */
    protected $optionLabelFactory;

    /**
     * @var AttributeOptionInterfaceFactory
     */
    protected $optionFactory;

    /**
     * @var ProductAttributeManagementInterface
     */
    protected $productAttributeManagement;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    protected $productAttributeRepository;

	/**
	 * @var SeasonInterfaceFactory
	 */
	protected $seasonFactory;

	/**
	 * @var SeasonRepositoryInterface
	 */
	protected $seasonRepository;

	/**
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeOptionManagementInterface $attributeOptionManagement
     * @param AttributeOptionLabelInterfaceFactory $optionLabelFactory
     * @param AttributeOptionInterfaceFactory $optionFactory
     * @param ProductAttributeManagementInterface $productAttributeManagement
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
	 * @param SeasonInterfaceFactory $seasonFactory
	 * @param SeasonRepositoryInterface $seasonRepository
	 */
	public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeOptionManagementInterface $attributeOptionManagement,
        AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        AttributeOptionInterfaceFactory $optionFactory,
        ProductAttributeManagementInterface $productAttributeManagement,
        ProductAttributeRepositoryInterface $productAttributeRepository,
        SeasonInterfaceFactory $seasonFactory,
        SeasonRepositoryInterface $seasonRepository
	) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->productAttributeManagement = $productAttributeManagement;
        $this->productAttributeRepository = $productAttributeRepository;
		$this->seasonFactory = $seasonFactory;
		$this->seasonRepository = $seasonRepository;
	}
	/**
	 * {@inheritdoc}
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
		$setup->startSetup();

		if (version_compare($context->getVersion(), '1.0.2', '<')) {
			$season = $this->seasonFactory->create();
			$season->setCode('all');
			$season->setLabel('All');
			$season->setLabel('All season');
			$season->setFlag(1);
			$this->seasonRepository->save($season);
		}

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $this->upgradeTo103($setup);
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $this->upgradeTo104();
        }

        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            $this->upgradeTo105($setup);
        }

		$setup->endSetup();
	}

    protected function upgradeTo103($setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add New Attribute Group
         */
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSet = $eavSetup->getAttributeSet($entityTypeId, 'Default');

        if(isset($attributeSet['attribute_set_id'])) {
            $eavSetup->addAttributeGroup(
                $entityTypeId,
                $attributeSet['attribute_set_id'],
                self::BU_ATTR_GROUP,
                12
            );
        }

        /**
         * Add int-boolean product attributes
         */
        $attributes = [
            'is_featured' => 'Featured Product',
            'is_bestseller' => 'Bestseller Product'
        ];

        foreach ($attributes as $key => $label) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                $key,
                [
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'attribute_set' => 'Default',
                    'type' => 'int',
                    'label' => $label,
                    'input' => 'boolean',
                    'backend' => '',
                    'frontend' => '',
                    'visible'  => true,
                    'required' => false,
                    'default' => '',
                    'unique' => false,
                    'group' => self::BU_ATTR_GROUP,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => false,
                    'visible_in_advanced_search' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => '',
                    'position' => 0,
                    'is_used_for_promo_rules' => true,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
        }

        /**
         * Add varchar-text product attributes
         */
        $attributes = [
            'barcode' => 'Barcode',
            'article_group' => 'Article Group',
            'category_offline' => 'Category Offline'
        ];

        foreach($attributes as $key => $label) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                $key,
                [
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'attribute_set' => 'Default',
                    'type' => 'varchar',
                    'label' => $label,
                    'input' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'visible'  => true,
                    'required' => false,
                    'default' => '',
                    'unique' => false,
                    'group' => self::BU_ATTR_GROUP,
                    'user_defined' => false,
                    'searchable' => true,
                    'filterable' => false,
                    'comparable' => true,
                    'visible_on_front' => true,
                    'wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => false,
                    'visible_in_advanced_search' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => '',
                    'position' => 1,
                    'is_used_for_promo_rules' => false,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true
                ]
            );
        }

        /**
         * Add dropdown product attributes
         */
        $attributes = [
            'size' => 'Size',
            'model_group' => 'Model Group'
        ];

        foreach($attributes as $key => $label) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                $key,
                [
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'attribute_set' => 'Default',
                    'type' => 'int',
                    'label' => $label,
                    'input' => 'select',
                    'backend' => '',
                    'frontend' => '',
                    'visible' => true,
                    'required' => false,
                    'default' => '',
                    'unique' => false,
                    'group' => self::BU_ATTR_GROUP,
                    'user_defined' => true,
                    'searchable' => true,
                    'filterable' => true,
                    'comparable' => true,
                    'visible_on_front' => false,
                    'wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => false,
                    'visible_in_advanced_search' => true,
                    'used_for_sort_by' => false,
                    'apply_to' => '',
                    'position' => 2,
                    'is_used_for_promo_rules' => false,
                    'is_used_in_grid' => true,
                    'is_visible_in_grid' => true,
                    'is_filterable_in_grid' => true
                ]
            );
        }
    }

    protected function upgradeTo104()
    {
        /**
         * Add initial option values into 'size' attribute
         */
        $optionValues = ['S','M','L','XL','XXL','50','55','60','65','70','75','80','85','90','95','100','105','110','115','120','125','130','135','140','145','150'];
        $this->addOptionValues($optionValues, 'size');

        /**
         * Add initial option values into 'model_group' attribute
         */
        $optionValues = [
            "Aigner '79 S",
            "Artigiana",
            "Basics",
            "Business",
            "Business Fs",
            "Carol",
            "Carol S",
            "Casual",
            "Commercial Collection M",
            "Commercial Collection S",
            "Como",
            "Como S",
            "Cybill",
            "Cybill S",
            "Dea",
            "Diadora",
            "Diane",
            "Elba",
            "Emilio",
            "Emilio M",
            "Emilio S",
            "Evita",
            "Fashion",
            "Fiorentina",
            "Flora M",
            "Flora S",
            "Garda",
            "Genoveva",
            "Genoveva M",
            "Genoveva S",
            "Grazia",
            "Grazia S",
            "Icon Cover",
            "Ivy",
            "Ivy M",
            "Ivy S",
            "Kira",
            "Logo",
            "Lori",
            "Lori S",
            "Lori Xs",
            "Luca",
            "Luca M",
            "Luca S",
            "Milano",
            "Mina",
            "Mina M",
            "Mina S",
            "Monaco",
            "Nevio",
            "Nova M",
            "Nova S",
            "Palermo L",
            "Palermo M",
            "Pisa",
            "Pisa S",
            "Pisa Xs",
            "Saffiano",
            "Seasonal",
            "Siena",
            "Sonderproduktion S",
            "Tara",
            "Tara L",
            "Tara S",
            "Tara Xs",
            "Verona",
            "Verona S",
            "Verona Xs",
            "Vicenza"
        ];
        $this->addOptionValues($optionValues, 'model_group');

        /**
         * Assign 'color' product attribute to attribute set 'Default' and attribute group 'Aigner Data'
         * Add initial option values into 'model_group' attribute
         */
        $this->productAttributeManagement->assign(4, 21, 'color', 8);
        $optionValues = [
            'Antic Red',
            'Antique White',
            'Aqua',
            'Bison Brown',
            'Bitter Chocolate Brown',
            'Black',
            'Black Coloured',
            'Blue',
            'Brown',
            'Burgundy',
            'Burnt Red',
            'Camel',
            'Cashmere Beige',
            'Cognac',
            'Country Green',
            'Cyan',
            'Dark Toffee Brown',
            'Dawn Blue',
            'Deep Blue',
            'Dusk Blue',
            'Dusty Rose',
            'Ebony',
            'Fango',
            'Feather Grey',
            'Fox Brown',
            'Gold',
            'Gray',
            'Green',
            'Gun Metal',
            'Ink',
            'Java Brown',
            'Marine',
            'Mud Green',
            'Multicolour',
            'Nougat',
            'Orange',
            'Pink',
            'Purple',
            'Red',
            'Silver',
            'Taupe',
            'Vacchetta Brown',
            'Velvet Cake Red',
            'White',
            'Yellow'
        ];
        $this->addOptionValues($optionValues, 'color');
    }

    protected function upgradeTo105($setup)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add int-boolean product attributes
         */
        $attributes = [
            'is_new' => 'New Product'
        ];

        foreach ($attributes as $key => $label) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                $key,
                [
                    'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    'attribute_set' => 'Default',
                    'type' => 'int',
                    'label' => $label,
                    'input' => 'boolean',
                    'backend' => '',
                    'frontend' => '',
                    'visible'  => true,
                    'required' => false,
                    'default' => '',
                    'unique' => false,
                    'group' => self::BU_ATTR_GROUP,
                    'user_defined' => true,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'wysiwyg_enabled' => false,
                    'is_html_allowed_on_front' => false,
                    'visible_in_advanced_search' => false,
                    'used_for_sort_by' => false,
                    'apply_to' => '',
                    'position' => 0,
                    'is_used_for_promo_rules' => true,
                    'is_used_in_grid' => false,
                    'is_visible_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
        }
    }

    /**
     * Force add attribute option values
     *
     * @param array $optionValues
     * @param $attrCode
     * @return void
     */
    private function addOptionValues(array $optionValues, $attrCode)
    {
        $attributeId = $this->productAttributeRepository->get($attrCode)->getAttributeId();
        foreach($optionValues as $value) {
            $optionLabel = $this->optionLabelFactory->create();
            $optionLabel->setStoreId(0);
            $optionLabel->setLabel($value);

            $option = $this->optionFactory->create();
            $option->setStoreLabels([$optionLabel]);
            $option->setSortOrder(0);
            $option->setIsDefault(false);

            $this->attributeOptionManagement->add(Product::ENTITY, $attributeId, $option);
        }
    }
}
