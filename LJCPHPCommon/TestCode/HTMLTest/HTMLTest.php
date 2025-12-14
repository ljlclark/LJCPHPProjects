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
  // LJCCommonLib: LJCCommon
  // LJCTextBuilderLib: LJCTextBuilder, LJCTextState
  // LJCHTMLLib: LJCHTML

  /// <summary>The HTML Section Test Class Library</summary>
  /// LibName: HTMLTest
  //  Classes: TestHTML

  $testBuilder = new TestHTML();
  $testBuilder->Run();

  // ********************
  /// <summary>Represents a built string value.</summary>
  /// <include path='items/LJCTextBuilder/*' file='Doc/LJCTextBuilder.xml'/>
  class TestHTML
  {
    public static function Run()
    {
      echo("\r\n");
      echo("*** LJCHTML ***");

      // Simple HTML build.
      self::Build();

      // Methods
      self::GetLink();
      self::GetMeta();
      self::GetMetas();
      self::GetBeginSelector();
      self::GetScript();
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
      $b->AddLine("<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\">");
      $b->AddLine("<head>");
      $b->AddLine("</head>");
      $b->AddLine("<body>");
      $b->AddLine("</body>");
      $b->AddText("</html>");
      $compare = $b->ToString();
      LJC::OutputDebugCompare("Build()", $result, $compare);
    }

    // --------------------
    // Create Methods

    // Gets the <link> element for a style sheet.
    private static function GetLink()
    {
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetLink("CSS/File.css", $textState);

      $compare = "<link href=\"CSS/File.css\" rel=\"stylesheet\" />";
      LJC::OutputDebugCompare("GetLink()", $result, $compare);
    }

    // Gets a <meta> element.
    private static function GetMeta()
    {
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetMeta("author", "John Q. Smith", $textState);

      $compare = "<meta name=\"author\" content=\"John Q. Smith\" />";
      LJC::OutputDebugCompare("GetMeta()", $result, $compare);
    }

    // Gets common <meta> elements.
    private static function GetMetas()
    {
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
      LJC::OutputDebugCompare("GetMetas()", $result, $compare);
    }

    private static function GetBeginSelector()
    {
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetBeginSelector(".name", $textState);

      $compare = ".name {";
      LJC::OutputDebugCompare("GetBeginSelector()", $result, $compare);
    }

    // Appends a <script> element for a style sheet.
    private static function GetScript()
    {
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetScript("Script/File.js", $textState);

      $compare = "<script src=\"Script/File.js\"></script>";
      LJC::OutputDebugCompare("GetScript()", $result, $compare);
    }

    // Gets the HTML beginning up to and including <head>.
    public static function GetHTMLBegin()
    {
      $textState = new LJCTextState();

      $copyright = [
        "Copyright (c) John Q. Smith and Contributors.",
        "Licensed under the MIT License.",
      ];

      // Example Method:
      $result = LJCHTML::GetHTMLBegin($textState, $copyright, "File.html");

      $b = new LJCTextBuilder();
      $b->AddLine("<!DOCTYPE html>");
      $b->AddLine("<!-- Copyright (c) John Q. Smith and Contributors. -->");
      $b->AddLine("<!-- Licensed under the MIT License. -->");
      $b->AddLine("<!-- File.html -->");
      $b->AddLine("<html lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\">");
      $b->AddText("<head>");
      $compare = $b->ToString();
      LJC::OutputDebugCompare("GetHTMLBegin()", $result, $compare);
    }

    // Gets the HTML end <body> and <html>.
    public static function GetHTMLEnd()
    {
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetHTMLEnd($textState);

      $b = new LJCTextBuilder();
      $b->AddLine("</body>");
      $b->AddText("</html>");
      $compare = $b->ToString();
      LJC::OutputDebugCompare("GetHTMLEnd()", $result, $compare);
    }

    // Gets the main HTML Head elements.
    public static function GetHTMLHead()
    {
      $textState = new LJCTextState();

      // Example Method:
      $result = LJCHTML::GetHTMLHead($textState, "Title", "Author");

      $b = new LJCTextBuilder();
      $b->AddLine("<title>Title</title>");
      $b->AddLine("<meta charset=\"utf-8\" />");
      $b->AddLine("<meta name=\"author\" content=\"Author\" />");
      $b->AddText("<meta name=\"viewport\" content=\"width=device-width");
      $b->AddText(" initial-scale=1\" />");
      $compare = $b->ToString();
      LJC::OutputDebugCompare("GetHTMLHead()", $result, $compare);
    }
  }
?>
