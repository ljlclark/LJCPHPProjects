<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // HTMLTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLLib.php";
  // LJCCommonLib: LJC
  // LJCTextBuilderLib: LJCTextBuilder, LJCTextState
  // LJCHTMLLib: LJCHTML

  /// <summary>The HTML Section Test Class Library</summary>
  /// LibName: HTMLTest

  $testBuilder = new HTMLTest();
  $testBuilder->Run();

  // ********************
  /// <summary>The HTML Section Test Class</summary>
  /// <include path='items/LJCHTML/*' file='Doc/LJCHTML.xml'/>
  class HTMLTest
  {
    /// <summary>Runs the LJCHTML tests.</summary>
    public static function Run()
    {
      // Setup static debug to output.
      $className = "TextBuilderTest";
      $methodName = "Run()";

      echo("\r\n");
      echo("*** LJCHTML ***");

      // Simple HTML build.
      self::Build();

      // Create Element Methods
      self::GetBeginSelector();
      self::GetLink();
      self::GetMeta();
      self::GetMetas();
      self::GetScript();

      // Create HTML Methods
      self::GetHTMLBegin();
      self::GetHTMLEnd();
      self::GetHTMLHead();
    }

    // Build a complete HTML document.
    private static function Build()
    {
      $textState = new LJCTextState();
      $tb = new LJCTextBuilder($textState);

      $copyright = [];
      $copyright[] = "Copyright (c) Lester J. Clark and Contributors";
      $copyright[] = "Licensed under the MIT License.";
      $fileName = "TestHTMLBuilderOutput.html";
      $createText = LJCHTML::GetHTMLBegin($textState, $copyright, $fileName);
      $tb->Text($createText, false);

      // Add head items.
      $tb->End("head", $textState);

      $tb->Begin("body", $textState, addIndent: false);
      // Use AddChildIndent after beginning an element.
      $tb->AddChildIndent(" ", $textState);

      $text = LJCHTML::GetHTMLEnd($textState);
      $tb->Text($text, false);
      $result = $tb->ToString();

      $b = new LJCTextBuilder($textState);
      $b->AddLine("<!DOCTYPE html>");
      $b->AddLine("<!-- Copyright (c) Lester J. Clark and Contributors -->");
      $b->AddLine("<!-- Licensed under the MIT License. -->");
      $b->AddLine("<!-- TestHTMLBuilderOutput.html -->");
      //$b->AddLine("<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\">");
      $b->AddLine("<html lang=\"en\">");
      $b->AddLine("<head>");
      $b->AddLine("</head>");
      $b->AddLine("<body>");
      $b->AddLine("</body>");
      $b->AddText("</html>");
      $compare = $b->ToString();
      LJC::OutputLogCompare("Build()", $result, $compare);
    }

    // --------------------
    // Create Element Methods

    // Gets beginning of style selector.
    private static function GetBeginSelector()
    {
      $textState = new LJCTextState();

      // Example Method:
      // Starts the text with a newline if the builder already has text, param
      // allowNewLine = true and builder text does not end with a newline.
      // The text begins with the current indent string if param
      // addIndent = true.
      // Defaults: addIndent = true, allowNewLine = true.
      $result = LJCHTML::GetBeginSelector(".name", $textState);

      $compare = ".name {";
      LJC::OutputLogCompare("GetBeginSelector()", $result, $compare);
    }

    // Gets the <link> element for a style sheet.
    private static function GetLink()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetLink("CSS/File.css", $textState);

      $compare = "<link href=\"CSS/File.css\" rel=\"stylesheet\" />";
      LJC::OutputLogCompare("GetLink()", $result, $compare);
    }

    // Gets a <meta> element.
    private static function GetMeta()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      // Example Method:
      $content = "width=device-width initial-scale=1";
      $result = LJCHTML::GetMeta("viewport", $content, $textState);

      $compare = "<meta name=\"viewport\"";
      $compare .= " content=\"width=device-width initial-scale=1\" />";
      LJC::OutputLogCompare("GetMeta()", $result, $compare);
    }

    // Gets common <meta> elements.
    private static function GetMetas()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetMetas("John Q. Smith", $textState, "A description.");

      $b = new LJCTextBuilder();
      $b->AddLine("<meta charset=\"utf-8\" />");
      $b->AddLine("<meta name=\"description\" content=\"A description.\" />");
      $b->AddLine("<meta name=\"author\" content=\"John Q. Smith\" />");
      $b->AddText("<meta name=\"viewport\" content=\"width=device-width");
      $b->AddText(" initial-scale=1\" />");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetMetas()", $result, $compare);
    }

    // Appends a <script> element for a style sheet.
    private static function GetScript()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetScript("Script/File.js", $textState);

      $compare = "<script src=\"Script/File.js\"></script>";
      LJC::OutputLogCompare("GetScript()", $result, $compare);
    }

    // ----------
    // Create HTML Methods

    // Gets the HTML beginning up to and including <head>.
    public static function GetHTMLBegin()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      $copyright = [
        "Copyright (c) John Q. Smith and Contributors",
        "Licensed under the MIT License.",
      ];

      // Example Method:
      $result = LJCHTML::GetHTMLBegin($textState, $copyright, "File.html");

      $b = new LJCTextBuilder();
      $b->AddLine("<!DOCTYPE html>");
      $b->AddLine("<!-- Copyright (c) John Q. Smith and Contributors -->");
      $b->AddLine("<!-- Licensed under the MIT License. -->");
      $b->AddLine("<!-- File.html -->");
      //$b->AddLine("<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\">");
      $b->AddLine("<html lang=\"en\">");
      $b->AddText("<head>");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetHTMLBegin()", $result, $compare);
    }

    // Gets the HTML end <body> and <html>.
    public static function GetHTMLEnd()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetHTMLEnd($textState);

      $b = new LJCTextBuilder();
      $b->AddLine("</body>");
      $b->AddText("</html>");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetHTMLEnd()", $result, $compare);
    }

    // Gets the main HTML Head elements.
    public static function GetHTMLHead()
    {
      // Root Method Begin
      $textState = new LJCTextState();

      // Example Method:
      $title = "The Title";
      $author = "John Q. Smith";
      $description = "The Description";
      $result = LJCHTML::GetHTMLHead($textState, $title, $author, $description);

      $b = new LJCTextBuilder();
      $b->AddLine("<title>The Title</title>");
      $b->AddLine("<meta charset=\"utf-8\" />");
      $b->AddLine("<meta name=\"description\" content=\"The Description\" />");
      $b->AddLine("<meta name=\"author\" content=\"John Q. Smith\" />");
      $b->AddText("<meta name=\"viewport\" content=\"width=device-width");
      $b->AddText(" initial-scale=1\" />");
      $compare = $b->ToString();
      LJC::OutputLogCompare("GetHTMLHead()", $result, $compare);
    }
  }
