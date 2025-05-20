<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.-->
  // LJCTextReaderLib.php
  declare(strict_types=1);
  // Path: Codeline/TextReader
  include_once "../LJCPHPCommon/LJCCollectionLib.php";
  include_once "../LJCPHPCommon/LJCDbAccessLib.php";
  include_once "../LJCPHPCommon/LJCDebugLib.php";
  include_once "../LJCPHPCommon/LJCTextLib.php";
  include_once "LJCTextRangesLib.php";
  // LJCCollectionLib: LJCCollectionBase
  // LJCDbAccessLib: LJCDbColumn, LJCDbColumns
  // LJCCommonLib: LJCCommon
  // LJCDebugLib: LJCDebug
  // LJCTextRangesLib: LJCTextRange, LJCTextRanges

  /// <summary>The PHP Text Reader Class Library</summary>
  /// LibName: LJCTextReaderLib
  //  LJCTextReader

  // ***************
  // Contains methods to read text files and parse into fields.
  // SetFieldConfig(), Clear(), FillDataObject(), GetString(), Read()
  /// <include path='items/LJCTextReader/*' file='Doc/LJCTextReader.xml'/>
  class LJCTextReader
  {
    // Initializes a class instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCTextReader.xml'/>
    public function __construct(string $fileSpec)
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCTextReaderLib", "LJCTextReader"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      if (false == file_exists($fileSpec))
      {
        throw new Exception("Input file '$fileSpec' was not found.");
      }
      $this->FileSpec = $fileSpec;

      $this->ConfigXMLSpec = null;
      $this->DbColumns = null;
      $this->FieldCount = 0;
      $this->FieldDelimiter = ",";
      $this->FieldNames = [];
      $this->FieldValues = [];
      $this->InputSteam = null;
      $this->IsFirstRead = true;
      $this->ValueCount = 0;
      $this->ValueDelimiter = "\"";
    } // __construct()

    // Set the field configuration.
    /// <include path='items/SetFieldConfig/*' file='Doc/LJCTextReader.xml'/>
    public function SetFieldConfig(string $configXMLSpec = null)
    {
      $enabled = false;
      $this->Debug->BeginMethod("SetConfig", $enabled);

      $this->ConfigXMLSpec = $configXMLSpec;
      $this->TextRanges = new LJCTextRanges($this->FieldDelimiter
        , $this->ValueDelimiter);

      // Open for reading.
      $this->InputStream = fopen($this->FileSpec, "r");
      if (null == $configXMLSpec)
      {
        $this->ConfigFromFirstLine();
      }
      else
      {
        $this->ConfigFromXMLFile($configXMLSpec);
      }

      $this->FieldCount = 0;
      if (isset($this->FieldNames))
      {
        $this->FieldCount = count($this->FieldNames);
      }

      $this->Debug->EndMethod($enabled);
    }  // SetConfig()

    // ---------------
    // Public Methods - LJCTextReader

    /// <summary>Clears the FieldValues property.</summary>
    public function Clear() : void
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clear", $enabled);

      $valuesLength = count($this->FieldValues);
      if ($valuesLength > 0)
      {
        for ($index = 0; $index < $this->FieldCount; $index++)
        {
          $name = $this->FieldNames[$index];
          $this->FieldValues[$name] = null;
        }
      }

      $this->Debug->EndMethod($enabled);
    }  // Clear()

    /// <summary>Populates a DataObject from the field values.</summary>
    /// <param name="$dataObject">The DataObject.</param>
    public function FillDataObject($dataObject)
    {
      $enabled = false;
      $this->Debug->BeginMethod("FillDataObject", $enabled);

      $dbColumns = $this->DbColumns;
      if (null != $dbColumns && count($dbColumns) > 0)
      {
        foreach ($dbColumns as $dbColumn)
        {
          $propertyName = $dbColumn->PropertyName;
          if (property_exists($dataObject, $propertyName))
          {
            // Using variable name for object property.
            $dataObject->$propertyName = $this->GetString($propertyName);
          }
        }
      }

      $this->Debug->EndMethod($enabled);
    } // FillDataObject()

    // Gets the field value.
    /// <include path='items/GetString/*' file='Doc/LJCTextReader.xml'/>
    public function GetString(string $fieldName) : string
    {
      $enabled = false;
      $this->Debug->BeginMethod("GetString", $enabled);
      $retValue = null;

      if (isset($this->FieldValues[$fieldName]))
      {
        $retValue = $this->FieldValues[$fieldName];
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // GetString()

    /// <summary>Reads the next input line.</summary>
    /// <returns>True if the line was read, otherwise false.</returns>
    public function Read() : bool
    {
      $enabled = false;
      $this->Debug->BeginMethod("Read", $enabled);
      $retValue = false;

      $this->Clear();
      if (false == feof($this->InputStream))
      {
        $line = (string)fgets($this->InputStream);
        $values = $this->TextRanges->SplitRanges($line);

        // Skip Config line if configured with XML file.
        if ($this->IsFirstRead
          && null != $this->ConfigXMLSpec
          && $this->IsConfig($values))
        {
          $line = (string)fgets($this->InputStream);
          $values = $this->TextRanges->Split($line);
        }
        $this->IsFirstRead = false;

        $valueLength = count($values);
        if ($valueLength > 0)
        {
          $this->ValueCount = 0;
          for ($index = 0; $index < $this->FieldCount; $index++)
          {
            if ($valueLength > $index)
            {
              $name = $this->FieldNames[$index];
              $value = $this->TrimNewline($values[$index]);
              if (null != $value)
              {
                $this->FieldValues[$name] = $value;
                $this->ValueCount++;
              }
            }
          }
          $retValue = true;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // Read()

    // ---------------
    // Private Methods - LJCTextReader

    // Configures the fields from the first line of the input file.
    private function ConfigFromFirstLine()
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ConfigFromFirstLine", $enabled);

      // First line must have the configuration.
      if (feof($this->InputStream))
      {
        throw new Exception("First line configuration was not found.");
      }
      $line = (string)fgets($this->InputStream);
      $this->IsFirstRead = false;

      $names = explode($this->FieldDelimiter, $line);
      $this->SetFieldNames($names);

      $this->Debug->EndMethod($enabled);
    } // ConfigFromFirstLine()

    // Configures the fields from an XML file.
    private function ConfigFromXMLFile(string $configXMLSpec)
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ConfigFromXMLFile", $enabled);

      // Must have a configuration XML file.
      if (false == file_exists($configXMLSpec))
      {
        throw new Exception("Config file '$configXMLSpec' was not found.");
      }
      $this->DbColumns = LJCDbColumns::Deserialize($configXMLSpec);
      foreach($this->DbColumns as $dbColumn)
      {
        $name = $dbColumn->PropertyName;
        $this->FieldNames[] = $name;
      }

      $this->Debug->EndMethod($enabled);
    } // ConfigFromXMLFile()

    // Checks if the line is a Config line.
    private function IsConfig($names)
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("IsConfig", $enabled);
      $retValue = false;

      if ("Config" == $names[0])
      {
        $retValue = true;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // IsConfig()

    // Sets the field names from the Config line.
    private function SetFieldNames(array $names)
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SetFieldNames", $enabled);

      if ($this->IsConfig($names))
      {
        $this->DbColumns = new LJCDbColumns();
        foreach ($names as $name)
        {
          $name = $this->TrimNewline($name);
          if ("Config" != $name)
          {
            $this->DbColumns->Add($name);
            $this->FieldNames[] = $name;
          }
        }
      }
      else
      {
        throw new Exception("First line configuration was not found.");
      }

      $this->Debug->EndMethod($enabled);
    } // SetFieldNames()

    // Remove the trailing newline without removing anything else.
    // <include path='items/TrimNewline/*' file='Doc/LJCTextReader.xml'/>
    private function TrimNewline(string $text) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("TrimNewline", $enabled);
      $retValue = $text;

      if (null != $retValue)
      {
        $length = strlen($retValue) - 2;
        if ($length > 1)
        {
          $end = substr($retValue, $length);
          $success = true;
          if ("\r\n" != $end)
          {
            $length++;
            $end = substr($retValue, $length);
            $success = false;
            if ("\n" == $end)
            {
              $success = true;
            }
          }
          if ($success)
          {
            $retValue = substr($retValue, 0, $length);
          }
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // TrimNewline()

    // ---------------
    // Properties - LJCTextReader

    /// <summary>The field configuration.</summary>
    public ?LJCDbColumns $DbColumns;

    /// <summary>The field count.</summary>
    public int $FieldCount;

    /// <summary>The field delimiter.</summary>
    public string $FieldDelimiter;

    /// <summary>The field names.</summary>
    public array $FieldNames;

    /// <summary>The field values.</summary>
    public array $FieldValues;

    /// <summary>The file specification.</summary>
    public string $FileSpec;

    /// <summary>The input stream.</summary>
    private $InputStream;

    /// <summary>The value count.</summary>
    public int $ValueCount;

    /// <summary>The value delimiter.</summary>
    public string $ValueDelimiter;
  }  // LJCTextReader
?>