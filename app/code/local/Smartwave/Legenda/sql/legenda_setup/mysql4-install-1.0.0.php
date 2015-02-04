<?php
/**
 * @package        Legenda_Category_Columns
 * @author        Eternal Friend
 * @copyright    Copyright 2014
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'sw_lg_cat_design', array(
    'group'             => 'Legenda Design',
    'label'             => 'Category page columns',    
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'legenda/system_config_source_attribute_columns',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->addAttribute('catalog_category', 'sw_lg_cat_banner', array(
    'group'             => 'Legenda Design',
    'label'             => 'Cagetory Banner Block',
    'note'              => "Please specify static block id to display category banner.",
    'type'              => 'text',
    'input'             => 'text',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => true,
    'is_html_allowed_on_front'    => true,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->addAttribute('catalog_category', 'sw_lg_cat_position', array(
    'group'             => 'Legenda Design',
    'label'             => 'Main Category List Position',    
    'type'              => 'varchar',
    'input'             => 'select',
    'source'            => 'legenda/system_config_source_attribute_catpos',
    'visible'           => true,
    'required'          => false,
    'backend'           => '',
    'frontend'          => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'user_defined'      => true,
    'visible_on_front'  => true,
    'wysiwyg_enabled'   => false,
    'is_html_allowed_on_front'    => false,
    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->endSetup();