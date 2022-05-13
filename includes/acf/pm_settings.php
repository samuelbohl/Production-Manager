<?php

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_pm_settings',
	'title' => 'Production Manager Settings',
	'fields' => array(
		array(
			'key' => 'field_production_manager_users',
			'label' => 'Production Manager Users',
			'name' => 'production_manager_users',
			'type' => 'user',
			'instructions' => 'Please select all users using the Production Manager.',
			'required' => 1,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '50',
				'class' => '',
				'id' => '',
			),
			'role' => '',
			'allow_null' => 0,
			'multiple' => 1,
			'return_format' => 'id',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'production-manager-settings',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'seamless',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
));

endif;