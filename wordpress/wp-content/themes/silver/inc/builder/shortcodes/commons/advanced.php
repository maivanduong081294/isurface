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
    'visibility'  => require( __DIR__ . '/visibility.php' ),
  ),
);
