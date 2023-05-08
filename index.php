<?php 

use Kirby\Cms\App as Kirby;

function parseValueFromQueryString( $value ){
    if( $value === true || $value === 'true' ){
        return true;
    } else if( $value === false || $value === 'false' ){
        return false;
    } else if( is_numeric($value) ){
        return (int)$value;
    }
    return null;
}

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
                    /* 404 */
                    return;
                }

                $allowed = option('moritzebeling.kirby-headless-image-transformations.allowed');
                $options = [];

                foreach( $allowed as $key => $value ){
                    if( $value === false ){
                        // skip if option is not allowed
                        continue;
                    } if( $value === true ){
                        // option is allowed without restrictions, so let’s add it if exists
                        $input = get($key, null);
                        if( $v = parseValueFromQueryString($input) ){
                            $options[$key] = $v;
                        }
                        continue;
                    } else if( is_array($value) ) {
                        // add option only when in array of allowed values
                        $input = get($key, null);
                        $v = parseValueFromQueryString($input);
                        if( $v && in_array( $v, $value, true ) ){
                            $options[$key] = $v;
                        }
                        continue;
                    }
                }

                if( count( $options ) > 0 ){
                    $image = $image->thumb( $options );
                }

                return go( $image->url() );
                
            }
        ],
    ]

]);