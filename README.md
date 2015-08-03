## dokuwiki gallery support
dokuwiki 2014-09-29d focused on simple yet functional gallery support.

It's based on original dokuwiki gallery plugin with the following features:

* colorbox support from http://jacklmoore.com/colorbox
* upper case and special characters support in page name, namespace and image files/folders
* exif information, GPS address and google map link if available displayed on top of image in colorbox mode
* exif information, GPS address and google map link if available displayed below image in single linked image mode
* eirect download links on top left of image in colorbox mode offering three sizes:
  * original
  * medium: scaled to 1360px
  * small: scaled to size as set in gallery mode or default to 680px in linked image mode
* numbering on top of group images in colorbox mode
* auto rotate images according to image's orientation tag if found
* fix scaling calculation in the original gallery plugin as well as colorbox
* works both in linked image as well as in gallery mode



## How to install

Copy dokuwiki-2014-09-29d to a suitable place on your server and move subfolders in dokuwiki-2014-09-29d/plugins to dokuwiki-2014-09-29d/lib/plugins. Add the following into user local settings: dokuwiki-2014-09-29d/conf/local.php
add the following to conf/local.php
```php
$conf['renderer_xhtml'] = 'colorbox';
$conf['fnencode'] = 'utf-8';
$conf['deaccent'] = '0';
$conf['mixedcase'] = 1;
$conf['specialcharacters'] = 1;
$conf['png_quality']) = 2;
```
