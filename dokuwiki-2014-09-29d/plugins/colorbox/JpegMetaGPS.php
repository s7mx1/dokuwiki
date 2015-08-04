<?php
/**
 * JPEG metadata reader/writer
 *
 * @license    BSD <http://www.opensource.org/licenses/bsd-license.php>
 * @link       http://github.com/sd/jpeg-php
 * @author     Sebastian Delmont <sdelmont@zonageek.com>
 * @author     Andreas Gohr <andi@splitbrain.org>
 * @author     Hakan Sandell <hakan.sandell@mydata.se>
 * @todo       Add support for Maker Notes, Extend for GIF and PNG metadata
 */

// Original copyright notice:
//
// Copyright (c) 2003 Sebastian Delmont <sdelmont@zonageek.com>
// All rights reserved.
//
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions
// are met:
// 1. Redistributions of source code must retain the above copyright
//    notice, this list of conditions and the following disclaimer.
// 2. Redistributions in binary form must reproduce the above copyright
//    notice, this list of conditions and the following disclaimer in the
//    documentation and/or other materials provided with the distribution.
// 3. Neither the name of the author nor the names of its contributors
//    may be used to endorse or promote products derived from this software
//    without specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS
// IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED
// TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
// PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
// HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
// SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
// TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
// PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
// LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
// NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
// SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE

require_once DOKU_INC.'inc/JpegMeta.php';

class JpegMetaGPS extends JpegMeta {

    function getExifInfo() {
        $this->_parseAll();

        $info = array();

        if ($this->_markers == null) {
            return false;
        }
        
        if ($this->getCamera()) $info['Camera'] = $this->getCamera();
        if ($this->_info['exif']['FNumber']['val']) $info['FN'] = 'F'.$this->_info['exif']['FNumber']['val'];
        if ($this->_info['exif']['FocalLength']['val']) $info['Focal'] = $this->_info['exif']['FocalLength']['val'].'mm';
        if ($this->_info['exif']['MeteringMode']) $info['Metering'] = $this->_info['exif']['MeteringMode'];
        if ($this->_info['exif']['ISOSpeedRatings']) $info['ISO'] = $this->_info['exif']['ISOSpeedRatings'];
        if ($this->_info['exif']['LightSource']) $info['Light'] = $this->_info['exif']['LightSource'];
        if ($this->_info['ExposureBiasValue']['val']) $info['Exposure'] = $this->_info['ExposureBiasValue']['val'].'EV';
        

        $shutter=$this->getShutterSpeed();
        if ($shutter) $info['Shutter'] = $shutter.'s';
        $dates = $this->getDates();
        
        

        $info['Dimension'] = $this->getWidth().'x'.$this->getHeight();
        
        $info['Size'] = $this->_info['file']['NiceSize'];
        
        $info['Date'] = $dates['EarliestTimeStr'];
        
        $longitude=$this->getGps( $this->_info['exif']['GPSLongitude'],$this->_info['exif']['GPSLongitudeRef']);
        $latitude=$this->getGps( $this->_info['exif']['GPSLatitude'],$this->_info['exif']['GPSLatitudeRef']);
        if ($latitude && $longitude) {
          $info['Latitude'] = rtrim($this->_info['exif']['GPSLatitudeRef']).$latitude;
          $info['Longitude'] = rtrim($this->_info['exif']['GPSLongitudeRef']).$longitude;
          $address = $this->getaddress($latitude,$longitude);
          if ($address) $info['Address'] = $address;
        }
        return $info;
    }

