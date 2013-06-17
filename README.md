# Laravel bundle API wrapper for Imgur anonymous functions

Imgur API provides easy way to handle images, the library makes it possible to use the API's [anonymous resources](http://api.imgur.com/resources_anon)

## Installation

Get your API key from [imgur.com](https://imgur.com/register/api_anon)

Set up the config file


    return array(   
       'imgur_apikey'   => '', // Imgur API key
       'imgur_format'   => 'json', // json OR xml
       'imgur_xml_type' => 'object', // array OR object
    );

Autoload the bundle

    return array(
       'docs' => array('handles' => 'docs'),
       'imgur' => array(
       'auto' => true
       ),
    );

## Implemented methods

* move_image() /Sideloading/
* upload()
* stats()
* album()
* image()
* delete()
* oembed()

## Using methods with parameters

### move_image()
Image sideloading allows you to move an image from another web host onto Imgur

    $params = array(
        'url'  => 'http://yoursite.com/picture.jpg',
        'edit' => FALSE
    );

    $response = imgur::move_image($params);

If you wish to edit the image first, set edit to TRUE

### upload()
Uploads an image

_Upload from url:_

    $params = array(
        'image'   => 'http://yoursite.com/picture.jpg',
        'type'    => 'url', // optional
        'name'    => 'image.jpg', // optional
        'title'   => 'Picture name', // optional
        'caption' => 'Picture caption' // optional
    );

_Upload from file:_

    $params = array(
        'image' => base64_encode(file_get_contents('/path/to/picture.jpg')),			
        'type'  => 'base64'
    );

    $response = imgur::upload($params);

### stats()
Display site statistics, such as bandwidth usage, images uploaded, image views, and average image size

    $response = imgur::stats('month');

_Possible parameters: 'today', 'week', 'month'_

### album()
Returns album information and lists all images that belong to the album

    $response = imgur::album($id);

### image()
Returns all the information about a certain image

    $response = imgur::image($hash);

### delete()
Deletes an image

    $response = imgur::delete($delete_hash);

### oembed()
Oembed allows you to make a request for an album or image URL and it will return the embed code as well as additional information about the object. For additional information please see the [oembed documentation](http://oembed.com)

    $params = array(
        'url'       => 'http://i.imgur.com/xxxxx.png',
        'format'    => 'json', // optional
        'maxheight' => 200, // optional
        'maxwidth'  => 200 // optional
    );

    $response = imgur::oembed($params);

## Return values

As you set in the config file, you can get json or xml(array or object) return values. If you have an error, a cURL exception will be thrown.

## License

[MIT License](http://www.opensource.org/licenses/MIT)

C. 2012 Barna Szalai (b.sz@devartpro.com)
