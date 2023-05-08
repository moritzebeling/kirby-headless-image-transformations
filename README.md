# Kirby Headless Image Transformations

This plugin adds a new route to your Kirby CMS installation that allows you to transform images on the fly. This is useful when you want to let the frontend decide what image size to load:

```
## Schema
https://your-kirby-website.com/thumbs/{image-id}.{ext}?{transformations}

## Original
https://your-kirby-website.com/thumbs/{image-id}.{ext}

## Thumbnail with 640px width and auto height
https://your-kirby-website.com/thumbs/{image-id}.{ext}?width=640

## Cropped thumbnail with 640px width and height
https://your-kirby-website.com/thumbs/{image-id}.{ext}?width=640&height=640&crop=true
```

## Installation

```bash
composer require moritzebeling/kirby-headless-image-transformations
composer update moritzebeling/kirby-headless-image-transformations
```

Or download/clone this repo into `site/plugins` of your Kirby project.

## URL Query parameters

You can use all options offered by the [Kirby `thumb()` method](https://getkirby.com/docs/reference/objects/cms/file/thumb#options):

```js
let options = {
    'autoOrient' => true, // bool
    'crop'       => false, // bool
    'blur'       => false, // bool
    'grayscale'  => false, // bool
    'height'     => null, // int
    'quality'    => 90, // int
    'width'      => null, // int
}
```

## Usage

This plugin can be helpful if you use Kirby as a Headless CMS and want to decide on your frontend which image size to load or to include in your srcset.

**Load single size:**

```js
function createQueryString( options = {} ){
    const queryString = Object.keys(options).map(key => key + '=' + options[key]).join('&');
    return queryString ? '?' + queryString : '';
}

const url_base = 'https://your-kirby-website.com/thumbs';
const image_id = 'page-id/image-filename.jpg';
const options = {
    width: 640,
    height: 640,
    crop: true,
};

const thumb_url = `${url_base}/{image_id}{createQueryString( options )}`;
```
```html
<img src="{thumb_url}" width="{options.width}" height="options.height" />
```

**Load multiple sizes for srcset:**

```js
/*
 * createQueryString, url_base, image_id same as in example above
 */

const sizes = [240,480,960];

let srcset = sizes.map( size => {
    const options = {
        width: size
    };
    return `${url_base}/{image_id}{createQueryString( options )} ${size}w`;
});

```
```html
<img src="{srcset[0]}" srcset="{srcset.join(', ')}" />
```

> Note that the html code above is just an example and wouln’t work in a vanilla setup, but require some type of templating engine, e.g. Svelte.

## Options and security considerations

Whenever a thumbnail is requested for the first time, Kirby will generate it and store it in the `media` folder. So whenever a thumb is requested for the first time, it will take a lille longer.

This also means that people with bad intentions could exploit this to overwhelm your server and fill up your disk space. So in order to prevent this, you should restrict the allowed transformations to only the ones you actually need.

**Default settings:**

```php
// site/config/config.php

return [
    'moritzebeling.headless-image-transformations' => [
        /*
        These values can either be:
        - false (not allowed, will be ignored and might fallback to Kirby's default settings)
        - true (any value is allowed, do not use in production)
        - array (list of allowed values)
        */
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
];
```

When sticking to the default options, it is only allowed to request thumbs with the specified widths and heights as well as cropping. If you need the other options, you should enable them via your `site/config/config.php` file.

The default values can be set here:
https://getkirby.com/docs/reference/system/options/thumbs

## Development

1. Install a fresh Kirby StarterKit
2. `cd site/plugins`
3. `git clone` this repo

Roadmap:
- [ ] Discuss if preset images sizes make sense
- [ ] Check if browser caching works as expected

## ☕️ Support

If you like this plugin, I would be glad if you would invite for on a coffee via [PayPal](http://more.moritzebeling.com/support)
If you have any ideas for further development or stumble upon any problems, please open an issue or PR. Thank you!

## Warranty

This plugin is work in progress and comes without any warranty. Use at your own risk.