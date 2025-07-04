<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.-->
  // LJCDocDataLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  // LJCCommonLib: LJC
  // LJCTextLib: LJCStringBuilder, LJCWriter
  // LJCCollectionLib: LJCCollectionBase
  // LJCDebugLib: LJCDebug

  // Contains Classes to represent DocData.
  /// <include path='items/LJCDocDataLib/*' file='Doc/LJCDocDataLib.xml'/>
  /// LibName: LJCDocDataLib
  //  LJCDocDataClass, LJCDocDataClasses LJCDocDataFile
  //  LJCDocDataMethod, LJCDocDataMethods
  //  LJCDocDataParam, LJCDocDataParams
  //  LJCDocDataProperty, LJCDocDataProperties

  // Main Object Graph
  // LJCDocDataFile
  //   LJCDocDataClasses
  //     LJCDocDataClass
  //       LJCDocDataMethods
  //         LJCDocDataMethod
  //           LJCDocDataParams
  //             LJCDocDataParam
  //       LJCDocDataProperties
  //         LJCDocDataProperty
  //   LJCDocDataMethods

  // #01 Correct XMLFileName - 5/1/25
  //   LJCIncludeLib.php
  // #02 Correct Syntac - 5/1/25
  //   LJCCollectionLib.php

  // ***************
  // Public: Clone()
  /// <summary>Represents a DocData Class.</summary>
  class LJCDocDataClass
  {
    // ---------------
    // Constructors

    // Initializes an object instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCDocDataClass.xml'/>
    public function __construct(string $name, ?string $summary = null)
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataClass"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->Code = null;
      $this->Groups = [];
      $this->Methods = null;
      $this->Name = $name;
      $this->Properties = null;
      $this->Remarks = null;
      $this->Summary = $summary;
    } // __construct()

    // ---------------
    // Public Methods - LJCDocDataClass

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      $retValue = new self($this->Name, $this->Summary);

      $retValue->Code = $this->Code;
      $retValue->Methods = $this->Methods;
      $retValue->Properties = $this->Properties;
      $retValue->Remarks = $this->Remarks;

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Public Properties - LJCDocDataClass

    /// <summary>The Code value.</summary>
    public ?string $Code;

    // *** Add ***
    public ?array $Groups;

    /// <summary>The Method array.</summary>
    public ?LJCDocDataMethods $Methods;

    /// <summary>The Name value.</summary>
    public ?string $Name;

    /// <summary>The Property array.</summary>
    public ?LJCDocDataProperties $Properties;

    /// <summary>The Remarks value.</summary>
    public ?string $Remarks;

    /// <summary>The Summary value.</summary>
    public ?string $Summary;
  } // LJCDocDataClass

  // ***************
  // Public: Clone(), AddObject(), Retrieve()
  /// <summary>Represents a collection of objects.</summary>
  class LJCDocDataClasses extends LJCCollectionBase
  {
    /// <summary>Initializes an object instance with the provided values.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataClasses"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    } // __construct

    /// <summary>Creates a clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Public Methods - LJCDocDataClasses

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function AddObject(LJCDocDataClass $item, $key = null)
      : ?LJCDocDataClass
    {
      $enabled = false;
      $this->Debug->BeginMethod("AddObject", $enabled);

      if (null == $key)
      {
        $key = $item->ID;
      }
      $retValue = $this->AddItem($item, $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // AddObject()

    // <summary>Retrieves the item by Key value.</summary>
    /// <include path='items/Retrieve/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function Retrieve($key, bool $throwError = true) : ?LJCDocDataClass
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);

      $retValue = $this->RetrieveItem($key, $throwError);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Retrieve()
  } // LJCDocDataClasses

  // ***************
  // Represents a DocData Lib File.
  // Static: Deserialize(), DeserializeString(), GetProperties()
  // Public: Clone(), Serialize(), SerializeToString()
  /// <include path='items/LJCDocDataFile/*' file='Doc/LJCDocDataFile.xml'/>
  class LJCDocDataFile
  {
    // ---------------
    // Public Static Functions

    // Deserializes the data from an LJCDocDataFile XML file.
    /// <include path='items/Deserialize/*' file='Doc/LJCDocDataFile.xml'/>
    public static function Deserialize(string $xmlFileSpec) : LJCDocDataFile
    {
      $retValue = null;

      $docNode = simplexml_load_file($xmlFileSpec);
      $retValue = self::CreateDocDataFile($docNode);
      return $retValue;
    } // Deserialize()

    // Deserializes the data from an LJCDocDataFile XML string.
    /// <include path='items/DeserializeString/*' file='Doc/LJCDocDataFile.xml'/>
    public static function DeserializeString(string $xmlString) : LJCDocDataFile
    {
      $retValue = null;

      $docNode = simplexml_load_string($xmlString);
      $retValue = self::CreateDocDataFile($docNode);
      return $retValue;
    } // DeserializeString()

    // Creates the LJCDocDataFile object.
    private static function CreateDocDataFile(SimpleXMLElement $xmlNode)
      : ?LJCDocDataFile
    {
      // Deserialize()
      // + DeserializeString()
      $retValue = null;

      if (null != $xmlNode)
      {
        $name = self::XMLToString($xmlNode->Name);
        $retValue = new LJCDocDataFile($name);
        $retValue->Classes = self::GetClasses($xmlNode);
        $retValue->Remarks = self::XMLToString($xmlNode->Remarks);
        $retValue->Summary = self::XMLToString($xmlNode->Summary);
      }
      return $retValue;
    } // CreateDocDataFile()

    // Deserialize Classes from the Doc node.
    private static function GetClasses(SimpleXMLElement $docNode)
      : ?LJCDocDataClasses
    {
      // Deserialize()
      // + DeserializeString()-CreateDocDataFile()
      $retValue = null;

      $classNodes = self::GetClassNodes($docNode);
      if (null != $classNodes)
      {
        $retValue = new LJCDocDataClasses();
        foreach ($classNodes as $classNode)
        {
          $name = self::XMLToString($classNode->Name);
          $class = new LJCDocDataClass($name);
          $retValue->AddObject($class, $name);
          $class->Summary = self::XMLToString($classNode->Summary);
          // *** Add ***
          $class->Groups = self::GetGroups($classNode);
          $class->Remarks = self::XMLToString($classNode->Remarks);
          $class->Methods = self::GetMethods($classNode);
          $class->Properties = self::GetProperties($classNode);
          $class->Code = self::XMLToString($classNode->Code, false);
        }
      }
      return $retValue;
    } // GetClasses()

    // Deserialize Methods from the Class node.
    private static function GetMethods(SimpleXMLElement $classNode)
      : ?LJCDocDataMethods
    {
      // Deserialize()
      // + DeserializeString()-CreateDocDataFile()-GetClasses()
      $retValue = null;

      $methodNodes = self::GetMethodNodes($classNode);
      if ($methodNodes != null)
      {
        $retValue = new LJCDocDataMethods();
        foreach ($methodNodes as $methodNode)
        {
          $name = self::XMLToString($methodNode->Name);
          $method = new LJCDocDataMethod($name);
          $retValue->AddObject($method, $name);
          $method->Summary = self::XMLToString($methodNode->Summary);
          // *** Add ***
          $method->ParentGroup = self::XMLToString($methodNode->ParentGroup);
          $method->Params = self::GetParams($methodNode);
          $method->Returns = self::XMLToString($methodNode->Returns);
          $method->Remarks = self::XMLToString($methodNode->Remarks);
          $method->Syntax = self::XMLToString($methodNode->Syntax);
          $method->Code = self::XMLToString($methodNode->Code, false);
        }
      }
      return $retValue;
    } // GetMethods()

    // Deserialize Groups from the Class node.
    private static function GetGroups(SimpleXMLElement $classNode)
    {
      // Deserialize()
      // + DeserializeString()-CreateDocDataFile()-GetClasses()
      $retValue = null;

      $groupNodes = self::GetGroupNodes($classNode);
      if ($groupNodes != null)
      {
        $retValue = [];
        foreach ($groupNodes as $groupNode)
        {
          $retValue[] = self::XMLToString($groupNode);
        }
      }
      return $retValue;
    }

    // Deserialize Params from the Method node.
    private static function GetParams(SimpleXMLElement $methodNode)
      : ?LJCDocDataParams
    {
      // Deserialize()
      // + DeserializeString()-CreateDocDataFile()-GetClasses()
      //   -GetMethods()
      $retValue = null;

      $paramNodes = self::GetParamNodes($methodNode);
      if ($paramNodes != null)
      {
        $retValue = new LJCDocDataParams();
        foreach ($paramNodes as $paramNode)
        {
          $name = self::XMLToString($paramNode->Name);
          $summary = self::XMLToString($paramNode->Summary);
          $param = new LJCDocDataParam($name, $summary);
          $retValue->AddObject($param, $name);
        }
      }
      return $retValue;
    } // GetParams()

    // Deserialize Properties from the Class node.
    /// <include path='items/GetProperties/*' file='Doc/LJCDocDataFile.xml'/>
    public static function GetProperties(SimpleXMLElement $classNode)
      : ?LJCDocDataProperties
    {
      // Deserialize()
      // + DeserializeString()-CreateDocDataFile()-GetClasses()
      $retValue = null;

      $propertyNodes = self::GetPropertyNodes($classNode);
      if ($propertyNodes != null)
      {
        $retValue = new LJCDocDataProperties();
        foreach ($propertyNodes as $propertyNode)
        {
          $name = self::XMLToString($propertyNode->Name);
          $property = new LJCDocDataProperty($name);
          $retValue->AddObject($property, $name);
          $property->Summary = self::XMLToString($propertyNode->Summary);
          $property->Returns = self::XMLToString($propertyNode->Returns);
          $property->Remarks = self::XMLToString($propertyNode->Remarks);
          $property->Syntax = self::XMLToString($propertyNode->Syntax);
        }
      }
      return $retValue;
    } // GetProperties()

    // ---------------
    // Static GetNodes Functions - LJCDocDataFile

    // Retrieves the Class nodes.
    private static function GetClassNodes(SimpleXMLElement $docNode)
      : ?SimpleXMLElement
    {
      $retValue = null;

      $nodes = $docNode->Classes;
      if (null != $nodes)
      {
        $retValue = $nodes->children();
      }
      return $retValue;
    } // GetClassNodes()

    // Retrieves the Group nodes.
    private static function GetGroupNodes(SimpleXMLElement $classNode)
      : ?array
    {
      $retArray = null;

      $retArray = $classNode->xPath("Group");
      return $retArray;
    }

    // Retrieves the Method nodes.
    private static function GetMethodNodes(SimpleXMLElement $classNode)
      : ?SimpleXMLElement
    {
      $retValue = null;

      $nodes = $classNode->Methods;
      if ($nodes != null)
      {
        $retValue = $nodes->children();
      }
      return $retValue;
    } // GetMethodNodes()

    // Retrieves the Para nodes.
    private static function GetParamNodes(SimpleXMLElement $functionNode)
      : ?SimpleXMLElement
    {
      $retValue = null;

      $nodes = $functionNode->Params;
      if ($nodes != null)
      {
        $retValue = $nodes->children();
      }
      return $retValue;
    } // GetParamNodes()

    // Retrieves the Property nodes.
    private static function GetPropertyNodes(SimpleXMLElement $classNode)
      : ?SimpleXMLElement
    {
      $retValue = null;

      $nodes = $classNode->Properties;
      if ($nodes != null)
      {
        $retValue = $nodes->children();
      }
      return $retValue;
    } // GetPropertyNodes()

    // Get a string value from the XML value.
    // Possible for Common code.
    private static function XMLToString(SimpleXMLElement $xmlValue
      , bool $trim = true) : ?string
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

    // ---------------
    // Constructors - LJCDocDataFile

    // Initializes an object instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCDocDataFile.xml'/>
    public function __construct(string $name, ?string $summary = null)
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataFile"
        , "w",  false);
      $this->Debug->IncludePrivate = true;

      $this->Classes = null;
      $this->Functions = null;
      $this->Name = $name;
      $this->Remarks = null;
      $this->Summary = $summary;
    } // __construct()

    // ---------------
    // Public Methods - LJCDocDataFile

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self($this->Name, $this->Summary);
      $retValue->Classes = $this->Classes;
      $retValue->Functions = $this->Functions;

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // Writes the serialized XML.
    /// <include path='items/Serialize/*' file='Doc/LJCDocDataFile.xml'/>
    public function Serialize(string $xmlFileSpec) : void
    {
      $enabled = false;
      $this->Debug->BeginMethod("Serialize", $enabled);

      $docDataXML = $this->SerializeToString();
      $stream = fopen($xmlFileSpec, "w");
      $this->Writer = new LJCWriter($stream);
      $this->Writer->FWrite($docDataXML);
      fclose($stream);

      $this->Debug->EndMethod($enabled);
    } // Serialize()

    // Creates the serialized XML string.
    /// <include path='items/SerializeToString/*' file='Doc/LJCDocDataFile.xml'/>
    public function SerializeToString($xmlFileName = null) : string
    {
      // Serialize()
      $enabled = false;
      $this->Debug->BeginMethod("SerializeToString", $enabled);

      $builder = new LJCStringBuilder();

      // Possible Common code.
      $builder->Line("<?xml version=\"1.0\"?>");
      $builder->Line("<!-- Copyright (c) Lester J. Clark and Contributors. -->");
      $builder->Line("<!-- Licensed under the MIT License. -->");
      if (null != $xmlFileName)
      {
        $builder->Line("<!-- $xmlFileName -->");
      }
      $builder->Text("<LJCDocDataFile xmlns:xsd=");
      $builder->Line("'http://www.w3.org/2001/XMLSchema'");
      $builder->Text("  xmlns:xsi=");
      $builder->Line("'http://www.w3.org/2001/XMLSchema-instance'>");

      $indent = 1;
      $builder->Tags("Name", $this->Name, $indent);
      $builder->Tags("Summary", $this->Summary, $indent);
      $builder->Tags("Remarks", $this->Remarks, $indent);

      if ($this->Classes != null)
      {
        $builder->Line("<Classes>", $indent);
        foreach ($this->Classes as $class)
        {
          $builder->Line("<Class>", $indent + 1);
          $builder->Tags("Name", $class->Name, $indent + 2);
          $builder->Tags("Summary", $class->Summary, $indent + 2);
          if (LJC::HasItems($class->Groups))
          {
            foreach ($class->Groups as $group)
            {
              $builder->Tags("Group", $group, $indent + 2);
            }
          }
          $builder->Tags("Remarks", $class->Remarks, $indent + 2);
          $builder->Text($this->SerializeMethods($class, $indent + 2));
          $builder->Text($this->SerializeProperties($class, $indent + 2));
          $builder->Tags("Code", $class->Code, $indent + 2);
          $builder->Line("</Class>", $indent + 1);
        }
        $builder->Line("</Classes>", $indent);
      }
      $builder->Line("</LJCDocDataFile>");

      $this->Debug->EndMethod($enabled);
      // *** Add ***
      $retValue = $builder->ToString();
      return $retValue;
    } // SerializeToString()
    
    // ---------------
    // Private Methods - LJCDocDataFile

    // Creates the serialized Methods XML.
    private function SerializeMethods(LJCDocDataClass $class, int $indent)
      : ?string
    {
      // SerializeToString()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeMethods", $enabled);

      $builder = new LJCStringBuilder();
      if ($class->Methods != null && count($class->Methods) > 0)
      {

        $builder->Line("<Methods>", $indent);
        foreach ($class->Methods as $method)
        {
          $builder->Line("<Method>", $indent + 1);
          $builder->Tags("Name", $method->Name, $indent + 2);
          $builder->Tags("Summary", $method->Summary, $indent + 2);
          // *** Add ***
          $builder->Tags("ParentGroup", $method->ParentGroup, $indent + 2);
          $builder->Text($this->SerializeParams($method->Params, $indent + 2));
          $builder->Tags("Returns", $method->Returns, $indent + 2);
          $builder->Tags("Remarks", $method->Remarks, $indent + 2);
          $builder->Tags("Syntax", $method->Syntax, $indent + 2);
          $builder->Tags("Code", $method->Code, $indent + 2);
          $builder->Line("</Method>", $indent + 1);
        }
        $builder->Line("</Methods>", $indent);
      }

      $this->Debug->EndMethod($enabled);
      return $builder->ToString();
    } // SerializeMethods()

    // Creates the serialized Params XML.
    private function SerializeParams(?LJCDocDataParams $params, int $indent)
      : ?string
    {
      // SerializeToString()-SerializeMethods()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateParams", $enabled);

      $builder = new LJCStringBuilder();
      if ($params != null && count($params) > 0)
      {
        $builder->Line("<Params>", $indent);
        foreach ($params as $param)
        {
          $builder->Line("<Param>", $indent + 1);
          $builder->Tags("Name", $param->Name, $indent + 2);
          $builder->Tags("Summary", $param->Summary, $indent + 2);
          $builder->Line("</Param>", $indent + 1);
        }
        $builder->Line("</Params>", $indent);
      }

      $this->Debug->EndMethod($enabled);
      return $builder->ToString();
    } // SerializeParams()

    // Appends the serialized Properties XML.
    private function SerializeProperties(LJCDocDataClass $class, int $indent)
      : ?string
    {
      // SerializeToString()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateProperties", $enabled);

      $builder = new LJCStringBuilder();
      if ($class->Properties != null && count($class->Properties) > 0)
      {
        $builder->Line("<Properties>", $indent);
        foreach ($class->Properties as $property)
        {
          $builder->Line("<Property>", $indent + 1);
          $builder->Tags("Name", $property->Name, $indent + 2);
          $builder->Tags("Summary", $property->Summary, $indent + 2);
          $builder->Tags("Returns", $property->Returns, $indent + 2);
          $builder->Tags("Remarks", $property->Remarks, $indent + 2);
          $builder->Tags("Syntax", $property->Syntax, $indent + 2);
          $builder->Line("</Property>", $indent + 1);
        }
        $builder->Line("</Properties>", $indent);
      }

      $this->Debug->EndMethod($enabled);
      return $builder->ToString();
    } // SerializeProperties()

    // ---------------
    // Public Properties - LJCDocDataFile

    /// <summary>The Class collection.</summary>
    public ?LJCDocDataClasses $Classes;

    /// <summary>The Function array.</summary>
    public ?LJCDocDataMethods $Functions;

    /// <summary>The Name value.</summary>
    public string $Name;

    /// <summary>The Name value.</summary>
    public ?string $Remarks;

    /// <summary>The Summary value.</summary>
    public ?string $Summary;

    // The Writer object.
    private LJCWriter $Writer;
  } // LJCDocDataFile

  // ***************
  // Public: Clone()
  /// <summary>Represents a DocData Function.</summary>
  class LJCDocDataMethod
  {
    // ---------------
    // Constructors

    // Initializes an object instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCDocDataMethod.xml'/>
    public function __construct(string $name, ?string $summary = null
      , ?string $returns = null)
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataMethod"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->Code = null;
      $this->Name = $name;
      // *** Add ***
      $this->ParentGroup = null;
      $this->Params = null;
      $this->Remarks = null;
      $this->Returns = $returns;
      $this->Summary = $summary;
      $this->Syntax = null;
    } // __construct()

    // ---------------
    // Public Methods - LJCDocDataMethod

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self($this->Name, $this->Summary, $this->Returns);
      $retValue->Code = $this->Code;
      $retValue->Params = $this->Params;
      $retValue->Remarks = $this->Remarks;

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Public Properties - LJCDocDataMethod

    /// <summary>The Code value.</summary>
    public ?string $Code;

    /// <summary>The Name value.</summary>
    public string $Name;

    /// <summary>The Param array.</summary>
    public ?LJCDocDataParams $Params;

    // *** Add ***
    // The method group name.
    public ?string $ParentGroup;

    /// <summary>The Remarks value.</summary>
    public ?string $Remarks;

    /// <summary>The Returns value.</summary>
    public ?string $Returns;

    /// <summary>The Summary value.</summary>
    public ?string $Summary;

    /// <summary>The Syntax value.</summary>
    public ?string $Syntax;
  } // LJCDocDataMethod

  // ***************
  // Public: Clone(), AddObject(), Retrieve()
  /// <summary>Represents a collection of objects.</summary>
  class LJCDocDataMethods extends LJCCollectionBase
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataMethods"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    }

    /// <summary>Creates a clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Public Methods - LJCDocDataMethods

    // <summary>Adds an object and key value.</summary>
    /// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function AddObject(LJCDocDataMethod $item, $key = null)
      : ?LJCDocDataMethod
    {
      $enabled = false;
      $this->Debug->BeginMethod("AddObject", $enabled);

      if (null == $key)
      {
        $key = $item->ID;
      }
      $retValue = $this->AddItem($item, $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // AddObject()

    // <summary>Retrieves the item by Key value.</summary>
    /// <include path='items/Retrieve/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function Retrieve($key, bool $throwError = true) : ?LJCDocDataMethod
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);

      $retValue = $this->RetrieveItem($key, $throwError);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Retrieve()
  } // LJCDocDataMethods

  // ***************
  // Public: Clone()
  /// <summary>Represents a DocData Parameter.</summary>
  class LJCDocDataParam
  {
    // ---------------
    // Constructors

    // Initializes an object instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCDocDataClass.xml'/>
    public function __construct(string $name, ?string $summary = null)
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataParam"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->Name = $name;
      $this->Summary = $summary;
    } // __construct()

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->StartMethod("Clone", $enabled);

      $retValue = new self($this->Name, $this->Summary);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Public Properties - LJCDocDataParam

    /// <summary>The Name value.</summary>
    public string $Name;

    /// <summary>The Summary value.</summary>
    public ?string $Summary;
  } // LJCDocDataParam

  // ***************
  // Public: Clone(), AddObject(), Retrieve()
  /// <summary>Represents a collection of objects.</summary>
  class LJCDocDataParams extends LJCCollectionBase
  {
    // ---------------
    // Constructors

    // <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataParams"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    } // __construct()

    /// <summary>Creates a clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Public Methods - LJCDocDataParams

    /// <summary>Adds an object and key value.</summary>
    /// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function AddObject(LJCDocDataParam $item, $key = null)
      : ?LJCDocDataParam
    {
      $enabled = false;
      $this->Debug->BeginMethod("AddObject", $enabled);

      if (null == $key)
      {
        $key = $item->Name;
      }
      $retValue = $this->AddItem($item, $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // AddObject()

    /// <summary>Retrieves the item by Key value.</summary>
    /// <include path='items/Retrieve/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function Retrieve($key, bool $throwError = true) : ?LJCDocDataParam
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);

      $retValue = $this->RetrieveItem($key, $throwError);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Retrieve()
  } // LJCDocDataParams

  // ***************
  // Public: Clone()
  /// <summary>Represents a DocData Property.</summary>
  class LJCDocDataProperty
  {
    // ---------------
    // Constructors

    // Initializes a class instance.
    /// <include path='items/construct/*' file='Doc/LJCDocDataProperty.xml'/>
    public function __construct(string $name, ?string $summary = null
      , ?string $returns = null)
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataProperty"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->Name = $name;
      $this->Remarks = null;
      $this->Returns = $returns;
      $this->Summary = $summary;
      $this->Syntax = null;
    } // __construct()

    /// <summary>Creates a Clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self($this->Name, $this->Summary, $this->Returns);
      $retValue->Remarks = $this->Remarks;

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Public Properties  - LJCDocDataProperty

    /// <summary>The Name value.</summary>
    public string $Name;

    /// <summary>The Remarks value.</summary>
    public ?string $Remarks;

    /// <summary>The Returns value.</summary>
    public ?string $Returns;

    /// <summary>The Summary value.</summary>
    public ?string $Summary;

    /// <summary>The Syntax value.</summary>
    public ?string $Syntax;
  } // LJCDocDataProperty

  // ***************
  // Public: Clone(), AddObject(), Retrieve()
  /// <summary>Represents a collection of objects.</summary>
  class LJCDocDataProperties extends LJCCollectionBase
  {
    // ---------------
    // Constructors

    // Initializes an object instance.
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataLib", "LJCDocDataProperties"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    }

    /// <summary>Creates a clone of the current object.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Public Methods - LJCDocDataProperties

    // <summary>Adds an object and key value.</summary>
    /// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function AddObject(LJCDocDataProperty $item, $key = null)
      : ?LJCDocDataProperty
    {
      $enabled = false;
      $this->Debug->BeginMethod("AddObject", $enabled);

      if (null == $key)
      {
        $key = $item->ID;
      }
      $retValue = $this->AddItem($item, $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // AddObject()

    // <summary>Retrieves the item by Key value.</summary>
    /// <include path='items/Retrieve/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function Retrieve($key, bool $throwError = true) : ?LJCDocDataProperty
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);

      $retValue = $this->RetrieveItem($key, $throwError);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Retrieve()
  } // LJCDocDataProperties
?>