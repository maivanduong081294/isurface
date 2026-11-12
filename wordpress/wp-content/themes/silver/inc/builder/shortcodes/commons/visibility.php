<?php

 return array(
    'type' => 'select',
    'heading' => 'Visibility',
    'default' => '',
    'options' => array(
        ''   => 'Visible',
        'hidden'  => 'Hidden',
        'hide-for-medium'  => 'Only for Desktop',
        'show-for-ml'      => 'Only for Tablet - Landscape',
        'show-for-md'      => 'Only for Tablet - Portrait',
        'show-for-medium hide-for-small' =>  'Only for Tablet (Portrait + Landscape)',
        'show-for-small'   =>  'Only for Mobile',
        'show-for-medium'   =>  'Hide for Desktop',
        'hide-for-ml'      => 'Hide for Tablet - Landscape',
        'hide-for-md'      => 'Hide for Tablet - Portrait',
        'hide-for-md hide-for-ml' => 'Hide for Tablet (Portrait + Landscape)',
        'hide-for-small'   =>  'Hide for Mobile',
    ),
);
