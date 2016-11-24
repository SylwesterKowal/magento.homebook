<?php
$installer = $this;
$installer->startSetup();


$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$sets = $setup->getAllAttributeSetIds('catalog_product');

$setup->addAttribute('catalog_product', 'homebook', array(
    'type' => 'int',
    'backend_type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Add to HomeBook XML',
    'input' => 'boolean',
    'frontend_class' => '',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => false,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false
));

//$setup->addAttributeToSet('catalog_product', 'Default', 'General', 'homebook');

foreach ($sets as $setId) {
    $set = $setup->getAttributeSet('catalog_product', $setId);
    $setup->addAttributeToSet('catalog_product', $setId, $setup->getDefaultAttributeGroupId('catalog_product', $setId), 'homebook');
}

$installer->endSetup();
	 