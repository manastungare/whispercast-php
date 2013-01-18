<?php
/**
    Whispercast : Internet Radio for Yourself
    Creates a playlist on-the-fly for iTunes/WinAmp in M3U format.
    Copyright (c) 2006 Manas Tungare

    @author      Manas Tungare (manas@tungare.name)
    @copyright   Manas Tungare, 2006.
    @version     1.0
    @license     http://www.gnu.org/copyleft/gpl.html
*/
/*
    This program is free software; you can redistribute it and/or
    modify it under the terms of the GNU General Public License
    as published by the Free Software Foundation; either version 2
    of the License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    To obtain a copy of the GNU General Public License, write to
    the Free Software Foundation, Inc., 51 Franklin Street,
    Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once("config.php");

//-----------------------------------------------------------------------------------------//
// POLICE LINE: DO NOT CROSS (unless you know what you're doing.)

// Constants
$FORMAT_M3U = "m3u";

// Calculated Globals
$scriptUrl =  strtolower(strtok($_SERVER['SERVER_PROTOCOL'], '/')).'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$musicRootUrl = strtolower(strtok($_SERVER['SERVER_PROTOCOL'], '/')).'://'.$_SERVER['HTTP_HOST'].$musicRoot;

// Start
$path = isset($_GET['p']) ? $_GET["p"] : "";
$format = isset($_GET['f']) ? $FORMAT_M3U : "";

if ($FORMAT_M3U !== $format) {
  global $scriptUrl, $title;
  $m3uUrl = $scriptUrl.'?f=m3u&amp;p='.rawurlencode($path);
  require_once("./header.php");
}
writePlaylist($path, $format);
if ($FORMAT_M3U !== $format) {
  require_once("./footer.php");
}

// End

/**
 * Writes to output a browsable music index or music playlist.
 * @param path The path (inside <code>$musicRoot</code>) to make a playlist/index for.
 * @param format Either the string "m3u" or the blank string "".
 * @return Nothing
 */
function writePlaylist($path, $format) {
  global $FORMAT_M3U;
  global $extensions, $musicRoot, $musicRootUrl, $scriptUrl;

  $dirName = substr($path, strrpos("/".$path, "/"));
  $realPath = realpath($path).'/';
  // Directory names should be exploded and then rawurlencoded;
  // instead we encode the whole string, and replace the "%2F" with "/"
  $pathEnc = str_replace("%2F", "/", rawurlencode($path));

  // --- Send headers ---
  if ($format === $FORMAT_M3U) {
    // header('Content-Type: text/plain'); // For testing
    header('Content-Type: audio/x-mpegurl');
    header('Content-Disposition: inline; filename="'.$dirName.'.m3u"');
  }

  // --- The meat ---
  $files = array();
  $dirHandle = @opendir($realPath);

  // Add an entry to go one level up
  if ($path !== "") {
    $dirs["[Up]"] = $scriptUrl.'?p='. substr($pathEnc, 0, strrpos($pathEnc, "/"));
  }

  while (false !== ($file = @readdir($dirHandle))) {
    if (substr($file, 0, 1) != ".") { // Ignore ".", "..", ".any_hidden_files"
      if (is_dir($realPath.$file)) {
        if ($FORMAT_M3U === $format) {
          addFilesRecursively(&$files, $path.'/'.$file);
        } else {
          $dirs[$file] = $scriptUrl.'?p='.rawurlencode(
            (($path === "") ? $path : $path.'/')  // Insert a "/" if $path is not blank
            .$file);
        }
      } else if (isExtensionOK($file)) {
        $files[$file] = $musicRootUrl
          .(($pathEnc === "") ? "" : "/") // Insert a "/" if $pathEnc is not blank
          .$pathEnc
          .'/'.rawurlencode($file);
      }
    }
  }
  @closedir($dirHandle);

  if ($FORMAT_M3U === $format && isset($files)) {
    // For an M3U, simply write out each URL on a line by itself
    foreach ($files as $display => $url) {
      echo $url."\n";
    }
  } else {
    // For an HTML page, write using callbacks.
    if (isset($dirs)) {
      natcasesort($dirs);
      foreach ($dirs as $display => $url) {
        writeDirectory($display, $url);
      }
    }
    if (isset($files)) {
      natcasesort($files);
      foreach ($files as $display => $url) {
        writeFile($display, $url);
      }
    }
  }
}

function addFilesRecursively(&$arr, $subDir) {
  global $extensions, $musicRoot, $musicRootUrl, $scriptRoot;

  $realSubDirPath = realpath($musicRoot.'/'.$subDir);
  $subDirPathEnc = str_replace("%2F", "/", rawurlencode($subDir));

  $subDirHandle = @opendir($realSubDirPath);
  while (false !== ($file = @readdir($subDirHandle))) {
    if (substr($file, 0, 1) != ".") { // Ignore ".", "..", ".any_hidden_files"
      if (is_dir($realSubDirPath.'/'.$file)) {
        addFilesRecursively($arr, $subDir.'/'.$file);
      } else if (isExtensionOK($file)) {
        $arr[$file] = $musicRootUrl
        .(($subDirPathEnc === "") ? "" : "/") // Insert a "/" if $subDirPathEnc is not blank
        .$subDirPathEnc
        .'/'.rawurlencode($file);
      }
    }
  }
  @closedir($subDirHandle);
}

function isExtensionOK($file) {
  global $extensions;

  $ext = strrchr($file, ".");
  if ($ext !== '' && false !== strpos($extensions, $ext)) {
    return true;
  }
}
?>
