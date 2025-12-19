<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommonLib.php
  declare(strict_types=1);

  // The Common PHP Class Library
  /// <include path='items/LJCCommonLib/*' file='Doc/LJCCommon.xml'/>
  // The LibName: XML comment triggers the file (library) HTML page generation.
  // It generates a page with the same name as the library.
  // LJCCommonLib.html
  /// LibName: LJCCommonLib
  // Classes: LJC

  // ***************
  /// <group name="String">String Functions</group>
  //    EndsWithNewLine(), GetDelimitedString(), GetTokens(), NewLineTrim(),
  //    Scrub(), Split(), StrPos(), StrRPos() 
  /// <group name="File">File Name Functions</group>
  //    GetFileName(), GetFileSpecPath()
  /// <group name="Check">Check Value Functions</group>
  //    HasElements(), HasItems(), HasValue(), HasXML() 
  /// <group name="Arr">Arrays Functions</group>
  //    RemoveString()
  /// <group name="Convert">Conversion Functions</group>
  //    ItemsToArray(), ToBool(), ToBoolInt(), XMLToString()
  /// <group name="Output">Output Functions</group>
  //    GetLogObjectText(), Location(), OutputLog(), OutputLogCompare(),
  //    OutputLogObject()
  // Contains common PHP functions.
  /// <include path='items/LJC/*' file='Doc/LJCCommon.xml'/>
  // The class triggers the class HTML page generation.
  // It generates a page with the same name as the class.
  // LJC/LJC.html
  class LJC
  {
    // ---------------
    // String Functions

    // Creates JSON from the provided value.
    /// <include path='items/CreateJSON/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>String</ParentGroup>
    // The method triggers the method HTML page generation.
    // It generates a page with the name class plus method.
    // LJC/LJCCreateJSON.html
    public static function CreateJSON($value): string
    {
      $retJSON = "";

      $retJSON = json_encode($value);
      return $retJSON;
    }

    // Indicates if the builder text ends with a newline.
    /// <include path='items/EndsWithNewLine/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>String</ParentGroup>
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
    /// <ParentGroup>String</ParentGroup>
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

    // Get string tokens.
    /// <include path='items/GetTokens/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>String</ParentGroup>
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

    // Removes the trailing newline from the text.
    /// <include path='items/NewLineTrim/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>String</ParentGroup>
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

    // Parses JSON into an object.
    /// <include path='items/ParseJSON/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>String</ParentGroup>
    public static function ParseJSON($json)
    {
      $retObject = null;

      $retObject = json_decode($json);
      return $retObject;
    }

    // Returns a scrubbed external value.
    /// <include path='items/Scrub/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>String</ParentGroup>
    public static function Scrub(string $text): string
    {
      $retValue = trim($text);
      $retValue = stripslashes($retValue);
      $retValue = htmlspecialchars($retValue);
      return $retValue;
    } // Scrub()

    // Splits a string on whitespace.
    /// <include path='items/Split/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>String</ParentGroup>
    public static function Split(string $text): array
    {
      $retValues = preg_split("/[\s,]+/", $text, 0, PREG_SPLIT_NO_EMPTY);
      return $retValues;
    }

    // Gets the first index for the search value.
    /// <include path='items/StrPos/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>String</ParentGroup>
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
    /// <ParentGroup>String</ParentGroup>
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

    // ---------------
    // File Name Functions

    // Gets the FileName from the file spec.
    /// <include path='items/GetFileName/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>File</ParentGroup>
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
    /// <ParentGroup>File</ParentGroup>
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

    // ---------------
    // Check Value Functions

    // Checks for array elements.
    /// <include path='items/HasElements/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Check</ParentGroup>
    public static function HasElements(?array $array): bool
    {
      $retValue = false;

      if (isset($array)
        && is_array($array)
        && count($array) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    }

    // Checks for collection items.
    /// <include path='items/HasItems/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Check</ParentGroup>
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

    // Checks for text.
    /// <include path='items/HasValue/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Check</ParentGroup>
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

    // Checks an XML element for a value.
    /// <include path='items/HasXML/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Check</ParentGroup>
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

    // ---------------
    // Array Functions

    // Remove an element from a string array by value.
    /// <include path='items/RemoveString/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Arr</ParentGroup>
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

    // ---------------
    // Convert Functions

    // Copy collection items to an indexed array.
    /// <include path='items/ToArray/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Convert</ParentGroup>
    public static function ToArray($items): array
    {
      $retArray = [];

      foreach ($items as $item)
      {
        $retArray[] = $item;
      }
      return $retArray;
    }

    // Returns a value as bool.
    /// <include path='items/ToBool/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Convert</ParentGroup>
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

    // Returns a text value as an int boolean.
    /// <include path='items/ToBoolInt/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Convert</ParentGroup>
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

    // Gets a string value from the XML element.
    /// <include path='items/XMLToString/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Convert</ParentGroup>
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
    } // XMLToString()

    // ---------------
    // Output Functions

    // Gets the object debug text.
    /// <include path='items/GetLogObjectText/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Output</ParentGroup>
    public static function GetLogObjectText(string $location, $object
      , bool $isObject = true, bool $bracket = false): string
    {
      $retDebugText = "";

      if ($location != null)
      {
        $retDebugText = "\r\n{$location}";
      }
      if ($object != null)
      {
        if ($isObject)
        {
          $retDebugText .= "\r\n";
        }
        else
        {
          $retDebugText .= " ";
        }
        $debugObject = print_r($object, true);
        $bracketChar = null;
        if ($bracket)
        {
          $bracketChar = "|";
        }
        $retDebugText .= "{$bracketChar}{$debugObject}{$bracketChar}";
      }
      return $retDebugText;
    }

    // Gets the location string.
    /// <include path='items/Location/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Output</ParentGroup>
    public static function Location(string $className, string $methodName
      , $valueName = null): string
    {
      $retLocation = "";

      if (LJC::HasValue($className))
      {
        $retLocation .= $className;
      }
      if (LJC::HasValue($retLocation)
        && LJC::HasValue($methodName))
      {
        $retLocation .= ".{$methodName}";
      }
      if (LJC::HasValue($retLocation)
        && LJC::HasValue($valueName))
      {
        $retLocation .= " {$valueName}";
      }
      if (LJC::HasValue($retLocation))
      {
        $retLocation .= ":";
      }
      return $retLocation;
    }

    // Outputs the value debug text.
    /// <include path='items/OutputLog/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Output</ParentGroup>
    public static function OutputLog(int $lineNumber, string $valueName = ""
      , $value = null, bool $output = true): string
    {
      $retText = "";

      if ($lineNumber > 0)
      {
        $retText .= "{$lineNumber}";
      }
      if (self::HasValue($valueName))
      {
        $retText .= " {$valueName}";
        if ($value != null)
        {
          $retText .= " = ";
        }
      }
      if ($value != null)
      {
        $retText .= "{$value}";
      }

      if (LJC::HasValue($retText))
      {
        $retText = "\r\n{$retText}";
      }
      if ($output)
      {
        echo($retText);
      }
      return $retText;
    }

    // Outputs the test compare text.
    /// <include path='items/OutputLogCompare/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Output</ParentGroup>
    public static function OutputLogCompare(string $methodName, string $result
      , string $compare, bool $bracket = false, bool $output = true): string
    {
      $retText = "";

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
        $bracketChar = null;
        if ($bracket)
        {
          $bracketChar = "|";
        }
        // *** Begin ***
        //echo("\r\n{$methodName}\r\n");
        //echo("{$bracketChar}{$result}{$bracketChar}\r\n");
        //echo(" !=\r\n");
        //echo("{$bracketChar}{$compare}{$bracketChar}\r\n");
        $retText = "\r\n{$methodName}\r\n";
        $retText .= "{$bracketChar}{$result}{$bracketChar}\r\n";
        $retText .= " !=\r\n";
        $retText .= "{$bracketChar}{$compare}{$bracketChar}\r\n";
        if ($output)
        {
          echo($retText);
        }
      }
      return $retText;
    }

    // Outputs the object debug text.
    /// <include path='items/OutputLogObject/*' file='Doc/LJCCommon.xml'/>
    /// <ParentGroup>Output</ParentGroup>
    public static function OutputLogObject(int $lineNumber, string $className
      , string $methodName, string $valueName, $value = null, $isObject = true
      , bool $bracket = false, bool $output = true): string
    {
      $retText = "";

      $location = LJC::Location($className, $methodName, $valueName);
      $retText = LJC::GetLogObjectText($location, $value, isObject: $isObject
        , bracket: $bracket);
      if ($output)
      {
        $retText = LJC::OutputLog($lineNumber, $valueName, $retText);
      }
      return $retText;
    }
  } // LJCCommon

