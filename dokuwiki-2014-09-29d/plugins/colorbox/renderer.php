<?php
/**
 * DokuWiki Plugin colorbox (Renderer Component)
 *
 * @license Public Domain
 * @author  Marcus von Appen <marcus@sysfault.org>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_LF')) define('DOKU_LF', "\n");
if (!defined('DOKU_TAB')) define('DOKU_TAB', "\t");
require_once DOKU_INC.'inc/parser/xhtml.php';

class renderer_plugin_colorbox extends Doku_Renderer_xhtml {

    /**
     * Make available as XHTML replacement renderer
     */
    function canRender($format){
        return ($format=='xhtml');
    }

    function _media($src, $title = null, $align = null, $width = null,
                    $height = null, $cache = null, $render = true) {
        $ret = '';

        list($ext, $mime) = mimetype($src);
        if(substr($mime, 0, 5) == 'image') {
            // first get the $title
            if(!is_null($title)) {
                $title = $this->_xmlEntities($title);
            } elseif($ext == 'jpg' || $ext == 'jpeg') {
                //try to use the caption from IPTC/EXIF
                require_once(DOKU_INC.'lib/plugins/colorbox/JpegMetaGPS.php');
                $jpeg = new JpegMetaGPS(mediaFN($src));
                if($jpeg !== false) $cap = $jpeg->getTitle();
                if(!empty($cap)) {
                    $title = $this->_xmlEntities($cap);
                }
            }
            if(!$render) {
                // if the picture is not supposed to be rendered
                // return the title of the picture
                if(!$title) {
                    // just show the sourcename
                    $title = $this->_xmlEntities(utf8_basename(noNS($src)));
                }
                return $title;
            }
            //add image tag
            if (($ext == 'jpg' || $ext == 'jpeg') && (!$width) && (!strpos($src,'wiki:dokuwiki'))) {
                // force to scale image to 680px
                $ret .= '<img src="'.ml($src, array('w' => "680", 'h' => $height, 'cache' => $cache, 'rev'=>$this->_getLastMediaRevisionAt($src))).'"';
            } else {
                $ret .= '<img src="'.ml($src, array('w' => $width, 'h' => $height, 'cache' => $cache, 'rev'=>$this->_getLastMediaRevisionAt($src))).'"';
            }
            $ret .= ' class="media'.$align.'"';

            if($title) {
                $ret .= ' title="'.$title.'"';
                $ret .= ' alt="'.$title.'"';
            } else {
                $ret .= ' alt=""';
            }

            if(!is_null($width))
                $ret .= ' width="'.$this->_xmlEntities($width).'"';
            if(!is_null($height))
                $ret .= ' height="'.$this->_xmlEntities($height).'"';
        } elseif(media_supportedav($mime, 'video') || media_supportedav($mime, 'audio')) {
            // first get the $title
            $title = !is_null($title) ? $this->_xmlEntities($title) : false;
            if(!$render) {
                // if the file is not supposed to be rendered
                // return the title of the file (just the sourcename if there is no title)
                return $title ? $title : $this->_xmlEntities(utf8_basename(noNS($src)));
            }

            $att          = array();
            $att['class'] = "media$align";
            if($title) {
                $att['title'] = $title;
            }

            if(media_supportedav($mime, 'video')) {
                //add video
                $ret .= $this->_video($src, $width, $height, $att);
            }
            if(media_supportedav($mime, 'audio')) {
                //add audio
                $ret .= $this->_audio($src, $att);
            }

        } elseif($mime == 'application/x-shockwave-flash') {
            if(!$render) {
                // if the flash is not supposed to be rendered
                // return the title of the flash
                if(!$title) {
                    // just show the sourcename
                    $title = utf8_basename(noNS($src));
                }
                return $this->_xmlEntities($title);
            }

            $att          = array();
            $att['class'] = "media$align";
            if($align == 'right') $att['align'] = 'right';
            if($align == 'left') $att['align'] = 'left';
            $ret .= html_flashobject(
                ml($src, array('cache' => $cache), true, '&'), $width, $height,
                array('quality' => 'high'),
                null,
                $att,
                $this->_xmlEntities($title)
            );
        } elseif($title) {
            // well at least we have a title to display
            $ret .= $this->_xmlEntities($title);
        } else {
            // just show the sourcename
            $ret .= $this->_xmlEntities(utf8_basename(noNS($src)));
        }

        return $ret;
    }

    public function internalmedia($src, $title=NULL, $align=NULL, $width=NULL,
                                  $height=NULL, $cache=NULL, $linking=NULL) {
        global $ID;
        list($src,$hash) = explode('#',$src,2);
        resolve_mediaid(getNS($ID),$src, $exists);

        $noLink = false;
        $render = ($linking == 'linkonly') ? false : true;
        $link = $this->_getMediaLinkConf($src, $title, $align, $width, $height, $cache, $render);

        list($ext,$mime,$dl) = mimetype($src,false);
        if(substr($mime,0,5) == 'image' && $render){
            if ($linking == NULL || $linking == '' || $linking == 'details') {
                $linking = 'direct';
            }
            $link['url'] = ml($src,array('id'=>$ID,'cache'=>$cache),($linking == 'direct'));
            } elseif($mime == 'application/x-shockwave-flash' && $render) {
            // don't link flash movies
            $noLink = true;
        }else{
            // add file icons
            $class = preg_replace('/[^_\-a-z0-9]+/i','_',$ext);
            $link['class'] .= ' mediafile mf_'.$class;
            $link['url'] = ml($src,array('id'=>$ID,'cache'=>$cache),true);
            if ($exists) $link['title'] .= ' (' . filesize_h(filesize(mediaFN($src))).')';
        }
    if($hash) $link['url'] .= '#'.$hash;
    //markup non existing files
    if (!$exists) {
         $link['class'] .= ' wikilink2';
    }
    
    //output formatted
    if ($linking == 'nolink' || $noLink) $this->doc .= $link['name'];
        else $this->doc .= $this->_formatLink($link);
    }
    // FIXME override any methods of Doku_Renderer_xhtml here
}

