<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCGenTextSectionLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
  require_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  require_once "$prefix/LJCPHPCommon/LJCTextFileLib.php";
  require_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  require_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  // LJCTextLib: LJCWriter
  // LJCDbAccessLib: LJCDbColumn, LJCDbColumns
  // LJCDebugLib: LJCDebug

  // The data classes for representing GenData XML.
  // The GenText Section Library
  /// <include path='items/LJCGenTextSectionLib/*' file='Doc/LJCGenTextSectionLib.xml'/>
  /// LibName: LJCGenTextSectionLib
  // LJCDirective
  // LJCSection, LJCSections
  // LJCItem, LJCItems
  // LJCReplacement, LJCReplacements

  // ***************
  // Represents a template Directive.
  // Static: GetDirective(), IsDirective(), IfElse(), IfEnd()
  //   , SectionBegin(), SectionEnd()
  // Public: IsIfBegin(), IsSectionBegin(), IsSectionEnd()
  /// <include path='items/LJCDirective/*' file='Doc/LJCDirective.xml'/>
  class LJCDirective
  {
    // Checks line for directive.
    /// <include path='items/GetDirective/*' file='Doc/LJCGenTextSectionLib.xml'/>
    public static function GetDirective(string $line
      , string $commentChars) : ?LJCDirective
    {
      $debug = new LJCDebug("LJCGenTextSectionLib", "LJCDirective"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("GetDirective", $enabled);

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
        $index = LJC::StrPos($line, "#");
        if ($index > -1)
        {
          $retValue = new LJCDirective("", $commentChars);
          $values = preg_split("/[\s,]+/", $line, 0, PREG_SPLIT_NO_EMPTY);
          if (count($values) > 0)
          {
            // The directive identifier.
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

      $debug->EndMethod($enabled);
      return $retValue;
    }

    // Checks if the line has a directive.
    /// <include path='items/IsDirective/*' file='Doc/LJCGenTextSectionLib.xml'/>
    public static function IsDirective(string $line
      , string $commentChars) : bool
    {
      $retValue = false;

      $directive = self::GetDirective($line, $commentChars);
      if ($directive != null)
      {
        $lowerName = strtolower($directive->Name);
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

    // Checks if directive ID = IfElse.
    /// <include path='items/IfElse/*' file='Doc/LJCGenTextSectionLib.xml'/>
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
    /// <include path='items/IfEnd/*' file='Doc/LJCGenTextSectionLib.xml'/>
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
    /// <include path='items/SectionBegin/*' file='Doc/LJCGenTextSectionLib.xml'/>
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
    /// <include path='items/SectionEnd/*' file='Doc/LJCGenTextSectionLib.xml'/>
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
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCDirective"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->Type = $type;
      $this->Name = trim($name);
    }

    // Checks if directive ID = IfBegin.
    /// <include path='items/IsIfBegin/*' file='Doc/LJCDirective.xml'/>
    public function IsIfBegin() : bool
    {
      $enabled = false;
      $this->Debug->BeginMethod("IsIfBegin", $enabled);
      $retValue = false;

      if ("#ifbegin" == strtolower($this->ID))
      {
        $retValue = true;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // Checks if directive ID = SectionBegin.
    /// <include path='items/IsSectionBegin/*' file='Doc/LJCDirective.xml'/>
    public function IsSectionBegin() : bool
    {
      $enabled = false;
      $this->Debug->BeginMethod("IsSectionBegin", $enabled);
      $retValue = false;

      if ("#sectionbegin" == strtolower($this->Name))
      {
        $retValue = true;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // Checks if directive ID = SectionEnd.
    /// <include path='items/IsSectionEnd/*' file='Doc/LJCDirective.xml'/>
    public function IsSectionEnd() : bool
    {
      $enabled = false;
      $this->Debug->BeginMethod("IsSectionEnd", $enabled);
      $retValue = false;

      if ("#sectionend" == strtolower($this->ID))
      {
        $retValue = true;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    /// <summary>The Directive Name.</summary>
    public string $Name;

    /// <summary>The Directive Type.</summary>
    public string $Type;

    /// <summary>The compare value.</summary>
    public string $Value;
  } // LJCDirective

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
      $this->Groups = [];
      $this->RepeatItems = new LJCItems();
    }

    /// <summary>Creates a copy of the current object.</summary>
    /// <returns>The cloned object.</returns>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $retValue = new self($this->Name);
      $retValue->Begin = $this->Begin;
      $retValue->CurrentItem = $this->CurrentItem;
      $retValue->RepeatItems = $this->RepeatItems;
      $retValue->Name = $this->Name;

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    /// <summary>The Section begin stream offset.</summary>
    public $Begin;

    /// <summary>The Current Item object.</summary>
    public LJCItem $CurrentItem;

    /// <summary>The item Group.</summary>
    public array $Groups;

    /// <summary>The Section Items.</summary>
    public LJCItems $RepeatItems;

    /// <summary>The Section name.</summary>
    public string $Name;
  } // LJCSection

  // ***************
  // Represents a collection of Section objects.
  // Static: AddReplacement(), CreateColumnData(), CreateSections() AddReplacement()
  // Collection Static: Deserialize(), DeserializeString(), Serialize()
  // Collection: GetKeys(), GetValues(), HasKey(), Item()
  // Data: Add(), Clone(), Retrieve(), Remove()
  /// <include path='items/LJCSections/*' file='Doc/LJCSections.xml'/>
  class LJCSections implements IteratorAggregate, \Countable
  {
    // ---------------
    // Static Methods - LJCSections

    // Adds a Replacement to an Item.
    /// <include path='items/AddReplacement/*' file='Doc/LJCSections.xml'/>
    public static function AddReplacement(LJCItem &$item, string $name
      , string $value) : void
    {
      $replacement = new LJCReplacement($name, $value);
      $item->Replacements->Add($replacement, $replacement->Name);
    }

    // Creates "Class" and "Properties" sections data from the table definition.
    /// <include path='items/CreateColumnData/*' file='Doc/LJCSections.xml'/>
    public static function CreateColumnData(LJCDbColumns $dbColumns
      , string $tableName, string $className = null) : LJCSections
    {
      $enabled = false;
      $debug = new LJCDebug("LJCGenTextSectionLib", "LJCSectionsStatic"
       , "a", $enabled);
      $debug->BeginMethod("CreateColumnData", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

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

      $debug->EndMethod($enabled);
      return $sections;
    } // CreateColumnData()

    // Creates the Section data from an XMLDoc node.
    /// <include path='items/CreateSections/*' file='Doc/LJCSections.xml'/>
    public static function CreateSections(SimpleXMLElement $xmlElement)
      : LJCSections
    {
      // Deserialize()
      // DeserializeString()
      $enabled = false;
      $debug = new LJCDebug("LJCGenTextSectionLib", "LJCSectionsStatic"
       , "a", $enabled);
      $debug->BeginMethod("CreateSections", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $sections = new LJCSections();
      if ($xmlElement != null)
      {
        $xmlSections = $xmlElement->Sections->children();
        if ($xmlSections != null)
        {
          foreach ($xmlSections as $xmlSection)
          {
            $sectionName = trim((string)$xmlSection->Name);
            $section = new LJCSection($sectionName);
            $section->Begin = (int)$xmlSection->Begin;

            $xmlGroupsNode = $xmlSection->Groups;
            if (self::ElementHasChildren($xmlGroupsNode))
            {
              $xmlGroups = $xmlGroupsNode->children();
              foreach ($xmlGroups as $xmlGroup)
              {
                $name = (string)$xmlGroup->Name;
                $heading = (string)$xmlGroup->Heading;
                $section->Groups[$name] = $heading;
              }
            }

            $xmlItems = $xmlSection->Items->children();
            foreach ($xmlItems as $xmlItem)
            {
              $name = trim((string)$xmlItem->Name);
              $item = new LJCItem($name);
              // *** Add ***
              $item->ParentGroup = (string)$xmlItem->ParentGroup;

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
              $section->RepeatItems->Add($item, $item->Name);
            }
            $sections->Add($section, $section->Name);
          }
        }
      }

      $debug->EndMethod($enabled);
      return $sections;	
    }

    public static function ElementHasChildren(SimpleXMLElement $element)
    {
      $retValue = false;

      if ($element != null)
      {
        $children = $element->children();
        if ($children != null)
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // ---------------
    // Collection Static Methods - LJCSections

    // Deserializes the data from a Sections XML file.
    /// <include path='items/Deserialize/*' file='Doc/LJCSections.xml'/>
    public static function Deserialize(string $xmlFileSpec) : LJCSections
    {
      $debug = new LJCDebug("LJCGenTextSectionLib", "LJCSectionsStatic"
       , "a", false);
      $enabled = false;
      $debug->BeginMethod("Deserialize", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      if (!file_exists($xmlFileSpec))
      {
        throw new Exception("File: {$xmlFileSpec} does not exist.");
      }
      else
      {
        $xmlElement = simplexml_load_file($xmlFileSpec);
        $retValue = self::CreateSections($xmlElement);
      }

      $debug->EndMethod($enabled);
      return $retValue;
    } // Deserialize()

    // Deserializes the data from a Sections XML string.
    /// <include path='items/DeserializeString/*' file='Doc/LJCSections.xml'/>
    public static function DeserializeString(string $xmlString) : LJCSections
    {
      $debug = new LJCDebug("LJCGenTextSectionLib", "LJCSectionsStatic"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("DeserializeString", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      $xmlElement = simplexml_load_string($xmlString);
      $retValue = self::CreateSections($xmlElement);

      $debug->EndMethod($enabled);
      return $retValue;
    } // DeserializeString()

    // Serializes the data to an XML file.
    /// <include path='items/Serialize/*' file='Doc/LJCSections.xml'/>
    public static function Serialize(string $xmlFile, LJCSections $sections
      , string $rootName): void
    {
      $textState = new LJCTextState();

      $hb = new LJCHTMLBuilder($textState);
      $hb->AddLine("<?xml version=\"1.0\"?>");
      $hb->Begin($rootName, $textState);

      $hb->Begin("Sections", $textState);
      foreach ($sections as $section)
      {
        $hb->Begin("Section", $textState);
        $hb->Create("Begin", $textState, strval($section->Begin));
        $hb->Create("Name", $textState, $section->Name);

        if ($section->Groups != null)
        {
          $hb->Begin("Groups", $textState);
          foreach ($section->Groups as $group)
          {
            $hb->Create("Group", $textState, $group);
          }
          $hb->End("Groups", $textState);
        }

        $hb->Begin("Items", $textState);	
        foreach ($section->RepeatItems as $item)
        {
          $hb->Begin("Item", $textState);
          $hb->Create("Name", $textState, $item->Name);
          $hb->Create("ParentGroup", $textState, $item->ParentGroup);

          $hb->Begin("Replacements", $textState);
          $replacements = $item->Replacements;
          foreach ($replacements as $replacement)
          {
            $hb->Begin("Replacement", $textState);
            $hb->Create("Name", $textState, $replacement->Name);
            $hb->Create("Value", $textState, $replacement->Value);
            $hb->End("Replacement", $textState);
          }
          $hb->End("Replacements", $textState);
          $hb->End("Item", $textState);
        }
        $hb->End("Items", $textState);
        $hb->End("Section", $textState);
      }
      $hb->End("Sections", $textState);

      $hb->End($rootName, $textState);
      $xml = $hb->ToString();
      LJCFileWriter::WriteFile($xml, $xmlFile);
    }

    // ---------------
    // Constructors - LJCSections

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCSections"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;
    }

    // ---------------
    // Collection Methods - LJCSections

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

    /// <summary>Get item by index.</summary>
    public function Item($index)
    {
      $retItem = null;

      $keys = self::GetKeys();
      if (count($keys) > $index)
      {
        $key = $keys[$index];
        $retItem = $this->Items[$key];
      }
      return $retItem;
    } // Item()

    // ---------------
    // Data Methods - LJCSections

    // Adds an object and key value.
    /// <include path='items/Add/*' file='Doc/LJCSections.xml'/>
    public function Add(LJCSection $item, $key = null) : void
    {
      $this->Debug->WriteStartText("Add");
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

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

    /// <summary>Creates an object clone.</summary>
    /// <returns>The cloned object.</returns>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->Add($item, $item->Name);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    /// <summary>Remove the item by Key value.</summary>
    /// <param name="$key">The element key.</param>
    public function Remove($key) : void
    {
      $this->Debug->WriteStartText("Remove");
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      if (false == $this->HasKey($key))
      {
        throw new Exception("Key: {$key} was not found.");
      }
      unset($this->Items[$key]);

      $this->Debug->AddIndent(-1);
    }

    // Get the item by Key value.
    /// <include path='items/Get/*' file='Doc/LJCSections.xml'/>
    public function Retrieve($key, bool $showError = true) : ?LJCSection
    {
      $this->Debug->WriteStartText("Retrieve");
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
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

    // ---------------
    // IteratorAggregate Interface Methods - LJCSection

    /// <summary>Allows foreach()</summary>
    public function getIterator() : Traversable
    {
      return new ArrayIterator($this->Items);
    }

    // ---------------
    // Countable Interface Methods - LJCSection

    /// <summary>Allows Count(object).</summary>
    public function count() : int
    {
      return count($this->Items);
    }

    // ---------------
    // Class Data - LJCSection

    // The elements array.
    private $Items = [];
  }  // LJCSections

  // ***************
  // Represents a Section Item.
  // Clone()
  /// <summary>Represents a Section Item.</summary>
  class LJCItem
  {
    /// <summary>Initializes an object instance with the provided values.</summary>
    /// <param name="$name">The Item name.</param>
    public function __construct(string $name, string $parentGroup = "")
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCItem"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $this->Name = trim($name);
      $this->ParentGroup = $parentGroup;
      $this->Replacements = new LJCReplacements();
    }

    /// <summary>Creates a Clone of the current object.</summary>
    /// <returns>The cloned object.</returns>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $retValue = new self($this->Name);
      $retValue->Replacements = $this->Replacements;
      $retValue->RootName = $this->RootName;
      $retValue->ParentGroup = $this->ParentGroup;

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ------------------
    // Properties - LJCItem

    /// <summary>The Item name.</summary>
    public string $Name;

    /// The group to which the item belongs.
    public string $ParentGroup;

    /// <summary>The Item replacements.</summary>
    public LJCReplacements $Replacements;

    /// <summary>The XML Root Name value.</summary>
    public string $RootName = "Items";
  } // LJCItem

  // ***************
  // Represents a collection of Item objects.
  // Collection: Clone(), GetKeys(), GetValues(), HasKey()
  // Data: Add(), Retrieve(), Remove()
  // <include path='items/LJCItems/*' file='Doc/LJCItems.xml'/>
  class LJCItems implements IteratorAggregate, \Countable
  {
    // ---------------
    // Constructors - LJCItems

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCItems"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    }

    // ----------------------
    // Collection Methods - LJCItems

    /// <summary>Creates an object clone.</summary>
    /// <returns>The cloned object.</returns>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->Add($item, $item->Name);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

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
    // <include path='items/HasKey/*' file='Doc/LJCItems.xml'/>
    public function HasKey($key) : bool
    {
      return isset($this->Items[$key]);
    }

    /// <summary>Get item by index.</summary>
    public function Item($index)
    {
      $retItem = null;

      $keys = self::GetKeys();
      if (count($keys) > $index)
      {
        $key = $keys[$index];
        $retItem = $this->Items[$key];
      }
      return $retItem;
    } // Item()

    // ----------------------
    // Data Methods - LJCItems

    // Adds an object and key value.
    // <include path='items/Add/*' file='Doc/LJCItems.xml'/>
    public function Add(LJCItem $item, $key = null) : void
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
    } // Add()

    // Find the first group item.
    public static function FindGroupItem(LJCItems $items, string $group)
      : ?LJCItem
    {
      $retItem = null;

      foreach ($items as $item)
      {
        if ($item->ParentGroup == $group)
        {
          $retItem = $item;
          break;
        }
      }
      return $retItem;
    } // FindGroupItem()

    /// <summary>Remove the item by Key value.</summary>
    /// <param name="$key">The element key.</param>
    public function Remove($key, bool $showError = true) : void
    {
      $this->Debug->WriteStartText("Remove");

      if (false == $this->HasKey($key))
      {
        if ($showError)
        {
          throw new Exception("Key: {$key} was not found.");
        }
      }
      unset($this->Items[$key]);

      $this->Debug->AddIndent(-1);
    } // Remove()

    // Get the item by Key value.
    // <include path='items/Get/*' file='Doc/LJCItems.xml'/>
    public function Retrieve($key, bool $showError = true) : ?LJCItem
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
    } // Retrieve()

    // ----------------------
    // IteratorAggregate Interface Methods - LJCItems

    /// <summary>Allows foreach()</summary>
    public function getIterator() : Traversable
    {
      return new ArrayIterator($this->Items);
    }

    // ----------------------
    // Countable Interface Methods - LJCItems

    /// <summary>Allows Count(object).</summary>
    public function count() : int
    {
      return count($this->Items);
    }

    // ------------------
    // Class Data - LJCItems

    // The elements array.
    private $Items = [];
  } // LJCItems

  // ***************
  // Represents Item Replacements.
  // Clone()
  /// <summary>Represents Item Replacements.</summary>
  class LJCReplacement
  {
    // Initializes an object instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCSections.xml'/>
    public function __construct(string $name, string $value)
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCReplacement"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->Name = trim($name);
      $this->Value = $value;
    }

    /// <summary>Creates a Clone of the current object.</summary>
    /// <returns>The cloned object.</returns>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self($this->Name, $this->Value);
      $retValue->RootName = $this->RootName;

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // ------------------
    // Properties - LJCReplacement

    /// <summary>The Replacement name.</summary>
    public string $Name;

    /// <summary>The Replacement value.</summary>
    public string $Value;

    /// <summary>The XML Root Name value.</summary>
    public string $RootName = "Replacements";
  } // LJCReplacement

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
      $this->Debug = new LJCDebug("LJCGenTextSectionLib", "LJCReplacements"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    }

    // ----------------------
    // Collection Methods - LJCReplacements

    // Creates an object clone.
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->Add($item, $item->Name);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // Indicates if a key already exists.
    /// <include path='items/HasKey/*' file='Doc/LJCReplacements.xml'/>
    public function HasKey($key) : bool
    {
      return isset($this->Items[$key]);
    }

    // ----------------------
    // Data Methods - LJCReplacements

    // Adds an object and key value.
    /// <include path='items/Add/*' file='Doc/LJCReplacements.xml'/>
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

    /// <summary>Remove the item by Key value.</summary>
    /// <param name="$key"></param>
    public function Remove($key) : void
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
    /// <include path='items/Retrieve/*' file='Doc/LJCReplacements.xml'/>
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
    // IteratorAggregate Interface Methods - LJCReplacements

    // Allows foreach()
    public function getIterator() : Traversable
    {
      return new ArrayIterator($this->Items);
    }

    // ----------------------
    // Countable Interface Methods - LJCReplacements

    // Allows Count(object)
    public function count() : int
    {
      return count($this->Items);
    }

    // ------------------
    // Class Data - LJCReplacements

    // The elements array.
    private $Items = [];
  } // LJCReplacements
?>
