<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // TextBuilderTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  // LJCCommonLib: LJC
  // LJCTextBuilderLib: LJCAttributes, LJCTextBuilder, LJCTextState

  /// <summary>The Text Builder Test Class Library</summary>
  /// LibName: TextBuilderTest

  $testBuilder = new TextBuilderTest();
  $testBuilder->Run();

  // ********************
  /// <summary>The Text Builder Test Class</summary>
  /// <include path='items/LJCTextBuilder/*' file='Doc/LJCTextBuilder.xml'/>
  class TextBuilderTest
  {
    /// <summary>Runs the LJCTextBuilder tests.</summary>
    public static function Run()
    {
      // Setup static debug to output.
      $className = "TextBuilderTest";
      $methodName = "Run()";

      echo("\r\n");
      echo("*** LJCTextBuilder ***");

      // Often one needs to create a text value from multiple lines and values.
      // The code could look something like this:
      $value = "Start";
      $finalText = "Line One"
        . " {$value}\r\n"
        . "Line Two\r\n"
        . "Line Two\r\n";
      // or
      $finalText = "Line One";
      $finalText .= " {$value}\r\n";
      $finalText .= "Line Two\r\n";
      $finalText .= "Line Three\r\n";

      // Result:
      // Line One Start
      // Line Two
      // Line Three

      // The text builder object simplifies this syntax and provides additional
      // functionality.

      // The text builder stores a built text string internally. The text is
      // retrieved with the ToString() method.
 
      // --------------------
      // Add Text Methods

      // The text builder "Add Text" methods are: AddLine() and AddText().

      // These "Add Text" methods add text to the builder unmodified except for
      // AddLine() which appends a newline.

      $value = "Start";
      $tb = new LJCTextBuilder();
      $tb->AddText("Line One");
      $tb->AddLine(" {$value}");
      $tb->AddLine("Line Two");
      $tb->AddLine("Line Three");
      $finalText = $tb->ToString();

      // Result:
      // Line One Start
      // Line Two
      // Line Three

      self::AddLine();
      self::AddText();

      // --------------------
      // Append Text Methods

      // The text builder "Append Text" methods are: Line() and Text().

      // The Text() method appends the supplied text without an ending newline.
      // This behavior is done for consistency and allows for appending to the
      // existing text if desired.

      // The "Append Text" methods start the appended text with a newline if the
      // builder already has text, param allowNewLine = true (default) and the
      // builder text does not already end with a newline.

      $value = "Start";
      $tb = new LJCTextBuilder();
      // Does not start with a newline as the builder has no text.
      $tb->Text("Line One");
      $tb->Text(" {$value}", allowNewLine: false);
      $tb->Text("Line Two");
      $tb->Text("Line Three");
      $finalText = $tb->ToString();

      // Result:
      // Line One Start
      // Line Two
      // Line Three

      self::Line();
      self::Text();

      // --------------------
      // Get Text Methods

      // The "Get Text" methods create and return the potentialy indented and
      // wrapped text but do not add it to the builder.
      
      // This allows for using the text outside of the builder.

      // The GetLine() and GetText() methods modify the returned text the same
      // as the Line() and Text() methods.

      // Use the "Append Text", "addIndent" parameter set to "false" when
      // appending text to the builder that was retrieved using a "Get Text"
      // method. This is because the indent may already have been added to the
      // text by the "Get Text" method.

      $tb = new LJCTextBuilder();
      // Manually adds an indent to the builder.
      // The increment parameter defaults to 1.
      $tb->AddIndent();
      $someText = $tb->GetText("Indented Text");
      $tb->Text($someText, addIndent: false);
      $finalText = $tb->ToString();

      // Result:
      //   Indented Text

      self::GetLine();
      self::GetText();

      // --------------------
      // Other Get Text Methods

      self::GetIndented();
      self::GetIndentString();
      self::GetWrapped();

      // --------------------
      // Get Attribs Methods

      // The "Get Attribs" methods provide for retrieving specific Attributes
      // without having to create an Attributes object and adding Attributes
      // manually.

      // See the individual test methods for examples.

      self::Attribs();
      self::GetAttribs();
      self::StartAttribs();
      self::StartXMLAttribs();
      self::TableAttribs();

      // --------------------
      // Append Element Methods

      // The "Append Element" methods are used for HTML or XML and provide
      // additional functionality for adding elements or nodes to a string.

      // The code for adding a start tag could look like this:
      $tb = new LJCTextBuilder();
      $tb->Text("<Element attribute=\"Start\">");
      $finalText = $tb->ToString();

      // Result:
      // <Element attribute="Start">

      // Using the attributes collection and the Begin() method allows for 
      // focusing on the data values and letting the "Append Element" methods
      // add the punctuation characters.
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder();
      $attribs = new LJCAttributes();
      $attribs->Add("attribute", "Start");
      $tb->Begin("Element", $textState, $attribs);
      $finalText = $tb->ToString();

      // Result:
      // <Element attribute="Start">

      // The first example may seem easier in this simple case, but it is more
      // challenging if multiple attributes are required or when entering
      // multiple nodes and trying to keep track of indentation. Indentation
      // and text state will be talked about next.

      // These methods append element tags and content. Begin() automatically
      // increments the IndentCount by 1. Create() also increments the
      // IndentCount by 1 if the isEmpty parameter is false and the close
      // parameter is false. End() automatically decrements the IndentCount by 1
      // if the IndentCount is currently greater than zero.&lt;br /&gt;

      // The "Append Element" methods append the supplied text without an ending
      // newline. This behavior is done for consistency and allows for appending
      // to the existing text if desired.

      // The "Append Element" methods start the appended text with a newline if
      // the builder already has text, param allowNewLine = true (default) and
      // the builder text does not already end with a newline.

      // Using Begin() and End() separately allows for adding the contained
      // elements.
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder();
      $tb->Begin("Parent", $textState);
      $tb->Begin("Child", $textState);
      $tb->End("Child", $textState);
      $tb->End("Parent", $textState);
      $finalText = $tb->ToString();

      // Result:
      // <Parent>
      //   <Child>
      //   </Child>
      // </Parent>

      // Notice that besides the nodes having all the required punctuation the
      // child nodes are indented automatically.

      // The Begin() method increments the indent count for the following text.
      // It keeps track of the current indent count internally and in the text
      // state object.

      // Use the "childIndent" argument To prevent incrementing the indent
      // count for the first child level nodes.

      // Typically the first level nodes of an HTML document are not indented.
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder();
      $attribs = $tb->StartAttribs();
      $tb->Begin("html", $textState, $attribs, childIndent: false);
      $tb->Begin("head", $textState);
      $tb->End("head", $textState);
      $tb->Begin("body", $textState);
      $tb->End("body", $textState);
      $tb->End("html", $textState);
      $finalText = $tb->ToString();

      // Result:
      // <html lang="en">
      // <head>
      // </head>
      // <body>
      // </body>
      // </html>
      
      self::Begin();
      self::Create();
      self::End();

      // --------------------
      // Get Element Methods

      // The "Get Element" methods apply the same rules as the "Append Element"
      // methods except that the text is only returned to the calling code
      // instead of being added to the builder.
      
      // This allows for using the text outside of the builder.

      // Use the "Append Text", "addIndent" parameter set to "false" when
      // appending text to the builder that was retrieved using a "Get Element"
      // method. This is because the indent may already have been added to the
      // text by the "Get Element" method.

      self::GetBegin();
      self::GetCreate();
      self::GetEnd();

      // --------------------
      // Other Methods

      // See the individual test methods for examples.

      self::AddChildIndent();
      self::AddIndent();
      self::EndsWithNewLine();
      self::GetTextState();
      self::HasText();
      self::IndentLength();
      self::StartWithNewLine();
    }

    // --------------------
    // Test Methods

    // Appends a text line without modification.
    private static function AddLine()
    {
      $tb = new LJCTextBuilder();

      // Example Method:
      $tb->AddLine("This is an appended line.");

      $tb->AddText(":");
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddText(":");
      $compare = $b->ToString();
      LJC::OutputLogCompare("AddLine()", $result, $compare);
    }

    // Appends text without modification.
    private static function AddText()
    {
      $tb = new LJCTextBuilder();

      // Example Method:
      $tb->AddText("This is some appended text.");
      $result = $tb->ToString();

      $compare = "This is some appended text.";
      LJC::OutputLogCompare("AddText()", $result, $compare);
    }

    // Appends a potentially indented text line to the builder.
    private static function Line()
    {
      $tb = new LJCTextBuilder();

      $tb->Text("This is an appended line.");
      $tb->AddIndent();

      // Example Method:
      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Ends the text with a newline.
      // Defaults: addIndent = true, allowNewLine = true.
      $tb->Line();

      $tb->Text("This is an indented line.");
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddLine();
      $b->AddText("  This is an indented line.");
      $compare = $b->ToString();
      LJC::OutputLogCompare("Line()", $result, $compare);
    }

    // Appends the potentially indented text.
    private static function Text()
    {
      $tb = new LJCTextBuilder();

      $tb->Text("This is an appended line.");
      $tb->AddIndent();

      // Example Method:
      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Defaults: addIndent = true, allowNewLine = true.
      $tb->Text("This is an indented line.");
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddText("  This is an indented line.");
      $compare = $b->ToString();
      LJC::OutputLogCompare("Text()", $result, $compare);
    }

    // Gets a modified text line.
    private static function GetLine()
    {
      $tb = new LJCTextBuilder();

      $tb->AddText("This is an appended line.");
      $tb->AddIndent();

      // Example Method:
      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Ends the text with a newline.
      // Defaults: addIndent = true, allowNewLine = true.
      $text = $tb->GetLine();
      $tb->Text($text, addIndent: false);

      $tb->Text(":");
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddText("  :");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetLine()", $result, $compare);
    }

    // Gets the potentially indented text.
    private static function GetText()
    {
      $tb = new LJCTextBuilder();

      $tb->Text("This is an appended line.");
      $tb->AddIndent();

      // Example Method:
      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Defaults: addIndent = true, allowNewLine = true.
      $text = $tb->GetText("This is an indented line.");
      $tb->AddText($text);
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddText("  This is an indented line.");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetText()", $result, $compare);
    }

    // Gets a new potentially indented line.
    private static function GetIndented()
    {
      $tb = new LJCTextBuilder();

      // Example Method:
      $result = $tb->GetIndented("This text is NOT indented.");
      $tb->AddText($result);

      $tb->AddIndent(2);
      $tb->AddLine();
      $result = $tb->GetIndented("This text is indented.");
      $tb->Text($result, addIndent: false);
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddLine("This text is NOT indented.");
      $b->AddText("    This text is indented.");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetIndented()", $result, $compare);
    }

    // Gets the current indent string.
    private static function GetIndentString()
    {
      $tb = new LJCTextBuilder();

      $tb->AddIndent();

      // Example Method:
      $text = $tb->GetIndentString();
      $tb->AddText($text);

      $tb->AddText(":");
      $result = $tb->ToString();

      $compare = "  :";
      LJC::OutputLogCompare("GetIndentString()", $result, $compare);
    }

    // Appends added text and new wrapped line if combined line > LineLimit.
    private static function GetWrapped()
    {
      $tb = new LJCTextBuilder();
      $tb->WrapEnabled = true;

      // Example Method:
      $tb->AddText("Now is the time for all good men to come to the aid of their");
      $tb->AddText(" country.");
      $tb->AddText(" Now is the time for all good men to come to the aid of their");
      $tb->AddText(" country.");
      $text = $tb->ToString();
      $result = $tb->GetWrapped($text);

      $b = new LJCTextBuilder();
      $b->AddText("Now is the time for all good men to come to the aid of");
      $b->AddLine(" their country. Now is the");
      $b->AddText("time for all good men to come to the aid of their country.");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetWrapped()", $result, $compare);
    }

    // --------------------
    // Get Attribs Methods

    // Gets common element attributes.
    private static function Attribs()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $className = "className";
      $id = "id";
      $attribs = $tb->Attribs($className, $id);

      $result = $tb->GetAttribs($attribs, $textState);

      $compare = " id=\"id\" class=\"className\"";
      LJC::OutputLogCompare("Attribs()", $result, $compare);
    }

    // Gets the attributes text.
    private static function GetAttribs()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      $tb = new LJCTextBuilder($textState);

      $className = "className";
      $id = "id";
      $attribs = $tb->Attribs($className, $id);

      // Example Method:
      $result = $tb->GetAttribs($attribs, $textState);

      $compare = " id=\"id\" class=\"className\"";
      LJC::OutputLogCompare("Attribs()", $result, $compare);
    }

    // Creates the HTML element attributes.
    private static function StartAttribs()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $attribs = $tb->StartAttribs();

      $result = $tb->GetAttribs($attribs, $textState);

      $compare = " lang=\"en\"";
      LJC::OutputLogCompare("Attribs()", $result, $compare);
    }

    // Creates the XML element attributes.
    private static function StartXMLAttribs()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $attribs = $tb->StartXMLAttribs();

      $result = $tb->GetAttribs($attribs, $textState);

      $compare = " xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"\r\n";
      $compare .= " xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"";
      LJC::OutputLogCompare("Attribs()", $result, $compare);
    }

    // Gets common table attributes.
    private static function TableAttribs()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $border = 1;
      $cellspacing = 2;
      $cellpadding = 3;
      // Defaults: border = 1, borderspacing = 0, cellpadding = 2.
      $attribs = $tb->TableAttribs($border, $cellspacing, $cellpadding);

      $result = $tb->GetAttribs($attribs, $textState);

      $compare = " style=\"border: 1px solid;";
      $compare .= " borderspacing: 2px;";
      $compare .= " cellpadding: 3px;\"";
      LJC::OutputLogCompare("Attribs()", $result, $compare);
    }

    // --------------------
    // Append Element Methods

    // Appends the element begin tag.
    private static function Begin()
    {
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $tb->Begin("body", $textState);
      $result = $tb->ToString();

      $compare = "<body>";
      LJC::OutputLogCompare("Begin()", $result, $compare);
    }

    // Appends an element.
    private static function Create()
    {
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $tb->Create("p", $textState);
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddText("<p></p>");
      $compare = $b->ToString();
      LJC::OutputLogCompare("Create()", $result, $compare);
    }

    // Appends the element end tag.
    private static function End()
    {
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $tb->End("p", $textState);
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddText("</p>");
      $compare = $b->ToString();
      LJC::OutputLogCompare("End()", $result, $compare);
    }

    // --------------------
    // Get Element Methods

    // Gets the element begin tag.
    private static function GetBegin()
    {
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $result = $tb->GetBegin("body", $textState);

      $b = new LJCTextBuilder();
      $b->AddText("<body>");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetBegin()", $result, $compare);
    }

    // Gets the element text.
    private static function GetCreate()
    {
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $result = $tb->GetCreate("p", "", $textState);

      $b = new LJCTextBuilder();
      $b->AddText("<p></p>");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetCreate()", $result, $compare);
    }

    // Gets the element end tag.
    private static function GetEnd()
    {
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $result = $tb->GetEnd("p", $textState);

      $b = new LJCTextBuilder();
      $b->AddText("</p>");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetEnd()", $result, $compare);
    }

    // --------------------
    // Other Methods

    // Adds the new (child) indents.
    private static function AddChildIndent()
    {
      $textState = new LJCTextState();

      $result = self::CustomBegin("body", $textState);

      $compare = "<body>";
      LJC::OutputLogCompare("AddChildIndent()", $result, $compare);
    }

    // The custom begin Element method.
    private static function CustomBegin(string $name, LJCTextState $textState
      , Attributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true) : string
    {
      $tb = new LJCTextBuilder($textState);

      $createText = $tb->GetBegin($name, $textState, $attribs, $addIndent
        , $childIndent);
      $tb->Text($createText, addIndent: false);

      // Use AddChildIndent after beginning an element.
      $tb->AddChildIndent($createText, $textState);

      $result = $tb->ToString();
      return $result;
    }

    // Changes the IndentCount by the provided value.
    private static function AddIndent()
    {
      $tb = new LJCTextBuilder();

      // Example Method:
      // The builder keeps track of the current number of indents.
      // Adds 1 indent by default.
      $tb->AddIndent();

      // Adds text without modification.
      $tb->AddText("This text is not indented.");

      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Defaults: addIndent = true, allowNewLine = true.
      $tb->Text("This text is indented.");

      // No Indent
      $tb->Text("Not indented.", addIndent: false);

      // Do not start a newline.
      $tb->AddText(" No start with newline.");
      $result = $tb->ToString();

      $b = new LJCTextBuilder();
      $b->AddLine("This text is not indented.");
      $b->AddLine("  This text is indented.");
      $b->AddText("Not indented. No start with newline.");
      $compare = $b->ToString();
      LJC::OutputLogCompare("AddIndent()", $result, $compare);
    }

    // Indicates if the builder text ends with a newline.
    private static function EndsWithNewLine()
    {
      $tb = new LJCTextBuilder();

      // Example Method:
      $retValue = $tb->EndsWithNewLine();
      $result = $retValue ? "true" : "false";

      $b = new LJCTextBuilder();
      $b->AddText("false");
      $compare = $b->ToString();
      LJC::OutputLogCompare("EndsWithNewLine()", $result, $compare);
      return $retValue;
    }

    // Gets a current LJCTextState object.
    private static function GetTextState()
    {
      $tb = new LJCTextBuilder();

      $textState = $tb->GetTextState();
      $retValue = $textState->getIndentCount();
      $result = strval($retValue);

      $compare = "0";
      LJC::OutputLogCompare("HasText()", $result, $compare);
    }

    // Indicates if the builder has text.
    private static function HasText()
    {
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder($textState);

      // Example Method:
      $retValue = $tb->HasText();
      $result = $retValue ? "true" : "false";

      $compare = "false";
      LJC::OutputLogCompare("HasText()", $result, $compare);
    }

    // Gets the current indent length.
    private static function IndentLength()
    {
      $tb = new LJCTextBuilder();

      // Example Method:
      $tb->AddIndent();
      $retValue = $tb->IndentLength();
      $result = strval($retValue);

      $compare = "2";
      LJC::OutputLogCompare("IndentLength()", $result, $compare);
    }

    // Checks if text can start with a newline.
    private static function StartWithNewLine()
    {
      $tb = new LJCTextBuilder();

      // Example Method:
      $allowNewLine = true;
      $retValue = $tb->StartWithNewLine($allowNewLine);
      $result = $retValue ? "true" : "false";

      $b = new LJCTextBuilder();
      $b->AddText("false");
      $compare = $b->ToString();
      LJC::OutputLogCompare("StartWithNewLine()", $result, $compare);
      return $retValue;
    }
  }

