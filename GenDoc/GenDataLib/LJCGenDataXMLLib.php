<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCGenDataXMLLib.php
  declare(strict_types=1);
  // Must refer to exact same file everywhere in codeline.
  // Path: LJCPHPProjectsDev/GenDoc/GenDataLib
  include_once "../../LJCPHPCommon/LJCDebugLib.php";
  include_once "../../LJCPHPCommon/LJCTextLib.php";
  // LJCCommonLib: LJCCommon
  // LJCDebugLib: LJCDebug

  // Contains Classes to generate GenData XML.
  /// <include path='items/LJCGenDataXMLLib/*' file='Doc/LJCGenDataXMLLib.xml'/>
  /// LibName: LJCGenDataXMLLib
  // LJCGenDataXML

  // ***************
  // Provides methods for creating GenData XML text.
  // Static: ItemBegin(), ItemEnd(), Replacement()
  //   , SectionBegin(), SectionEnd(), XMLHead()
  /// <include path='items/LJCGenDataXML/*' file='Doc/LJCGenDataXML.xml'/>
  class LJCGenDataXML
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocGenDataXMLLib", "LJCGenDataXML"
        , false);
      $this->Debug->IncludePrivate = true;
    }

    // ---------------
    // Common GenData XML Methods - LJCGenDataXML

    // Creates the Item begin string.
    /// <include path='items/ItemBegin/*' file='Doc/LJCGenDataXML.xml'/>
    public static function ItemBegin(string $name, int $indent) : string
    {
      $debug = new LJCDebug("LJCGenDataXMLLib", "LJCGenDataXML"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("ItemBegin", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Line("<Item>", $indent);
      $indent++;
      $builder->Tags("Name", $name, $indent);
      $builder->Line("<Replacements>", $indent);
      $retValue = $builder->ToString();

      $debug->EndMethod($enabled);
      return $retValue;
    }

    // Creates the Item end string.
    /// <include path='items/ItemEnd/*' file='Doc/LJCGenDataXML.xml'/>
    public static function ItemEnd(int $indent) : string
    {
      $debug = new LJCDebug("LJCGenDataXMLLib", "LJCGenDataXML"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("ItemEnd", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Line("</Replacements>", $indent);
      $indent--;
      $builder->Line("</Item>", $indent);		
      $retValue = $builder->ToString();

      $debug->EndMethod($enabled);
      return $retValue;
    }

    // Creates the Replacement string.
    /// <include path='items/Replacement/*' file='Doc/LJCGenDataXML.xml'/>
    public static function Replacement(string $name, ?string $value
      , int $indent) : ?string
    {
      $debug = new LJCDebug("LJCGenDataXMLLib", "LJCGenDataXML"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("Replacement", $enabled);
      $retValue = null;

      if ($value != null)
      {
        $builder = new LJCStringBuilder();
        $indent++;
        $builder->Line("<Replacement>", $indent);
        $indent++;
        $builder->Tags("Name", $name, $indent);
        $builder->Tags("Value", $value, $indent);
        $indent--;
        $builder->Line("</Replacement>", $indent);
        $retValue = $builder->ToString();
      }

      $debug->EndMethod($enabled);
      return $retValue;
    }

    // Creates the Section begin string.
    /// <include path='items/SectionBegin/*' file='Doc/LJCGenDataXML.xml'/>
    public static function SectionBegin(string $name, int $indent) : string
    {
      $debug = new LJCDebug("LJCGenDataXMLLib", "LJCGenDataXML"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("SectionBegin", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Line("<Section>", $indent);
      $indent++;
      $builder->Line("<Begin/>", $indent);
      $builder->Tags("Name", $name, $indent);
      $builder->Line("<Items>", $indent);
      $retValue = $builder->ToString();

      $debug->EndMethod($enabled);
      return $retValue;
    }

    // Creates the Section end string.
    /// <include path='items/SectionEnd/*' file='Doc/LJCGenDataXML.xml'/>
    public static function SectionEnd(int $indent) : string
    {
      $debug = new LJCDebug("LJCGenDataXMLLib", "LJCGenDataXML"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("SectionEnd", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Line("</Items>", $indent);				
      $indent--;
      $builder->Line("</Section>", $indent);				
      $retValue = $builder->ToString();

      $debug->EndMethod($enabled);
      return $retValue;
    }

    // Creates an XML file head string.
    /// <param name="$fileName">The file name.</param>
    /// <returns>The head XML string.</returns>
    public static function XMLHead(string $xmlFileName, string $rootName = "Data"
      , string $years = "2022") : string
    {
      $debug = new LJCDebug("LJCGenDataXMLLib", "LJCGenDataXML"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("XMLHead", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();

      $builder->Line("<?xml version=\"1.0\"?>");
      $builder->Text("<!-- Copyright (c) Lester J. Clark &years -");
      $builder->Line(" All Rights Reserved -->");
      if (null != $xmlFileName)
      {
        $builder->Line("<!-- $xmlFileName -->");
      }
      $builder->Text("<$rootName xmlns:xsd=");
      $builder->Line("'http://www.w3.org/2001/XMLSchema'");
      $builder->Text("  xmlns:xsi=");
      $builder->Line("'http://www.w3.org/2001/XMLSchema-instance'>");
      $retValue = $builder->ToString();

      $debug->EndMethod($enabled);
      return $retValue;
    }
  }
?>