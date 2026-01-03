<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommonFileLib.php
  declare(strict_types=1);

  /// <summary>The Common File Class Library</summary>
  
  // The LibName: XML comment triggers the file (library) HTML page generation.
  // It generates a page with the same name as the library.
  // LJCCommonFileLib.html
  /// LibName: LJCCommonFileLib
  // Classes: LJCCommonFile

  // ***************
  // Static: GetDebugFileName(), GetIndexedDebugFileName(), MkDir()
  /// <summary>Contains common functions.</summary>

  // A class triggers the class HTML page generation.
  // It generates a page with the same name as the class.
  // LJCCommonFile/LJCCommonFile.html
  class LJCCommonFile
  {
    // Gets the Debug file name.
    /// <include path='items/GetDebugFileName/*' file='Doc/LJCCommon.xml'/>

    // A method triggers the method HTML page generation.
    // It generates a page with the name: class plus method.
    // LJCCommonFile/LJCCommonFileGetDebugFileName.html
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
      , string $fileName): string
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
    public static function MkDir(string $folder): void
    {
      if (false == file_exists($folder))
      {
        mkdir($folder);
      }
    } // MkDir()
  } // LJCCommonFile
?>
