<?php
  // LJCGenDataXMLLib.php
  declare(strict_types=1);
  $path = "../..";
  // Must refer to exact same file everywhere in codeline.
  require_once "$path/LJCPHPCommon/LJCTextLib.php";
  require_once "$path/GenDoc/GenDataLib/LJCDebugLib.php";

  // Contains Classes to generate GenData XML.
  /// <include path='items/LJCGenDataXMLLib/*' file='Doc/LJCGenDataXMLLib.xml'/>
  /// LibName: LJCGenDataXMLLib

  // Provides methods for creating GenData XML text.
  /// <include path='items/LJCGenDataXML/*' file='Doc/LJCGenDataXML.xml'/>
  class LJCGenDataXML
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $isEnabled = false;
      $this->Debug = new LJCDebug("LJCDocGenDataXMLLib", "LJCGenDataXML"
        , $isEnabled);
      $this->Debug->IncludePrivate = true;
    }

    // ---------------
    // Common GenData XML Methods - LJCGenDataXML

    // Creates the Item begin string.
    /// <include path='items/ItemBegin/*' file='Doc/LJCGenDataXML.xml'/>
    public static function ItemBegin(string $name, int $indent) : string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->AppendLine("<Item>", $indent);
      $indent++;
      $builder->AppendTags("Name", $name, $indent);
      $builder->AppendLine("<Replacements>", $indent);
      $retValue = $builder->ToString();
      return $retValue;
    }

    // Creates the Item end string.
    /// <include path='items/ItemEnd/*' file='Doc/LJCGenDataXML.xml'/>
    public static function ItemEnd(int $indent) : string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->AppendLine("</Replacements>", $indent);
      $indent--;
      $builder->AppendLine("</Item>", $indent);		
      $retValue = $builder->ToString();
      return $retValue;
    }

    // Creates the Replacement string.
    /// <include path='items/Replacement/*' file='Doc/LJCGenDataXML.xml'/>
    public static function Replacement(string $name, ?string $value
      , int $indent) : ?string
    {
      $retValue = null;

      if ($value != null)
      {
        $builder = new LJCStringBuilder();
        $indent++;
        $builder->AppendLine("<Replacement>", $indent);
        $indent++;
        $builder->AppendTags("Name", $name, $indent);
        $builder->AppendTags("Value", $value, $indent);
        $indent--;
        $builder->AppendLine("</Replacement>", $indent);
        $retValue = $builder->ToString();
      }
      return $retValue;
    }

    // Creates the Section begin string.
    /// <include path='items/SectionBegin/*' file='Doc/LJCGenDataXML.xml'/>
    public static function SectionBegin(string $name, int $indent) : string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->AppendLine("<Section>", $indent);
      $indent++;
      $builder->AppendLine("<Begin/>", $indent);
      $builder->AppendTags("Name", $name, $indent);
      $builder->AppendLine("<Items>", $indent);
      $retValue = $builder->ToString();
      return $retValue;
    }

    // Creates the Section end string.
    /// <include path='items/SectionEnd/*' file='Doc/LJCGenDataXML.xml'/>
    public static function SectionEnd(int $indent) : string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->AppendLine("</Items>", $indent);				
      $indent--;
      $builder->AppendLine("</Section>", $indent);				
      $retValue = $builder->ToString();
      return $retValue;
    }

    // Creates an XML file head string.
    /// <param name="$fileName">The file name.</param>
    /// <returns>The head XML string.</returns>
    public static function XMLHead(string $xmlFileName, string $rootName = "Data"
      , string $years = "2022") : string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();

      $builder->AppendLine("<?xml version=\"1.0\"?>");
      $builder->Append("<!-- Copyright (c) Lester J. Clark &years -");
      $builder->AppendLine(" All Rights Reserved -->");
      if (null != $xmlFileName)
      {
        $builder->AppendLine("<!-- $xmlFileName -->");
      }
      $builder->Append("<$rootName xmlns:xsd=");
      $builder->AppendLine("'http://www.w3.org/2001/XMLSchema'");
      $builder->Append("  xmlns:xsi=");
      $builder->AppendLine("'http://www.w3.org/2001/XMLSchema-instance'>");
      $retValue = $builder->ToString();
      return $retValue;
    }
  }
?>