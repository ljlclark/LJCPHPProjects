<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCHTMLBuilderLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  // LJCCollectionLib: LJCCollectionBase
  // LJCCommonLib: LJCCommon
  // LJCTextLib: LJCWriter

  /// <summary>The Common Text Output Class Library</summary>
  /// LibName: LJCHTMLBuilderLib
  //  Classes: LJCHTMLBuilder, LJCAttribute, LJCAttributes

  // ********************
  // Methods: ToString(), AddChildIndent(), AddIndent(), EndsWithNewLine()
  //   , StartWithNewLine(), HasText()
  //
  // Append Text: AddLine(), AddText(), Line(), Text()
  // Get Text: GetAttribs(), GetIndented(), GetIndentString(), GetLine()
  //   , GetText()
  //
  // Append Element: Begin(), Create(), End()
  // Get Element: GetBegin(), GetBeginSelector(), GetCreate(), GetEnd()
  //
  // Append Create Element: Link(), Meta(), Metas(), Script()
  // Get Create Element: GetLink(), GetMeta(), GetMetas(), GetScript()
  //
  // Append HTML: HTMLBegin()
  // Get HTML: GetHTMLBegin(), GetHTMLEnd(), GetHTMLHead()
  //
  // Get Attrib: Attribs(), StartAttribs(), TableAttribs()
  //
  /// <summary>Represents a built string value.</summary>
  /// <include path='items/LJCHTMLBuilder/*' file='Doc/LJCHTMLBuilder.xml'/>
  class LJCHTMLBuilder
  {
    // ----------
    // Constructors

    /// <summary>Initializes a class instance.</summary>
    public function __construct(?LJCTextState $textState = null)
    {
      $this->BuilderValue = "";
      $this->IndentCharCount = 2;
      $this->IndentCount = 0;
      if ($textState != null)
      {
        $this->AddIndent($textState->IndentCount);
      }
      $this->LineLength = 0;
      $this->LineLimit = 80;
      $this->WrapEnabled = false;
    } // __construct()

    // ----------
    // Data Class Methods

    // Gets the built string.
    /// <include path='items/ToString/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function ToString()
    {
      return $this->BuilderValue;
    } // ToString()

    // ----------
    // Methods

    // Adds the new (child) indents.
    /// <include path='items/AddChildIndent/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function AddChildIndent(string $createText, LJCTextState $textState)
    {
      if (LJCCommon::HasValue($createText)
        && $textState->ChildIndentCount > 0)
      {
        $this->AddIndent($textState->ChildIndentCount);
        $textState->IndentCount += $textState->ChildIndentCount;
        $textState->ChildIndentCount = 0;
      }
    }

    // Changes the IndentCount by the provided value.
    /// <include path='items/AddIndent/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function AddIndent($increment = 1) : int
    {
      $this->IndentCount += $increment;
      if ($this->IndentCount < 0)
      {
        $this->IndentCount = 0;
      }
      return $this->IndentCount;
    } // AddIndent()

    // Indicates if the builder text ends with a newline.
    /// <include path='items/EndsWithNewLine/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function EndsWithNewLine() : bool
    {
      $retValue = false;

      if (strlen($this->BuilderValue) > 0)
      {
        $length = strlen($this->BuilderValue);
        if ("\n" == $this->BuilderValue[$length - 1])
        {
          $retValue = true;
        }
      }
      return $retValue;
    }

    // Checks if text can start with a newline.
    /// <include path='items/StartWithNewLine/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function StartWithNewLine(bool $allowNewLine) : bool
    {
      $retValue = false;

      if ($allowNewLine
        && $this->HasText()
        && !$this->EndsWithNewLine())
      {
        $retValue = true;
      }
      return $retValue;
    }

    /// <summary>Indicates if the builder has text.</summary>
    /// <returns>true if builder has text; otherwise false.</returns>
    public function HasText() : bool
    {
      $retValue = false;

      if (strlen($this->BuilderValue) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    }

    // ----------
    // Append Text Methods
    
    // Adds a text line without modification.
    /// <include path='items/AddLine/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function AddLine(string $text = null) : string
    {
      $retText = "{$text}\r\n";
      $this->BuilderValue .= $retText;
      return $retText;
    }

    // Adds text without modification.
    /// <include path='items/AddText/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function AddText(string $text)
    {
      if (LJCCommon::HasValue($text))
      {
        $this->BuilderValue .= $text;
      }
    }

    // Adds a potentially indented text line to the builder.
    /// <include path='items/Line/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Line(?string $text = null, bool $addIndent = true
      , bool $allowNewLine = true) : string
    {
      $retText = $this->GetLine($text, $addIndent, $allowNewLine);
      $this->BuilderValue .= $retText;
      return $retText;
    }

    // Adds the potentially indented text.
    /// <include path='items/Text/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Text(string $text, bool $addIndent = true
      , bool $allowNewLine = true) : string
    {
      $retText = $this->GetText($text, $addIndent, $allowNewLine);
      if (LJCCommon::HasValue($retText))
      {
        $this->BuilderValue .= $retText;
      }
      return $retText;
    }

    // ----------
    // Get Text Methods

    // Gets the attributes text.
    /// <include path='items/GetAttribs/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetAttribs(?LJCAttributes $attribs, LJCTextState $textState)
      : string
    {
      $retText = "";

      // *** Next Line *** Change
      if (LJCCommon::HasItems($attribs))
      {
        $hb = new LJCHTMLBuilder($textState);
        $isFirst = true;
        foreach ($attribs as $attrib)
        {
          if (!$isFirst)
          {
            // Wrap line for large attribute value.
            if (LJCCommon::HasValue($attrib->Value)
              && strlen($attrib->Value) > 35)
            {
              $hb->AddText("\r\n{$GetIndentString()}");
            }
          }
          $isFirst = false;

          $hb->AddText(" {$attrib->Name}");
          if (LJCCommon::HasValue($attrib->Value))
          {
            $hb->AddText("=\"{$attrib->Value}\"");
          }
        }
        $retText = $hb->ToString();
      }
      return $retText;
    }

    // Gets a new potentially indented line.
    /// <include path='items/GetIndented/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetIndented(string $text) : string
    {
      $retText = "";

      // Allow add of blank characters.
      if ($text != null)
      {
        $retText = $this->GetIndentString();
        $retText .= $text;
      }
      return $retText;
    }

    // Gets the current indent string.
    /// <include path='items/GetIndentString/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetIndentString() : string
    {
      $retValue = str_repeat(" ", $this->IndentLength());
      return $retValue;
    }

    // Gets a modified text line.
    /// <include path='items/GetLine/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetLine(string $text = null, bool $addIndent = true
      , bool $allowNewLine = true) : string
    {
      $retLine = $this->GetText($text, $addIndent, $allowNewLine);
      $retLine .= "\r\n";
      return $retLine;
    }

    // Gets the potentially indented text.
    /// <include path='items/GetText/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetText(?string $text, bool $addIndent = true
      , bool $allowNewLine = true) : string
    {
      $retText = "";

      // Start with newline if text exists.
      if ($this->StartWithNewLine($allowNewLine))
      {
        $retText = "\r\n";
      }

      if (LJCCommon::HasValue($text))
      {
        $retText .= $text;

        if ($addIndent)
        {
          // Recreate string.
          $retText = $this->GetIndented($text);
        }

        if ($this->StartWithNewLine($allowNewLine))
        {
          // Recreate string.
          $retText = "\r\n";
          if ($addIndent)
          {
            $retText .= $this->GetIndentString();
          }
          $retText .= $text;
        }

        if ($this->WrapEnabled)
        {
          $retText = $this->GetWrapped($retText);
        }
      }
      return $retText;
    }

    // Appends added text and new wrapped line if combined line > LineLimit.
    /// <include path='items/GetWrapped/*' file='Doc/HTMLBuilder.xml'/>
    public function GetWrapped(string $text) : string
    {
      $retText = $text;

      $buildText = "";
      $workText = $text;
      $totalLength = $this->LineLength + $this->TextLength($workText);
      if ($totalLength < $this->LineLimit)
      {
        // No wrap.
        $this->LineLength += $this->TextLength($text);
      }

      while ($totalLength > $this->LineLimit)
      {
        // Index where text can be added to the current line
        // and the remainder is wrapped.
        $wrapIndex = $this->WrapIndex($workText);
        if ($wrapIndex > -1)
        {
          // Adds leading space if line exists and wrapIndex > 0.
          $addText = $this->GetAddText($retText, $wrapIndex);
          $buildText .= "{$addText}\r\n";

          // Next text up to LineLimit - prepend length without leading space.
          $wrapText = $this->WrapText($workText, $wrapIndex);
          // *** Different than TextBuilder ***
          $indentString = $this->GetIndentString();
          $lineText = "{$indentString}{$wrapText}";
          $LineLength = strlen($lineText);
          $buildText .= $lineText;

          // End loop unless there is more text.
          $totalLength = 0;

          // Get index of next section.
          $nextIndex = $wrapIndex + strlen($wrapText);
          if (!str_starts_with($workText, ","))
          {
            // Adjust for removed leading space.
            $nextIndex++;
          }

          // Get next work text if available.
          if ($nextIndex < strlen($workText))
          {
            $tempText = substr($workText, $nextIndex);
            $workText = $tempText;
            $totalLength = $this->LineLength + $this->TextLength($workText);
          }
        }
      }

      if ($buildText != null
        && strlen($buildText) > 0)
      {
        $retText = $buildText;
      }
      return $retText;
    }

    // ----------
    // Append Element Methods

    // Appends the element begin tag.
    /// <include path='items/Begin/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Begin(string $name, LJCTextState $textState
      , LJCAttributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true) : string
    {
      $createText = $this->GetBegin($name, $textState, $attribs, $addIndent
        , $childIndent);
      $this->Text($createText, false);

      // Use AddChildIndent after beginning an element.
      $this->AddChildIndent($createText, $textState);

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    }

    // Appends an element.
    /// <include path='items/Create/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Create(string $name, string $text, LJCTextState $textState
      , LJCAttributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true, bool $isEmpty = false, bool $close = true)
      : string
    {
      // Adds the indent string.
      $createText = $this->GetCreate($name, $text, $textState, $htmlAttribs
        , $addIndent, $childIndent, $isEmpty, $close);
      $this->Text($createText, false);
      if (!$close)
      {
        // Use AddChildIndent after beginning an element.
        $this->AddChildIndent($createText, $textState);
      }

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    }

    // Appends the element end tag.
    /// <include path='items/End/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function End(string $name, LJCTextState $textState
      , bool $addIndent = true) : string
    {
      $createText = $this->GetEnd($name, $textState, $addIndent);
      $this->Text($createText, false);

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    }

    // ----------
    // Get Element Methods

    // Gets the element begin tag.
    /// <include path='items/GetBegin/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetBegin(string $name, LJCTextState $textState
      , LJCAttributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $createText = $this->GetCreate($name, "", $textState, $attribs
        , $addIndent, $childIndent, close: false);
      $hb->Text($createText, false);

      // Only use AddChildIndent() if additional text is added in this method.
      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets beginning of style selector.
    /// <include path='items/GetBeginSelector/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetBeginSelector(string $selectorName, LJCTextState $textState)
      : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $hb->Text($selectorName);
      $hb->AddText(" {");

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets the element text.
    /// <include path='items/GetCreate/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetCreate(string $name, string $text
      , LJCTextState $textState, LJCAttributes $attribs = null
      , bool $addIndent = true, bool $childIndent = true, bool $isEmpty = false
      , bool $close = true) : string
    {
      $textState->ChildIndentCount = 0; // ?
      $hb = new LJCHTMLBuilder($textState);

      // Start text with the opening tag.
      $hb->Text("<{$name}", $addIndent);
      $getText = $this->GetAttribs($attribs, $textState);
      $hb->AddText($getText);
      if ($isEmpty)
      {
        $hb->AddText(" /");
        $close = false;
      }
      $hb->AddText(">");

      // Content is added if not an empty element.
      $isWrapped = false;
      if (!$isEmpty)
      {
        $content = $this->Content($text, $textState, $isEmpty, $isWrapped);
        $hb->AddText($content);
      }

      // Close the element.
      if ($close)
      {
        if ($isWrapped)
        {
          $hb->Line();
          $hb->AddText($this->GetIndentString());
        }
        $hb->AddText("</{$name}>");
      }

      // Increment ChildIndentCount if not empty and not closed.
      if (!$isEmpty
        && !$close
        && $childIndent)
      {
        $textState->ChildIndentCount++;
      }

      $retElement = $hb->ToString();
      return $retElement;
    }

    // Gets the element end tag.
    /// <include path='items/GetEnd/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetEnd(string $name, LJCTextState $textState
      , bool $addIndent = true) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $this->AddSyncIndent($this, $textState, -1);
      $hb->Text("</{$name}>", $addIndent);

      $retElement = $hb->ToString();
      return $retElement;
    }

    // ----------
    // Append Create Element

    // Appends a <link> element for a style sheet.
    /// <include path='items/Link/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Link(string $fileName, LJCTextState $textState) : string
    {
      $createText = $this->GetLink($fileName, $textState);
      $this->Text($createText, false);

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    }

    // Appends a <meta> element.
    /// <include path='items/Meta/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Meta(string $name, string $content, LJCTextState $textState)
      : string
    {
      $createText = $this->GetMeta($name, $content, $textState);
      $this->Text($createText, false);

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    }

    // Appends common <meta> elements.
    /// <include path='items/Metas/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Metas(string $author, LJCTextState $textState
      , string $description = null, string $keywords = null
      , string $charSet = "utf-8") : string
    {
      $createText = $this->GetMetas($author, $textState, $description, $keywords
        , $charSet);
      $this->Text($createText, false);

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    }

    // Appends a <script> element for a style sheet.
    /// <include path='items/Script/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Script(string $fileName, LJCTextState $textState) : string
    {
      $createText = $this->GetScript($fileName, $textState);
      $this->Text($createText, false);

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    }

    // ----------
    // Get Create Element

    // Gets the <link> element for a style sheet.
    /// <include path='items/GetLink/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetLink(string $fileName, LJCTextState $textState) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs.Add("rel", "stylesheet");
      $attribs.Add("type", "text/css");
      $attribs.Add("href", $fileName);
      $createText = $hb->GetCreate("link", null, $textState, $attribs
        , isEmpty: true);
      $hb->Text($createText, false);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets a <meta> element.
    /// <include path='items/GetMeta/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetMeta(string $name, string $content
      , LJCTextState $textState) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs.Add("name", $name);
      $attribs.Add("content", $content);
      $createText = $hb->GetCreate("meta", null, $textState, $attribs
        , isEmpty: true);
      $hb->Text($createText, false);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets common <meta> elements.
    /// <include path='items/GetMetas/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetMetas(string $author, LJCTextState $textState
      , string $description = null, string $keywords = null
      , string $charSet = "utf-8") : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs.Add("charset", $charSet);
      $createText = $hb->GetCreate("meta", null, $textState, $attribs
        , isEmpty: true);
      $hb->Text($createText, false);

      if ($this->HasValue($description))
      {
        $hb->Meta("description", $description, $textState);
      }
      if ($this->HasValue($keywords))
      {
        $hb->Meta("keywords", $keywords, $textState);
      }
      $hb.Meta("author", $author, $textState);
      $content = "width=device-width initial-scale=1";
      $hb->Meta("viewport", $content, $textState);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets the <script> element.
    /// <include path='items/GetScript/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetScript(string $fileName, LJCTextState $textState)
      : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $attribs = new LJCAttributes();
      $attribs.Add("src", $fileName);
      $createText = $hb->GetCreate("script", null, $textState, $attribs);
      $hb->Text($createText, false);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // ----------
    // Append HTML Methods

    // Creates the HTML beginning up to and including <head>.
    /// <include path='items/HTMLBegin/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function HTMLBegin(LJCTextState $textState
      , array $copyright = null, string $fileName = null) : string
    {
      $retValue = $this->GetHTMLBegin($textState, $copyright, $fileName);
      $this->Text($retValue, false);

      // Append Method
      $this->UpdateState($textState);
      return $retValue;
    }

    // ----------
    // Get HTML Methods

    // Gets the HTML beginning up to <head>.
    /// <include path='items/GetHTMLBegin/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetHTMLBegin(LJCTextState $textState
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
    public function GetHTMLEnd(LJCTextState $textState) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $text = $hb->GetEnd("body", $textState, false);
      $hb->Text($text, false);

      $text = $hb->GetEnd("html", $textState, false);
      $hb->Text($text, false);
      $this->AddSyncIndent($hb, $textState);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // Gets the main HTML Head elements.
    /// <include path='items/GetHTMLHead/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetHTMLHead(LJCTextState $textState, string $title = null
      , string $author = null, string $description = null) : string
    {
      $hb = new LJCHTMLBuilder($textState);

      $hb->Create("title", $title, $textState, childIndent: false);
      $hb.Metas($author, $textState, $description);

      $retValue = $hb->ToString();
      return $retValue;
    }

    // ----------
    // Get Attribs Methods

    // Gets common element attributes.
    /// <include path='items/Attribs/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Attribs(string $className = null, string $id = null)
      : LJCAttributes
    {
      $retAttribs = new LJCAttributes();
      if ($this->HasValue($id))
      {
        $retAttribs.Add("id", $id);
      }
      if ($this->HasValue($className))
      {
        $this->retAttribs.Add("class", $className);
      }
      return $retAttribs;
    }

    // Creates the HTML element attributes.
    /// <include path='items/StartAttribs/*' file='Doc/HTMLBuilder.xml'/>
    public function StartAttribs() : LJCAttributes
    {
      $retAttributes = new LJCAttributes();
      $retAttributes->Add("lang", "en");
      $retAttributes->Add("xmlns", "http://www.w3.org/1999/xhtml");
      return $retAttributes;
    }

    // Gets common table attributes.
    /// <include path='items/TableAttribs/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function TableAttribs(int $border = 1, int $cellSpacing = 0
      , int $cellPadding = 2, string $className = null, string $id = null)
      : LJCAttributes
    {
      $retAttribs = Attribs(className, $id);
      $retAttribs.Add("border", strval($border));
      $retAttribs.Add("cellspacing", strval($cellSpacing));
      $retAttribs.Add("cellpadding", strval($cellPadding));
      return $retAttribs;
    }

    // ----------
    // Private Methods

    // Adds indent to builders and sync object.
    private function AddSyncIndent(LJCHTMLBuilder $hb, LJCTextState $state
      , int $value = 1)
    {
      $this->AddIndent($value);
      $hb->AddIndent($value);
      $state->IndentCount += $value;
    }

    // Creates the content text.
    private function Content(string $text, LJCTextState $textState, bool $isEmpty
      , bool &$isWrapped) : string
    {
      $retValue = "";

      // Add text content.
      $isWrapped = false;
      if (!$isEmpty
        && LJCCommon::HasValue($text))
      {
        if (strlen($text) > 80 - $this->IndentLength)
        {
          $isWrapped = true;
          $retValue .= "\r\n";
          $this->AddSyncIndent($this, $textState);
          $textValue = $this->GetText($text);
          $retValue .= $textValue;
          $this->AddSyncIndent($this, $textState, -1);
          $retValue .= "\r\n";
          $this->LineLength = 0;
        }
        else
        {
          $retValue .= $text;
        }
      }
      return $retValue;
    }

    // Gets the text to add to the existing line.
    private function GetAddText(string $text, int $addLength) : string
    {
      $retText = substr($text, 0, $addLength);
      if ($this->LineLength > 0
        && $addLength > 0)
      {
        // Add a leading space.
        $retText = " {$retText}";
      }
      return $retText;
    }

    // Gets the current indent length.
    private function IndentLength() : int
    {
      return $this->IndentCount * $this->IndentCharCount;
    }

    // Gets the text length if not null.
    // Move to LJCCommon?
    private function TextLength(?string $text) : int
    {
      $retLength = 0;

      if ($text != null)
      {
        $retLength = strlen($text);
      }
      return $retLength;
    }

    // Updates the text state values.
    private function UpdateState(?LJCTextState $textState)
    {
      if ($textState != null)
      {
        $this->IndentCount = $textState->IndentCount;
      }
    }

    // Calculates the index at which to wrap the text.
    private function WrapIndex(string $text) : int
    {
      $retIndex = -1;

      $totalLength = $this->LineLength + $this->TextLength($text);
      if ($totalLength > $this->LineLimit)
      {
        // Length of additional characters that fit in LineLimit.
        // Only get up to next LineLimit length;
        $currentLength = $this->LineLength;
        if ($currentLength > $this->LineLimit)
        {
          $currentLength = $this->LineLimit;
        }
        $wrapLength = $this->LineLimit - $currentLength;

        // *** Different than TextBuilder ***
        // Get wrap point in allowed length.
        // Wrap on a space.
        $retIndex = LJCCommon::StrRPos($text, " ", $wrapLength);
        if (-1 == $retIndex)
        {
          // Wrap index not found; Wrap at new text.
          $retIndex = 0;
        }
      }
      return $retIndex;
    }

    // Get next text up to LineLimit without leading space.
    private function WrapText(string $text, int $wrapIndex) : string
    {
      $retText;

      $nextLength = strlen($text) - $wrapIndex;

      // Leave room for prepend text.
      // *** Different than TextBuilder ***
      if ($nextLength <= $this->LineLimit - $this->IndentLength())
      {
        // Get text at the wrap index.
        $retText = substr($text, $wrapIndex, $nextLength);
        if (str_starts_with($retText, " "))
        {
          // Remove leading space.
          $retText = substr($retText, 1);
        }
      }
      else
      {
        // Get text from next section.
        $startIndex = $wrapIndex;
        $tempText = substr($text, startIndex);
        if (str_starts_with($tempText, " "))
        {
          $tempText = substr($tempText, 1);
          $startIndex++;
        }
        // *** Different than TextBuilder ***
        $nextLength = $this->LineLimit - $this->IndentLength;
        $nextLength = LJCCommon::StrRPos($tempText, " ", $nextLength);
        $retText = substr($text, $startIndex, $nextLength);
      }
      return $retText;
    }

    // ----------
    // Properties

    // <summary>The indent character count.</summary>
    public int $IndentCharCount;

    public bool $WrapEnabled;

    // The built string value.
    private ?string $BuilderValue;

    // The current indent count.
    private int $IndentCount;

    private int $LineLength;

    private int $LineLimit;
  }

  // ********************
  // Methods:
  /// <summary>Represents a node attribute.</summary>
  class LJCAttribute
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct(string $name = null, string $value = null)
    {
      $this->Name = $name;
      $this->Value = $value;
    } // __construct()
  }

  // ********************
  // Methods:
  /// <summary>Represents a collection of node or element attributes.</summary>
  class LJCAttributes extends LJCCollectionBase
  {
    // Creates an object and adds it to the collection.
    public function Add(string $name, string $value = null, $key = null)
      : ?LJCAttribute
    {
      $retValue = null;

      if (null == $key)
      {
        $key = $name;
      }

      $item = new LJCAttribute($name, $value);
      $retValue = $this->AddObject($item, $key);
      return $retValue;
    }

    // Adds an object and key value.
    public function AddObject(LJCAttribute $item, $key = null) : ?LJCAttribute
    {
      if (null == $key)
      {
        $key = $item->Name;
      }
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    }// AddObject()

    //public array $Items;
  }

  // ********************
  /// <summary>Represents the text state.</summary>
  class LJCTextState
  {
    // ----------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    /// <param name="$indentCount"></param>
    public function __construct(int $indentCount = 0, bool $hasText = false)
    {
      $this->IndentCount = $indentCount;
      $this->HasText = $hasText;
      $this->ChildIndentCount = 0;
    } // __construct()

    // ----------
    // Properties

    // <summary>The current Child IndentCount value.</summary>
    public int $ChildIndentCount;

    // <summary>Indicates if the current builder has text.</summary>
    public bool $HasText;

    // <summary>The current IndentCount value.</summary>
    public int $IndentCount;
  }
?>
