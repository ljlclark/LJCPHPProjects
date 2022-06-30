<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCTextRangesLib.php
  declare(strict_types=1);
  $devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
  require_once "$devPath/LJCPHPCommon/LJCCollectionLib.php";
  require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
  require_once "$devPath/LJCPHPCommon/LJCTextLib.php";

  /// <summary>The PHP Text Ranges Class Library</summary>
  /// LibName: LJCTextRangesLib

  // ***************
  /// <summary>Represents the text range.</summary>
  class LJCTextRange
  {
    // Initializes a class instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCTextRange.xml'/>
    public function __construct(int $beginIndex, int $endIndex)
    {
      $this->BeginIndex = $beginIndex;
      $this->EndIndex = $endIndex;
    }

    /// <summary>Creates an object clone.</summary>
    /// <returns>The cloned item.</returns>
    public function Clone() : self
    {
      $retValue = new self($this->BeginIndex, $this->EndIndex);
      return $retValue;
    }

    // ---------------
    // Properties - LJCTextRange

    /// <summary>The region beginning index.</summary>
    public int $BeginIndex;

    /// <summary>The region ending index.</summary>
    public int $EndIndex;
  }  // LJCTextRange

  // ***************
  /// <summary>Represents a collection of LJCTextRange objects.</summary>
  class LJCTextRanges extends LJCCollectionBase
  {
    // Initializes a class instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCTextRanges.xml'/>
    public function __construct(string $fieldDelimiter = ","
      , string $valueDelimiter = "\"")
    {
      $this->FieldDelimiter = $fieldDelimiter;
      $this->ValueDelimiter = $valueDelimiter;

      $this->DebugWriter = new LJCDebugWriter("LJCTextRanges");
    }

    // ---------------
    // Public Collection Methods - LJCTextRanges

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCTextRanges.xml'/>
    public function Add(int $beginIndex, int $endIndex, $key = null)
      : ?LJCTextRange
    {
      $retValue = null;

      if (null == $key)
      {
        $key = $beginIndex;
      }

      $item = new LJCTextRange($beginIndex, $endIndex);
      $retValue = $this->AddObject($item , $key);
      return $retValue;
    }

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCTextRanges.xml'/>
    public function AddObject(LJCTextRange $item, $key = null) : ?LJCTextRange
    {
      if (null == $key)
      {
        $key = strval($item->BeginIndex);
      }
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    }

    /// <summary>Creates an object clone.</summary>
    /// <returns>The cloned item.</returns>
    public function Clone() : self
    {
      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);
      return $retValue;
    }

    // Get the item by Key value.
    /// <include path='items/Get/*' file='Doc/LJCTextRanges.xml'/>
    public function Get($key, bool $throwError = true) : ?LJCTextRange
    {
      $retValue = $this->GetItem($key, $throwError);
      return $retValue;
    }

    // ---------------
    // Public Other Methods - LJCTextRanges

    // Determines if a delimiter is in a text value.
    /// <include path='items/IsInValue/*' file='Doc/LJCTextRanges.xml'/>
    public function IsInValue(int $index) : bool
    {
      $retValue = false;

      foreach ($this->Items as $value)
      {
        if ($value->BeginIndex <= $index && $value->EndIndex >= $index)
        {
          $retValue = true;
          break;
        }
      }
      return $retValue;
    }

    // Sets value ranges and returns true if a range was defined.
    /// <include path='items/SetRanges/*' file='Doc/LJCTextRanges.xml'/>
    public function SetRanges(string $text) : bool
    {
      $retValue = false;

      $this->Clear();

      // Search for beginning of first value.
      $currentIndex = 0;
      $beginIndex = LJCCommon::StrPos($text, $this->ValueDelimiter);
      while ($beginIndex > -1)
      {
        // Search for ending of value.
        $currentIndex = $beginIndex + 1;
        $endIndex = LJCCommon::StrPos($text, $this->ValueDelimiter
          , $currentIndex);
        if (-1 == $endIndex)
        {
          $beginIndex = -1;
          continue;
        }
        if (false == $this->VerifyValue($text, $endIndex))
        {
          $beginIndex = $endIndex;
          continue;
        }

        // Add value range.
        $retValue = true;
        $this->Add($beginIndex, $endIndex);

        // Search for beginning of next value.
        $currentIndex = $endIndex + 1;
        $endIndex = -1;
        $beginIndex = LJCCommon::StrPos($text, $this->ValueDelimiter
          , $currentIndex);
      }
      return $retValue;
    }  // SetRanges()

    // Splits a line of text on the delimiters not enclosed in a value.
    /// <include path='items/Split/*' file='Doc/LJCTextRanges.xml'/>
    public function Split(string $line) : array
    {
      $retValue = [];

      if (false == $this->SetRanges($line))
      {
        $retValue = explode($this->FieldDelimiter, $line);
      }
      else
      {
        $currentIndex;
        $beginIndex = -1;
        $lineLength = strlen($line);
        if (null != $line && $lineLength > 0)
        {
          $beginIndex = 0;
        }
        while ($beginIndex > -1 && $beginIndex < $lineLength)
        {
          // Skip value delimiter at beginning of field.
          $field = substr($line, $beginIndex);
          if (str_starts_with(trim($field), $this->ValueDelimiter))
          {
            $index = LJCCommon::StrPos($field, $this->ValueDelimiter);
            $beginIndex += $index + 1;
          }

          // Find the end of field.
          $currentIndex = $beginIndex + 1;
          $endIndex = LJCCommon::StrPos($line, $this->FieldDelimiter, $currentIndex);
          if (-1 == $endIndex)
          {
            // No more field delimiters so use length to get last field.
            $endIndex = $lineLength;
          }
          if ($endIndex > -1)
          {
            // Get delimiter that is not already in a value?
            while ($this->IsInValue($endIndex))
            {
              $currentIndex = $endIndex + 1;
              $endIndex = LJCCommon::StrPos($line, $this->FieldDelimiter
                , $currentIndex);
              if (-1 == $endIndex)
              {
                // No more field delimiters so use length to get last field.
                $endIndex = $lineLength;
              }
            }

            // Get the field value.
            if ($endIndex > -1)
            {
              $currentIndex = $endIndex + 1;
              $value = trim(substr($line, $beginIndex, $endIndex - $beginIndex));

              // Remove trailing value delimiter.
              if (str_ends_with($value, $this->ValueDelimiter))
              {
                $value = substr($value, 0, strlen($value) - 1);
              }

              $retValue[] = $value;
            }
          }

          // Set beginning of next field.
          $beginIndex = $currentIndex;
        }
      }
      return $retValue;
    }  // Split()

    // ---------------
    // Private Methods - LJCTextRanges

    // Removes only the leading blanks not other whitespace.
    // <include path='items/Split/*' file='Doc/LJCTextRanges.xml'/>
    private function RemoveLeadingBlanks(string $text) : string
    {
      $length = strlen($text);
      if($length > 1)
      {
        for ($index = 0; $index < $length; $index++)
        {
          if (" " != $text[$index])
          {
            $text = substr($text, $index);
            break;
          }
        }
      }
      return $text;
    }

    // A field value must be immediately followed by the field delimiter
    // or be at the end of the string to be a valid value.
    // Otherwise it contains an embeded value delimiter.
    private function VerifyValue(string $text, int $endIndex) : bool
    {
      $retValue = false;

      // Verify ending of value.
      $verifyStartIndex = $endIndex + 1;
      $verifyIndex = LJCCommon::StrPos($text, $this->FieldDelimiter
        , $verifyStartIndex);
      if (-1 == $verifyIndex)
      {
        $verifyIndex = strlen($text) - 1;
      }
      $verifyLen = $verifyIndex - $verifyStartIndex + 1;
      $verifyValue = substr($text, $verifyStartIndex, $verifyLen);
      $verifyValue = $this->RemoveLeadingBlanks($verifyValue);

      if ($verifyValue == $this->FieldDelimiter)
      {
        $retValue = true;
      }
      else
      {
        $beginIndex = $verifyIndex;
      }
      return $retValue;
    }  // VerifyValue()

    // Output the debug value.
    private function Debug(string $text, bool $addLine = true) : void
    {
      $this->DebugWriter->Debug($text, $addLine);
    }

    // ---------------
    // Properties

    /// <summary>The field delimiter.</summary>
    public string $FieldDelimiter;

    /// <summary>The text region delimiter.</summary>
    public string $ValueDelimiter;
  }  // LJCTextRanges
?>