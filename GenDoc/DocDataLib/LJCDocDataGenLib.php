<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCDocDataGenLib.php
  declare(strict_types=1);
  $webCommonPath = "c:/inetpub/wwwroot/LJCPHPCommon";
  $devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
  require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
  require_once "$devPath/LJCPHPCommon/LJCTextLib.php";
  require_once "LJCDocDataLib.php";
  require_once "LJCCommentsLib.php";

  // Contains Classes to generate DocData XML strings and optionally files.
  /// <include path='items/LJCDocDataGenLib/*' file='Doc/LJCDocDataGenLib.xml'/>
  /// LibName: LJCDocDataGenLib

  // Main Call Tree
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
      return $retValue;
    }

    // Gets the Property Name if present. 
    private static function GetPropertyName(array $tokens) : ?string
    {
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
      return $retValue;
    }

    // Gets the Function Name from the function token.
    // <include path='items/ScrubFunctionName/*' file='Doc/LJCDocDataGen.xml'/>
    private static function ScrubFunctionName(string $functionToken) : string
    {
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
      return $retValue;
    }

    // Gets the Property Name from the property token.
    private static function ScrubPropertyName(string $propertyToken) : string
    {
      $length = strlen($propertyToken);
      $retValue = substr($propertyToken, 0, $length - 1);
      return $retValue;
    }

    // ---------------
    // Constructors - LJCDocDataGen

    /// <summary>
    ///		Initializes a class instance.
    ///		And More.
    /// </summary>
    public function __construct()
    {
      $this->DebugClass = "LJCDocDataGen";
      $this->ClassName = null;
      $this->Comments = new LJCComments();
      $this->DocDataFile = new LJCDocDataFile("");
      $this->FunctionName= null;
      $this->InputStream = null;
      $this->LibName = null;
      $this->Line = null;
      $this->PropertyName = null;

      $this->DebugWriter = null;
      //$this->DebugWriter = new LJCDebugWriter("DocDataGen");
    }

    // ---------------
    // Public Methods

    // Creates and writes the DocData XML.
    /// <include path='items/CreateDocXMLString/*' file='Doc/LJCDocDataGen.xml'/>
    public function CreateDocDataXMLString(string $codeFileSpec, bool $writeXML = false
      , string $outputPath = null) : ?string
    {
      $loc = "$this->DebugClass.CreateDocDataXMLString";
      $retValue = null;

      $this->LibName = LJCCommon::GetFileName($codeFileSpec);
      $this->Comments->LibName = $this->LibName;
      $this->DocDataFile = new LJCDocDataFile($this->LibName);
      $retValue = $this->ProcessCode($codeFileSpec);
      if ($writeXML)
      {
        $outputFileSpec = $this->DocOutputFileSpec($codeFileSpec, $outputPath);
        LJCWriter::WriteFile($retValue, $outputFileSpec);
      }
      return $retValue;
    }

    // Generates the Doc data for the file.
    /// <include path='items/ProcessCode/*' file='Doc/LJCDocDataGen.xml'/>
    public function ProcessCode(string $codeFileSpec) : ?string
    {
      $retValue = null;

      $success = true;
      if (false == file_exists($codeFileSpec))
      {
        $success = false;
      }
      if ($success)
      {
        $this->InputStream = fopen($codeFileSpec, "r+");
        if (null == $this->InputStream)
        {
          $success = false;
        }
      }
      if ($success)
      {
        while(false == feof($this->InputStream))
        {
          $this->Line = (string)fgets($this->InputStream);

          // Process Lib or XML Comment. Skips Null line and Comment Line.
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
      return $retValue;
    }  // ProcessCode()

    // ---------------
    // Private Methods - LJCDocDataGen

    // Creates the DocData XML output file spec.
    // <include path='items/DocOutputFileSpec/*' file='Doc/LJCGenDataXML.xml'/>
    private function DocOutputFileSpec(string $codeFileSpec
      , string $outputPath = null) : string
    {
      $retValue = null;

      if (null == $outputPath)
      {
        $outputPath = "../XMLDocData";
      }
      LJCCommon::MkDir($outputPath);
      $fileName = LJCCommon::GetFileName($codeFileSpec) . ".xml";
      $retValue = "$outputPath/$fileName";
      return $retValue;
    }

    // Indicates if the Syntax eligible statement is continued.
    private function IsSyntaxContinue(string $line) : bool
    {
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
      return $retValue;
    }

    // Process XML Comment or Skip Null line and Comment Line.
    private function LineProcessed(?string $line, string $codeFileSpec) : bool
    {
      $loc = "$this->DebugClass.LineProcessed";
      $retValue = false;

      $trimLine = trim($line);
      if (null == $trimLine)
      {
        $retValue = true;
      }

      if (false == $retValue)
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
              $retValue = true;
            }
          }

          if (false == $retValue)
          {
            // Sets the line XML Comment values or Include file values.
            $this->Comments->SetComment($line, $codeFileSpec);
            $retValue = true;
          }
        }
      }

      if (false == $retValue)
      {
        $position = LJCCommon::StrPos($trimLine, "//");
        if (0 == $position)
        {
          $retValue = true;
        }
      }
      return $retValue;
    }  // LineProcessed()

    // Process the Class XML data.
    private function ProcessClass() : void
    {
      $loc = "LJCDocDataGen.ProcessClass";

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
    }

    // Process the Function XML data.
    private function ProcessFunction() : void
    {
      $loc = "LJCDocDataGen.ProcessFunction";

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
    }

    // Processes the Class, Function or Property.
    // $tokens - The array of line tokens.
    private function ProcessItem(array $tokens) : void
    {
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
    }  // ProcessItem()

    // Process the Lib XML data.
    private function ProcessLib() : void
    {
      $loc = "LJCDocDataGen.ProcessLib";

      $docDataFile = $this->DocDataFile;
      $docDataFile->Summary = $this->Comments->Summary;
      $docDataFile->Remarks = $this->Comments->Remarks;

      $this->Comments->ClearComments();
    }

    // Process the Property XML data.
    private function ProcessProperty() : void
    {
      $loc = "LJCDocDataGen.ProcessProperty";

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
    }

    // Sets the Syntax value for a function.
    private function SetFunctionSyntax() : void
    {
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
    }

    // Writes the debug value.
    private function Debug(string $text, bool $addLine = true) : void
    {
      if (isset($this->DebugWriter))
      {
        $this->DebugWriter->Debug($text, $addLine);
      }
    }

    // Writes an output line.
    private function Output($text = null, $value = null)
    {
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

    // The Input File Stream.
    private $InputStream;

    // The Lib name.
    private ?string $LibName;

    // The current Line.
    private ?string $Line;

    // The Property name.
    private ?string $PropertyName;
  }
?>