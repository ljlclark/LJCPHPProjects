<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // CommonTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  // LJCCommonLib: LJC
  // LJCCollectionLib: LJCCollectionBase

  /// <summary>The Common Test Class Library</summary>
  /// LibName: CommonTest

  class Names extends LJCCollectionBase
  {
    public function Add(string $text)
    {
      $this->AddItem($text);
    }

    public string $Name;
  }

  $testBuilder = new CommonTest();
  $testBuilder->Run();

  // ********************
  /// <summary>The Common Test Class</summary>
  class CommonTest
  {
    /// <summary>Runs the Common tests.</summary>
    public static function Run()
    {
      // Setup static debug to output.
      $className = "CommonTest";
      $methodName = "Run()";

      echo("\r\n");
      echo("*** LJCCommon ***");

      // String Functions
      self::CreateJSON();
      self::EndsWithNewLine();
      self::GetDelimitedString();
      self::GetTokens();
      self::NewLineTrim();
      self::ParseJSON();
      self::Scrub();
      self::Split();
      self::StrPos();
      self::StrRPos();

      // File Name Functions
      self::GetFileName();
      self::GetFileSpecPath();

      // Check Value Functions
      self::HasElements();
      self::HasItems();
      self::HasValue();
      self::HasXML();

      // Array Functions
      self::RemoveString();

      // Convert Functions
      self::ToArray();
      self::ToBool();
      self::ToBoolInt();
      self::XMLToString();

      // Output Functions
      self::OutputLogCompare();
      self::HasDiff();
      self::ShowFirstDiff();
      self::ShowWhiteSpace();

      // Output Class Functions
      self::Location();
      self::Log();
      self::LogObject();
      self::LogValue();
    }

    // --------------------
    // String Functions

    // Creates JSON from the provided value.
    private static function CreateJSON()
    {
      // Cast array to stdClass object.
      $value = (object) [
        "Name" => "John",
        "Number" => 5,
      ];
      $result = LJC::CreateJSON($value);

      $compare = "{\"Name\":\"John\",\"Number\":5}";
      LJC::OutputLogCompare("CreateJSON()", $result, $compare);
    }

    // Indicates if the builder text ends with a newline.
    private static function EndsWithNewLine()
    {
      $text = "What?\r\n";
      $value = LJC::EndsWithNewLine($text);
      $result = $value ? "true" : "false";

      $compare = "true";
      LJC::OutputLogCompare("EndsWithNewLine()", $result, $compare);
    }

    // Gets the string between the delimiters.
    private static function GetDelimitedString()
    {
      $text = "This | here |.";
      $beginDelimiter = "|";
      $endDelimiter = "|";
      $result = LJC::GetDelimitedString($text, $beginDelimiter, $endDelimiter);

      $compare = "here";
      LJC::OutputLogCompare("GetDelimitedString()", $result, $compare);
    }

    // Get string tokens.
    private static function GetTokens()
    {
      $text = "Now|is|the|time.";
      $values = LJC::GetTokens($text, "/\|/");
      $result = join(", ", $values);

      $compare = "Now, is, the, time.";
      LJC::OutputLogCompare("GetTokens()", $result, $compare);
    }

    // Removes newline from text.
    private static function NewLineTrim()
    {
      $text = "Now\r\n";
      $result = LJC::NewLineTrim($text);

      $compare = "Now";
      LJC::OutputLogCompare("NewLineTrim()", $result, $compare);

      $text = "Now\n";
      $result = LJC::NewLineTrim($text);

      $compare = "Now";
      LJC::OutputLogCompare("NewLineTrim()", $result, $compare);
    }

    // Parses JSON into an object.
    private static function ParseJSON()
    {
      $text = "{\"Name\":\"John\",\"Number\":5}";
      $object = LJC::ParseJSON($text);
      $result = print_r($object, true);

      // Cast array to stdClass object.
      $object = (object) [
        "Name" => "John",
        "Number" => 5,
      ];
      $compare = print_r($object, true);
      LJC::OutputLogCompare("ParseJSON()", $result, $compare);
    }

    // Returns a scrubbed external value.
    private static function Scrub()
    {
      $result = LJC::Scrub(" </tag> ");

      $compare = "&lt;/tag&gt;";
      LJC::OutputLogCompare("Scrub()", $result, $compare);
    }

    // Splits a string on whitespace.
    private static function Split()
    {
      $text = "Now is the time.";
      $values = LJC::Split($text);
      $result = join(", ", $values);

      $compare = "Now, is, the, time.";
      LJC::OutputLogCompare("GetTokens()", $result, $compare);
    }

    // Gets the first index for the search value.
    private static function StrPos()
    {
      $text = "This here.";
      $find = "Here";
		  $index = LJC::StrPos($text, $find);
      $result = strval($index);

      $compare = "5";
      LJC::OutputLogCompare("StrPos()", $result, $compare);

		  $index = LJC::StrPos("This here.", "Here", exact: true);
      $result = strval($index);

      $compare = "-1";
      LJC::OutputLogCompare("StrPos()", $result, $compare);
    }

    // Gets the last index for the search value.
    private static function StrRPos()
    {
      $text = "This here is here.";
      $find = "Here";
		  $index = LJC::StrRPos($text, $find);
      $result = strval($index);

      $compare = "13";
      LJC::OutputLogCompare("StrRPos()", $result, $compare);

		  $index = LJC::StrPos("This here.", "Here", exact: true);
      $result = strval($index);

      $compare = "-1";
      LJC::OutputLogCompare("StrRPos()", $result, $compare);
    }

    // --------------------
    // File Name Functions

    // Gets the FileName from the file spec.
    private static function GetFileName()
    {
      $fileSpec = "../Folder/File.txt";
      $result = LJC::GetFileName($fileSpec);

      $compare = "File";
      LJC::OutputLogCompare("GetFileName()", $result, $compare);
    }

    // Gets the Path from the file spec.
    private static function GetFileSpecPath()
    {
      $fileSpec = "../Folder/File.txt";
      $result = LJC::GetFileSpecPath($fileSpec);

      $compare = "../Folder";
      LJC::OutputLogCompare("GetFileSpecPath()", $result, $compare);
    }

    // --------------------
    // Check Value Functions

    // Checks for array elements.
    private static function HasElements()
    {
      $array = [];
      $value = LJC::HasElements($array);
      $result = $value ? "true" : "false";

      $compare = "false";
      LJC::OutputLogCompare("HasElements()", $result, $compare);
    }

    // Checks for collection items.
    private static function HasItems()
    {
      $names = new Names();
      $names->Add("First");

      $value = LJC::HasItems($names);
      $result = $value ? "true" : "false";

      $compare = "true";
      LJC::OutputLogCompare("HasItems()", $result, $compare);
    }

    // Checks for text.
    private static function HasValue()
    {
      $text = "Text";
      $value = LJC::HasValue($text);
      $result = $value ? "true" : "false";

      $compare = "true";
      LJC::OutputLogCompare("HasValue()", $result, $compare);
    }

    // Checks an XML element for a value.
    private static function HasXML()
    {
      $xml = new SimpleXMLElement("<name>Name</name>");
      $string = LJC::XMLToString($xml);
      $value = LJC::HasXML($xml);
      $result = $value ? "true" : "false";

      $compare = "true";
      LJC::OutputLogCompare("HasXML()", $result, $compare);
    }

    // --------------------
    // Array Functions

    // Remove element from string array by value.
    private static function RemoveString()
    {
      $array = ["First", "Second", "Third"];
      LJC::RemoveString($array, "Second");
      $result = join(" ", $array);

      $compare = "First Third";
      LJC::OutputLogCompare("RemoveString()", $result, $compare);
    }

    // --------------------
    // Convert Functions

    // Copy collection items to an indexed array.
    private static function ToArray()
    {
      $names = new Names();
      $names->Add("First");
      $array = LJC::ToArray($names);
      $result = $array[0];

      $compare = "First";
      LJC::OutputLogCompare("ToArray()", $result, $compare);
    }

    // Returns a value as bool.
    private static function ToBool()
    {
      $text = "false";
      $value = LJC::ToBool($text);
      $result = $value ? "true" : "false";

      $compare = "false";
      LJC::OutputLogCompare("ToBool()", $result, $compare);
    }

    // Returns a text value as int boolean.
    private static function ToBoolInt()
    {
      $text = "false";
      $value = LJC::ToBoolInt($text);
      $result = strval($value);

      $compare = "0";
      LJC::OutputLogCompare("ToBoolInt()", $result, $compare);
    }

    // Gets a string value from the XML value.
    private static function XMLToString()
    {
      $xml = new SimpleXMLElement("<name>Name</name>");
      $result = LJC::XMLToString($xml);

      $compare = "Name";
      LJC::OutputLogCompare("XMLToString()", $result, $compare);
    }

    // --------------------
    // Output Functions

    // Outputs the test compare text.
    private static function OutputLogCompare()
    {
      $methodName = "OutputLogCompare()";

      $result = "Test";

      $compare = "Test";
      $output = LJC::OutputLogCompare($methodName, $result, $compare
        , output: false);
      if ("" == $output)
      {
        $output = "-";
      }
      $outputCompare = "-";
      LJC::OutputLogCompare($methodName, $output, $outputCompare);


      $compare = "Te st";
      $output = LJC::OutputLogCompare($methodName, $result, $compare
        , output: false);
      $outputCompare = "\r\nOutputLogCompare()\r\n";
      $outputCompare .= "Test\r\n";
      $outputCompare .= " !=\r\n";
      $outputCompare .= "Te st\r\n";
      LJC::OutputLogCompare($methodName, $output, $outputCompare);
    }

    // Checks if two strings are different.
    private static function HasDiff()
    {
      $result = "This is the first line.\n";
      $result .= "This is the second line.\n";

      $compare = "This is the first line.\n";
      $compare .= "This is the second line.\n";
      if (LJC::HasDiff($result, $compare))
      {
        echo("\r\n\r\nHasDiff()");
        LJC::ShowFirstDiff($result, $compare);
      }
    }

    // Shows the first difference between two strings.
    private static function ShowFirstDiff()
    {
      $result = "This is the first line.\n";
      $result .= "This is the second line.\n";

      $compare = "This is the first line.\n";
      $compare .= "This is the second line.\n";
      if (LJC::HasDiff($result, $compare))
      {
        echo("\r\n\r\nShowFirstDiff()");
        LJC::ShowFirstDiff($result, $compare);
      }
    }

    // Returns a string that shows the whitespace.
    private static function ShowWhiteSpace()
    {
      $result = LJC::ShowWhiteSpace("This is a line.\n");

      $compare = "This is a line.\\n";
      LJC::OutputLogCompare("ShowWhiteSpace()", $result, $compare);
    }

    // --------------------
    // Output Class Functions

    // Gets the location string.
    private static function Location()
    {
      $output = new Output("CommonTest");
      $output->MethodName = "Output.Location";

      $valueName = "\$value";
      $lineNumber = __line__;
      $result = $output->Location($lineNumber, $valueName);

      $compare = "{$lineNumber} CommonTest.Output.Location \$value:";
      LJC::OutputLogCompare("Location()", $result, $compare);
    }

    // Outputs the value or object text.
    private static function Log()
    {
      $output = new Output("CommonTest");
      $output->MethodName = "Output.Log";

      // Cast array to stdClass object.
      $object = (object) [
        "Name" => "John",
        "Number" => 5,
      ];
      $value = LJC::CreateJSON($object);
      $lineNumber = __line__;
      // Log value.
      $result = $output->Log($lineNumber, "\$value", $value, output: false);

      $compare = "\r\n{$lineNumber} \$value = {\"Name\":\"John\",\"Number\":5}";
      LJC::OutputLogCompare("Output.Log()", $result, $compare);

      $value = print_r($object, true);
      $lineNumber = __line__;
      // Log object.
      $result = $output->Log($lineNumber, "\$value", $value, output: false);

      $tb = new LJCTextBuilder();
      $tb->AddText("\r\n{$lineNumber} \$value = stdClass Object\n");
      $tb->AddText("(\n");
      $tb->AddText("    [Name] => John\n");
      $tb->AddText("    [Number] => 5\n");
      $tb->AddText(")\n");
      $compare = $tb->ToString();
      LJC::OutputLogCompare("Output.Log()", $result, $compare);
    }

    // Outputs the object text.
    private static function LogObject()
    {
      $output = new Output("CommonTest");
      $output->MethodName = "Output.LogObject";

      // Cast array to stdClass object.
      $object = (object) [
        "Name" => "John",
        "Number" => 5,
      ];
      $value = print_r($object, true);
      $lineNumber = __line__;
      // Log object.
      $result = $output->Log($lineNumber, "\$value", $value, output: false);

      $tb = new LJCTextBuilder();
      $tb->AddText("\r\n{$lineNumber} \$value = stdClass Object\n");
      $tb->AddText("(\n");
      $tb->AddText("    [Name] => John\n");
      $tb->AddText("    [Number] => 5\n");
      $tb->AddText(")\n");
      $compare = $tb->ToString();
      LJC::OutputLogCompare("Output.Log()", $result, $compare);
    }

    // Outputs the value text.
    private static function LogValue()
    {
      $output = new Output("CommonTest", "Output.LogValue");
      $output->Method = "Output.LogValue";

      // Cast array to stdClass object.
      $object = (object) [
        "Name" => "John",
        "Number" => 5,
      ];
      $value = LJC::CreateJSON($object);
      $lineNumber = __line__;
      // Log value.
      $result = $output->LogValue($lineNumber, "\$value", $value, output: false);

      $compare = "\r\n{$lineNumber} \$value = {\"Name\":\"John\",\"Number\":5}";
      LJC::OutputLogCompare("Output.Log()", $result, $compare);
    }
  }
