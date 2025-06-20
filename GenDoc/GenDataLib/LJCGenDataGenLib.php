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
  // LJCCommonLib: LJCCommon
  // LJCGenDocConfigLib: LJCGenDocConfig
  // LJCGenTextLib: LJCStringBuilder
  // LJCGenTextFileLib: LJCFileWriter
  // LJCGenDataXMLLib:LJCGenDataXML
  // LJCDebugLib: LJCDebug

  // Contains classes to create GenData and HTML Doc from DocData.
  /// <include path='items/LJCGenDataGenLib/*' file='Doc/LJCGenDataGenLib.xml'/>
  /// LibName: LJCGenDataGenLib
  // LJCGenDataGen

  // Calling Code
  // GenCodeDocLib.php

  // Main Call Tree
  // CreateLibXML() public
  //   CreateLibString()
  //     CreateLibClassString()
  //   OutputLibSpec() public
  //
  //   CreateClassesXML()
  //     CreateClassXML()
  //       CreateClassString()
  //         CreateClassMethodsString()
  //       OutputClassSpec() public
  //
  //       CreateMethodsXML()
  //         CreateMethodXML()
  //           CreateMethodString();
  //           OutputMethodSpec()
  //
  //       CreatePropertiesXML()
  //         CreatePropertyXML()
  //           CreatePropertyString()
  //           OutputPropertySpec()

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
      $this->Debug = new LJCDebug("LJCGenDataGenLib", "LJCGenDataGen"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    }

    // Sets the GenDoc config.
    public function SetConfig(LJCGenDocConfig $config)
    {
      $this->HTMLPath = "../../../WebSitesDev/CodeDoc/LJCPHPCodeDoc/HTML";
      $this->GenDocConfig = $config;
      if (LJCCommon::HasValue($config->OutputPath))
      {
        $this->HTMLPath = $config->OutputPath;
      }
      LJCCommonFile::MkDir($this->HTMLPath);
    }
    
    // ---------------
    // Lib Methods - LJCGenDataGen

    // Creates a Lib GenData XML string and optional file.
    /// <include path='items/CreateLibXMLString/*' file='Doc/LJCGenDataGen.xml'/>
    //public function CreateLibXMLString(string $docXMLString, string $codeFileSpec
    //  , bool $writeGenDataXML = false, string $outputPath = null) : string
    public function CreateLibXMLString(string $docXMLString
      , string $codeFileSpec) : string
    {
      $enabled = false;
      $this->Debug->BeginMethod("CreateLibXMLString", $enabled);
      $retValue = null;

      // GenData XML file name same as source file with .xml extension.
      $fileName = LJCCommon::GetFileName($codeFileSpec) . ".xml";
      // Start Testing
      $docDataFile = LJCDocDataFile::DeserializeString($docXMLString);
      $retValue = $this->CreateLibString($docDataFile, $fileName);

      // Write XML data.
      $writeGenDataXML = $this->GenDocConfig->WriteGenDataXML;
      if ("LJCDocDataGenLib.xml" == $fileName
        || "GenCodeDocLib.xml" == $fileName)
      {
        $writeGenDataXML = true;
      }
      if ($writeGenDataXML && $retValue != null)
      {
        $genDataXMLPath = $this->GenDocConfig->GenDataXMLPath;
        $outputFileSpec = $this->OutputLibSpec($codeFileSpec, $genDataXMLPath);
        LJCFileWriter::WriteFile($retValue, $outputFileSpec);
      }

      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "LibTemplate.html");
        $htmlPath = $this->HTMLPath;
        $htmlFileName = LJCCommon::GetFileName($codeFileSpec);
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
        // *** Add ***
        $this->CreateClassesXML($docDataFile, $writeGenDataXML, $htmlPath);
      }
      // *** Move Up ***
      //$this->CreateClassesXML($docDataFile, $writeGenDataXML, $this->HTMLPath);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // Creates a Lib GenData XML string.
    // <include path='items/CreateLibString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateLibString(LJCDocDataFile $docDataFile
      , string $fileName)	: ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateLibString", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->Line("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Main", $indent);
      $builder->Text($value);

      // Items Begin Lines
      $indent += 2;
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
      $value = $this->CreateLibClassString($docDataFile);
      $builder->Text($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->Line("</Sections>", $indent);
      $indent--;
      $builder->Line("</Data>", $indent);
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // CreateLibString()

    // Creates a Lib Class section GenData XML string.
    // <include path='items/CreateLibClassString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateLibClassString(LJCDocDataFile $docDataFile) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateLibClassString", $enabled);
      $retValue = null;

      $classes = $docDataFile->Classes;
      if ($classes != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Class", $indent);
        $builder->Text($value);

        $indent += 2;
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
    }

    // Creates a Lib GenData XML output file spec.
    // <include path='items/OutputLibSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function OutputLibSpec(string $codeFileSpec
      , string $outputPath = null) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("OutputLibSpec", $enabled);
      $retValue = null;

      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData";
      }
      LJCCommonFile::MkDir($outputPath);
      $fileName = LJCCommon::GetFileName($codeFileSpec) . ".xml";
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }
    
    // ---------------
    // Class Methods - LJCGenDataGen

    // Creates a Class GenData XML string.
    // <include path='items/CreateClassString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateClassString(LJCDocDataClass $class
      , string $fileName, string $libName) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateClassString", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->Line("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Class", $indent);
      $builder->Text($value);

      // Items Begin Lines
      $indent += 2;
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
      $value = $this->CreateClassMethodString($class);
      $builder->Text($value);

      // Properties Section			
      $value = $this->CreateClassPropertyString($class);
      $builder->Text($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->Line("</Sections>", $indent);
      $indent--;
      $builder->Line("</Data>", $indent);
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // CreateClassString()

    // Creates a Class Methods section GenData XML string.
    // <include path='items/CreateClassMethodString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateClassMethodString(LJCDocDataClass $class) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateClassMethhodString", $enabled);
      $retValue = null;

      $methods = $class->Methods;
      if ($methods != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Function", $indent);
        $builder->Text($value);

        $indent += 2;
        foreach ($methods as $method)
        {
          // Items Begin Lines
          $text = $method->Name;
          $value = LJCGenDataXML::ItemBegin($text, $indent);
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
    }  // CreateClassMethodString()

    // Creates a Class Methods section GenData XML string.
    private function CreateClassPropertyString(LJCDocDataClass $class) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateClassPropertyString", $enabled);
      $retValue = null;

      $properties = $class->Properties;
      if ($properties != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Property", $indent);
        $builder->Text($value);

        $indent += 2;
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
    }

    // Creates the Class GenData XML strings and optionally files.
    // <include path='items/CreateClassesXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateClassesXML(LJCDocDataFile $docDataFile
      , bool $writeGenDataXML = true, string $outputPath = null) : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateClassesXML", $enabled);

      $libName = $docDataFile->Name;
      $classes = $docDataFile->Classes;
      if ($classes != null)
      {
        foreach ($classes as $class)
        {
          $this->CreateClassXML($class, $libName, $writeGenDataXML
            , $outputPath);
        }
      }

      $this->Debug->EndMethod($enabled);
    }

    // Creates a Class GenData XML string and optional file.
    // <include path='items/CreateClassXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateClassXML(LJCDocDataClass $class, string $libName
      , bool $writeGenDataXML = true, string $outputPath = null)	: string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateClassXML", $enabled);
      $retValue = null;

      $fileName = $class->Name . ".xml";
      $retValue = $this->CreateClassString($class, $fileName, $libName);

      if ($writeGenDataXML && $retValue != null)
      {
        $outputClassSpec = $this->OutputClassSpec($class, $outputPath);
        LJCFileWriter::WriteFile($retValue, $outputClassSpec);
      }

      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "ClassTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }

      $this->CreateMethodsXML($class, $libName, $writeGenDataXML);
      $this->CreatePropertiesXML($class, $libName, $writeGenDataXML);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // Creates a Class GenData XML output file spec.
    // <include path='items/OutputClassSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function OutputClassSpec(LJCDocDataClass $class
      , string $outputPath = null) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("OutputClassSpec", $enabled);
      $retValue = null;

      $name = $class->Name;
      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData/$name";
      }
      LJCCommonFile::MkDir($outputPath);
      $fileName = LJCCommon::GetFileName($name) . ".xml";
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // ---------------
    // Method Methods - LJCGenDataGen

    // Creates a Method GenData XML string.
    // <include path='items/CreateMethodString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateMethodString(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $fileName, string $libName) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateMethodString", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->Line("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Function", $indent);
      $builder->Text($value);

      // Items Begin Lines
      $indent += 2;
      $text = $method->Name;
      $value = LJCGenDataXML::ItemBegin($text, $indent);
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
      $value = $this->CreateMethodParamString($method);
      $builder->Text($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->Line("</Sections>", $indent);
      $indent--;
      $builder->Line("</Data>", $indent);
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // CreateMethodString()

    // <summary>Creates a Method Params section GenData XML string.</summary>
    // <param name="$method">The Method object.</param>
    private function CreateMethodParamString(LJCDocDataMethod $method) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateMethodParamString", $enabled);
      $retValue = null;

      $params = $method->Params;
      if ($params != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Parameters", $indent);
        $builder->Text($value);

        $indent += 2;
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
    }

    // Creates the Method GenData class XML strings and optionally files.
    // <include path='items/CreateMethodsXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateMethodsXML(LJCDocDataClass $class, string $libName
      , bool $writeGenDataXML = true, string $outputPath = null) : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateMethodsXML", $enabled);

      $methods = $class->Methods;
      if ($methods != null)
      {
        foreach ($methods as $method)
        {
          $this->CreateMethodXML($class, $method, $libName, $writeGenDataXML
            , $outputPath);
        }
      }

      $this->Debug->EndMethod($enabled);
    }

    // Creates a Method GenData XML string and optional file.
    // <include path='items/CreateMethodXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateMethodXML(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $libName, bool $writeGenDataXML = false
      , string $outputPath = null) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateMethodXML", $enabled);
      $retValue = null;

      $fileName = $method->Name;
      $retValue = $this->CreateMethodString($class, $method, $fileName
        , $libName);

      if ($writeGenDataXML && $retValue != null)
      {
        $outputFileSpec = $this->OutputMethodSpec($class, $method, $outputPath);
        LJCFileWriter::WriteFile($retValue, $outputFileSpec);
      }

      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "FunctionTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name$method->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }
  
    // Creates a Method GenData XML output file spec.
    // <include path='items/OutputMethodSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function OutputMethodSpec(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $outputPath = null) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("OutputMethodSpec", $enabled);
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

    // ---------------
    // Property Methods - LJCGenDataGen

    // Creates a Method GenData XML string.
    // <include path='items/CreatePropertyString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreatePropertyString(LJCDocDataClass $class
      , LJCDocDataProperty $property, string $fileName, string $libName)
        : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreatePropertyString", $enabled);
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->Line("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Property", $indent);
      $builder->Text($value);

      // Items Begin Lines
      $indent += 2;
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
    }

    // Creates the Property GenData class XML strings and optionally files.
    // <include path='items/CreatePropertiesXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreatePropertiesXML(LJCDocDataClass $class, string $libName
      , bool $writeGenDataXML = true, string $outputPath = null) : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreatePropertiesXML", $enabled);

      $properties = $class->Properties;
      if ($properties != null)
      {
        foreach ($properties as $property)
        {
          $this->CreatePropertyXML($class, $property, $libName, $writeGenDataXML
            , $outputPath);
        }
      }

      $this->Debug->EndMethod($enabled);
    }

    // Creates a Method GenData XML string and optional file.
    // <include path='items/CreatePropertyXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreatePropertyXML(LJCDocDataClass $class
      , LJCDocDataProperty $property, string $libName
      , bool $writeGenDataXML = false, string $outputPath = null) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreatePropertyXML", $enabled);

      $retValue = null;

      $fileName = $property->Name . ".xml";
      $retValue = $this->CreatePropertyString($class, $property, $fileName
        , $libName);

      if ($writeGenDataXML && $retValue != null)
      {
        $outputFileSpec = $this->OutputPropertySpec($class, $property
          , $outputPath);
        LJCFileWriter::WriteFile($retValue, $outputFileSpec);
      }

      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "PropertyTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name$property->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }
  
    // Creates a Method GenData XML output file spec.
    // <include path='items/OutputPropertySpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function OutputPropertySpec(LJCDocDataClass $class
      , LJCDocDataProperty $property, string $outputPath = null) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("OutputPropertySpec", $enabled);
      $retValue = null;

      $name = $class->Name;
      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData/$name";
      }
      LJCCommonFile::MkDir($outputPath);
      $fileName = $property->Name . ".xml";
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

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
    }

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
    }

    // ---------------
    // Properties - LJCDocDataGen

    // The path for HTML output.
    public string $HTMLPath;

    // The GenDoc configuration.
    private LJCGenDocConfig $GenDocConfig;
  }
  ?>