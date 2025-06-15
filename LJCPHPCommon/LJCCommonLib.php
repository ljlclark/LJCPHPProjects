<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommonLib.php
  declare(strict_types=1);

  /// <summary>The Common PHP Class Library</summary>
  /// LibName: LJCCommonLib
  // Classes: LJCCommon

  // ***************
  // Static: Scrub(), Split(), StrPos(), StrRPos(), GetBool()
  //   GetDelimitedString(), GetFileName(), GetFileSpecPath(), GetTokens()
  /// <summary>Contains common functions.</summary>
  class LJCCommon
  {
    // ---------------
    // Static Functions

    // Display debug text.
    public static function Debug(int $lineNumber, string $text)
    {
      echo("\r\n$lineNumber {$text}");
    }

    /// <summary>Returns a text value as int.</summary>
    // *** Change *** 5/25/25
    public static function GetBool(?string $text) : int
    {
      //$retValue = true;
      $retValue = 1;

      if (null == $text)
      {
        //$retValue = false;
        $retValue = 0;
      }
      else
      {
        switch (strtolower(trim($text)))
        {
          case "false":
          case "":
          case "0":
            //$retValue = false;
            $retValue = 0;
            break;
        }
      }
      return $retValue;
    } // GetBool()

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

    /// <summary>Get string tokens.</summary>
    /// <param name="$text">The string value.</param>
    /// <param name="$splitString">The split string value.</param>
    /// <returns>The split string array.</returns>
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

    // Checks for array elements.
    public static function HasElements($array) : bool
    {
      $retValue = false;

      if (is_array($array)
        && count($array) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    }

    // Checks for array elements.
    public static function HasItems($collection) : bool
    {
      $retValue = false;

      if (isset($collection)
        && count($collection) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    }

    /// <summary>Check for text.</summary>
    /// <param name="$text"></param>
    /// <returns>
    ///   true if the text has other than white space; otherwise false;
    /// </returns>
    public static function HasValue($text) : bool
    {
      $retValue = false;

      if ($text != null
        && strlen(trim($text)) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    }

    // Returns a scrubbed external value.
    /// <include path='items/Scrub/*' file='Doc/LJCCommon.xml'/>
    public static function Scrub(string $text) : string
    {
      $retValue = trim($text);
      $retValue = stripslashes($retValue);
      $retValue = htmlspecialchars($retValue);
      return $retValue;
    } // Scrub()

    /// <summary>Splits a string on whitespace.</summary>
    public static function Split(string $text) : array
    {
      $retValues = preg_split("/[\s,]+/", $text, 0, PREG_SPLIT_NO_EMPTY);
      return $retValues;
    }

    // Gets the first index for the search value.
    /// <include path='items/StrPos/*' file='Doc/LJCCommon.xml'/>
    public static function StrPos(?string $text, ?string $find
      , int $startIndex = 0, bool $exact = false) : int
    {
      $retValue = -1;

      $isFound = false;
      if ($text != null && $find !=null)
      {
        $index = stripos($text, $find, $startIndex);
        if ($exact)
        {
          $index = strpos($text, $find, $startIndex);
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
    public static function StrRPos(?string $text, ?string $find, int $startIndex = 0
      , bool $exact = false) : int
    {
      $retValue = -1;

      $isFound = false;
      if ($text != null
        && $find !=null)
      {
        // *** Add ***
        $value = $text;
        if ($startIndex > 0)
        {
          $value = substr($text, 0, $startIndex + 1);
        }
        // *** Change ***
        //$index = strripos($text, $find, $start);
        $index = strripos($value, $find);
        // *** End   ***
        if ($exact)
        {
          // *** Change ***
          //$index = strrpos($text, $find, $start);
          $index = strrpos($value, $find);
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

    // Writes the test compare text.
    public static function WriteCompare(string $methodName, string $result, string $compare)
    {
      if (!self::HasValue($result))
      {
        $result = "No Result";
      }
      if (!self::HasValue($compare))
      {
        $compare = "No Compare";
      }

      if ($result != $compare)
      {
        echo("\r\n{$methodName}\r\n");
        echo("$result\r\n");
        echo(" !=\r\n");
        echo("$compare\r\n");
      }
    }
  } // LJCCommon
?>
