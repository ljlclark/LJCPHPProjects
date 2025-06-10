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
    // Methods


  }
?>
