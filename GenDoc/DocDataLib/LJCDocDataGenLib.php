<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDocDataGenLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonFileLib.php";
  include_once "$prefix/GenDoc/GenCodeDoc/LJCGenDocConfigLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextFileLib.php";
  include_once "$prefix/GenDoc/DocDataLib/LJCDocDataLib.php";
  include_once "$prefix/GenDoc/DocDataLib/LJCCommentsLib.php";
  // LJCDebugLib: LJCDebug
  // LJCCommonLib: LJC
  // LJCCommonFileLib: LJCCommonFile
  // LJCGenDocConfigLib: LJCGenDocConfig
  // LJCTextLib: LJCWriter
  // LJCTextFileLib: LJCFileWriter
  // LJCDocDataLib: LJCDocDataClass, LJCDocDataClasses, LJCDocDataFile
  //   , LJCDocDataMethod, LJCDocDataMethods, LJCDocDataParam, LJCDocDataParams
  // LJCCommentsLib: LJCComments

  // Contains Classes to generate DocData XML strings and optionally files.
  /// <include path='items/LJCDocDataGenLib/*' file='Doc/LJCDocDataGenLib.xml'/>
  /// LibName: LJCDocDataGenLib
  // The contained classes:
  //  LJCDocDataGen

  // Main Call Tree:
  // TestDocDataGen.php
  // GenCodeDoc.CreateFilePages()
  // SerializeDocData() public
  //   ProcessCode() public
  //     LineProcessed()
  //       ProcessLib()
  //     GetTokens()
  //     ProcessItem()
  //       ProcessClass()
  //       GetPropertyName()
  //         ScrubPropertyName()
  //       ProcessProperty()
  //       GetFunctionName()
  //       ScrubFunctionName()
  //       ProcessFunction()
  //         SetFunctionSyntax()
  //           IsSyntaxContinue()
  //   DocOutputFileSpec() public

  // ***************
  // Public: SerializeDocData(), ProcessCode()
  /// <summary>
  ///		Provides methods to generate DocData XML files from a code file.
  /// </summary>
  /// <group name="Static">Static Methods</group>
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="Main">Class Methods</group>
  class LJCDocDataGen
  {
    // ---------------
    // Private Static Functions

    // Sets the Function Name if present.
    // <include path='items/GetFunctionName/*' file='Doc/LJCDocDataGen.xml'/>
    private static function GetFunctionName(array $tokens): ?string
    {
      $enabled = false;
      $debug = new LJCDebug("LJCDocDataGenLib", "LJCDocDataGen"
       , "a", $enabled);
      $debug->BeginMethod("GetFunctionName", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      if ("static" == $tokens[1]
        && count($tokens) > 3)
      {
        if ("function" == $tokens[2])
        {
          // Function definition is "modifier static function functionName";
          $retValue = self::ScrubFunctionName($tokens[3]);
        }
      }
      if ("function" == $tokens[1]
        && count($tokens) > 2)
      {
        // Function definition is "modifier function functionName";
        $retValue = self::ScrubFunctionName($tokens[2]);
      }

      $debug->EndMethod($enabled);
      return $retValue;
    } // GetFunctionName()

    // Gets the Property Name if present. 
    // Error if including the next line.
    // <include path='items/GetPropertyName/*' file='Doc/LJCDocDataGen.xml'/>
    private static function GetPropertyName(array $tokens): ?string
    {
      $enabled = false;
      $debug = new LJCDebug("LJCDocDataGenLib", "LJCDocDataGen"
       , "a", $enabled);
      $debug->BeginMethod("GetPropertyName", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      if ("$" == substr($tokens[1], 0, 1))
      {
        $retValue = self::ScrubPropertyName($tokens[1]);
      }
      if (null == $retValue
        || count($tokens) > 2)
      {
        if ("$" == substr($tokens[2], 0, 1))
        {
          $retValue = self::ScrubPropertyName($tokens[2]);
        }
      }

      $debug->EndMethod($enabled);
      return $retValue;
    }  // GetPropertyName()

    // Gets the Function Name from the function token.
    // <include path='items/ScrubFunctionName/*' file='Doc/LJCDocDataGen.xml'/>
    private static function ScrubFunctionName(string $functionToken): string
    {
      $enabled = false;
      $debug = new LJCDebug("LJCDocDataGenLib", "LJCDocDataGen"
       , "a", $enabled);
      $debug->BeginMethod("ScrubFunctionName", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = $functionToken;

      $position = LJC::StrPos($retValue, "construct(");
      if ($position >= 0)
      {
        $length = strlen($retValue) - 2;
        $retValue = substr($retValue, 2, $length);
      }

      $position = LJC::StrPos($retValue, "(");
      if ($position >= 0)
      {
        $length = strlen($retValue);
        $length -= $length - $position;
        $retValue = substr($retValue, 0, $length);
      }

      $debug->EndMethod($enabled);
      return $retValue;
    } // ScrubFunctionName

    // Gets the Property Name from the property token.
    // <include path='items/ScrubPropertyName/*' file='Doc/LJCDocDataGen.xml'/>
    private static function ScrubPropertyName(string $propertyToken): string
    {
      $length = strlen($propertyToken);
      $retValue = substr($propertyToken, 0, $length - 1);
      return $retValue;
    } // ScrubPropertyName

    // ---------------
    // Constructors - LJCDocDataGen

    /// <summary>Initializes a class instance.</summary>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCDocDataGenLib", "LJCDocDataGen"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;

      $this->ClassName = null;
      $this->Comments = new LJCComments();
      //$this->DocDataFile = new LJCDocDataFile("");
      $this->FunctionName= null;
      $this->InputStream = null;
      $this->LibName = null;
      $this->Line = null;
      $this->PropertyName = null;
    } // __construct()

    // Sets the GenDoc config.
    /// <ParentGroup>Main</ParentGroup>
    public function SetConfig(LJCGenDocConfig $config): void
    {
      $this->GenDocConfig = $config;
    }

    // ---------------
    // Public Methods - LJCDocDataGen

    // Creates and writes the DocData XML.
    /// <include path='items/CreateDocDataXMLString/*' file='Doc/LJCDocDataGen.xml'/>
    /// <ParentGroup>Main</ParentGroup>
    public function SerializeDocData(string $codeFileSpec): ?string
    {
      // GetCodDocLib.php
      // GenCodeDoc.CreateFilePages()
      $enabled = false;
      $this->Debug->BeginMethod("SerializeDocData", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      // Populate Library(File) XMLComment values.
      $this->LibName = LJC::GetFileName($codeFileSpec);
      $this->Comments->LibName = $this->LibName;
      $this->DocDataFile = new LJCDocDataFile($this->LibName);

      $retValue = $this->ProcessCode($codeFileSpec);
      $this->WriteLibDocXML($retValue, $codeFileSpec, $this->LibName);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // CreateDocDataXMLString()

    // Generates the Doc data for the file.
    /// <include path='items/ProcessCode/*' file='Doc/LJCDocDataGen.xml'/>
    /// <ParentGroup>Main</ParentGroup>
    public function ProcessCode(string $codeFileSpec): ?string
    {
      // SerializeDocData()
      // ProcessCode()
      $enabled = false;
      $this->Debug->BeginMethod("ProcessCode", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      $success = true;
      if (false == file_exists($codeFileSpec))
      {
        $success = false;
        $this->Debug->BeginMethod("ProcessCode");
        $this->Debug->EndMethod();
      }
      if ($success)
      {
        $this->InputStream = fopen($codeFileSpec, "r+");
        if (null == $this->InputStream)
        {
          $success = false;

          // Enable for error message.
          $saveEnabled = $this->Debug->IsEnabled();
          if (!$saveEnabled)
          {
            $this->Debug->setEnabled(true);
          }
          $this->Debug->BeginMethod("ProcessCode");
          $this->Debug->Write(__LINE__." Unable to open {$codeFileSpec}.");
          $this->Debug->EndMethod();
          $this->Debug->setEnabled($saveEnabled);
        }
      }
      if ($success)
      {
        while(false == feof($this->InputStream))
        {
          $this->Line = (string)fgets($this->InputStream);

          // Process Lib, XML Comment, empty line and Comment Line.
          if ($this->LineProcessed($this->Line, $codeFileSpec))
          {
            continue;
          }

          // Check for Class, Function or Property.
          $tokens = LJC::GetTokens($this->Line);
          if (count($tokens) < 2)
          {
            continue;
          }

          // Process Class, Function or Property.
          $this->ProcessItem($tokens);
        }
        fclose($this->InputStream);
      }

      if ($this->DocDataFile != null)
      {
        $retValue = $this->DocDataFile->SerializeToString(null);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // ProcessCode()

    // ---------------
    // Private Methods - LJCDocDataGen

    // Process XML Comment or Skip Null line and Comment Line.
    private function LineProcessed(?string $line, string $codeFileSpec): bool
    {
      // ProcessCode()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("LineProcessed", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retProcessed = false;

      $trimLine = trim($line);
      if (null == $trimLine)
      {
        $retProcessed = true;
      }

      if (false == $retProcessed)
      {
        $position = LJC::StrPos($trimLine, "///");
        if (0 == $position)
        {
          $tokens = LJC::GetTokens($trimLine);
          if (count($tokens) > 1)
          {
            if ("LibName:" == $tokens[1])
            {
              $this->ProcessLib();
              $retProcessed = true;
            }
          }

          if (false == $retProcessed)
          {
            // Sets the line XML Comment values or Include file values.
            $this->Comments->SetComment($line, $codeFileSpec);
            $retProcessed = true;
          }
        }
      }

      if (false == $retProcessed)
      {
        $position = LJC::StrPos($trimLine, "//");
        if (0 == $position)
        {
          $retProcessed = true;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retProcessed;
    } // LineProcessed()

    // Writes an output line.
    private function Output($text = null, $value = null): void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("Output", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $lib = "";
      //$lib = "LJCCommonLib";
      if ("" == $lib
        || $lib == $this->LibName
        || $lib == $this->IncludeFile->LibName)
      {
        LJCWriter::Write($text);
        if ($value != null)
        {
          if (is_array($value))
          {
            foreach ($value as $key => $value)
            {
              LJCWriter::WriteLine(":\r\n|$key| |$value|");
            }
          }
          else
          {
            LJCWriter::Write(":\r\n|$value|");
          }
        }
        LJCWriter::WriteLine("");
      }

      $this->Debug->EndMethod($enabled);
    } // Output()

    // ---------------
    // Private Process Item Methods - LJCDocDataGen

    // Copy the Class XML comments into the DocData objects.
    private function ProcessClass(): void
    {
      // SerializeDocData()-ProcessCode()-ProcessItem()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessClass", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $classes = $this->DocDataFile->Classes;
      if (null == $classes)
      {
        $classes = new LJCDocDataClasses();
        $this->DocDataFile->Classes = $classes;
      }
      $name = $this->ClassName;
      $summary = $this->Comments->Summary;
      $class = new LJCDocDataClass($name, $summary);
      $classes->AddObject($class, $name);
      $class->Syntax = trim($this->Line);

      // Get Comment values.
      $class->Code = $this->Comments->Code;
      foreach ($this->Comments->Groups as $key => $value)
      {
        $class->Groups[$key] = $value;
      }
      $class->Remarks = $this->Comments->Remarks;

      $this->Comments->ClearComments();

      $this->Debug->EndMethod($enabled);
    } // ProcessClass()

    // Processes the Class, Function or Property.
    // $tokens - The array of line tokens.
    private function ProcessItem(array $tokens): void
    {
      // SerializeDocData()-ProcessCode()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessItem", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $isFunction = false;
      switch(strtolower($tokens[0]))
      {
        case "class":
          // class name
          $this->ClassName = $tokens[1];
          $this->ProcessClass();
          break;

        case "protected":
        case "public":
          // public (dataType) $PropertyName
          $name = self::GetPropertyName($tokens);
          if ($name != null)
          {
            $this->PropertyName = $name;
            $this->ProcessProperty();
          }
          else
          {
            // public (static) function name();
            $name = self::GetFunctionName($tokens);
            if ($name != null)
            {
              $isFunction = true;
              $this->FunctionName = $name;
            }
          }
          break;

        case "function":
          $isFunction = true;
          $this->FunctionName = self::ScrubFunctionName($tokens[1]);
          break;
      }

      if ($isFunction)
      {
        $this->ProcessFunction();
      }

      $this->Debug->EndMethod($enabled);
    } // ProcessItem()

    // Copy the Lib XML comments into the DocData objects.
    private function ProcessLib(): void
    {
      // SerializeDocData()-ProcessCode()-LineProcessed()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessLib", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $docDataFile = $this->DocDataFile;
      $docDataFile->Summary = $this->Comments->Summary;
      $docDataFile->Remarks = $this->Comments->Remarks;

      $this->Comments->ClearComments();

      $this->Debug->EndMethod($enabled);
    } // ProcessLib()

    // Copy the Property XML comments into the DocData objects.
    private function ProcessProperty(): void
    {
      // SerializeDocData()-ProcessCode()-ProcessItem()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessProperty", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $classes = $this->DocDataFile->Classes;
      //$class = $classes->Get($this->ClassName);
      $class = $classes->Retrieve($this->ClassName);
      $properties = $class->Properties;
      if (null == $properties)
      {
        $properties = new LJCDocDataProperties();
        $class->Properties = $properties;
      }
      $name = $this->PropertyName;
      $summary = $this->Comments->Summary;
      $returns = $this->Comments->Returns;
      $property = new LJCDocDataProperty($name, $summary, $returns);
      $properties->AddObject($property, $name);

      $property->Syntax = trim($this->Line);
      $property->Remarks = $this->Comments->Remarks;

      $this->Comments->ClearComments();

      $this->Debug->EndMethod($enabled);
    } // ProcessProperty()
    
    // ---------------
    // Private Process Function Methods - LJCGenDataGen

    // Indicates if the Syntax eligible statement is continued.
    private function IsSyntaxContinue(string $line): bool
    {
      // SetFunctionSyntax()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("IsSyntaxContinue", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = false;

      $trimLine = trim($line);
      $textIndex = LJC::StrRPos($trimLine, ":");
      if ($textIndex > 0)
      {
        $textIndex = strlen($trimLine) - 1;
      }
      if ($textIndex < 0)
      {
        $textIndex = LJC::StrRPos($trimLine, ")");
      }
      $textLength = strlen($trimLine);
      if ($textIndex < $textLength - 2)
      {
        $retValue = true;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // IsSyntaxContinue()

    // Copy the Function XML comments into the DocData objects.
    private function ProcessFunction(): void
    {
      // SerializeDocData()-ProcessCode()-ProcessItem()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessFunction", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $classes = $this->DocDataFile->Classes;
      $class = $classes->Retrieve($this->ClassName);
      $methods = $class->Methods;
      if (null == $methods)
      {
        $methods = new LJCDocDataMethods();
        $class->Methods = $methods;
      }
      $name = $this->FunctionName;
      $summary = $this->Comments->Summary;
      $returns = $this->Comments->Returns;
      $method = new LJCDocDataMethod($name, $summary, $returns);
      $methods->AddObject($method, $name);

      // Get Comment values.
      $method->Code = $this->Comments->Code;
      $method->Params = $this->Comments->Params;
      // *** Add ***
      $method->ParentGroup = $this->Comments->ParentGroup;
      $method->Remarks = $this->Comments->Remarks;
      $this->SetFunctionSyntax();
      $method->Syntax = $this->Syntax;

      $this->Comments->ClearComments();

      $this->Debug->EndMethod($enabled);
    } // ProcessFunction()

    // Sets the Syntax value for a function.
    private function SetFunctionSyntax(): void
    {
      // SerializeDocData()-ProcessCode()-ProcessItem()-ProcessFunction()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SetFunctionSyntax", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $trimLine = trim($this->Line);
      $this->Syntax = htmlspecialchars($trimLine);
      $syntaxContinue = $this->IsSyntaxContinue($trimLine);
      while ($syntaxContinue)
      {
        if (feof($this->InputStream))
        {
          $syntaxContinue = false;
        }
        else
        {
          $this->Line = fgets($this->InputStream);
          $trimLine = trim($this->Line);
          $this->Syntax .= "&lt;br /&gt;";
          $this->Syntax .= htmlspecialchars($trimLine);
          $syntaxContinue = $this->IsSyntaxContinue($trimLine);
        }
      }

      $this->Debug->EndMethod($enabled);
    } // SetFunctionSyntax()
    
    // ---------------
    // Private Write XML Methods - LJCGenDataGen

    // Creates a Lib DocData XML output file spec.
    /// <include path='items/DocOutputFileSpec/*' file='Doc/LJCDocDataGen.xml'/>
    private function LibGenXMLSpec(string $codeFileSpec
      , string $outputPath = null): string
    {
      // SerializeDocData()-WriteLibDocXML()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("LibGenXMLSpec", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      if (null == $outputPath)
      {  
        $outputPath = "../XMLDocData";
      }
      LJCCommonFile::MkDir($outputPath);
      $fileName = LJC::GetFileName($codeFileSpec) . ".xml";
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // LibGenXMLSpec()

    // Writes the LibGenXML file.
    private function WriteLibDocXML(string $libDocXML, string $codeFileSpec
      , string $fileName): bool
    {
      // SerializeDocData()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("WriteLibDocXML", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = false;

      $writeDocDataXML = $this->GenDocConfig->WriteDocDataXML;
      // *** Begin *** Debug Output
      if ("LJCHTMLTableLib" == $fileName)
      {
        $writeDocDataXML = true;
      }
      // *** End   ***

      if ($writeDocDataXML
        && $libDocXML != null)
      {
        $retValue = true;
        $docDataXMLPath = $this->GenDocConfig->DocDataXMLPath;
        $fileSpec = $this->LibGenXMLSpec($codeFileSpec, $docDataXMLPath);
        LJCFileWriter::WriteFile($libDocXML, $fileSpec);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // ---------------
    // Private Properties - LJCDocDataGen

    // The Class name.
    private ?string $ClassName;

    // The XML Comments object.
    private LJCComments $Comments;

    // The DocDataFile object.
    private ?LJCDocDataFile $DocDataFile;

    // The Function name.
    private ?string $FunctionName;

    // The GenDoc configuration.
    private LJCGenDocConfig $GenDocConfig;

    // The Input File Stream.
    private $InputStream;

    // The Lib name.
    private ?string $LibName;

    // The current Line.
    private ?string $Line;

    // The Property name.
    private ?string $PropertyName;
  } // LJCDocDataGen
?>