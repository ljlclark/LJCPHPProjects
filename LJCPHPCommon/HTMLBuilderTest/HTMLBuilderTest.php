<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // HTMLBuilderTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  // LJCCommonLib: LJCCommon
  // LJCHTMLBuilderLib: LJCHTMLBuilder, LJCAttributes, LJCTextState

  /// <summary>The Common Text Output Class Library</summary>
  /// LibName: HTMLBuilderTest

  $testBuilder = new TestHTMLBuilder();
  $testBuilder->Run();

  // ********************
  // Methods: 
  /// <summary>The LJCHTMLBuilder tests.</summary>
  /// <include path='items/LJCHTMLBuilder/*' file='Doc/LJCHTMLBuilder.xml'/>
  class TestHTMLBuilder
  {
    /// <summary>Runs the LJCHTMLBuilder tests.</summary>
    public static function Run()
    {
      echo("\r\n");
      echo("*** LJCHTMLBuilder ***");

      // Methods
      self::AddChildIndent();
      self::AddIndent();
      self::EndsWithNewLine();
      self::StartWithNewLine();
      self::HasText();

      // Text Methods
      self::AddLine();
      self::AddText();
      self::Line();
      self::Text();
      self::GetAttribs();
      self::GetIndented();
      self::GetIndentString();
      self::GetLine();
      self::GetText();
      self::GetWrapped();

      // Element Methods
      self::Begin();
      self::Create();
      self::End();
      self::GetBegin();
      self::GetCreate();
      self::GetEnd();
    }

    // --------------------
    // Methods

    // Adds the new (child) indents.
    private static function AddChildIndent()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      $result = self::CustomBegin("body", $textState);

      $compare = "<body>";
      LJCCommon::WriteCompare("AddChildIndent()", $result, $compare);
    }

    // The custom begin Element method.
    private static function CustomBegin(string $name, LJCTextState $textState
      , Attributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true) : string
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder($textState);

      $createText = $hb->GetBegin($name, $textState, $attribs, $addIndent
        , $childIndent);
      $hb->Text($createText, false);

      // Use AddChildIndent after beginning an element.
      $hb->AddChildIndent($createText, $textState);

      $result = $hb->ToString();
      return $result;
    }

    // Changes the IndentCount by the provided value.
    private static function AddIndent()
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder();

      // Example Method:
      // The builder keeps track of the current number of indents.
      // Adds 1 indent by default.
      $hb->AddIndent();

      // Adds text without modification.
      $hb->AddText("This text is not indented.");

      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Defaults: addIndent = true, allowNewLine = true.
      $hb->Text("This text is indented.");

      // No Indent
      $hb->Text("Not indented.", false);

      // Do not start a newline.
      $hb->AddText(" No start with newline.");
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->AddLine("This text is not indented.");
      $b->AddLine("  This text is indented.");
      $b->AddText("Not indented. No start with newline.");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("AddIndent()", $result, $compare);
    }

    // Indicates if the builder text ends with a newline.
    private static function EndsWithNewLine()
    {
      $hb = new LJCHTMLBuilder();

      $retValue = $hb->EndsWithNewLine();
      $result = strval($retValue);
      if ("" == $result)
      {
        $result = "False";
      }

      $b = new LJCHTMLBuilder();
      $b->AddText("False");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("EndsWithNewLine()", $result, $compare);
      return $retValue;
    }

    // Checks if text can start with a newline.
    private static function StartWithNewLine()
    {
      $hb = new LJCHTMLBuilder();

      $retValue = $hb->StartWithNewLine(true);
      $result = strval($retValue);
      if ("" == $result)
      {
        $result = "False";
      }

      $b = new LJCHTMLBuilder();
      $b->AddText("False");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("StartWithNewLine()", $result, $compare);
      return $retValue;
    }

    // Indicates if the builder has text.
    private static function HasText()
    {
    }

    // --------------------
    // Text Methods

    // Appends a text line without modification.
    private static function AddLine()
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder();

      // Example Method:
      // Adds text that ends with a newline.
      $hb->AddLine("This is an appended line.");

      $hb->AddText(":");
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddText(":");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("AddLine()", $result, $compare);
    }

    // Appends text without modification.
    private static function AddText()
    {
      $hb = new LJCHTMLBuilder();

      // Example Method:
      // Adds text without modification.
      $hb->AddText("This is some appended text.");
      $result = $hb->ToString();

      $compare = "This is some appended text.";
      LJCCommon::WriteCompare("AddText()", $result, $compare);
    }

    // Appends a potentially indented text line to the builder.
    private static function Line()
    {
      $hb = new LJCHTMLBuilder();

      $hb->Text("This is an appended line.");

      // The builder keeps track of the current number of indents.
      $hb->AddIndent();

      // Example Method:
      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Ends the text with a newline.
      // Defaults: addIndent = true, allowNewLine = true.
      $hb->Line();

      $hb->Text("This is an indented line.");
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddLine();
      $b->AddText("  This is an indented line.");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("Line()", $result, $compare);
    }

    // Appends the potentially indented text.
    private static function Text()
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder();

      $hb->Text("This is an appended line.");

      // The builder keeps track of the current number of indents.
      $hb->AddIndent();

      // Example Method:
      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Defaults: addIndent = true, allowNewLine = true.
      $hb->Text("This is an indented line.");
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddText("  This is an indented line.");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("Text()", $result, $compare);
    }

    // Gets the attributes text.
    private static function GetAttribs()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("class", "Selector");
      $hb->Begin("div", $textState, $attribs);
      $hb->End("div", $textState);
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->AddLine("<div class=\"Selector\">");
      $b->AddText("</div>");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("GetAttribs()", $result, $compare);
    }

    // Gets a new potentially indented line.
    private static function GetIndented()
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder();

      // Example Method:
      $result = $hb->GetIndented("This text is NOT indented.");
      $hb->AddText($result);

      // The builder keeps track of the current number of indents.
      $hb->AddIndent(2);
      $hb->AddLine();
      $result = $hb->GetIndented("This text is indented.");
      $hb->Text($result, false);
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->AddLine("This text is NOT indented.");
      $b->AddText("    This text is indented.");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("GetIndented()", $result, $compare);
    }

    // Gets the current indent string.
    private static function GetIndentString()
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder();

      $hb->AddIndent(1);

      // Example Method:
      $result = $hb->GetIndentString();
      $hb->Text($result, false);

      $hb->AddText("  :");
      $result = $hb->ToString();

      $compare = "  :";
      LJCCommon::WriteCompare("GetIndentString()", $result, $compare);
    }

    // Gets a modified text line.
    private static function GetLine()
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder();

      $hb->AddText("This is an appended line.");

      // The builder keeps track of the current number of indents.
      $hb->AddIndent();

      // Example Method:
      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Ends the text with a newline.
      // Defaults: addIndent = true, allowNewLine = true.
      $text = $hb->GetLine();
      $hb->Text($text, false);

      $hb->Text(":");
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddText("  :");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("GetLine()", $result, $compare);
    }

    // Gets the potentially indented text.
    private static function GetText()
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder();

      $hb->Text("This is an appended line.");

      // The builder keeps track of the current number of indents.
      $hb->AddIndent();

      // Example Method:
      // Starts the text with a newline if the builder already has text
      // and param allowNewLine = true and builder text does not end with
      // a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Defaults: addIndent = true, allowNewLine = true.
      $text = $hb->GetText("This is an indented line.");
      $hb->AddText($text);
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->AddLine("This is an appended line.");
      $b->AddText("  This is an indented line.");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("GetText()", $result, $compare);
    }

    // Appends added text and new wrapped line if combined line > LineLimit.
    private static function GetWrapped()
    {
      // Defaults: IndentCharCount = 2, LineLimit = 80, WrapEnabled = false.
      $hb = new LJCHTMLBuilder();
      $hb->WrapEnabled = true;

      // Example Method:
      $b = new LJCHTMLBuilder();
      $b->AddText("Now is the time for all good men to come to the aid of their");
      $b->AddText(" country.");
      $b->AddText(" Now is the time for all good men to come to the aid of their");
      $b->AddText(" country.");
      $text = $b->ToString();
      $result = $hb->GetWrapped($text);

      $b = new LJCHTMLBuilder();
      $b->AddText("Now is the time for all good men to come to the aid of");
      $b->AddLine(" their country. Now is the");
      $b->AddText("time for all good men to come to the aid of their country.");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("GetGetWrapped()", $result, $compare);
    }

    // --------------------
    // Element Methods

    // Appends the element begin tag.
    private static function Begin()
    {
    }

    // Appends an element.
    private static function Create()
    {
    }

    // Appends the element end tag.
    private static function End()
    {
    }

    // Gets the element begin tag.
    private static function GetBegin()
    {
    }

    // Gets the element text.
    private static function GetCreate()
    {
    }

    // Gets the element end tag.
    private static function GetEnd()
    {
    }
  }
?>
