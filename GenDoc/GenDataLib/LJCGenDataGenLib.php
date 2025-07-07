<?php
  // LJCGenDataGenLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonFileLib.php";
  include_once "$prefix/GenDoc/GenCodeDoc/LJCGenDocConfigLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextFileLib.php";
  include_once "$prefix/GenTextLib/LJCGenTextLib.php";
  include_once "$prefix/GenDoc/GenDataLib/LJCGenDataXMLLib.php";
  // LJCDebugLib: LJCDebug
  // LJCCommonLib: LJC
  // LJCCommonFileLib: LJCCommonFile
  // LJCGenDocConfigLib: LJCGenDocConfig
  // LJCGenTextLib: LJCStringBuilder
  // LJCGenTextFileLib: LJCFileWriter
  // LJCGenDataXMLLib:LJCGenDataXML

  // Contains classes to create GenData XML and HTML Doc from DocData XML.
  /// <include path='items/LJCGenDataGenLib/*' file='Doc/LJCGenDataGenLib.xml'/>
  /// LibName: LJCGenDataGenLib
  // LJCGenDataGen

  // Calling Code
  // GenCodeDocLib.php

  // Main Call Tree
  // SerializeLib() public
  //   SerializeLibXML()
  //     SerializeLibClass()
  //   SerializeClasses()
  //     SerializeClass()
  //       SerializeClassXML()
  //         SerializeClassMethod()
  //         SerializeClassProperty()
  //       SerializeMethods()
  //         SerializeMethod()
  //           SerializeMethodXML();
  //             SerializeMethodParam()
  //       SerializeProperties()
  //         SerializeProperty()
  //           SerializePropertyXML()

  // ***************
  // Provides methods to generate GenData XML files and HTML Doc.
  // Public: CreateLibXMLString()
  /// <include path='items/LJCGenDataGen/*' file='Doc/LJCGenDataGen.xml'/>
  class LJCGenDataGen
  {
    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenDataGenLib", "LJCGenDataGen"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;
    }

    // Sets the GenDoc config.
    public function SetConfig(LJCGenDocConfig $config)
    {
      $this->HTMLPath = "../../../WebSitesDev/CodeDoc/LJCPHPCodeDoc/HTML";
      $this->GenDocConfig = $config;
      if (LJC::HasValue($config->OutputPath))
      {
        $this->HTMLPath = $config->OutputPath;
      }
      LJCCommonFile::MkDir($this->HTMLPath);
    }
    
    // ---------------
    // Lib Methods - LJCGenDataGen

    // Creates a Lib GenData XML string and optional file.
    /// <include path='items/SerializeLib/*' file='Doc/LJCGenDataGen.xml'/>
    public function SerializeLib(string $docXML, string $codeFileSpec) : string
    {
      // GenCodeDoc.CreateFilePages()
      $enabled = false;
      $this->Debug->BeginMethod("SerializeLib", $enabled);
      $retLibGenXML = null;

      // GenData XML file name same as source file with .xml extension.
      $fileName = LJC::GetFileName($codeFileSpec) . ".xml";
      $docDataFile = LJCDocDataFile::DeserializeString($docXML);
      $retLibGenXML = $this->SerializeLibXML($docDataFile, $fileName);
      $writeGenDataXML = $this->WriteLibGenXML($retLibGenXML, $codeFileSpec
        , $fileName);

      // Write HTML file.
      if ($retLibGenXML != null)
      {
        $htmlText = $this->GetHTMLText($retLibGenXML, "LibTemplate.html");
        $htmlPath = $this->HTMLPath;
        $htmlFileName = LJC::GetFileName($codeFileSpec);
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
        $this->SerializeClasses($docDataFile, $writeGenDataXML, $htmlPath);
      }

      $this->Debug->EndMethod($enabled);
      return $retLibGenXML;
    }

    // Creates a Lib GenData XML string.
    // <include path='items/SerializeLibXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeLibXML(LJCDocDataFile $docDataFile
      , string $fileName)	: ?string
    {
      // SerializeLib()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeLibXML", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->Line("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Main", $indent);
      $builder->Text($value);

      // Items
      $indent++;
      $builder->Line("<Items>", $indent);
      $indent++;
      $value = LJCGenDataXML::ItemBegin("Main", $indent);
      $builder->Text($value);
      $indent++;

      // Replacements
      $text = $docDataFile->Summary;
      $value = LJCGenDataXML::Replacement("_FileSummary_", $text, $indent);
      $builder->Text($value);

      $classes = $docDataFile->Classes;
      if ($classes != null)
      {
        $text = (string)count($classes);
        $value = LJCGenDataXML::Replacement("_ItemCount_", $text, $indent);
        $builder->Text($value);
      }

      $text = $docDataFile->Name;
      $value = LJCGenDataXML::Replacement("_LibName_", $text, $indent);
      $builder->Text($value);

      $text = "LJCPHPCodeDoc.html";
      $value = LJCGenDataXML::Replacement("_ProjectListFile_", $text, $indent);
      $builder->Text($value);

      $text = $docDataFile->Remarks;
      $value = LJCGenDataXML::Replacement("_Remarks_", $text, $indent);
      $builder->Text($value);

      // Items End Lines				
      $value = LJCGenDataXML::ItemEnd($indent);
      $builder->Text($value);

      // Section End Lines
      $indent -= 2;
      $value = LJCGenDataXML::SectionEnd($indent);
      $builder->Text($value);

      // Class Section			
      $value = $this->SerializeLibClass($docDataFile);
      $builder->Text($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->Line("</Sections>", $indent);
      $indent--;
      $builder->Line("</Data>", $indent);
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // SerializeLibXML()

    // Creates a Lib Class section GenData XML string.
    // <include path='items/SerializeLibClass/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeLibClass(LJCDocDataFile $docDataFile) : ?string
    {
      // SerializeLib()-SerializeLibXML()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeLibClass", $enabled);
      $retValue = null;

      $classes = $docDataFile->Classes;
      if ($classes != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Class", $indent);
        $builder->Text($value);

        // Items
        $indent++;
        $builder->Line("<Items>", $indent);
        $indent++;
        foreach ($classes as $class)
        {
          // Items Begin Lines
          $text = $class->Name;
          $value = LJCGenDataXML::ItemBegin($text, $indent);
          $builder->Text($value);
          $indent++;

          // Replacements
          $text = $class->Name;
          $value = LJCGenDataXML::Replacement("_ClassName_", $text, $indent);
          $builder->Text($value);

          $text = $class->Summary;
          $value = LJCGenDataXML::Replacement("_ClassSummary_", $text, $indent);
          $builder->Text($value);

          // Items End Lines				
          $value = LJCGenDataXML::ItemEnd($indent);
          $builder->Text($value);
          $indent--;
        }

        // Section End Lines
        $indent--;
        $value = LJCGenDataXML::SectionEnd($indent);
        $builder->Text($value);
        $retValue = $builder->ToString();
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SerializeLibClass()
    
    // ---------------
    // Class Methods - LJCGenDataGen

    // Creates a Class GenData XML string and optional file.
    // <include path='items/SerializeClass/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeClass(LJCDocDataClass $class, string $libName
      , bool $writeGenDataXML = true, string $outputPath = null)	: string
    {
      // SerializeLib()-SerializeClasses()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeClass", $enabled);
      $retClassGenXML = null;

      $fileName = $class->Name . ".xml";
      $retClassGenXML = $this->SerializeClassXML($class, $fileName, $libName);
      $writeGenDataXML = $this->WriteClassGenXML($retClassGenXML, $class
        , $fileName);

      // Write HTML file.
      if ($retClassGenXML != null)
      {
        $htmlText = $this->GetHTMLText($retClassGenXML, "ClassTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }

      $this->SerializeMethods($class, $libName, $writeGenDataXML);
      $this->SerializeProperties($class, $libName, $writeGenDataXML);

      $this->Debug->EndMethod($enabled);
      return $retClassGenXML;
    } // SerializeClass()

    // Creates the Class GenData XML strings and optionally files.
    // <include path='items/SerializeClasses/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeClasses(LJCDocDataFile $docDataFile
      , bool $writeGenDataXML = true, string $outputPath = null) : void
    {
      // SerializeLib()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeClasses", $enabled);

      $libName = $docDataFile->Name;
      $classes = $docDataFile->Classes;
      if ($classes != null)
      {
        foreach ($classes as $class)
        {
          $this->SerializeClass($class, $libName, $writeGenDataXML
            , $outputPath);
        }
      }

      $this->Debug->EndMethod($enabled);
    } // SerializeClasses()

    // Creates a Class Methods section GenData XML string.
    // <include path='items/SerializeClassMethod/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeClassMethod(LJCDocDataClass $class) : ?string
    {
      // SerializeLib()-SerializeClassesXML()-SerializeClass()
      //   -SerializeClassXML()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeClassMethod", $enabled);
      $retValue = null;

      $methods = $class->Methods;
      if ($methods != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Function", $indent);
        $builder->Text($value);

        // Items
        $indent++;
        $builder->Line("<Items>", $indent);
        $indent++;
        foreach ($methods as $method)
        {
          // Items Begin Lines
          $text = $method->Name;
          $parentGroup = $method->ParentGroup;
          $value = LJCGenDataXML::ItemBegin($text, $indent, $parentGroup);
          $builder->Text($value);
          $indent++;

          // Replacements
          $text = $method->Name;
          $value = LJCGenDataXML::Replacement("_FunctionName_", $text, $indent);
          $builder->Text($value);

          $text = $method->Summary;
          $value = LJCGenDataXML::Replacement("_FunctionSummary_", $text, $indent);
          $builder->Text($value);

          // Items End Lines				
          $value = LJCGenDataXML::ItemEnd($indent);
          $builder->Text($value);
          $indent--;
        }

        // Section End Lines
        $indent--;
        $value = LJCGenDataXML::SectionEnd($indent);
        $builder->Text($value);
        $retValue = $builder->ToString();
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // SerializeClassMethod()

    // Creates a Class Properties section GenData XML string.
    private function SerializeClassProperty(LJCDocDataClass $class) : ?string
    {
      // SerializeLib()-SerializeClassesXML()-SerializeClass()
      //   -SerializeClassXML()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeClassProperty", $enabled);
      $retValue = null;

      $properties = $class->Properties;
      if ($properties != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Property", $indent);
        $builder->Text($value);

        // Items
        $indent++;
        $builder->Line("<Items>", $indent);
        $indent++;
        foreach ($properties as $property)
        {
          // Items Begin Lines
          $text = $property->Name;
          $value = LJCGenDataXML::ItemBegin($text, $indent);
          $builder->Text($value);
          $indent++;

          // Replacements
          $text = $property->Name;
          $value = LJCGenDataXML::Replacement("_PropertyName_", $text, $indent);
          $builder->Text($value);

          $text = $property->Summary;
          $value = LJCGenDataXML::Replacement("_PropertySummary_", $text, $indent);
          $builder->Text($value);

          // Items End Lines				
          $value = LJCGenDataXML::ItemEnd($indent);
          $builder->Text($value);
          $indent--;
        }

        // Section End Lines
        $indent--;
        $value = LJCGenDataXML::SectionEnd($indent);
        $builder->Text($value);
        $retValue = $builder->ToString();
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SerializeClassProperty()

    // Creates a Class GenData XML string.
    // <include path='items/SerializeClassXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeClassXML(LJCDocDataClass $class
      , string $fileName, string $libName) : string
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeClassXML", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->Line("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Class", $indent);
      $builder->Text($value);

      // *** Begin *** Add Named Groups
      if ($class->Groups != null)
      {
        $first = true;
        foreach ($class->Groups as $key => $value)
        {
          $text = "";
          if (!$first)
          {
            $text .= "\r\n";
          }
          $first = false;
          $text .= "<group name=\"{$key}\">{$value}</group>";
          $builder->Text($text);
        }
      }
      // *** End   ***

      // Items
      $indent++;
      $builder->Line("<Items>", $indent);
      $indent++;
      $value = LJCGenDataXML::ItemBegin("Class", $indent);
      $builder->Text($value);
      $indent++;

      $text = htmlspecialchars($class->Code);
      $value = LJCGenDataXML::Replacement("_Code_", $text, $indent);
      $builder->Text($value);

      $value = LJCGenDataXML::Replacement("_LibName_", $libName, $indent);
      $builder->Text($value);

      // Replacements
      $text = $class->Name;
      $value = LJCGenDataXML::Replacement("_ClassName_", $text, $indent);
      $builder->Text($value);

      $text = $class->Summary;
      $value = LJCGenDataXML::Replacement("_ClassSummary_", $text, $indent);
      $builder->Text($value);

      $properties = $class->Properties;
      if ($properties != null)
      {
        if (count($properties) > 0)
        {
          $value = LJCGenDataXML::Replacement("_HasProperties_", (string)true
            , $indent);
          $builder->Text($value);
        }
      }

      $methods = $class->Methods;
      if ($methods != null)
      {
        $text = (string)count($methods);
        $value = LJCGenDataXML::Replacement("_ItemCount_", $text, $indent);
        $builder->Text($value);
      }

      $text = $class->Remarks;
      $value = LJCGenDataXML::Replacement("_Remarks_", $text, $indent);
      $builder->Text($value);

      $text = "class $class->Name";
      $value = LJCGenDataXML::Replacement("_Syntax_", $text, $indent);
      $builder->Text($value);

      // Items End Lines				
      $value = LJCGenDataXML::ItemEnd($indent);
      $builder->Text($value);

      // Section End Lines
      $indent -= 2;
      $value = LJCGenDataXML::SectionEnd($indent);
      $builder->Text($value);

      // Method Section			
      $value = $this->SerializeClassMethod($class);
      $builder->Text($value);

      // Properties Section			
      $value = $this->SerializeClassProperty($class);
      $builder->Text($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->Line("</Sections>", $indent);
      $indent--;
      $builder->Line("</Data>", $indent);
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // SerializeClassXML()

    // ---------------
    // Method Methods - LJCGenDataGen

    // Creates a Method GenData XML string and optional file.
    // <include path='items/SerializeMethod/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeMethod(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $libName, bool $writeGenDataXML = false
      , string $outputPath = null) : string
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      //   -SerializeMethods()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeMethod", $enabled);
      $retValue = null;

      $fileName = $method->Name;
      $retValue = $this->SerializeMethodXML($class, $method, $fileName
        , $libName);
      $this->WriteMethodGenXML($retValue, $class, $method, $fileName);

      // Write HTML file.
      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "FunctionTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name$method->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SerializeMethod()

    // Creates the Method GenData class XML strings and optionally files.
    // <include path='items/SerializeMethods/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeMethods(LJCDocDataClass $class, string $libName
      , bool $writeGenDataXML = true, string $outputPath = null) : void
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeMethods", $enabled);

      $methods = $class->Methods;
      if ($methods != null)
      {
        foreach ($methods as $method)
        {
          $this->SerializeMethod($class, $method, $libName, $writeGenDataXML
            , $outputPath);
        }
      }

      $this->Debug->EndMethod($enabled);
    } // SerializeMethods()

    // <summary>Creates a Method Params section GenData XML string.</summary>
    // <param name="$method">The Method object.</param>
    private function SerializeMethodParam(LJCDocDataMethod $method) : ?string
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      //   -SerializeMethods()-SerializeMethod()-SerializeMethodXML()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeMethodParam", $enabled);
      $retValue = null;

      $params = $method->Params;
      if ($params != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Parameters", $indent);
        $builder->Text($value);

        // Items
        $indent++;
        $builder->Line("<Items>", $indent);
        $indent++;
        foreach ($params as $param)
        {
          // Items Begin Lines
          $text = $param->Name;
          $value = LJCGenDataXML::ItemBegin($text, $indent);
          $builder->Text($value);
          $indent++;

          // Replacements
          $text = $param->Name;
          $value = LJCGenDataXML::Replacement("_ParamName_", $text, $indent);
          $builder->Text($value);

          $text = $param->Summary;
          $value = LJCGenDataXML::Replacement("_ParamSummary_", $text, $indent);
          $builder->Text($value);

          // Items End Lines				
          $value = LJCGenDataXML::ItemEnd($indent);
          $builder->Text($value);
          $indent--;
        }

        // Section End Lines
        $indent--;
        $value = LJCGenDataXML::SectionEnd($indent);
        $builder->Text($value);
        $retValue = $builder->ToString();
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SerializeMethodParam()

    // Creates a Method GenData XML string.
    // <include path='items/SerializeMethodXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeMethodXML(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $fileName, string $libName) : ?string
    {
      // CreateLibXMLString()-SerializeClasses()-SerializeClass()
      //   -SerializeMethods()-SerializeMethod()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeMethodXML", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->Line("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Function", $indent);
      $builder->Text($value);

      // Items
      $indent++;
      $builder->Line("<Items>", $indent);
      $indent++;
      $text = $method->Name;
      $parentGroup = $method->ParentGroup;
      $value = LJCGenDataXML::ItemBegin($text, $indent, $parentGroup);
      $builder->Text($value);
      $indent++;

      // Replacements
      $text = $method->Name;
      $value = LJCGenDataXML::Replacement("_FunctionName_", $text, $indent);
      $builder->Text($value);

      $text = $method->Summary;
      $value = LJCGenDataXML::Replacement("_FunctionSummary_", $text, $indent);
      $builder->Text($value);

      $text = $class->Name;
      $value = LJCGenDataXML::Replacement("_ClassName_", $text, $indent);
      $builder->Text($value);

      $text = $method->Code;
      $value = LJCGenDataXML::Replacement("_Code_", $text, $indent);
      $builder->Text($value);

      $params = $method->Params;
      if ($params != null)
      {
        if (count($params) > 0)
        {
          $value = LJCGenDataXML::Replacement("_HasParameters_", (string)true
            , $indent);
          $builder->Text($value);
        }
      }

      $value = LJCGenDataXML::Replacement("_LibName_", $libName, $indent);
      $builder->Text($value);

      $text = $method->Remarks;
      $value = LJCGenDataXML::Replacement("_Remarks_", $text, $indent);
      $builder->Text($value);

      $text = $method->Returns;
      $value = LJCGenDataXML::Replacement("_Returns_", $text, $indent);
      $builder->Text($value);

      $text = htmlspecialchars($method->Syntax);
      $value = LJCGenDataXML::Replacement("_Syntax_", $text, $indent);
      $builder->Text($value);

      // Items End Lines				
      $value = LJCGenDataXML::ItemEnd($indent);
      $builder->Text($value);

      // Section End Lines
      $indent -= 2;
      $value = LJCGenDataXML::SectionEnd($indent);
      $builder->Text($value);

      // Parameters Section			
      $value = $this->SerializeMethodParam($method);
      $builder->Text($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->Line("</Sections>", $indent);
      $indent--;
      $builder->Line("</Data>", $indent);
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // SerializeMethodXML()

    // ---------------
    // Property Methods - LJCGenDataGen

    // Creates the Property GenData class XML strings and optionally files.
    // <include path='items/SerializeProperties/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeProperties(LJCDocDataClass $class, string $libName
      , bool $writeGenDataXML = true, string $outputPath = null) : void
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeProperties", $enabled);

      $properties = $class->Properties;
      if ($properties != null)
      {
        foreach ($properties as $property)
        {
          $this->SerializeProperty($class, $property, $libName, $writeGenDataXML
            , $outputPath);
        }
      }

      $this->Debug->EndMethod($enabled);
    } // SerializeProperties()

    // Creates a Property GenData XML string and optional file.
    // <include path='items/SerializeProperty/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializeProperty(LJCDocDataClass $class
      , LJCDocDataProperty $property, string $libName
      , bool $writeGenDataXML = false, string $outputPath = null) : string
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      //   -SerializeProperties()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializeProperty", $enabled);

      $retValue = null;

      //$fileName = $property->Name . ".xml";
      $fileName = $property->Name;
      $retValue = $this->SerializePropertyXML($class, $property, $fileName
        , $libName);
      $this->WritePropertyGenXML($retValue, $class, $property, $fileName);

      // Write HTML file.
      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "PropertyTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name$property->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SerializeProperty()

    // Creates a Property GenData XML string.
    // <include path='items/SerializePropertyXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function SerializePropertyXML(LJCDocDataClass $class
      , LJCDocDataProperty $property, string $fileName, string $libName)
        : ?string
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      //   -SerializeProperty()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SerializePropertyXML", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->Line("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Property", $indent);
      $builder->Text($value);

      // Items
      $indent++;
      $builder->Line("<Items>", $indent);
      $indent++;
      $text = $property->Name;
      $value = LJCGenDataXML::ItemBegin($text, $indent);
      $builder->Text($value);
      $indent++;

      // Replacements
      $text = $class->Name;
      $value = LJCGenDataXML::Replacement("_ClassName_", $text, $indent);
      $builder->Text($value);

      $value = LJCGenDataXML::Replacement("_LibName_", $libName, $indent);
      $builder->Text($value);

      $text = $property->Name;
      $value = LJCGenDataXML::Replacement("_PropertyName_", $text, $indent);
      $builder->Text($value);

      $text = $property->Summary;
      $value = LJCGenDataXML::Replacement("_PropertySummary_", $text, $indent);
      $builder->Text($value);

      $text = $property->Remarks;
      $value = LJCGenDataXML::Replacement("_Remarks_", $text, $indent);
      $builder->Text($value);

      $text = $property->Returns;
      $value = LJCGenDataXML::Replacement("_Returns_", $text, $indent);
      $builder->Text($value);

      $text = $property->Syntax;
      $value = LJCGenDataXML::Replacement("_Syntax_", $text, $indent);
      $builder->Text($value);

      // Items End Lines				
      $value = LJCGenDataXML::ItemEnd($indent);
      $builder->Text($value);

      // Section End Lines
      $indent -= 2;
      $value = LJCGenDataXML::SectionEnd($indent);
      $builder->Text($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->Line("</Sections>", $indent);
      $indent--;
      $builder->Line("</Data>", $indent);
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SerializePropertyXML()

    // ---------------
    // Private Methods - LJCGenDataGen

    // Generate the HTML text.
    private function GetHTMLText(string $sectionsXMLString
      , string $templateFileName) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetHTMLText", $enabled);
      $retValue = null;

      if ($sectionsXMLString != null)
      {
        // Relative to calling program directory.
        // Path: LJCPHPProjectsDev/GenDoc/GenCodeDocLib
        $templateFileSpec = "../GenDataLib/Templates/$templateFileName";
        $sections = LJCSections::DeserializeString($sectionsXMLString);
        $genText = new LJCGenText();
        $retValue = $genText->ProcessTemplate($templateFileSpec, $sections);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetHTMLText()

    // Write the HTML file.
    private function WriteHTML(string $htmlText, string $htmlPath
      , string $fileName) : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("WriteHTML", $enabled);

      LJCCommonFile::MkDir($htmlPath);
      $fileSpec = "$htmlPath/$fileName" . ".html";
      LJCFileWriter::WriteFile($htmlText, $fileSpec);

      $this->Debug->EndMethod($enabled);
    } // WriteHTML()
    
    // ---------------
    // Private Write XML Methods - LJCGenDataGen

    // Creates a Class GenData XML output file spec.
    // <include path='items/OutputClassSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function ClassGenXMLSpec(LJCDocDataClass $class
      , string $outputPath = null) : string
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ClassGenXMLSpec", $enabled);
      $retValue = null;

      $name = $class->Name;
      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData/$name";
      }
      LJCCommonFile::MkDir($outputPath);
      $fileName = LJC::GetFileName($name) . ".xml";
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // Creates a Lib GenData XML output file spec.
    // <include path='items/OutputLibSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function LibGenXMLSpec(string $codeFileSpec
      , string $outputPath = null) : string
    {
      // WriteLibGenXMLFile()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("LibGenXMLSpec", $enabled);
      $retValue = null;

      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData";
      }
      LJCCommonFile::MkDir($outputPath);
      $fileName = LJC::GetFileName($codeFileSpec) . ".xml";
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }
  
    // Creates a Method GenData XML output file spec.
    // <include path='items/OutputMethodSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function MethodGenXMLSpec(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $outputPath = null) : string
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      //   -SerializeMethods()-SerializeMethod()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("MethodGenXMLSpec", $enabled);
      $retValue = null;

      $name = $class->Name;
      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData/$name";
      }
      LJCCommonFile::MkDir($outputPath);
      $fileName = $method->Name . ".xml";
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }
  
    // Creates a Property GenData XML output file spec.
    // <include path='items/OutputPropertySpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function PropertyGenXMLSpec(LJCDocDataClass $class
      , LJCDocDataProperty $property, string $outputPath = null) : string
    {
      // CreateLibXMLString()-SerializeClasses()-SerializeClass()
      //   -SerializeProperty()
      $enabled = true;
      $this->Debug->BeginPrivateMethod("PropertyGenXMLSpec", $enabled);
      $retValue = null;

      $name = $class->Name;
      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData/$name";
      }
      LJCCommonFile::MkDir($outputPath);
      $fileName = $property->Name . ".xml";
      $this->Debug->Write(__line__." fileName = {$fileName}");
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // Writes the LibGenXML file.
    private function WriteLibGenXML(string $libGenXML, string $codeFileSpec
      , string $fileName)
    {
      // GenCodeDoc.CreateFilePages()-SerializeLib()
      $retValue = false;

      $writeGenDataXML = $this->GenDocConfig->WriteGenDataXML;
      // *** Begin *** Debug Output
      if ("LJCHTMLTableLib.xml" == $fileName)
      {
        $writeGenDataXML = true;
      }
      // *** End   ***

      if ($writeGenDataXML
        && $libGenXML != null)
      {
        $retValue = true;
        $genDataXMLPath = $this->GenDocConfig->GenDataXMLPath;
        $fileSpec = $this->LibGenXMLSpec($codeFileSpec, $genDataXMLPath);
        LJCFileWriter::WriteFile($libGenXML, $fileSpec);
      }
      return $retValue;
    }

    // Writes the ClassGenXML file.
    private function WriteClassGenXML(string $classGenXML
      , LJCDocDataClass $class, string $fileName)
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      $retValue = false;

      $writeGenDataXML = $this->GenDocConfig->WriteGenDataXML;
      // *** Begin *** Debug Output
      if ("LJCHTMLTable.xml" == $fileName)
      {
        $writeGenDataXML = true;
      }
      // *** End   ***

      if ($writeGenDataXML
        && $classGenXML != null)
      {
        $retValue = true;
        $genDataXMLPath = $this->GenDocConfig->GenDataXMLPath;
        $fileSpec = $this->ClassGenXMLSpec($class, $genDataXMLPath);
        LJCFileWriter::WriteFile($classGenXML, $fileSpec);
      }
      return $retValue;
    }

    // Writes the MethodGenXML file.
    private function WriteMethodGenXML(string $methodGenXML
      , LJCDocDataClass $class, LJCDocDataMethod $method, string $fileName)
    {
      // SerializeLib()-SerializeClasses()-SerializeClass()
      //   -SerializeMethods()-SerializeMethod()
      $retValue = false;

      $writeGenDataXML = $this->GenDocConfig->WriteGenDataXML;
      // *** Begin *** Debug Output
      if ("ArrayArrayHTML" == $fileName)
      {
        $writeGenDataXML = true;
      }
      // *** End   ***

      if ($writeGenDataXML
        && $methodGenXML != null)
      {
        $retValue = true;
        $genDataXMLPath = $this->GenDocConfig->GenDataXMLPath;
        $fileSpec = $this->MethodGenXMLSpec($class, $method, $genDataXMLPath);
        LJCFileWriter::WriteFile($methodGenXML, $fileSpec);
      }
      return $retValue;
    }

    // Writes the PropertyGenXML file.
    private function WritePropertyGenXML(string $propertyGenXML
      , LJCDocDataClass $class, LJCDocDataProperty $property, string $fileName)
    {
      // CreateLibXMLString()-SerializeClasses()-SerializeClass()
      //   -SerializeProperty()
      $retValue = false;

      $writeGenDataXML = $this->GenDocConfig->WriteGenDataXML;
      // *** Begin *** Debug Output
      //if ("ArrayArrayHTML" == $fileName)
      //{
        $writeGenDataXML = true;
        $this->Debug->Write(__line__." fileName = {$fileName}");
      //}
      // *** End   ***

      if ($writeGenDataXML
        && $propertyGenXML != null)
      {
        $retValue = true;
        $genDataXMLPath = $this->GenDocConfig->GenDataXMLPath;
        $fileSpec = $this->PropertyGenXMLSpec($class, $property
          , $genDataXMLPath);
        LJCFileWriter::WriteFile($propertyGenXML, $fileSpec);
      }
      return $retValue;
    }

    // ---------------
    // Properties - LJCDocDataGen

    // The path for HTML output.
    public string $HTMLPath;

    // The GenDoc configuration.
    private LJCGenDocConfig $GenDocConfig;
  }
  ?>