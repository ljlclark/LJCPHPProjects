<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCGenTextSectionLib.php
  declare(strict_types=1);
  $devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
  require_once "$devPath/LJCPHPCommon/LJCTextLib.php";
  require_once "$devPath/LJCPHPCommon/LJCDBAccessLib.php";

  // The utility to generate text from a template and custom data.
  // The GenText Section Library
  /// <include path='items/LJCGenTextSectionLib/*' file='Doc/LJCGenTextSectionLib.xml'/>
  /// LibName: LJCGenTextSectionLib

  // ***************
  // Represents a template Directive.
  /// <include path='items/LJCDirective/*' file='Doc/LJCDirective.xml'/>
  class LJCDirective
  {
    // Find any Directives in a line.
    /// <include path='items/Find/*' file='Doc/LJCDirective.xml'/>
    public static function Find(string $line) : ?LJCDirective
    {
      $success = true;
      $retValue = null;

      if (strpos($line, "#") < 0)
      {
        $success = false;
      }

      if ($success)
      {
        $tokens = preg_split("/[\s,]+/", $line, 0, PREG_SPLIT_NO_EMPTY);
        if (count($tokens) > 1)
        {
          if ("#" == $tokens[1][0])
          {
            if ("#SectionBegin" == $tokens[1]
              || "#SectionEnd" == $tokens[1]
              || "#Value" == $tokens[1]
              || "#IfBegin" == $tokens[1]
              || "#IfElse" == $tokens[1]
              || "#IfEnd" == $tokens[1])
            {
              $retValue = new LJCDirective($tokens[1], "");
              if (count($tokens) > 2)
              {
                $retValue->Name = $tokens[2];
              }
              if (count($tokens) > 3)
              {
                $retValue->Value = $tokens[3];
              }
            }
          }
        }
      }
      return $retValue;
    }

    // Initializes an object instance.
    /// <include path='items/construct/*' file='Doc/LJCDirective.xml'/>
    public function __construct(string $type, string $name)
    {
      $this->Type = $type;
      $this->Name = trim($name);
    }

    /// <summary>The Directive Name.</summary>
    public string $Name;

    /// <summary>The Directive Type.</summary>
    public string $Type;

    /// <summary>The compare value.</summary>
    public string $Value;
  }

  // ***************
  // Represents a template Section.
  /// <include path='items/LJCSection/*' file='Doc/LJCSection.xml'/>
  class LJCSection
  {
    /// <summary>Initializes an object instance.</summary>
    /// <param name="$name">The Section name.</param>.
    public function __construct(string $name)
    {
      $this->Name = trim($name);
      $this->Items = [];
    }

    /// <summary>Creates a copy of the current object.</summary>
    public function Clone() : self
    {
      $retValue = new self($this->Name);
      $retValue->Begin = $this->Begin;
      $retValue->CurrentItem = $this->CurrentItem;
      $retValue->Items = $this->Items;
      $retValue->Name = $this->Name;
      return $retValue;
    }

    /// <summary>The Section begin stream offset.</summary>
    public $Begin;

    /// <summary>The Current Item object.</summary>
    public LJCItem $CurrentItem;

    /// <summary>The Section Items.</summary>
    public array $Items;

    /// <summary>The Section name.</summary>
    public string $Name;
  }

  // ***************
  // Represents a collection of Section objects.
  /// <include path='items/LJCSections/*' file='Doc/LJCSections.xml'/>
  class LJCSections implements IteratorAggregate, \Countable
  {
    // ------------------------
    // Static Functions

    // Creates the data from a table definition.
    /// <include path='items/CreateColumnData/*' file='Doc/LJCSections.xml'/>
    public static function CreateColumnData(LJCDbColumns $dbColumns
      , string $tableName, string $className = null) : LJCSections
    {
      if ($className == null)
      {
        $className = $tableName;
      }

      $sections = new LJCSections();
      $section = new LJCSection("Class");
      $sections->Add($section, $section->Name);
      $Item = new LJCItem($section->Name);
      $section->Items[] = $Item;
      self::AddReplacement($Item, "_ClassName_", $className);
      self::AddReplacement($Item, "_TableName_", $tableName);

      $section = new LJCSection("Properties");
      $sections->Add($section, $section->Name);
      foreach ($dbColumns as $dbColumn)
      {
        $propertyName = $dbColumn->PropertyName;
        $Item = new LJCItem($propertyName);
        $section->Items[] = $Item;

        $dataTypeName = LJCDbColumn::GetDataType($dbColumn->MySQLTypeName);
        self::AddReplacement($Item, "_DataType_", $dataTypeName);
        if ("string" == $dataTypeName)
        {
          self::AddReplacement($Item, "_MaxLength_", (string)$dbColumn->MaxLength);
        }
        self::AddReplacement($Item, "_PropertyName_", $propertyName);
      }
      return $sections;
    }

    // Adds a Replacement to an Item.
    /// <include path='items/AddReplacement/*' file='Doc/LJCSections.xml'/>
    public static function AddReplacement(LJCItem &$item, string $name
      , string $value) : void
    {
      $replacement = new LJCReplacement($name, $value);
      $item->Replacements[] = $replacement;
    }

    // Deserializes the data from a Sections XML file.
    /// <include path='items/Deserialize/*' file='Doc/LJCSections.xml'/>
    public static function Deserialize(string $xmlFileSpec) : LJCSections
    {
      $retValue = null;

      $xmlElement = simplexml_load_file($xmlFileSpec);
      $retValue = self::CreateSections($xmlElement);
      return $retValue;
    }

    // Deserializes the data from a Sections XML string.
    /// <include path='items/DeserializeString/*' file='Doc/LJCSections.xml'/>
    public static function DeserializeString(string $xmlString) : LJCSections
    {
      $retValue = null;

      $xmlElement = simplexml_load_string($xmlString);
      $retValue = self::CreateSections($xmlElement);
      return $retValue;
    }

    // Creates the Section data from an XMLDoc node.
    /// <include path='items/CreateSections/*' file='Doc/LJCSections.xml'/>
    public static function CreateSections(SimpleXMLElement $xmlElement)
      : LJCSections
    {
      $sections = new LJCSections();
      if ($xmlElement)
      {
        $xmlSections = $xmlElement->Sections->children();
        if ($xmlSections != null)
        {
          foreach ($xmlSections as $xmlSection)
          {
            $sectionName = trim((string)$xmlSection->Name);
            $section = new LJCSection($sectionName);
            $section->Begin = (int)$xmlSection->Begin;

            $xmlItems = $xmlSection->Items->children();
            foreach ($xmlItems as $xmlItem)
            {
              $name = (string)$xmlItem->Name;
              $item = new LJCItem($name);

              $xmlReplacements = $xmlItem->Replacements->children();
              if ($xmlReplacements != null)
              {
                foreach ($xmlReplacements as $xmlReplacement)
                {
                  $name = (string)$xmlReplacement->Name;
                  $value = (string)$xmlReplacement->Value;
                  $replacement = new LJCReplacement($name, $value);
                  $item->Replacements[] = $replacement;
                }
              }
              $section->Items[] = $item;
            }
            $sections->Add($section, $section->Name);
          }
        }
      }
      return $sections;	
    }

    // Serializes the data to an XML file.
    /// <include path='items/Serialize/*' file='Doc/LJCSections.xml'/>
    public static function Serialize(string $xmlFile, LJCSections $sections
      , string $rootName) : void
    {
      $stream = fopen($xmlFile, "w");
      $writer = new LJCWriter($stream);
      $writer->FWriteLine("<?xml version=\"1.0\"?>");
      $writer->FWriteLine("<".$rootName.">");

      $writer->FWriteLine("<Sections>", 1);
      foreach ($sections as $section)
      {
        $writer->FWriteLine("<Section>", 2);
        $writer->FWriteLine("<Begin>".$section->Begin."</Begin>", 3);
        $writer->FWriteLine("<Name>".$section->Name."</Name>", 3);
  
        $writer->FWriteLine("<Items>", 3);	
        foreach ($section->Items as $item)
        {
          $writer->FWriteLine("<Item>", 4);
          $writer->FWriteLine("<Name>".$item->Name."</Name>", 5);

          $writer->FWriteLine("<Replacements>", 5);
          $replacements = $item->Replacements;
          foreach ($replacements as $replacement)
          {
            $writer->FWriteLine("<Replacement>", 6);
            $writer->FWriteLine("<Name>".$replacement->Name."</Name>", 7);
            $writer->FWriteLine("<Value>".$replacement->Value."</Value>", 7);
            $writer->FWriteLine("</Replacement>", 6);
          }
          $writer->FWriteLine("</Replacements>", 5);
          $writer->FWriteLine("</Item>", 4);
        }
        $writer->FWriteLine("</Items>", 3);
        $writer->FWriteLine("</Section>", 2);
      }

      $writer->FWriteLine("</Sections>", 1);
      $writer->FWriteLine("</".$rootName.">");
      fclose($stream);
    }

    // ----------------------
    // *** Public Methods ***

    // Adds an object and key value.
    /// <include path='items/Add/*' file='Doc/LJCSections.xml'/>
    public function Add(LJCSection $item, $key = null) : void
    {
      if (null === $key)
      {
        $this->Items[] = $item;
      }
      else
      {
        if ($this->HasKey($key))
        {
          throw new Exception("Key: {$key} already in use.");
        }
        $this->Items[$key] = $item;
      }
    }

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->Add($item);
      }
      unset($item);
      return $retValue;
    }

    // Get the item by Key value.
    /// <include path='items/Get/*' file='Doc/LJCSections.xml'/>
    public function Get($key, bool $showError = true) : ?LJCSection
    {
      $retValue = null;

      $success = true;
      if (false == $this->HasKey($key))
      {
        $success = false;
        if ($showError)
        {
          throw new Exception("Key: '$key' was not found.");
        }
      }
      if ($success)
      {
        $retValue = $this->Items[$key];
      }
      return $retValue;
    }

    /// <summary>Gets an indexed array of keys.</summary>
    /// <returns>The indexed keys array.</returns>
    public function GetKeys() : array
    {
      return array_keys($this->Items);
    }

    /// <summary>Gets an indexed array of objects.</summary>
    /// <returns>The indexed values array.</returns>
    public function GetValues() : array
    {
      return array_values($this->Items);
    }

    // Indicates if a key already exists.
    /// <include path='items/HasKey/*' file='Doc/LJCSections.xml'/>
    public function HasKey($key) : bool
    {
      return isset($this->Items[$key]);
    }

    /// <summary>Remove the item by Key value.</summary>
    /// <param name="$key">The element key.</param>
    public function Remove($key) : void
    {
      if (false == $this->HasKey($key))
      {
        throw new Exception("Key: {$key} was not found.");
      }
      unset($this->Items[$key]);
    }

    /// <summary>Allows foreach()</summary>
    public function getIterator() : Traversable
    {
      return new ArrayIterator($this->Items);
    }

    /// <summary>Allows Count(object).</summary>
    public function count() : int
    {
      return count($this->Items);
    }

    // ------------------
    // *** Class Data ***

    // The elements array.
    private $Items = [];
  }

  // ***************
  /// <summary>Represents a Section Item.</summary>
  class LJCItem
  {
    /// <summary>Initializes an object instance with the provided values.</summary>
    /// <param name="$name">The Item name.</param>
    public function __construct(string $name)
    {
      $this->Name = trim($name);
      $this->Replacements = [];
    }

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $retValue = new self($this->Name);
      $retValue->Replacements = $this->Replacements;
      $retValue->RootName = $this->RootName;
      return $retValue;
    }

    // ------------------
    // *** Properties ***

    /// <summary>The Item name.</summary>
    public string $Name;

    /// <summary>The Item replacements.</summary>
    public $Replacements;

    /// <summary>The XML Root Name value.</summary>
    public string $RootName = "Items";
  }

  // ***************
  /// <summary>Represents Item Replacements.</summary>
  class LJCReplacement
  {
    // Initializes an object instance with the provided values.
    /// <param name="$name">The Replacement name.</param>
    /// <param name="$value">The Replacement value.</param>
    public function __construct(string $name, string $value)
    {
      $this->Name = trim($name);
      $this->Value = $value;
    }

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $retValue = new self($this->Name, $this->Value);
      $retValue->RootName = $this->RootName;
      return $retValue;
    }

    // ------------------
    // *** Properties ***

    /// <summary>The Replacement name.</summary>
    public string $Name;

    /// <summary>The Replacement value.</summary>
    public string $Value;

    /// <summary>The XML Root Name value.</summary>
    public string $RootName = "Replacements";
  }
?>
