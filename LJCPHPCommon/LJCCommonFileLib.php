<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommonFileLib.php
  declare(strict_types=1);

  /// <summary>The Common PHP Class Library</summary>
  /// LibName: LJCCommonFileLib
  // Classes: LJCCommonFile

  // ***************
  // Static: GetDebugFileName(), GetIndexedDebugFileName(), MkDir()
  /// <summary>Contains common functions.</summary>
  class LJCCommonFile
  {
    // Gets the Debug file name.
    /// <include path='items/GetDebugFileName/*' file='Doc/LJCCommon.xml'/>
    public static function GetDebugFileName(string $folder, string $fileName)
      : string
    {
      $retValue = "$folder/$fileName.txt";

      self::MkDir($folder);
      return $retValue;
    } // GetDebugFileName()

    // Gets the indexed Debug file name.
    /// <include path='items/GetIndexedDebugFileName/*' file='Doc/LJCCommon.xml'/>
    public static function GetIndexedDebugFileName(string $folder
      , string $fileName)	: string
    {
      $retValue = "$folder/$fileName.txt";

      self::MkDir($folder);

      $index = 1;
      while (file_exists($retValue))
      {
        $index++;
        $retValue = "$folder/$fileName$index.txt";
      }
      return $retValue;
    } // GetIndexedDebugFileName()

    // Creates the specified folder if it does not already exist.
    /// <param name="$folder">The folder name.</param>
    public static function MkDir(string $folder)
    {
      if (false == file_exists($folder))
      {
        mkdir($folder);
      }
    } // MkDir()
  } // LJCCommonFile
?>
