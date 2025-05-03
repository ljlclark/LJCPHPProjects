<?php
  // LJCGenDataGenLib.php
  declare(strict_types=1);
  $path = "../..";
  // Must refer to exact same file everywhere in codeline.
  require_once "$path/LJCPHPCommon/LJCTextLib.php";
  require_once "$path/GenTextLib/LJCGenTextLib.php";
  require_once "$path/GenDoc/GenDataLib/LJCGenDataXMLLib.php";
  require_once "$path/GenDoc/GenDataLib/LJCDebug.php";

  // Contains classes to create GenData from DocData.
  /// <include path='items/LJCGenDataGenLib/*' file='Doc/LJCGenDataGenLib.xml'/>
  /// LibName: LJCGenDataGenLib

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
  // Provides methods to generate GenData XML files.
  /// <include path='items/LJCGenDataGen/*' file='Doc/LJCGenDataGen.xml'/>
  class LJCGenDataGen
  {
    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $isEnabled = false;
      $this->Debug = new LJCDebug("LJCDocDataGenLib", "LJCGenDataGen"
        , $isEnabled);
      $this->Debug->IncludePrivate = true;

      $this->HTMLPath = "../../../WebSitesDev/CodeDoc/LJCPHPCodeDoc/HTML";
      LJCCommon::MkDir($this->HTMLPath);

      $this->DebugWriter = null;
      // Create DebugWriter if writing debug data.
      //$this->DebugWriter = new LJCDebugWriter("LJCGenDataGen");
    }
    
    // ---------------
    // Lib Methods - LJCGenDataGen

    // Creates a Lib GenData XML string and optional file.
    /// <include path='items/CreateLibXMLString/*' file='Doc/LJCGenDataGen.xml'/>
    public function CreateLibXMLString(string $docXMLString, string $codeFileSpec
      , bool $writeXML = false, string $outputPath = null) : string
    {
      $this->Debug->WriteStartText("CreateLibXMLString");
      $retValue = null;

      // GenData XML file name same as source file with .xml extension.
      $fileName = LJCCommon::GetFileName($codeFileSpec) . ".xml";
      $this->Debug("fileName = $fileName");
      // Start Testing
      $docDataFile = LJCDocDataFile::DeserializeString($docXMLString);
      $retValue = $this->CreateLibString($docDataFile, $fileName);
      if ($writeXML && $retValue != null)
      {
        $outputFileSpec = $this->OutputLibSpec($codeFileSpec, $outputPath);
        LJCWriter::WriteFile($retValue, $outputFileSpec);
      }

      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "LibTemplate.html");
        $htmlPath = $this->HTMLPath;
        $htmlFileName = LJCCommon::GetFileName($codeFileSpec);
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }
      $this->CreateClassesXML($docDataFile, $writeXML, $outputPath);

      $this->Debug->AddIndent(-1);
      return $retValue;
    }

    // Creates a Lib GenData XML string.
    // <include path='items/CreateLibString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateLibString(LJCDocDataFile $docDataFile
      , string $fileName)	: ?string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Append(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->AppendLine("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Main", $indent);
      $builder->Append($value);

      // Items Begin Lines
      $indent += 2;
      $value = LJCGenDataXML::ItemBegin("Main", $indent);
      $builder->Append($value);
      $indent++;

      // Replacements
      $text = $docDataFile->Summary;
      $value = LJCGenDataXML::Replacement("_FileSummary_", $text, $indent);
      $builder->Append($value);

      $classes = $docDataFile->Classes;
      if ($classes != null)
      {
        $text = (string)count($classes);
        $value = LJCGenDataXML::Replacement("_ItemCount_", $text, $indent);
        $builder->Append($value);
      }

      $text = $docDataFile->Name;
      $value = LJCGenDataXML::Replacement("_LibName_", $text, $indent);
      $builder->Append($value);

      $text = "LJCPHPCodeDoc.html";
      $value = LJCGenDataXML::Replacement("_ProjectListFile_", $text, $indent);
      $builder->Append($value);

      $text = $docDataFile->Remarks;
      $value = LJCGenDataXML::Replacement("_Remarks_", $text, $indent);
      $builder->Append($value);

      // Items End Lines				
      $value = LJCGenDataXML::ItemEnd($indent);
      $builder->Append($value);

      // Section End Lines
      $indent -= 2;
      $value = LJCGenDataXML::SectionEnd($indent);
      $builder->Append($value);

      // Class Section			
      $value = $this->CreateLibClassString($docDataFile);
      $builder->Append($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->AppendLine("</Sections>", $indent);
      $indent--;
      $builder->AppendLine("</Data>", $indent);
      $retValue = $builder->ToString();
      return $retValue;
    }  // CreateLibString()

    // Creates a Lib Class section GenData XML string.
    // <include path='items/CreateLibClassString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateLibClassString(LJCDocDataFile $docDataFile) : ?string
    {
      $retValue = null;

      $classes = $docDataFile->Classes;
      if ($classes != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Class", $indent);
        $builder->Append($value);

        $indent += 2;
        foreach ($classes as $class)
        {
          // Items Begin Lines
          $text = $class->Name;
          $value = LJCGenDataXML::ItemBegin($text, $indent);
          $builder->Append($value);
          $indent++;

          // Replacements
          $text = $class->Name;
          $value = LJCGenDataXML::Replacement("_ClassName_", $text, $indent);
          $builder->Append($value);

          $text = $class->Summary;
          $value = LJCGenDataXML::Replacement("_ClassSummary_", $text, $indent);
          $builder->Append($value);

          // Items End Lines				
          $value = LJCGenDataXML::ItemEnd($indent);
          $builder->Append($value);
          $indent--;
        }

        // Section End Lines
        $indent--;
        $value = LJCGenDataXML::SectionEnd($indent);
        $builder->Append($value);
        $retValue = $builder->ToString();
      }
      return $retValue;
    }

    // Creates a Lib GenData XML output file spec.
    // <include path='items/OutputLibSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function OutputLibSpec(string $codeFileSpec
      , string $outputPath = null) : string
    {
      $retValue = null;

      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData";
      }
      LJCCommon::MkDir($outputPath);
      $fileName = LJCCommon::GetFileName($codeFileSpec) . ".xml";
      $retValue = "$outputPath/$fileName";
      return $retValue;
    }
    
    // ---------------
    // Class Methods - LJCGenDataGen

    // Creates a Class GenData XML string.
    // <include path='items/CreateClassString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateClassString(LJCDocDataClass $class
      , string $fileName, string $libName) : string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Append(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->AppendLine("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Class", $indent);
      $builder->Append($value);

      // Items Begin Lines
      $indent += 2;
      $value = LJCGenDataXML::ItemBegin("Class", $indent);
      $builder->Append($value);
      $indent++;

      $text = htmlspecialchars($class->Code);
      $value = LJCGenDataXML::Replacement("_Code_", $text, $indent);
      $builder->Append($value);

      $value = LJCGenDataXML::Replacement("_LibName_", $libName, $indent);
      $builder->Append($value);

      // Replacements
      $text = $class->Name;
      $value = LJCGenDataXML::Replacement("_ClassName_", $text, $indent);
      $builder->Append($value);

      $text = $class->Summary;
      $value = LJCGenDataXML::Replacement("_ClassSummary_", $text, $indent);
      $builder->Append($value);

      $properties = $class->Properties;
      if ($properties != null)
      {
        if (count($properties) > 0)
        {
          $value = LJCGenDataXML::Replacement("_HasProperties_", (string)true
            , $indent);
          $builder->Append($value);
        }
      }

      $methods = $class->Methods;
      if ($methods != null)
      {
        $text = (string)count($methods);
        $value = LJCGenDataXML::Replacement("_ItemCount_", $text, $indent);
        $builder->Append($value);
      }

      $text = $class->Remarks;
      $value = LJCGenDataXML::Replacement("_Remarks_", $text, $indent);
      $builder->Append($value);

      $text = "class $class->Name";
      $value = LJCGenDataXML::Replacement("_Syntax_", $text, $indent);
      $builder->Append($value);

      // Items End Lines				
      $value = LJCGenDataXML::ItemEnd($indent);
      $builder->Append($value);

      // Section End Lines
      $indent -= 2;
      $value = LJCGenDataXML::SectionEnd($indent);
      $builder->Append($value);

      // Method Section			
      $value = $this->CreateClassMethodString($class);
      $builder->Append($value);

      // Properties Section			
      $value = $this->CreateClassPropertyString($class);
      $builder->Append($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->AppendLine("</Sections>", $indent);
      $indent--;
      $builder->AppendLine("</Data>", $indent);
      $retValue = $builder->ToString();
      return $retValue;
    }  // CreateClassString()

    // Creates a Class Methods section GenData XML string.
    // <include path='items/CreateClassMethodString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateClassMethodString(LJCDocDataClass $class) : ?string
    {
      $retValue = null;

      $methods = $class->Methods;
      if ($methods != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Function", $indent);
        $builder->Append($value);

        $indent += 2;
        foreach ($methods as $method)
        {
          // Items Begin Lines
          $text = $method->Name;
          $value = LJCGenDataXML::ItemBegin($text, $indent);
          $builder->Append($value);
          $indent++;

          // Replacements
          $text = $method->Name;
          $value = LJCGenDataXML::Replacement("_FunctionName_", $text, $indent);
          $builder->Append($value);

          $text = $method->Summary;
          $value = LJCGenDataXML::Replacement("_FunctionSummary_", $text, $indent);
          $builder->Append($value);

          // Items End Lines				
          $value = LJCGenDataXML::ItemEnd($indent);
          $builder->Append($value);
          $indent--;
        }

        // Section End Lines
        $indent--;
        $value = LJCGenDataXML::SectionEnd($indent);
        $builder->Append($value);
        $retValue = $builder->ToString();
      }
      return $retValue;
    }  // CreateClassMethodString()

    // Creates a Class Methods section GenData XML string.
    private function CreateClassPropertyString(LJCDocDataClass $class) : ?string
    {
      $retValue = null;

      $properties = $class->Properties;
      if ($properties != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Property", $indent);
        $builder->Append($value);

        $indent += 2;
        foreach ($properties as $property)
        {
          // Items Begin Lines
          $text = $property->Name;
          $value = LJCGenDataXML::ItemBegin($text, $indent);
          $builder->Append($value);
          $indent++;

          // Replacements
          $text = $property->Name;
          $value = LJCGenDataXML::Replacement("_PropertyName_", $text, $indent);
          $builder->Append($value);

          $text = $property->Summary;
          $value = LJCGenDataXML::Replacement("_PropertySummary_", $text, $indent);
          $builder->Append($value);

          // Items End Lines				
          $value = LJCGenDataXML::ItemEnd($indent);
          $builder->Append($value);
          $indent--;
        }

        // Section End Lines
        $indent--;
        $value = LJCGenDataXML::SectionEnd($indent);
        $builder->Append($value);
        $retValue = $builder->ToString();
      }
      return $retValue;
    }

    // Creates the Class GenData XML strings and optionally files.
    // <include path='items/CreateClassesXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateClassesXML(LJCDocDataFile $docDataFile
      , bool $writeXML = true, string $outputPath = null) : void
    {
      $libName = $docDataFile->Name;
      $classes = $docDataFile->Classes;
      if ($classes != null)
      {
        foreach ($classes as $class)
        {
          $this->CreateClassXML($class, $libName, $writeXML, $outputPath);
        }
      }
    }

    // Creates a Class GenData XML string and optional file.
    // <include path='items/CreateClassXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateClassXML(LJCDocDataClass $class, string $libName
      , bool $writeXML = true, string $outputPath = null)	: string
    {
      $retValue = null;

      $fileName = $class->Name . ".xml";
      $retValue = $this->CreateClassString($class, $fileName, $libName);
      if ($writeXML && $retValue != null)
      {
        $outputClassSpec = $this->OutputClassSpec($class, $outputPath);
        LJCWriter::WriteFile($retValue, $outputClassSpec);
      }

      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "ClassTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }

      $this->CreateMethodsXML($class, $libName, $writeXML);
      $this->CreatePropertiesXML($class, $libName, $writeXML);
      return $retValue;
    }

    // Creates a Class GenData XML output file spec.
    // <include path='items/OutputClassSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function OutputClassSpec(LJCDocDataClass $class
      , string $outputPath = null) : string
    {
      $retValue = null;

      $name = $class->Name;
      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData/$name";
      }
      LJCCommon::MkDir($outputPath);
      $fileName = LJCCommon::GetFileName($name) . ".xml";
      $retValue = "$outputPath/$fileName";
      return $retValue;
    }

    // ---------------
    // Method Methods - LJCGenDataGen

    // Creates a Method GenData XML string.
    // <include path='items/CreateMethodString/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateMethodString(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $fileName, string $libName) : ?string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Append(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->AppendLine("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Function", $indent);
      $builder->Append($value);

      // Items Begin Lines
      $indent += 2;
      $text = $method->Name;
      $value = LJCGenDataXML::ItemBegin($text, $indent);
      $builder->Append($value);
      $indent++;

      // Replacements
      $text = $method->Name;
      $value = LJCGenDataXML::Replacement("_FunctionName_", $text, $indent);
      $builder->Append($value);

      $text = $method->Summary;
      $value = LJCGenDataXML::Replacement("_FunctionSummary_", $text, $indent);
      $builder->Append($value);

      $text = $class->Name;
      $value = LJCGenDataXML::Replacement("_ClassName_", $text, $indent);
      $builder->Append($value);

      $text = $method->Code;
      $value = LJCGenDataXML::Replacement("_Code_", $text, $indent);
      $builder->Append($value);

      $params = $method->Params;
      if ($params != null)
      {
        if (count($params) > 0)
        {
          $value = LJCGenDataXML::Replacement("_HasParameters_", (string)true
            , $indent);
          $builder->Append($value);
        }
      }

      $value = LJCGenDataXML::Replacement("_LibName_", $libName, $indent);
      $builder->Append($value);

      $text = $method->Remarks;
      $value = LJCGenDataXML::Replacement("_Remarks_", $text, $indent);
      $builder->Append($value);

      $text = $method->Returns;
      $value = LJCGenDataXML::Replacement("_Returns_", $text, $indent);
      $builder->Append($value);

      $text = htmlspecialchars($method->Syntax);
      $value = LJCGenDataXML::Replacement("_Syntax_", $text, $indent);
      $builder->Append($value);

      // Items End Lines				
      $value = LJCGenDataXML::ItemEnd($indent);
      $builder->Append($value);

      // Section End Lines
      $indent -= 2;
      $value = LJCGenDataXML::SectionEnd($indent);
      $builder->Append($value);

      // Parameters Section			
      $value = $this->CreateMethodParamString($method);
      $builder->Append($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->AppendLine("</Sections>", $indent);
      $indent--;
      $builder->AppendLine("</Data>", $indent);
      $retValue = $builder->ToString();
      return $retValue;
    }  // CreateMethodString()

    // <summary>Creates a Method Params section GenData XML string.</summary>
    // <param name="$method">The Method object.</param>
    private function CreateMethodParamString(LJCDocDataMethod $method) : ?string
    {
      $retValue = null;

      $params = $method->Params;
      if ($params != null)
      {
        $builder = new LJCStringBuilder();

        // Section Begin Lines.
        $indent = 2;
        $value = LJCGenDataXML::SectionBegin("Parameters", $indent);
        $builder->Append($value);

        $indent += 2;
        foreach ($params as $param)
        {
          // Items Begin Lines
          $text = $param->Name;
          $value = LJCGenDataXML::ItemBegin($text, $indent);
          $builder->Append($value);
          $indent++;

          // Replacements
          $text = $param->Name;
          $value = LJCGenDataXML::Replacement("_ParamName_", $text, $indent);
          $builder->Append($value);

          $text = $param->Summary;
          $value = LJCGenDataXML::Replacement("_ParamSummary_", $text, $indent);
          $builder->Append($value);

          // Items End Lines				
          $value = LJCGenDataXML::ItemEnd($indent);
          $builder->Append($value);
          $indent--;
        }

        // Section End Lines
        $indent--;
        $value = LJCGenDataXML::SectionEnd($indent);
        $builder->Append($value);
        $retValue = $builder->ToString();
      }
      return $retValue;
    }

    // Creates the Method GenData class XML strings and optionally files.
    // <include path='items/CreateMethodsXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateMethodsXML(LJCDocDataClass $class, string $libName
      , bool $writeXML = true, string $outputPath = null) : void
    {
      $methods = $class->Methods;
      if ($methods != null)
      {
        foreach ($methods as $method)
        {
          $this->CreateMethodXML($class, $method, $libName, $writeXML, $outputPath);
        }
      }
    }

    // Creates a Method GenData XML string and optional file.
    // <include path='items/CreateMethodXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreateMethodXML(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $libName, bool $writeXML = false
      , string $outputPath = null) : string
    {
      $retValue = null;

      $fileName = $method->Name;
      $retValue = $this->CreateMethodString($class, $method, $fileName
        , $libName);
      if ($writeXML && $retValue != null)
      {
        $outputFileSpec = $this->OutputMethodSpec($class, $method, $outputPath);
        LJCWriter::WriteFile($retValue, $outputFileSpec);
      }

      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "FunctionTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name$method->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }
      return $retValue;
    }
  
    // Creates a Method GenData XML output file spec.
    // <include path='items/OutputMethodSpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function OutputMethodSpec(LJCDocDataClass $class
      , LJCDocDataMethod $method, string $outputPath = null) : string
    {
      $retValue = null;

      $name = $class->Name;
      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData/$name";
      }
      LJCCommon::MkDir($outputPath);
      $fileName = $method->Name . ".xml";
      $retValue = "$outputPath/$fileName";
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
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Append(LJCGenDataXML::XMLHead($fileName));

      // Sections and Section Begin Lines.
      $indent = 1;
      $builder->AppendLine("<Sections>", $indent);
      $indent++;
      $value = LJCGenDataXML::SectionBegin("Property", $indent);
      $builder->Append($value);

      // Items Begin Lines
      $indent += 2;
      $text = $property->Name;
      $value = LJCGenDataXML::ItemBegin($text, $indent);
      $builder->Append($value);
      $indent++;

      // Replacements
      $text = $class->Name;
      $value = LJCGenDataXML::Replacement("_ClassName_", $text, $indent);
      $builder->Append($value);

      $value = LJCGenDataXML::Replacement("_LibName_", $libName, $indent);
      $builder->Append($value);

      $text = $property->Name;
      $value = LJCGenDataXML::Replacement("_PropertyName_", $text, $indent);
      $builder->Append($value);

      $text = $property->Summary;
      $value = LJCGenDataXML::Replacement("_PropertySummary_", $text, $indent);
      $builder->Append($value);

      $text = $property->Remarks;
      $value = LJCGenDataXML::Replacement("_Remarks_", $text, $indent);
      $builder->Append($value);

      $text = $property->Returns;
      $value = LJCGenDataXML::Replacement("_Returns_", $text, $indent);
      $builder->Append($value);

      $text = $property->Syntax;
      $value = LJCGenDataXML::Replacement("_Syntax_", $text, $indent);
      $builder->Append($value);

      // Items End Lines				
      $value = LJCGenDataXML::ItemEnd($indent);
      $builder->Append($value);

      // Section End Lines
      $indent -= 2;
      $value = LJCGenDataXML::SectionEnd($indent);
      $builder->Append($value);

      // Sections and File End Lines
      $indent -= 2;
      $builder->AppendLine("</Sections>", $indent);
      $indent--;
      $builder->AppendLine("</Data>", $indent);
      $retValue = $builder->ToString();
      return $retValue;
    }

    // Creates the Property GenData class XML strings and optionally files.
    // <include path='items/CreatePropertiesXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreatePropertiesXML(LJCDocDataClass $class, string $libName
      , bool $writeXML = true, string $outputPath = null) : void
    {
      $properties = $class->Properties;
      if ($properties != null)
      {
        foreach ($properties as $property)
        {
          $this->CreatePropertyXML($class, $property, $libName, $writeXML, $outputPath);
        }
      }
    }

    // Creates a Method GenData XML string and optional file.
    // <include path='items/CreatePropertyXML/*' file='Doc/LJCGenDataGen.xml'/>
    private function CreatePropertyXML(LJCDocDataClass $class
      , LJCDocDataProperty $property, string $libName, bool $writeXML = false
      , string $outputPath = null) : string
    {
      $retValue = null;

      $fileName = $property->Name . ".xml";
      $retValue = $this->CreatePropertyString($class, $property, $fileName
        , $libName);
      if ($writeXML && $retValue != null)
      {
        $outputFileSpec = $this->OutputPropertySpec($class, $property
          , $outputPath);
        LJCWriter::WriteFile($retValue, $outputFileSpec);
      }

      if ($retValue != null)
      {
        $htmlText = $this->GetHTMLText($retValue, "PropertyTemplate.html");
        $htmlPath = "$this->HTMLPath/$class->Name";
        $htmlFileName = "$class->Name$property->Name";
        $this->WriteHTML($htmlText, $htmlPath, $htmlFileName);
      }
      return $retValue;
    }
  
    // Creates a Method GenData XML output file spec.
    // <include path='items/OutputPropertySpec/*' file='Doc/LJCGenDataGen.xml'/>
    private function OutputPropertySpec(LJCDocDataClass $class
      , LJCDocDataProperty $property, string $outputPath = null) : string
    {
      $retValue = null;

      $name = $class->Name;
      if (null == $outputPath)
      {
        $outputPath = "../XMLGenData/$name";
      }
      LJCCommon::MkDir($outputPath);
      $fileName = $property->Name . ".xml";
      $retValue = "$outputPath/$fileName";
      return $retValue;
    }

    // ---------------
    // Private Methods - LJCGenDataGen

    // Generate the HTML text.
    private function GetHTMLText(string $sectionsXMLString
      , string $templateFileName) : string
    {
      global $dev;
      $retValue = null;

      if ($sectionsXMLString != null)
      {
        $templateFileSpec = "$dev/GenDoc/GenDataLib/Templates/$templateFileName";
        $sections = LJCSections::DeserializeString($sectionsXMLString);
        $genText = new LJCGenText();
        $retValue = $genText->ProcessTemplate($templateFileSpec, $sections);
      }
      return $retValue;
    }

    // Write the HTML file.
    private function WriteHTML(string $htmlText, string $htmlPath
      , string $fileName) : void
    {
      LJCCommon::MkDir($htmlPath);
      $fileSpec = "$htmlPath/$fileName" . ".html";
      LJCWriter::WriteFile($htmlText, $fileSpec);
    }

    // Writes the debug value.
    private function Debug(string $text, bool $addLine = true) : void
    {
      if (isset($this->DebugWriter))
      {
        $this->DebugWriter->Debug($text, $addLine);
      }
    } // Debug()

    // The path for HTML output.
    public string $HTMLPath;
  }
  ?>