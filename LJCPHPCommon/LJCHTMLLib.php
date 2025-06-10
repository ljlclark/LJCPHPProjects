<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCHTMLSectionLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";

  /// <summary>The HTML Section Class Library</summary>
  /// LibName: LJCHTMLLib
  //  Classes: LJCHTML

  /// <summary>Provides methods for creating HTML sections.</summary>
  class LJCHTML
  {
    // ----------
    // Get Create Element

    // Gets the <link> element for a style sheet.
    /// <include path='items/GetLink/*' file='Doc/LJCHTMLBuilder.xml'/>
    public static function GetLink(string $fileName, LJCTextState $textState) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("href", $fileName);
      $attribs->Add("rel", "stylesheet");
      // Arg 2 different than HTMLBuilder.cs.
      $createText = $hb->GetCreate("link", "", $textState, $attribs
        , isEmpty: true);
      $hb->Text($createText, false);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets a <meta> element.
    /// <include path='items/GetMeta/*' file='Doc/LJCHTMLBuilder.xml'/>
    public static function GetMeta(string $name, string $content
      , LJCTextState $textState) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("name", $name);
      $attribs->Add("content", $content);
      // Arg 2 different than HTMLBuilder.cs.
      $createText = $hb->GetCreate("meta", "", $textState, $attribs
        , isEmpty: true);
      LJCCommon::Debug(__line__, $createText);
      $hb->Text($createText, false);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets common <meta> elements.
    /// <include path='items/GetMetas/*' file='Doc/LJCHTMLBuilder.xml'/>
    public static function GetMetas(string $author, LJCTextState $textState
      , string $description = null, string $keywords = null
      , string $charSet = "utf-8") : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("charset", $charSet);
      // Arg 2 different than HTMLBuilder.cs.
      $createText = $hb->GetCreate("meta", "", $textState, $attribs
        , isEmpty: true);
      $hb->Text($createText, false);

      if (LJCCommon::HasValue($description))
      {
        $createText = self::GetMeta("description", $description, $textState);
        LJCCommon::Debug(__line__, $createText);
        $hb->Text($createText, false);
      }
      if (LJCCommon::HasValue($keywords))
      {
        $createText = self::GetMeta("keywords", $keywords, $textState);
        LJCCommon::Debug(__line__, $createText);
        $hb->Text($createText, false);
      }
      $createText = self::GetMeta("author", $author, $textState);
      $hb->Text($createText, false);
      LJCCommon::Debug(__line__, $createText);
      $content = "width=device-width initial-scale=1";
      $createText = self::GetMeta("viewport", $content, $textState);
      LJCCommon::Debug(__line__, $createText);
      $hb->Text($createText, false);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets beginning of style selector.
    /// <include path='items/GetBeginSelector/*' file='Doc/LJCHTMLBuilder.xml'/>
    public static function GetBeginSelector(string $selectorName, LJCTextState $textState)
      : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $hb->Text($selectorName);
      $hb->AddText(" {");

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets the <script> element.
    /// <include path='items/GetScript/*' file='Doc/LJCHTMLBuilder.xml'/>
    public static function GetScript(string $fileName, LJCTextState $textState)
      : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs->Add("src", $fileName);
      // Arg 2 different than HTMLBuilder.cs.
      $createText = $hb->GetCreate("script", "", $textState, $attribs);
      $hb->Text($createText, false);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // ----------
    // Get HTML Methods

    // Gets the HTML beginning up to <head>.
    /// <include path='items/GetHTMLBegin/*' file='Doc/LJCHTMLBuilder.xml'/>
    public static function GetHTMLBegin(LJCTextState $textState
      , array $copyright = null, string $fileName = null) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $hb->Text("<!DOCTYPE html>");
      if (LJCCommon::HasElements($copyright))
      {
        foreach ($copyright as $line)
        {
          $hb->Text("<!-- {$line} -->");
        }
      }
      if (LJCCommon::HasValue($fileName))
      {
        $hb->Text("<!-- {$fileName} -->");
      }

      $startAttribs = $hb->StartAttribs();
      $createText = $hb->GetBegin("html", $textState, $startAttribs
        , false);
      $hb->Text($createText, false);

      $createText = $hb->GetBegin("head", $textState, null, false);
      $hb->Text($createText, false);

      // Only use AddChildIndent() if additional text is added in this method.
      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets the HTML end <body> and <html>.
    /// <include path='items/GetHTMLEnd/*' file='Doc/LJCHTMLBuilder.xml'/>
    public static function GetHTMLEnd(LJCTextState $textState) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $text = $hb->GetEnd("body", $textState, false);
      $hb->Text($text, false);

      $createText = $hb->GetEnd("html", $textState, false);
      $hb->Text($createText, false);
      //$hb->AddSyncIndent($hb, $textState); //?

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets the main HTML Head elements.
    /// <include path='items/GetHTMLHead/*' file='Doc/LJCHTMLBuilder.xml'/>
    public static function GetHTMLHead(LJCTextState $textState, string $title = null
      , string $author = null, string $description = null) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $hb->Create("title", $title, $textState, childIndent: false);
      $createText = self::GetMetas($author, $textState, $description);
      $hb->Text($createText, false);

      $retValue = $hb->ToString();
      return $retValue;
    }
  }
?>
