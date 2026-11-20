<?php

return array(
  'type' => 'group',
  'heading' => 'Advanced',
  'options' => array(
    '_id' => array(
        'type' => 'textfield',
        'heading' => 'ID',
        'param_name' => '_id',
        'default' => '',
    ),
    'class' => array(
        'type' => 'textfield',
        'heading' => 'Class',
        'param_name' => 'class',
        'default' => '',
    ),
    'order' => array(
        'type' => 'css-value',
        'heading' => 'Order',
        'responsive' => true,
        'default' => '0',
        'css_property' => 'order',
        'placeholder' => '0',
        'description' => 'Visual order in flex/grid. Lower numbers appear first; keyboard and reading order stay unchanged.',
    ),
    'visibility'  => require( __DIR__ . '/visibility.php' ),
  ),
);
