<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJC**Lib.php
  declare(strict_types=1);
  $devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
  require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
  require_once "$devPath/LJCPHPCommon/LJCTextLib.php";

  // Contains Classes to - **.
  /// <include path='items/LJC**Lib/*' file='Doc/LJC**Lib.xml'/>
  /// LibName: LJC**Lib
  
  // ***************
  // Provides methods for - **.
  /// <include path='items/LJC**/*' file='Doc/LJC**.xml'/>
  class LJC**
  {
    // ---------------
    // Public Static Functions

    // Deserializes the data from an LJC** XML file.
    /// <include path='items/Deserialize/*' file='Doc/LJC**.xml'/>
    public static function Deserialize(string $xmlFileSpec) : LJC**
    {
      $retValue = null;

      $docNode = simplexml_load_file($xmlFileSpec);
      $retValue = self::Create**($docNode);
      return $retValue;
    }

    // Deserializes the data from an LJC** XML string.
    /// <include path='items/DeserializeString/*' file='Doc/LJC**.xml'/>
    public static function DeserializeString(string $xmlString) : LJC**
    {
      $retValue = null;

      $docNode = simplexml_load_string($xmlString);
      $retValue = self::Create**($docNode);
      return $retValue;
    }

    // ---------------
    // Private Static Functions

    // Creates the LJCDocDataFile object.
    private static function Create**(SimpleXMLElement $xmlNode)
      : ?LJC**
    {
      $retValue = null;

      if (null != $xmlNode)
      {
        $name = self::Value($xmlNode->Name);
        $retValue = new LJC**($name);
        $retValue->Items = self::GetItems($xmlNode);
      }
      return $retValue;
    }

    // Deserialize Items from the Doc node.
    private static function GetItems(SimpleXMLElement $docNode)
      : ?LJCItems
    {
      $retValue = null;

      $itemNodes = self::GetItemNodes($docNode);
      if (null != $classNodes)
      {
        $retValue = new LJCItem**s();
        foreach ($itemNodes as $itemNode)
        {
          $name = self::Value($itemNode->Name);
          $item = new LJC**($name);
          $retValue->AddObject($item, $name);
          $item->Property = self::Value($itemNode->Property);
        }
      }
      return $retValue;
    }

    // ---------------
    // Static GetNodes Functions - LJC**

    // Retrieves the Class nodes.
    private static function GetItemNodes(SimpleXMLElement $docNode)
      : ?SimpleXMLElement
    {
      $retValue = null;

      $nodes = $docNode->Items;
      if (null != $nodes)
      {
        $retValue = $nodes->children();
      }
      return $retValue;
    }

    // Get the value from the XML value.
    private static function Value(SimpleXMLElement $xmlValue
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
    }

    // ---------------
    // Constructors

    /// <summary>
    ///   Initializes an object instance with the provided values.
    /// </summary>
    /// <param name="$name">The name value.</param>
    public function __construct(string $name, string $code)
    {
      $this->Name = null;
      $this->Code = null;

      $this->DebugWriter = new LJCDebugWriter("LJCTextRanges");
    }

    // Creates a Clone of the current object.
    /// <include path='items/Clone/*' file='../../CommonDoc/PHPDataClass.xml.xml'/>
    public function Clone() : self
    {
      $loc = "LJC**.Clone";
      // Testing
      $this->Debug("$loc here: $this->Name");
      $retValue = new self($this->Name);
      $retValue->Code = $this->Code;
      return $retValue;
    }

    // ---------------
    // Public Methods - LJC**

    // Writes the serialized XML.
    /// <include path='items/Serialize/*' file='Doc/LJC**.xml'/>
    public function Serialize(string $xmlFileSpec) : void
    {
      $docDataXML = $this->SerializeToString();
      $stream = fopen($xmlFileSpec, "w");
      $this->Writer = new LJCWriter($stream);
      $this->Writer->FWrite($docDataXML);
      fclose($stream);
    }

    // Creates the serialized XML string.
    /// <include path='items/SerializeToString/*' file='Doc/LJC**.xml'/>
    public function SerializeToString($xmlFileName = null) : string
    {
      $builder = new LJCStringBuilder();

      $builder->AppendLine("<?xml version=\"1.0\"?>");
      $builder->Append("<!-- Copyright (c) Lester J. Clark 2022 -");
      $builder->AppendLine(" All Rights Reserved -->");
      if (null != $xmlFileName)
      {
        $builder->AppendLine("<!-- $xmlFileName -->");
      }
      $builder->Append("<LJC** xmlns:xsd=");
      $builder->AppendLine("'http://www.w3.org/2001/XMLSchema'");
      $builder->Append("  xmlns:xsi=");
      $builder->AppendLine("'http://www.w3.org/2001/XMLSchema-instance'>");

      $indent = 1;
      $builder->AppendTags("Name", $this->Name, $indent);
      if ($this->Items != null)
      {
        $builder->AppendLine("<Items>", $indent);
        foreach ($this->Items as $item)
        {
          $builder->AppendLine("<Item>", $indent + 1);
          $builder->AppendTags("Name", $Item->Name, $indent + 2);
          $builder->AppendLine("</Item>", $indent + 1);
        }
        $builder->AppendLine("</Items>", $indent);
      }
      $builder->AppendLine("</LJC**>");
      return $builder->ToString();
    }

    // ---------------
    // Private Methods - LJC**

    // Output the debug value.
    private function Debug(string $text, bool $addLine = true) : void
    {
      $this->DebugWriter->Debug($text, $addLine);
    }

    // ---------------
    // Public Properties - LJC**

    /// <summary>The Code value.</summary>
    public ?string $Code;

    /// <summary>The Name value.</summary>
    public ?string $Name;

    // ---------------
    // Private Properties - LJC**

  }  // LJC**
?>