<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCGenTextSectionLib.php
  declare(strict_types=1);
  // Path: LJCPHPProjectsDev/GenDoc/LJCDocDataLib
  include_once "../../LJCPHPCommon/LJCDebugLib.php";
  require_once "../../LJCPHPCommon/LJCTextLib.php";
  require_once "../../LJCPHPCommon/LJCDBAccessLib.php";
  // LJCTextLib: LJCWriter
  // LJCDbAccessLib: LJCDbColumn, LJCDbColumns
  // LJCDebugLib: LJCDebug

  // The data classes for representing GenData XML.
  // The GenText Section Library
  /// <include path='items/LJCGenTextSectionLib/*' file='Doc/LJCGenTextSectionLib.xml'/>
  /// LibName: LJCGenTextSectionLib
  // LJCDirective
  // LJCSection, LJCSections
  // LJCItem
  // LJCReplacement, LJCReplacements

  // ***************
  // Represents a template Directive.
  // Static: GetDirective(), IsDirective(), IfElse(), IfEnd()
  //   , SectionBegin(), SectionEnd()
  // Public: IsIfBegin(), IsSectionBegin(), IsSectionEnd()
  /// <include path='items/LJCDirective/*' file='Doc/LJCDirective.xml'/>
  class LJCDirective
  {
    /// <summary>
    /// Checks line for directive and returns the directive object.
    /// </summary>
    public static function GetDirective(string $line
      , string $commentChars) : ?LJCDirective
    {
      $enabled = false;
      if ($enabled)
      {
        $writer = new LJCWriter("LJCDirective.txt", "a");
        $writer->FWriteLine("GetDirective()");
      }

      // ToDo: Why not doing append?
      //$debug = new LJCDebug("", "LJCDirective", "a");
      //$debug->WriteStartText("GetDirective");
      $values = [];
      $retValue = null;

      // Templates directive is in a comment.
      if (str_starts_with(trim($line), $commentChars))
      {
        // Template directive starts with "#".
        $index = LJCCommon::StrPos($line, "#");
        if ($index > -1)
        {
          $retValue = new LJCDirective("", $commentChars);
          $values = preg_split("/[\s,]+/", $line, 0, PREG_SPLIT_NO_EMPTY);
          if (count($values) > 0)
          {
            // The directive identifier.
            // *** Change *** 5/11/25
            $retValue->Type = $values[1];
          }
          if (count($values) > 2)
          {
            $retValue->Name = $values[2];
          }
          if (count($values) > 3)
          {
            $retValue->Value = $values[3];
          }
        }
      }
      if ($enabled
        && isset($writer))
      {
        $writer->FClose();
        //$debug->Close();
      }
      return $retValue;
    }

    /// <summary>Checks if the line has a directive.</summary>
    public static function IsDirective(string $line
      , string $commentChars) : bool
    {
      $retValue = false;

      $directive = self::GetDirective($line, $commentChars);
      if ($directive != null)
      {
        $lowerName = strtolower($directive->ID);
        if ("#commentchars" == $lowerName 
          || "#placeholderbegin" == $lowerName
          || "#placeholderend" == $lowerName
          || "#sectionbegin" == $lowerName
          || "#sectionend" == $lowerName 
          || "#value" == $lowerName 
          || "#ifbegin" == $lowerName 
          || "#ifend" == $lowerName)
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // Checks if directive ID = IfElses.
    public static function IfElse(string $line, string $commentChars)
      : bool
    {
      $retValue = false;

      $directive = self::GetDirective($line, $commentChars);
      if ($directive != null)
      {
        if ("#ifelse" == strtolower($directive->ID))
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // Checks if directive ID = IfEnd.
    public static function IfEnd(string $line, string $commentChars)
      : bool
    {
      $retValue = false;

      $directive = self::GetDirective($line, $commentChars);
      if ($directive != null)
      {
        if ("#ifend" == strtolower($directive->ID))
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // Checks if directive ID = SectionBegin.
    public static function SectionBegin(string $line, string $commentChars)
      : bool
    {
      $retValue = false;

      $directive = self::GetDirective($line, $commentChars);
      if ($directive != null)
      {
        if ("#sectionbegin" == strtolower($directive->ID))
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // Checks if directive ID = SectionEnd.
    public static function SectionEnd(string $line, string $commentChars)
      : bool
    {
      $retValue = false;

      $directive = self::GetDirective($line, $commentChars);
      if ($directive != null)
      {
        if ("#sectionend" == strtolower($directive->ID))
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // Initializes an object instance.
    /// <include path='items/construct/*' file='Doc/LJCDirective.xml'/>
    public function __construct(string $type, string $name)
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCDirective"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;

      $this->Type = $type;
      $this->Name = trim($name);
    }

    // Checks if directive ID = IfBegin.
    public function IsIfBegin() : bool
    {
      $this->Debug->WriteStartText("IsIfBegin");
      $retValue = false;

      if ("#ifbegin" == strtolower($this->ID))
      {
        $retValue = true;
      }

      $this->Debug->AddIndent(-1);
      return $retValue;
    }

    // Checks if directive ID = SectionBegin.
    public function IsSectionBegin() : bool
    {
      $this->Debug->WriteStartText("IsSectionBegin");
      $retValue = false;

      if ("#sectionbegin" == strtolower($this->ID))
      {
        $retValue = true;
      }

      $this->Debug->AddIndent(-1);
      return $retValue;
    }

    // Checks if directive ID = SectionEnd.
    public function IsSectionEnd() : bool
    {
      $this->Debug->WriteStartText("IsSectionEnd");
      $retValue = false;

      if ("#sectionend" == strtolower($this->ID))
      {
        $retValue = true;
      }

      $this->Debug->AddIndent(-1);
      return $retValue;
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
  // Clone()
  /// <include path='items/LJCSection/*' file='Doc/LJCSection.xml'/>
  class LJCSection
  {
    /// <summary>Initializes an object instance.</summary>
    /// <param name="$name">The Section name.</param>.
    public function __construct(string $name)
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCSection"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;

      $this->Name = trim($name);
      $this->RepeatItems = [];
    }

    /// <summary>Creates a copy of the current object.</summary>
    public function Clone() : self
    {
      $this->Debug->WriteStartText("Clone");

      $retValue = new self($this->Name);
      $retValue->Begin = $this->Begin;
      $retValue->CurrentItem = $this->CurrentItem;
      $retValue->RepeatItems = $this->RepeatItems;
      $retValue->Name = $this->Name;

      $this->Debug->AddIndent(-1);
      return $retValue;
    }

    /// <summary>The Section begin stream offset.</summary>
    public $Begin;

    /// <summary>The Current Item object.</summary>
    public LJCItem $CurrentItem;

    /// <summary>The Section Items.</summary>
    public array $RepeatItems;

    /// <summary>The Section name.</summary>
    public string $Name;
  }

  // ***************
  // Represents a collection of Section objects.
  // Static: CreateColumnData(), CreateSections(), AddReplacement()
  // Collection Static: Deserialize(), DeserializeString(), Serialize()
  // Collection: Clone(), GetKeys(), GetValues(), HasKey()
  // Data: Add(), Retrieve(), Remove()
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
      $item->Replacements->Add($replacement);
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
                  $item->Replacements->Add($replacement, $name);
                }
              }
              $section->RepeatItems[] = $item;
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
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCSections"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;
    }

    // ----------------------
    // *** Collection Methods ***

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $this->Debug->WriteStartText("Clone");
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->Add($item);
      }
      unset($item);

      $this->Debug->AddIndent(-1);
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

    // ----------------------
    // *** Data Methods ***

    // Adds an object and key value.
    /// <include path='items/Add/*' file='Doc/LJCSections.xml'/>
    public function Add(LJCSection $item, $key = null) : void
    {
      $this->Debug->WriteStartText("Add");

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

      $this->Debug->AddIndent(-1);
    }

    // Get the item by Key value.
    /// <include path='items/Get/*' file='Doc/LJCSections.xml'/>
    public function Retrieve($key, bool $showError = true) : ?LJCSection
    {
      $this->Debug->WriteStartText("Retrieve");
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

      $this->Debug->AddIndent(-1);
      return $retValue;
    }

    /// <summary>Remove the item by Key value.</summary>
    /// <param name="$key">The element key.</param>
    public function Remove($key) : void
    {
      $this->Debug->WriteStartText("Remove");

      if (false == $this->HasKey($key))
      {
        throw new Exception("Key: {$key} was not found.");
      }
      unset($this->Items[$key]);

      $this->Debug->AddIndent(-1);
    }

    // ----------------------
    // *** Interface Methods ***

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
  // Represents a Section Item.
  // Clone()
  /// <summary>Represents a Section Item.</summary>
  class LJCItem
  {
    /// <summary>Initializes an object instance with the provided values.</summary>
    /// <param name="$name">The Item name.</param>
    public function __construct(string $name)
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCItem"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;

      $this->Name = trim($name);
      $this->Replacements = new LJCReplacements();
    }

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $this->Debug->WriteStartText("Clone");

      $retValue = new self($this->Name);
      $retValue->Replacements = $this->Replacements;
      $retValue->RootName = $this->RootName;

      $this->Debug->AddIndent(-1);
      return $retValue;
    }

    // ------------------
    // *** Properties ***

    /// <summary>The Item name.</summary>
    public string $Name;

    /// <summary>The Item replacements.</summary>
    public LJCReplacements $Replacements;

    /// <summary>The XML Root Name value.</summary>
    public string $RootName = "Items";
  }

  // ***************
  // Represents Item Replacements.
  // Clone()
  /// <summary>Represents Item Replacements.</summary>
  class LJCReplacement
  {
    // Initializes an object instance with the provided values.
    /// <param name="$name">The Replacement name.</param>
    /// <param name="$value">The Replacement value.</param>
    public function __construct(string $name, string $value)
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCReplacement"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;

      $this->Name = trim($name);
      $this->Value = $value;
    }

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $this->Debug->WriteStartText("Clone");

      $retValue = new self($this->Name, $this->Value);
      $retValue->RootName = $this->RootName;

      $this->Debug->AddIndent(-1);
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

  // ***************
  // Represents a collection of Replacement objects.
  // Collection: Clone(), HasKey()
  // Data: Add(), Delete(), Retrieve()
  class LJCReplacements implements IteratorAggregate, \Countable
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCReplacements"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;
    }

    // ----------------------
    // *** Collection Methods ***

    // Creates an object clone.
    public function Clone() : self
    {
      $this->Debug->WriteStartText("Clone");
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->Add($item);
      }
      unset($item);

      $this->Debug->AddIndent(-1);
      return $retValue;
    }

    // Indicates if a key already exists.
    public function HasKey($key) : bool
    {
      return isset($this->Items[$key]);
    }

    // ----------------------
    // *** Data Methods ***

    // Adds an object and key value.
    public function Add(LJCReplacement $item, $key = null) : void
    {
      $this->Debug->WriteStartText("Add");

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

      $this->Debug->AddIndent(-1);
    }

    // Delete the item by Key value.
    public function Delete($key) : void
    {
      $this->Debug->WriteStartText("Delete");

      if (false == $this->HasKey($key))
      {
        throw new Exception("Key: {$key} was not found.");
      }
      unset($this->Items[$key]);

      $this->Debug->AddIndent(-1);
    }

    // Get the item by Key value.
    public function Retrieve($key, bool $showError = true) : ?LJCReplacement
    {
      $this->Debug->WriteStartText("Retrieve");
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

      $this->Debug->AddIndent(-1);
      return $retValue;
    }

    // ----------------------
    // *** Interface Methods ***

    // Allows foreach()
    public function getIterator() : Traversable
    {
      return new ArrayIterator($this->Items);
    }

    // Allows Count(object)
    public function count() : int
    {
      return count($this->Items);
    }

    // ------------------
    // *** Class Data ***

    // The elements array.
    private $Items = [];
  }
?>
