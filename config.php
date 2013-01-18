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
//-----------------------------------------------------------------------------------------//
//  Configuration

/**
 * Full path from root of web-server; must begin with a slash.
 */
$musicRoot = "/Music";

/**
 * For cosmetic purposes only.
 */
$title = "Whispercast Music Library";

/**
 * Make sure your media player supports whatever you put here.
 * Must include a period before the extension: ".ogg" is good; "ogg" is not.
 */
$extensions = ".mp3";

/**
 * Callback for writing a directory entry
 * @param $display Directory name that should be displayed
 * @param $url URL that it should link to.
 */
function writeDirectory($display, $url) {
  echo('      <a href="'.$url.'"><img src="./folder.png" alt="[=]" /> '.$display.'</a><br/>'."\n");
}

/**
 * Callback for writing a file entry
 * @param $display Filename that should be displayed
 * @param $url URL that it should link to.
 */
function writeFile($display, $url) {
  echo('      <a href="'.$url.'"><img src="./file.png" alt="(x)" /> '.$display.'</a><br/>'."\n");
}
?>