    function getShortExifInfo() {
        $this->_parseAll();

        $info = array();

        if ($this->_markers == null) {
            return false;
        }
        
        if ($this->getCamera()) $info['Camera'] = $this->getCamera();
        if ($this->_info['exif']['FNumber']['val']) $info['FN'] = 'F'.$this->_info['exif']['FNumber']['val'];
        if ($this->_info['exif']['FocalLength']['val']) $info['Focal'] = $this->_info['exif']['FocalLength']['val'].'mm';
        if ($this->_info['exif']['MeteringMode']) $info['Metering'] = 'M'.$this->_info['exif']['MeteringMode'];
        if ($this->_info['exif']['ISOSpeedRatings']) $info['ISO'] = $this->_info['exif']['ISOSpeedRatings'].'ISO';
        if ($this->_info['exif']['LightSource']) $info['Light'] = 'L'.$this->_info['exif']['LightSource'];
        if ($this->_info['ExposureBiasValue']['val']) $info['Exposure'] = $this->_info['ExposureBiasValue']['val'].'EV';
        
        $shutter=$this->getShutterSpeed();
        if ($shutter) $info['Shutter'] = $shutter.'s';
        
        $dates = $this->getDates();
        
        

        $info['Dimension'] = $this->getWidth().'x'.$this->getHeight();
        
        $info['Size'] = $this->_info['file']['NiceSize'];
        
        $info['Date'] = $dates['EarliestTimeStr'];
        
        $longitude=$this->getGps( $this->_info['exif']['GPSLongitude'],$this->_info['exif']['GPSLongitudeRef']);
        $latitude=$this->getGps( $this->_info['exif']['GPSLatitude'],$this->_info['exif']['GPSLatitudeRef']);
        if ($latitude && $longitude) {
          $info['Latitude'] = rtrim($this->_info['exif']['GPSLatitudeRef']).$latitude;
          $info['Longitude'] = rtrim($this->_info['exif']['GPSLongitudeRef']).$longitude;
          $address = $this->getaddress($latitude,$longitude);
          if ($address) $info['Address'] = 'Adr: '.$address;
        }
        return $info;
    }
   
    function getGPSInfo() {
        global $conf;
        $this->_parseAll();
        if ($this->_markers == null) {
            return false;
        }
        $longitude=$this->getGps( $this->_info['exif']['GPSLongitude'],$this->_info['exif']['GPSLongitudeRef']);
        $latitude=$this->getGps( $this->_info['exif']['GPSLatitude'],$this->_info['exif']['GPSLatitudeRef']);
        if ($conf['syslog']) syslog(LOG_WARNING,'[colorbox:JpegMetaGPS.php] getGPSInfo:latitude:'. $latitude);
        if ($conf['syslog']) syslog(LOG_WARNING,'[colorbox:JpegMetaGPS.php] getGPSInfo:longitude:'. $longitude);
        if ($latitude && $longitude) {
        $link = 'http://maps.google.com/maps?q='.$latitude.','.$longitude;
        return $link;
        } else {
        return false;
        }

    }

    function getaddress($lat,$lng) {
    $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
    $json = @file_get_contents($url);
    $data=json_decode($json);
    $status = $data->status;
    if($status=="OK")
    return $data->results[0]->formatted_address;
    else
    return false;
    }

