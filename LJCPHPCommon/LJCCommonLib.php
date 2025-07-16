<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommonLib.php
  declare(strict_types=1);

  /// <summary>The Common PHP Class Library</summary>
  /// LibName: LJCCommonLib
  // Classes: LJC

  // ***************
  // Static: Debug(), EndsWithNewLine(), GetDelimitedString()
  //    GetFileName(), GetFileSpecPath(), GetTokens()
  //    HasElements(), HasItems(), HasValue(), HasXML(), NewLineTrim() 
  //    Scrub(), Split(), StrPos(), StrRPos(), ToBool(), ToBoolInt()
  //    WriteCompare(), XMLToString()
  /// <summary>Contains common functions.</summary>
  class LJC
  {
    // ---------------
    // Static Functions

    // Display debug text.
    public static function Debug(int $lineNumber = 0, string $text = ""
      , $value = null): void
    {
      echo("\r\n");
      if ($lineNumber > 0)
      {
        echo("$lineNumber");
      }
      if (self::HasValue($text))
      {
        echo(" {$text}");
        if ($value != null)
        {
          echo(" = ");
        }
      }
      if ($value != null)
      {
        echo("{$value}");
      }
    }

    // Indicates if the builder text ends with a newline.
    public static function EndsWithNewLine(string $text): bool
    {
      $retValue = false;

      if (strlen($text) > 0)
      {
        $length = strlen($text);
        if ("\n" == $text[$length - 1])
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // Gets the string between the delimiters.
    /// <include path='items/GetDelimitedString/*' file='Doc/LJCCommon.xml'/>
    public static function GetDelimitedString(string $text, string $beginDelimiter
      , ?string $endDelimiter, bool $lTrim = true, bool $rTrim = true): ?string
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
    public static function GetFileName(string $fileSpec): string
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
    public static function GetFileSpecPath(string $fileSpec): string
    {
      $retValue = "";

      $length = LJC::StrRPos($fileSpec, "/");
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
    public static function HasElements($array): bool
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
    public static function HasItems($collection): bool
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
    public static function HasValue($text): bool
    {
      $retValue = false;

      if ($text != null
        && strlen(trim($text)) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    }

    /// <summary>Checks an XML element for a value.
    /// <param name="$xmlElement">The simple xml element.</param>
    /// <returns>
    ///   true if element text has other than white space; otherwise false;
    /// </returns>
    public static function HasXML(SimpleXMLElement $xmlElement): bool
    {
      $retValue = false;

      if ($xmlElement != null)
      {
        $value = self::XMLToString($xmlElement);
        if (strlen($value) > 0)
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // Remove newline from text.
    public static function NewLineTrim(string $text): string
    {
      $retValue = $text;

      if (strlen($text) > 0)
      {
        $length = strlen($text);
        if ("\n" == $text[$length - 1])
        {
          $text = substr($text, 0, $length - 1);
        }
        $length = strlen($text);
        if ("\r" == $text[$length - 1])
        {
          $text = substr($text, 0, $length - 1);
        }
        $retValue = $text;
      }
      return $retValue;
    }

    // Remove element from string array by value.
    public static function RemoveString(array &$array, string $value): void
    {
      if (self::HasElements($array))
      {
        $key = array_search($value, $array);

        // Check empty string because index could be "0" which
        // evaluates to false.
        if ($key != "")
        {
          unset($array[$key]);
        }
      }
    }

    // Returns a scrubbed external value.
    /// <include path='items/Scrub/*' file='Doc/LJCCommon.xml'/>
    public static function Scrub(string $text): string
    {
      $retValue = trim($text);
      $retValue = stripslashes($retValue);
      $retValue = htmlspecialchars($retValue);
      return $retValue;
    } // Scrub()

    /// <summary>Splits a string on whitespace.</summary>
    public static function Split(string $text): array
    {
      $retValues = preg_split("/[\s,]+/", $text, 0, PREG_SPLIT_NO_EMPTY);
      return $retValues;
    }

    // Gets the first index for the search value.
    /// <include path='items/StrPos/*' file='Doc/LJCCommon.xml'/>
    public static function StrPos(?string $text, ?string $find
      , int $startIndex = 0, bool $exact = false): int
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
      , bool $exact = false): int
    {
      $retValue = -1;

      $isFound = false;
      if ($text != null
        && $find !=null)
      {
        $value = $text;
        if ($startIndex > 0)
        {
          $value = substr($text, 0, $startIndex + 1);
        }
        $index = strripos($value, $find);
        if ($exact)
        {
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

    /// <summary>Returns a value as bool.</summary>
    public static function ToBool($value): bool
    {
      $retValue = boolval($value);

      $testValue = strtolower(trim($value));
      if ("0" == $testValue
        || "false" == $testValue)
      {
        $retValue = false;
      }
      return $retValue;
    } // ToBool()

    /// <summary>Returns a text value as int boolean.</summary>
    public static function ToBoolInt(?string $text): int
    {
      $retValue = 1;

      if (null == $text)
      {
        $retValue = 0;
      }
      else
      {
        switch (strtolower(trim($text)))
        {
          case "false":
          case "":
          case "0":
            $retValue = 0;
            break;
        }
      }
      return $retValue;
    } // ToBoolInt()

    // Writes the test compare text.
    public static function WriteCompare(string $methodName, string $result
      , string $compare): void
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

    // Get a string value from the XML value.
    public static function XMLToString(SimpleXMLElement $xmlValue
      , bool $trim = true): ?string
    {
      $retValue = null;

      if ($xmlValue != null)
      {
        $retValue = (string)$xmlValue;
        if (true == $trim)
        {
          $retValue = trim($retValue);
        }
      }
      return $retValue;
    } // Value()
  } // LJCCommon
?>
