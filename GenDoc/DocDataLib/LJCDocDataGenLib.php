<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDocDataGenLib.php
  declare(strict_types=1);
  // Path: LJCPHPProjectsDev/GenDoc/DocDataLib
  include_once "../../LJCPHPCommon/LJCDebugLib.php";
  include_once "../../LJCPHPCommon/LJCCommonLib.php";
  include_once "../../LJCPHPCommon/LJCTextLib.php";
  include_once "LJCDocDataLib.php";
  include_once "LJCCommentsLib.php";
  // The used classes:
  // LJCCommonLib: LJCCommon
  // LJCTextLib: LJCWriter
  // LJCDebugLib: LJCDebug
  // LJCDocDataLib: LJCDocDataClass, LJCDocDataClasses, LJCDocDataFile
  //   , LJCDocDataMethod, LJCDocDataMethods, LJCDocDataParam, LJCDocDataParams
  // LJCCommentsLib: LJCComments

  // Contains Classes to generate DocData XML strings and optionally files.
  /// <include path='items/LJCDocDataGenLib/*' file='Doc/LJCDocDataGenLib.xml'/>
  /// LibName: LJCDocDataGenLib
  // The contained classes:
  //  LJCDocDataGen

  // Calling Code:
  // TestDocDataGen.php
  // GenCodeDoc->CreateFilePages()
  //
  // Main Call Tree:
  // CreateDocDataXMLString() public
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
  // Public: CreateDocDataXMLString(), ProcessCode()
  /// <summary>
  ///		Provides methods to generate DocData XML files from a code file.
  /// </summary>
  class LJCDocDataGen
  {
    // ---------------
    // Private Static Functions

    // Sets the Function Name if present.
    // <include path='items/GetFunctionName/*' file='Doc/LJCDocDataGen.xml'/>
    private static function GetFunctionName(array $tokens)	: ?string
    {
      $debug = new LJCDebug("LJCGenTextSectionLib", "LJCDocDataGen"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("GetFunctionName", $enabled);
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
    // <include path='items/GetPropertyName/*' file='Doc/LJCDocDataGen.xml'/>
    private static function GetPropertyName(array $tokens) : ?string
    {
      $debug = new LJCDebug("LJCGenTextSectionLib", "LJCDocDataGen"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("GetPropertyName", $enabled);
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
    private static function ScrubFunctionName(string $functionToken) : string
    {
      $debug = new LJCDebug("LJCGenTextSectionLib", "LJCDocDataGen"
       , "w", false);
      $enabled = false;
      $debug->BeginMethod("ScrubFunctionName", $enabled);
      $retValue = $functionToken;

      $position = LJCCommon::StrPos($retValue, "construct(");
      if ($position >= 0)
      {
        $length = strlen($retValue) - 2;
        $retValue = substr($retValue, 2, $length);
      }

      $position = LJCCommon::StrPos($retValue, "(");
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
    private static function ScrubPropertyName(string $propertyToken) : string
    {
      $length = strlen($propertyToken);
      $retValue = substr($propertyToken, 0, $length - 1);
      return $retValue;
    } // ScrubPropertyName

    // ---------------
    // Constructors - LJCDocDataGen

    /// <summary>
    ///		Initializes a class instance.
    ///		And More.
    /// </summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataGenLib", "LJCDocDataGen"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->ClassName = null;
      $this->Comments = new LJCComments();
      $this->DocDataFile = new LJCDocDataFile("");
      $this->FunctionName= null;
      $this->InputStream = null;
      $this->LibName = null;
      $this->Line = null;
      $this->PropertyName = null;
    } // __construct()

    // ---------------
    // Public Methods - LJCDocDataGen

    // Creates and writes the DocData XML.
    /// <include path='items/CreateDocDataXMLString/*' file='Doc/LJCDocDataGen.xml'/>
    public function CreateDocDataXMLString(string $codeFileSpec
      , bool $writeXML = false, string $outputPath = null) : ?string
    {
      $enabled = false;
      $this->Debug->BeginMethod("CreateDocDataXMLString", $enabled);
      $retValue = null;

      // Populate Library(File) XMLComment values.
      $this->LibName = LJCCommon::GetFileName($codeFileSpec);
      // ***** Begin
      if ("LJCDocDataGenLib" == $this->LibName
        || "GenCodeDocLib" == $this->LibName)
      {
        $writeXML = true;
      }
      // ***** End
      $this->Comments->LibName = $this->LibName;
      $this->DocDataFile = new LJCDocDataFile($this->LibName);

      $retValue = $this->ProcessCode($codeFileSpec);
      if ($writeXML)
      {
        $outputFileSpec = $this->DocOutputFileSpec($codeFileSpec, $outputPath);
        LJCWriter::WriteFile($retValue, $outputFileSpec);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // CreateDocDataXMLString()

    // Generates the Doc data for the file.
    /// <include path='items/ProcessCode/*' file='Doc/LJCDocDataGen.xml'/>
    public function ProcessCode(string $codeFileSpec) : ?string
    {
      $enabled = false;
      $this->Debug->BeginMethod("ProcessCode", $enabled);
      $retValue = null;

      $success = true;
      if (false == file_exists($codeFileSpec))
      {
        $success = false;
        $this->Debug->BeginMethod("ProcessCode");
        $this->Debug->Write(__LINE__." $codeFileSpec was not found.");
        $this->Debug->EndMethod();
      }
      if ($success)
      {
        $this->InputStream = fopen($codeFileSpec, "r+");
        if (null == $this->InputStream)
        {
          $success = false;
          $this->Debug->BeginMethod("ProcessCode");
          $this->Debug->Write(__LINE__." Unable to open $codeFileSpec.");
          $this->Debug->EndMethod();
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
          $tokens = LJCCommon::GetTokens($this->Line);
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

    // Creates the DocData XML output file spec.
    /// <include path='items/DocOutputFileSpec/*' file='Doc/LJCDocDataGen.xml'/>
    private function DocOutputFileSpec(string $codeFileSpec
      , string $outputPath = null) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("DocOutputFileSpec", $enabled);
      $retValue = null;

      if (null == $outputPath)
      {
        $outputPath = "../XMLDocData";
      }
      LJCCommon::MkDir($outputPath);
      $fileName = LJCCommon::GetFileName($codeFileSpec) . ".xml";
      $retValue = "$outputPath/$fileName";

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // DocOutputFileSpec()

    // Indicates if the Syntax eligible statement is continued.
    private function IsSyntaxContinue(string $line) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("IsSyntaxContinue", $enabled);
      $retValue = false;

      $trimLine = trim($line);
      $textIndex = LJCCommon::StrRPos($trimLine, ":");
      if ($textIndex > 0)
      {
        $textIndex = strlen($trimLine) - 1;
      }
      if ($textIndex < 0)
      {
        $textIndex = LJCCommon::StrRPos($trimLine, ")");
      }
      $textLength = strlen($trimLine);
      if ($textIndex < $textLength - 2)
      {
        $retValue = true;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // IsSyntaxContinue()

    // Process XML Comment or Skip Null line and Comment Line.
    private function LineProcessed(?string $line, string $codeFileSpec) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("LineProcessed", $enabled);
      $retProcessed = false;

      $trimLine = trim($line);
      if (null == $trimLine)
      {
        $retProcessed = true;
      }

      if (false == $retProcessed)
      {
        $position = LJCCommon::StrPos($trimLine, "///");
        if (0 == $position)
        {
          $tokens = LJCCommon::GetTokens($trimLine);
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
        $position = LJCCommon::StrPos($trimLine, "//");
        if (0 == $position)
        {
          $retProcessed = true;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retProcessed;
    } // LineProcessed()

    // Process the Class XML data.
    private function ProcessClass() : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessClass", $enabled);

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
      $class->Remarks = $this->Comments->Remarks;
      $class->Code = $this->Comments->Code;

      $this->Comments->ClearComments();

      $this->Debug->EndMethod($enabled);
    } // ProcessClass()

    // Process the Function XML data.
    private function ProcessFunction() : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessFunction", $enabled);

      $classes = $this->DocDataFile->Classes;
      $class = $classes->Get($this->ClassName);
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

      $this->SetFunctionSyntax();
      $method->Params = $this->Comments->Params;
      $method->Syntax = $this->Syntax;
      $method->Remarks = $this->Comments->Remarks;
      $method->Code = $this->Comments->Code;

      $this->Comments->ClearComments();

      $this->Debug->EndMethod($enabled);
    } // ProcessFunction()

    // Processes the Class, Function or Property.
    // $tokens - The array of line tokens.
    private function ProcessItem(array $tokens) : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessItem", $enabled);

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

    // Process the Lib XML data.
    private function ProcessLib() : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessLib", $enabled);

      $docDataFile = $this->DocDataFile;
      $docDataFile->Summary = $this->Comments->Summary;
      $docDataFile->Remarks = $this->Comments->Remarks;

      $this->Comments->ClearComments();

      $this->Debug->EndMethod($enabled);
    } // ProcessLib()

    // Process the Property XML data.
    private function ProcessProperty() : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessProperty", $enabled);

      $classes = $this->DocDataFile->Classes;
      $class = $classes->Get($this->ClassName);
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

    // Sets the Syntax value for a function.
    private function SetFunctionSyntax() : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SetFunctionSyntax", $enabled);

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

    // Writes an output line.
    private function Output($text = null, $value = null)
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("Output", $enabled);

      $lib = "";
      //$lib = "LJCCommonLib";
      if ("" == $lib
        ||$lib == $this->LibName
        || $lib == $this->IncludeFile->LibName)
      {
        LJCWriter::Write($text);
        if ($value != null)
        {
          if (is_array($value))
          {
            foreach ($value as $item)
            {
              LJCWriter::WriteLine(":\r\n|$item|");
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
    // Private Properties - LJCDocDataGen

    // The Class name.
    private ?string $ClassName;

    // The XML Comments object.
    private LJCComments $Comments;

    // The DocDataFile object.
    private ?LJCDocDataFile $DocDataFile;

    // The Function name.
    private ?string $FunctionName;

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