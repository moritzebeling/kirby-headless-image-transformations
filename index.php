<?php 

use Kirby\Cms\App as Kirby;

Kirby::plugin('moritzebeling/kirby-headless-image-transformations', [

    'options' => [
        'allowed' => [
            'autoOrient' => false,
            'crop'       => [true,false],
            'blur'       => false,
            'grayscale'  => false,
            'height'     => [40,80,160,240,360,480,640,720,960,1280,1440,1920,2560,3200],
            'quality'    => false,
            'width'      => [40,80,160,240,360,480,640,720,960,1280,1440,1920,2560,3200],
        ]
    ],
    
    'routes' => [
        [
            'pattern' => 'thumbs/(:all)',
            'action'  => function ( $id ) {

                $image = kirby()->image( $id );

                if( !$image ){
                    return;
                }

                if( $width = get('width', false) ){
                    $image = $image->resize( $width );
                }

                return go( $image->url() );
                
            }
        ],
    ]

]);