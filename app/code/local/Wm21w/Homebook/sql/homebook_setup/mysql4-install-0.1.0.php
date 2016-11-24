<?php
$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$sets = $setup->getAllAttributeSetIds('catalog_product');

$sets->addAttribute('catalog_product', 'homebook', array(
    'type' => 'int',
    'backend' => '',
    'frontend' => '',
    'label' => 'Homebook XML',
    "input" => "select",
    "source" => "eav/entity_attribute_source_boolean",
    'class' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => false,
    'default' => '0',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => false,
    'unique' => false,
    'apply_to' => '',
    'is_configurable' => false
));

foreach ($sets as $setId) {
    $set = $setup->getAttributeSet('catalog_product', $setId);
    $setup->addAttributeToSet('catalog_product', $setId, $setup->getDefaultAttributeGroupId('catalog_product', $setId), 'homebook');
}


$installer->endSetup();
	 