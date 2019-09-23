<?php
defined('_JEXEC') or die('Restricted access');
/**
* Attachments component
* @package Attachments
* @Copyright (C) 2007, 2008 Jonathan M. Cameron, All Rights Reserved
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @link http://joomlacode.org/gf/project/attachments/frs/
* @author Jonathan M. Cameron
**/

class AttachmentsFileTypes {

    var $attachments_icon_from_file_extension;
    var $attachments_icon_from_mime_type;

    function _init()
    {
        global $attachments_icon_from_file_extension;
        global $attachments_icon_from_mime_type;
        
        $attachments_icon_from_file_extension = array( 
              'aif' => 'music.gif',
              'aiff' => 'music.gif',
              'avi' => 'video.gif',
              'bmp' => 'image.gif',
              'bz2' => 'archive.gif',
              'c' => 'c.gif',
              'c++' => 'cpp.gif',
              'cab' => 'zip.gif',
              'cc' => 'cpp.gif',
              'cpp' => 'cpp.gif',
              'css' => 'css.gif',
              'csv' => 'csv.gif',
              'doc' => 'word.gif',
              'eps' => 'eps.gif',
              'gif' => 'image.gif',
              'h' => 'h.gif',
              'iv' => '3d.gif',
              'jpg' => 'image.gif',
              'js' => 'js.gif',
              'midi' => 'midi.gif',
              'mov' => 'mov.gif',
              'mp3' => 'music.gif',
              'mpeg' => 'video.gif',
              'mpg' => 'video.gif',
              'odg' => 'odg.gif',
              'odp' => 'odp.gif',
              'ods' => 'ods.gif',
              'odt' => 'odt.gif',
              'pdf' => 'pdf.gif',
              'php' => 'php.gif',
              'png' => 'image.gif',
              'pps' => 'ppt.gif',
              'ppt' => 'ppt.gif',
              'ps' => 'ps.gif',
              'ra' => 'audio.gif',
              'ram' => 'audio.gif',
              'rtf' => 'rtf.gif',
              'sql' => 'sql.gif',
              'swf' => 'flash.gif',
              'tar' => 'archive.gif',
              'txt' => 'text.gif',
              'vrml' => '3d.gif',
              'wav' =>  'audio.gif',
              'wmv' => 'video.gif',
              'wma' => 'music.gif',
              'wrl' => '3d.gif',
              'xls' => 'excel.gif',
              'xml' => 'xml.gif',
              'zip' => 'zip.gif'
              );

        $attachments_icon_from_mime_type = array(
              'application/bzip2' => 'archive.gif',
              'application/excel' => 'excel.gif',
              'application/msword' => 'word.gif',
              'application/pdf' => 'pdf.gif',
              'application/postscript' => 'ps.gif',
              'application/powerpoint' => 'ppt.gif',
              'application/vnd.ms-cab-compressed' => 'zip.gif',
              'application/vnd.ms-excel' => 'excel.gif',
              'application/vnd.ms-powerpoint' => 'ppt.gif',
              'application/vnd.ms-pps' => 'ppt.gif',
              'application/vnd.ms-word' => 'word.gif',
              'application/vnd.oasis.opendocument.graphics' => 'odg.gif',
              'application/vnd.oasis.opendocument.presentation' => 'odp.gif',
              'application/vnd.oasis.opendocument.spreadsheet' => 'ods.gif',
              'application/vnd.oasis.opendocument.text' => 'odt.gif',
              'application/x-bz2' => 'archive.gif',
              'application/x-javascript' => 'js.gif', 
              'application/x-midi' => 'midi.gif', 
              'application/x-shockwave-flash' => 'flash.gif',
              'application/x-tar' => 'archive.gif', 
              'application/x-vrml' => '3d.gif', 
              'application/x-zip' => 'zip.gif', 
              'application/xml' => 'xml.gif',
              'audio/mpeg' => 'music.gif',
              'audio/x-aiff' => 'music.gif',
              'audio/x-pn-realaudio' => 'audio.gif', 
              'audio/x-wav' => 'audio.gif', 
              'audio/x-ms-wma' => 'music.gif',
              'image/bmp' => 'image.gif',
              'image/gif' => 'image.gif',
              'image/jpeg' => 'image.gif',
              'image/png' => 'image.gif',
              'model/vrml' => '3d.gif',
              'text/css' => 'css.gif',
              'text/html' => 'html.gif',
              'text/plain' => 'text.gif',
              'text/rtf' => 'rtf.gif',
              'video/mpeg' => 'video.gif',
              'video/quicktime' => 'mov.gif',
              'video/x-ms-wmv' => 'video.gif',
              'video/x-msvideo' => 'video.gif'
               );
    }

    function icon_filename($filename, $mime_type)
    {
        $path_info = pathinfo($filename);

        global $attachments_icon_from_file_extension;
        global $attachments_icon_from_mime_type;
        
        if (!isset( $attachments_icon_from_file_extension )) {
            AttachmentsFileTypes::_init();
            }
                
        // Try the extension first
        $extension = strtolower($path_info['extension']);
        $iconf = $attachments_icon_from_file_extension[$extension];
        if ( strlen($iconf) > 0 ) 
            return $iconf;
                
        // Try the mime type
        $iconf = $attachments_icon_from_mime_type[$mime_type];
        if ( $iconf && (strlen($iconf) > 0) )
            return $iconf;
        else
            return '';
    }
        
    function unique_icon_filenames()
    {
        global $attachments_icon_from_file_extension;
        
        if (!isset( $attachments_icon_from_file_extension )) {
            AttachmentsFileTypes::_init();
            }
        
        $vals = array_unique(array_values($attachments_icon_from_file_extension));
        sort($vals);
                
        return $vals;
    }

}
?>