    function getGps($exifCoord, $hemi) {

        $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]['val']) : 0;
        $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]['val']) : 0;
        $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]['val']) : 0;
    
        $flip = (rtrim($hemi) == 'W' or rtrim($hemi) == 'S') ? -1 : 1;
    
        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    
    }
    
    function gps2Num($coordPart) {
    
        $parts = explode('/', $coordPart);
    
        if (count($parts) <= 0)
            return 0;
    
        if (count($parts) == 1)
            return $parts[0];
    
        return floatval($parts[0]) / floatval($parts[1]);
    }


    /*************************************************************/
    function _readIFD($data, $base, $offset, $isBigEndian, $mode) {
        $EXIFTags = $this->_exifTagNames($mode);

        $numEntries = $this->_getShort($data, $base + $offset, $isBigEndian);
        $offset += 2;

        $exifTIFFOffset = 0;
        $exifTIFFLength = 0;
        $exifThumbnailOffset = 0;
        $exifThumbnailLength = 0;

        for ($i = 0; $i < $numEntries; $i++) {
            $tag = $this->_getShort($data, $base + $offset, $isBigEndian);
            $offset += 2;
            $type = $this->_getShort($data, $base + $offset, $isBigEndian);
            $offset += 2;
            $count = $this->_getLong($data, $base + $offset, $isBigEndian);
            $offset += 4;

            if (($type < 1) || ($type > 12))
                return false; // Unexpected Type

            $typeLengths = array( -1, 1, 1, 2, 4, 8, 1, 1, 2, 4, 8, 4, 8 );

            $dataLength = $typeLengths[$type] * $count;
            if ($dataLength > 4) {
                $dataOffset = $this->_getLong($data, $base + $offset, $isBigEndian);
                $rawValue = $this->_getFixedString($data, $base + $dataOffset, $dataLength);
            } else {
                $rawValue = $this->_getFixedString($data, $base + $offset, $dataLength);
            }
            $offset += 4;

            switch ($type) {
                case 1:    // UBYTE
                    if ($count == 1) {
                        $value = $this->_getByte($rawValue, 0);
                    } else {
                        $value = array();
                        for ($j = 0; $j < $count; $j++)
                            $value[$j] = $this->_getByte($rawValue, $j);
                    }
                    break;
                case 2:    // ASCII
                    $value = $rawValue;
                    break;
                case 3:    // USHORT
                    if ($count == 1) {
                        $value = $this->_getShort($rawValue, 0, $isBigEndian);
                    } else {
                        $value = array();
                        for ($j = 0; $j < $count; $j++)
                            $value[$j] = $this->_getShort($rawValue, $j * 2, $isBigEndian);
                    }
                    break;
                case 4:    // ULONG
                    if ($count == 1) {
                        $value = $this->_getLong($rawValue, 0, $isBigEndian);
                    } else {
                        $value = array();
                        for ($j = 0; $j < $count; $j++)
                            $value[$j] = $this->_getLong($rawValue, $j * 4, $isBigEndian);
                    }
                    break;
                case 5:    // URATIONAL
                    if ($count == 1) {
                        $a = $this->_getLong($rawValue, 0, $isBigEndian);
                        $b = $this->_getLong($rawValue, 4, $isBigEndian);
                        $value = array();
                        $value['val'] = 0;
                        $value['num'] = $a;
                        $value['den'] = $b;
                        if (($a != 0) && ($b != 0)) {
                            $value['val'] = $a / $b;
                        }
                    } else {
                        $value = array();
                        for ($j = 0; $j < $count; $j++) {
                            $a = $this->_getLong($rawValue, $j * 8, $isBigEndian);
                            $b = $this->_getLong($rawValue, ($j * 8) + 4, $isBigEndian);
                            //$value = array();
                            $value[$j]['val'] = 0;
                            $value[$j]['num'] = $a;
                            $value[$j]['den'] = $b;
                            if (($a != 0) && ($b != 0))
                                $value[$j]['val'] = $a / $b;
                        }
                    }
                    break;
                case 6:    // SBYTE
                    if ($count == 1) {
                        $value = $this->_getByte($rawValue, 0);
                    } else {
                        $value = array();
                        for ($j = 0; $j < $count; $j++)
                            $value[$j] = $this->_getByte($rawValue, $j);
                    }
                    break;
                case 7:    // UNDEFINED
                    $value = $rawValue;
                    break;
                case 8:    // SSHORT
                    if ($count == 1) {
                        $value = $this->_getShort($rawValue, 0, $isBigEndian);
                    } else {
                        $value = array();
                        for ($j = 0; $j < $count; $j++)
                            $value[$j] = $this->_getShort($rawValue, $j * 2, $isBigEndian);
                    }
                    break;
                case 9:    // SLONG
                    if ($count == 1) {
                        $value = $this->_getLong($rawValue, 0, $isBigEndian);
                    } else {
                        $value = array();
                        for ($j = 0; $j < $count; $j++)
                            $value[$j] = $this->_getLong($rawValue, $j * 4, $isBigEndian);
                    }
                    break;
                case 10:   // SRATIONAL
                    if ($count == 1) {
                        $a = $this->_getLong($rawValue, 0, $isBigEndian);
                        $b = $this->_getLong($rawValue, 4, $isBigEndian);
                        $value = array();
                        $value['val'] = 0;
                        $value['num'] = $a;
                        $value['den'] = $b;
                        if (($a != 0) && ($b != 0))
                            $value['val'] = $a / $b;
                    } else {
                        $value = array();
                        for ($j = 0; $j < $count; $j++) {
                            $a = $this->_getLong($rawValue, $j * 8, $isBigEndian);
                            $b = $this->_getLong($rawValue, ($j * 8) + 4, $isBigEndian);
                            $value = array();
                            $value[$j]['val'] = 0;
                            $value[$j]['num'] = $a;
                            $value[$j]['den'] = $b;
                            if (($a != 0) && ($b != 0))
                                $value[$j]['val'] = $a / $b;
                        }
                    }
                    break;
                case 11:   // FLOAT
                    $value = $rawValue;
                    break;

                case 12:   // DFLOAT
                    $value = $rawValue;
                    break;
                default:
                    return false; // Unexpected Type
            }

            $tagName = '';
            if (($mode == 'ifd0') && ($tag == 0x8769)) {  // ExifIFDOffset
                $this->_readIFD($data, $base, $value, $isBigEndian, 'exif');
            } elseif (($mode == 'ifd0') && ($tag == 0x8825)) {  // GPSIFDOffset
                $this->_readIFD($data, $base, $value, $isBigEndian, 'gps');
            } elseif (($mode == 'ifd1') && ($tag == 0x0111)) {  // TIFFStripOffsets
                $exifTIFFOffset = $value;
            } elseif (($mode == 'ifd1') && ($tag == 0x0117)) {  // TIFFStripByteCounts
                $exifTIFFLength = $value;
            } elseif (($mode == 'ifd1') && ($tag == 0x0201)) {  // TIFFJFIFOffset
                $exifThumbnailOffset = $value;
            } elseif (($mode == 'ifd1') && ($tag == 0x0202)) {  // TIFFJFIFLength
                $exifThumbnailLength = $value;
            } elseif (($mode == 'exif') && ($tag == 0xA005)) {  // InteropIFDOffset
                $this->_readIFD($data, $base, $value, $isBigEndian, 'interop');
            }
            // elseif (($mode == 'exif') && ($tag == 0x927C)) {  // MakerNote
            // }
            else {
                if (isset($EXIFTags[$tag])) {
                    $tagName = $EXIFTags[$tag];
                    if (isset($this->_info['exif'][$tagName])) {
                        if (!is_array($this->_info['exif'][$tagName])) {
                            $aux = array();
                            $aux[0] = $this->_info['exif'][$tagName];
                            $this->_info['exif'][$tagName] = $aux;
                        }

                        $this->_info['exif'][$tagName][count($this->_info['exif'][$tagName])] = $value;
                    } else {
                        $this->_info['exif'][$tagName] = $value;
                    }
                }
                /*
                 else {
                    echo sprintf("<h1>Unknown tag %02x (t: %d l: %d) %s in %s</h1>", $tag, $type, $count, $mode, $this->_fileName);
                    // Unknown Tags will be ignored!!!
                    // That's because the tag might be a pointer (like the Exif tag)
                    // and saving it without saving the data it points to might
                    // create an invalid file.
                }
                */
            }
        }

        if (($exifThumbnailOffset > 0) && ($exifThumbnailLength > 0)) {
            $this->_info['exif']['JFIFThumbnail'] = $this->_getFixedString($data, $base + $exifThumbnailOffset, $exifThumbnailLength);
        }

        if (($exifTIFFOffset > 0) && ($exifTIFFLength > 0)) {
            $this->_info['exif']['TIFFStrips'] = $this->_getFixedString($data, $base + $exifTIFFOffset, $exifTIFFLength);
        }

        $nextOffset = $this->_getLong($data, $base + $offset, $isBigEndian);
        return $nextOffset;
    }


}

/* vim: set expandtab tabstop=4 shiftwidth=4: */
