<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommonLib.php
  declare(strict_types=1);
  // Path: Codeline
  include_once "LJCPHPCommon/LJCCommonLib.php";

  /// <summary>The Common PHP Class Library</summary>
  /// LibName: LJCCommonLib
  // Classes: LJCCommon

  // ***************
  // Static: RelativePrefix()
  /// <summary>Contains common functions.</summary>
  class LJCRoot
  {
    // ---------------
    // Static Functions

    // Gets the relative path for a codeline.
    /// <param name="$codeline">The codeline name.</param>
    public static function RelativePrefix(string $codeline)
    {
      $debug = new LJCDebug("LJCCommonLib", "LJCCommon"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("RelativePrefix", $enabled);
      $retText = "";

      $cwd = getcwd();
      $folders = explode("\\", $cwd);
      $count = count($folders);
      $found = false;
      for ($index = 0; $index < $count; $index++)
      {
        $folder = $folders[$index];
        if ($found)
        {
          $retText .= "../";
        }
        if ($folder == $codeline)
        {
          $found = true;
        }
      }
      $retText = substr($retText, 0, strlen($retText) - 1);

      $debug->EndMethod($enabled);
      return $retText;
    }
  } // LJCRoot
?>