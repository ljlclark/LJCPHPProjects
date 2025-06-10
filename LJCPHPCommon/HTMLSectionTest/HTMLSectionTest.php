<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // HTMLSectionTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  // LJCCommonLib: LJCCommon
  // LJCHTMLBuilderLib: LJCHTMLBuilder, LJCAttribute, LJCAttributes
  //   , LJCTextState

  /// <summary>The Common Text Output Class Library</summary>
  /// LibName: LJCHTMLBuilderLib
  //  Classes: LJCHTMLBuilder, LJCAttribute, LJCAttributes

  $testBuilder = new TestHTMLSection();
  $testBuilder->Run();

  // ********************
  // Methods: 
  /// <summary>Represents a built string value.</summary>
  /// <include path='items/LJCHTMLBuilder/*' file='Doc/LJCHTMLBuilder.xml'/>
  class TestHTMLSection
  {
    public static function Run()
    {
      echo("\r\n");
      echo("*** HTMLSectionLib ***");

      // Simple HTML build.
      self::Build();

      // Methods
      self::Link();
      self::Meta();
      self::Metas();
      self::Script();

      self::GetLink();
      self::GetMeta();
    }

    private static function Build()
    {
      $textState = new LJCTextState();
      $hb = new LJCHTMLBuilder();

      $copyright = [];
      $copyright[] = "Copyright (c) Lester J. Clark and Contributors";
      $copyright[] = "Licensed under the MIT License.";
      $fileName = "TestHTMLBuilderOutput.html";
      $hb->HTMLBegin($textState, $copyright, $fileName);
      // Add head items.
      $hb->End("head", $textState);

      $hb->Begin("body", $textState, addIndent: false);
      // Use AddChildIndent after beginning an element.
      $hb->AddChildIndent(" ", $textState);

      $text = $hb->GetHTMLEnd($textState);
      $hb->Text($text, false);
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
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
      //LJCCommon::WriteCompare("Build()", $result, $compare);
    }

    // --------------------
    // Append Create Methods

    // Appends a <link> element for a style sheet.
    private static function Link()
    {
      $textState = new LJCTextState();
      $hb = new LJCHTMLBuilder();

      // Example Method:
      $hb->Link("CSS/File.css", $textState);
      $result = $hb->ToString();

      $compare = "<link href=\"CSS/File.css\" rel=\"stylesheet\" />";
      LJCCommon::WriteCompare("Link()", $result, $compare);
    }

    // Appends a <meta> element.
    private static function Meta()
    {
      $textState = new LJCTextState();
      $hb = new LJCHTMLBuilder();

      // Example Method:
      $hb->Meta("author", "John Q. Smith", $textState);
      $result = $hb->ToString();

      $compare = "<meta name=\"author\" content=\"John Q. Smith\" />";
      LJCCommon::WriteCompare("Meta()", $result, $compare);
    }

    // Appends common <meta> elements.
    private static function Metas()
    {
      $textState = new LJCTextState();
      $hb = new LJCHTMLBuilder();

      // Example Method:
      $hb->Metas("John Q. Smith", $textState, "A description.");
      $result = $hb->ToString();

      $b = new LJCHTMLBuilder();
      $b->Line("<meta charset=\"utf-8\" />");
      $b->Line("<meta name=\"description\" content=\"A description.\" />");
      $b->Line("<meta name=\"author\" content=\"John Q. Smith\" />");
      $b->Text("<meta name=\"viewport\" content=\"width=device-width");
      $b->AddText(" initial-scale=1\" />");
      $compare = $b->ToString();
      LJCCommon::WriteCompare("Meta()", $result, $compare);
    }

    // Appends a <script> element for a style sheet.
    private static function Script()
    {
      $textState = new LJCTextState();
      $hb = new LJCHTMLBuilder();

      // Example Method:
      $hb->Script("Script/File.js", $textState);
      $result = $hb->ToString();

      $compare = "<script src=\"Script/File.js\"></script>";
      LJCCommon::WriteCompare("Script()", $result, $compare);
    }

    // --------------------
    // Append Create Methods

    // Gets the <link> element for a style sheet.
    private static function GetLink()
    {
      $textState = new LJCTextState();
      $hb = new LJCHTMLBuilder();

      // Example Method:
      $result = $hb->GetLink("CSS/File.css", $textState);

      $compare = "<link href=\"CSS/File.css\" rel=\"stylesheet\" />";
      LJCCommon::WriteCompare("GetLink()", $result, $compare);
    }

    // Gets a <meta> element.
    private static function GetMeta()
    {
      $textState = new LJCTextState();
      $hb = new LJCHTMLBuilder();

      // Example Method:
      $result = $hb->GetMeta("author", "John Q. Smith", $textState);

      $compare = "<meta name=\"author\" content=\"John Q. Smith\" />";
      LJCCommon::WriteCompare("GetMeta()", $result, $compare);
    }
  }
?>
