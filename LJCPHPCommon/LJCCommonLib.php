<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommonLib.php
  declare(strict_types=1);

  // Classes
  // File
  //   LJCCommon

  /// <summary>The Common PHP Class Library</summary>
  /// LibName: LJCCommonLib

  // ***************
  /// <summary>Contains common functions.</summary>
  class LJCCommon
  {
    // ---------------
    // Static Functions

    // Gets the first index for the search value.
    /// <include path='items/StrPos/*' file='Doc/LJCCommon.xml'/>
    public static function StrPos(?string $text, ?string $find
      , int $start = 0, bool $exact = false) : int
    {
      $retValue = -1;

      $isFound = false;
      if ($text != null && $find !=null)
      {
        $index = stripos($text, $find, $start);
        if ($exact)
        {
          $index = strpos($text, $find, $start);
        }

        // strpos and stripos are inconsistant.
        // if not found: == null and >= 0 are true.
        if ($index === 0)
        {
          // if first index: === 0, == null and >= 0 are true. 
          $isFound = true;
        }
        else
        {
          // if not first index: != null and >= 0 are true. 
          if ($index != null && $index >= 0)
          {
            $isFound = true;
          }
        }
        if ($isFound)
        {
          $retValue = $index;
        }
      }
      return $retValue;
    } // StrPos()

    // Gets the last index for the search value.
    /// <include path='items/StrRPos/*' file='Doc/LJCCommon.xml'/>
    public static function StrRPos(?string $text, ?string $find
      , int $start = 0, bool $exact = false) : int
    {
      $retValue = -1;

      $isFound = false;
      if ($text != null && $find !=null)
      {
        $index = strripos($text, $find, $start);
        if ($exact)
        {
          $index = strrpos($text, $find, $start);
        }

        // strrpos and strripos are inconsistant.
        // if not found: == null and >= 0 are true.
        if ($index === 0)
        {
          // if first index: === 0, == null and >= 0 are true. 
          $isFound = true;
        }
        else
        {
          // if not first index: != null and >= 0 are true. 
          if ($index != null && $index >= 0)
          {
            $isFound = true;
          }
        }
        if ($isFound)
        {
          $retValue = $index;
        }
      }
      return $retValue;
    } // StrRPos()

    /// <summary>Returns a text value as boolean.</summary>
    public static function GetBool(?string $text) : bool
    {
      $retValue = true;

      if (null == $text)
      {
        $retValue = false;
      }
      else
      {
        switch (strtolower(trim($text)))
        {
          case "false":
          case "":
          case "0":
            $retValue = false;
            break;
        }
      }
      return $retValue;
    } // GetBool()

    // Gets the Debug file name.
    /// <include path='items/GetDebugFileName/*' file='Doc/LJCCommon.xml'/>
    public static function GetDebugFileName(string $folder, string $fileName)
      : string
    {
      $retValue = "$folder/$fileName.txt";

      self::MkDir($folder);
      return $retValue;
    } // GetDebugFileName()

    // Gets the string between the delimiters.
    /// <include path='items/GetDelimitedString/*' file='Doc/LJCCommon.xml'/>
    public static function GetDelimitedString(string $text, string $beginDelimiter
      , ?string $endDelimiter, bool $lTrim = true, bool $rTrim = true) : ?string
    {
      $retValue = null;

      $begin = self::StrPos($text, $beginDelimiter);
      if ($begin >= 0)
      {
        $begin += strlen($beginDelimiter);

        $end = -1;
        if ($endDelimiter != null)
        {
          $end = self::StrPos($text, $endDelimiter, $begin);
        }
        if ($end < 0)
        {
          // Set to end of line.
          $end = strlen($text) + 1;
        }
        $length = $end - $begin;
        $retValue = substr($text, $begin, $length);
        if ($lTrim)
        {
          $retValue = ltrim($retValue);
        }
        if ($rTrim)
        {
          $retValue = rtrim($retValue);
        }
      }
      return $retValue;
    } // GetDelimitedString()

    // Gets the FileName from the file spec.
    /// <include path='items/GetFileName/*' file='Doc/LJCCommon.xml'/>
    public static function GetFileName(string $fileSpec) : string
    {
      $retValue = $fileSpec;

      $begin = self::StrRPos($fileSpec, "/");
      if ($begin < 0)
      {
        $begin = -1;
      }
      $begin++;
      $end = self::StrRPos($fileSpec, ".");
      if ($end < 0)
      {
        $end = strlen($fileSpec);
      }
      $length = $end - $begin;
      $retValue = substr($fileSpec, $begin, $length);
      return $retValue;
    } // GetFileName()

    // Gets the Path from the file spec.
    /// <include path='items/GetFileSpecPath/*' file='Doc/LJCCommon.xml'/>
    public static function GetFileSpecPath(string $fileSpec) : string
    {
      // *** Change *** 5/4/25
      //$retValue = $fileSpec;
      $retValue = "";

      $length = LJCCommon::StrRPos($fileSpec, "/");
      if ($length >= 0)
      {
        $retValue = substr($fileSpec, 0, $length);
      }
      return $retValue;
    } // GetFileSpecPath()

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

    /// <summary>Get string tokens.</summary>
    /// <param name="$text">The string value.</param>
    /// <param name="$splitString">The split string value.</param>
    public static function GetTokens(string $text, ?string $splitString = null)
      : array
    {
      if (null == $splitString)
      {
        $splitString = "/[\s,]+/";
      }
      $trimLine = trim($text);
      $retValue = preg_split($splitString, $trimLine, 0, PREG_SPLIT_NO_EMPTY);
      return $retValue;
    } // GetTokens()

    /// <summary>Creates the specified folder if it does not already exist.</summary>
    /// <param name="$folder">The folder name.</param>
    public static function MkDir(string $folder)
    {
      if (false == file_exists($folder))
      {
        mkdir($folder);
      }
    } // MkDir()

    // Returns a scrubbed external value.
    /// <include path='items/Scrub/*' file='Doc/LJCCommon.xml'/>
    public static function Scrub(string $text) : string
    {
      $retValue = trim($text);
      $retValue = stripslashes($retValue);
      $retValue = htmlspecialchars($retValue);
      return $retValue;
    } // Scrub()
  } // LJCCommon
?>