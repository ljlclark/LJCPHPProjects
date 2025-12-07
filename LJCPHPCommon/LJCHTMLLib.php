<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCHTMLLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  // LJCTextBuilderLib: LJCAttributes, LJCTextBuilder, LJCTextState

  /// <summary>The HTML Section Class Library</summary>
  /// LibName: LJCHTMLLib
  //  Classes: LJCHTML

  // ********************
  /// <summary>Provides methods for creating HTML sections.</summary>
  /// <group name="Element">Create Elements</group>
  //    GetBeginSelector(), GetLink(), GetMeta(), GetMetas(), GetScript()
  /// <group name="HTML">Create HTML</group>
  //    GetHTMLBegin(), GetHTMLEnd(), GetHTMLHead()
  class LJCHTML
  {
    // ----------
    // Get Create Element

    // Gets beginning of style selector.
    /// <include path='items/GetBeginSelector/*' file='Doc/LJCHTML.xml'/>
    /// <ParentGroup>Element</ParentGroup>
    public static function GetBeginSelector(string $selectorName, LJCTextState $textState)
      : string
    {
      $tb = new LJCTextBuilder($textState);

      $tb->Text($selectorName);
      $tb->AddText(" {");

      $retValue = $tb->ToString();
      return $retValue;
    }

    // Gets the <link> element for a style sheet.
    /// <include path='items/GetLink/*' file='Doc/LJCHTML.xml'/>
    /// <ParentGroup>Element</ParentGroup>
    public static function GetLink(string $fileName, LJCTextState $textState) : string
    {
      $tb = new LJCTextBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("href", $fileName);
      $attribs->Add("rel", "stylesheet");
      // Arg 2 different than HTMLBuilder.cs.
      $createText = $tb->GetCreate("link", "", $textState, $attribs
        , isEmpty: true);
      $tb->Text($createText, false);

      $retValue = $tb->ToString();
      return $retValue;
    }

    // Gets a <meta> element.
    /// <include path='items/GetMeta/*' file='Doc/LJCHTML.xml'/>
    /// <ParentGroup>Element</ParentGroup>
    public static function GetMeta(string $name, string $content
      , LJCTextState $textState) : string
    {
      $tb = new LJCTextBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("name", $name);
      $attribs->Add("content", $content);
      // Arg 2 different than HTMLBuilder.cs.
      $createText = $tb->GetCreate("meta", "", $textState, $attribs
        , isEmpty: true);
      $tb->Text($createText, false);

      $retValue = $tb->ToString();
      return $retValue;
    }

    // Gets common <meta> elements.
    /// <include path='items/GetMetas/*' file='Doc/LJCHTML.xml'/>
    /// <ParentGroup>Element</ParentGroup>
    public static function GetMetas(string $author, LJCTextState $textState
      , string $description = null, string $keywords = null
      , string $charSet = "utf-8") : string
    {
      $tb = new LJCTextBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("charset", $charSet);
      // Arg 2 different than HTMLBuilder.cs.
      $createText = $tb->GetCreate("meta", "", $textState, $attribs
        , isEmpty: true);
      $tb->Text($createText, false);

      if (LJC::HasValue($description))
      {
        $createText = self::GetMeta("description", $description, $textState);
        $tb->Text($createText, false);
      }
      if (LJC::HasValue($keywords))
      {
        $createText = self::GetMeta("keywords", $keywords, $textState);
        $tb->Text($createText, false);
      }
      $createText = self::GetMeta("author", $author, $textState);
      $tb->Text($createText, false);
      $content = "width=device-width initial-scale=1";
      $createText = self::GetMeta("viewport", $content, $textState);
      $tb->Text($createText, false);

      $retValue = $tb->ToString();
      return $retValue;
    }

    // Gets the <script> element.
    /// <include path='items/GetScript/*' file='Doc/LJCHTML.xml'/>
    /// <ParentGroup>Element</ParentGroup>
    public static function GetScript(string $fileName, LJCTextState $textState)
      : string
    {
      $tb = new LJCTextBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("src", $fileName);
      // Arg 2 different than HTMLBuilder.cs.
      $createText = $tb->GetCreate("script", "", $textState, $attribs);
      $tb->Text($createText, false);

      $retValue = $tb->ToString();
      return $retValue;
    }

    // ----------
    // Get HTML Methods

    // Gets the HTML beginning up to <head>.
    /// <include path='items/GetHTMLBegin/*' file='Doc/LJCHTML.xml'/>
    /// <ParentGroup>HTML</ParentGroup>
    public static function GetHTMLBegin(LJCTextState $textState
      , array $copyright = null, string $fileName = null) : string
    {
      $tb = new LJCTextBuilder($textState);

      $tb->Text("<!DOCTYPE html>");
      if (LJC::HasElements($copyright))
      {
        foreach ($copyright as $line)
        {
          $tb->Text("<!-- {$line} -->");
        }
      }
      if (LJC::HasValue($fileName))
      {
        $tb->Text("<!-- {$fileName} -->");
      }

      $startAttribs = $tb->StartAttribs();
      $createText = $tb->GetBegin("html", $textState, $startAttribs
        , false);
      $tb->Text($createText, false);

      $createText = $tb->GetBegin("head", $textState, null, false);
      $tb->Text($createText, false);

      // Only use AddChildIndent() if additional text is added in this method.
      $retValue = $tb->ToString();
      return $retValue;
    }

    // Gets the HTML end <body> and <html>.
    /// <include path='items/GetHTMLEnd/*' file='Doc/LJCHTML.xml'/>
    /// <ParentGroup>HTML</ParentGroup>
    public static function GetHTMLEnd(LJCTextState $textState) : string
    {
      $tb = new LJCTextBuilder($textState);

      $text = $tb->GetEnd("body", $textState, false);
      $tb->Text($text, false);

      $createText = $tb->GetEnd("html", $textState, false);
      $tb->Text($createText, false);
      //$tb->AddSyncIndent($tb, $textState); //?

      $retValue = $tb->ToString();
      return $retValue;
    }

    // Gets the main HTML Head elements.
    /// <include path='items/GetHTMLHead/*' file='Doc/LJCHTML.xml'/>
    /// <ParentGroup>HTML</ParentGroup>
    public static function GetHTMLHead(LJCTextState $textState, string $title = null
      , string $author = null, string $description = null) : string
    {
      $tb = new LJCTextBuilder($textState);

      $tb->Create("title", $textState, $title, childIndent: false);
      $createText = self::GetMetas($author, $textState, $description);
      $tb->Text($createText, false);

      $retValue = $tb->ToString();
      return $retValue;
    }
  }
?>
