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
  //include_once "$prefix/LJCPHPCommon/LJCDbAccessLib.php";
  // LJCCollectionLib: LJCCollectionBase
  // LJCCommonLib: LJC
  // LJCTextLib: LJCWriter

  /// <summary>The Common Text Output Class Library</summary>
  /// LibName: LJCHTMLBuilderLib
  //  Classes: LJCHTMLBuilder, LJCAttribute, LJCAttributes, LJCTextState

  // ********************
  // Methods: ToString(), AddChildIndent(), AddIndent(), EndsWithNewLine()
  //   , StartWithNewLine(), HasText()
  //
  // Append Text: AddLine(), AddText(), Line(), Text()
  // Get Text: GetAttribs(), GetIndented(), GetIndentString(), GetLine()
  //   , GetText()
  //
  // Append Element: Begin(), Create(), End()
  // Get Element: GetBegin(), GetCreate(), GetEnd()
  //
  // Get Attrib: Attribs(), StartAttribs(), TableAttribs()
  //
  // Represents a built string value.
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
      $childIndentCount = $textState->ChildIndentCount;

      if (LJC::HasValue($createText)
        && $childIndentCount > 0)
      {
        $this->AddIndent($childIndentCount);
        $textState->IndentCount += $childIndentCount;
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
      $builderValue = $this->BuilderValue;
      $retValue = false;

      if (strlen($builderValue) > 0)
      {
        $length = strlen($builderValue);
        if ("\n" == $builderValue[$length - 1])
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

    // Gets the current LJCTextState object.
    public function GetTextState()
    {
      $indentCount = $this->IndentCount;
      $retState = new LJCTextState($indentCount);
      return $retState;
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

    // Gets the current indent length.
    public function IndentLength() : int
    {
      return $this->IndentCount * $this->IndentCharCount;
    }

    // ----------
    // Append Text Methods
    
    // Appends a text line without modification.
    /// <include path='items/AddLine/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function AddLine(string $text = null) : string
    {
      $retText = "{$text}\r\n";
      $this->BuilderValue .= $retText;
      return $retText;
    }

    // Appends text without modification.
    /// <include path='items/AddText/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function AddText(string $text)
    {
      if (LJC::HasValue($text))
      {
        $this->BuilderValue .= $text;
      }
    }

    // Appends a potentially indented text line to the builder.
    /// <include path='items/Line/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Line(?string $text = null, bool $addIndent = true
      , bool $allowNewLine = true) : string
    {
      $retText = $this->GetLine($text, $addIndent, $allowNewLine);
      $this->BuilderValue .= $retText;
      return $retText;
    }

    // Appends the potentially indented text.
    /// <include path='items/Text/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Text(string $text, bool $addIndent = true
      , bool $allowNewLine = true) : string
    {
      $retText = $this->GetText($text, $addIndent, $allowNewLine);
      if (LJC::HasValue($retText))
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

      if (LJC::HasItems($attribs))
      {
        $hb = new LJCHTMLBuilder($textState);
        $isFirst = true;
        foreach ($attribs as $attrib)
        {
          $name = $attrib->Name;
          $value = $attrib->Value;

          if (!$isFirst)
          {
            // Wrap line for large attribute value.
            if (LJC::HasValue($value)
              && strlen($value) > 35)
            {
              $hb->AddText("\r\n{$GetIndentString()}");
            }
          }
          $isFirst = false;

          $hb->AddText(" {$name}");
          if (LJC::HasValue($value))
          {
            $hb->AddText("=\"{$value}\"");
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

      if (LJC::HasValue($text))
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
    /// <include path='items/GetWrapped/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function GetWrapped(string $text) : string
    {
      $lineLength = $this->LineLength;
      $lineLimit = $this->LineLimit;
      $retText = $text;

      $buildText = "";
      $workText = $text;
      $totalLength = $lineLength + $this->TextLength($workText);
      if ($totalLength < $lineLimit)
      {
        // No wrap.
        $this->LineLength += $this->TextLength($text);
      }

      while ($totalLength > $lineLimit)
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
          $this->LineLength = strlen($lineText);
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
            $totalLength = $lineLength + $this->TextLength($workText);
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
    public function Create(string $name, LJCTextState $textState
      , string $text = "", LJCAttributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true, bool $isEmpty = false, bool $close = true)
      : string
    {
      // Adds the indent string.
      $createText = $this->GetCreate($name, $text, $textState, $attribs
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

      $this->AddSyncIndent($hb, $textState, -1);
      $hb->Text("</{$name}>", $addIndent);

      $retElement = $hb->ToString();
      return $retElement;
    }

    // ----------
    // Get Attribs Methods

    // Gets common element attributes.
    /// <include path='items/Attribs/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function Attribs(string $className = null, string $id = null)
      : LJCAttributes
    {
      $retAttribs = new LJCAttributes();
      if (LJC::HasValue($id))
      {
        $retAttribs->Add("id", $id);
      }
      if (LJC::HasValue($className))
      {
        $retAttribs->Add("class", $className);
      }
      return $retAttribs;
    }

    // Creates the HTML element attributes.
    /// <include path='items/StartAttribs/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function StartAttribs() : LJCAttributes
    {
      $retAttribs = new LJCAttributes();
      $retAttribs->Add("lang", "en");
      $retAttribs->Add("xmlns", "http://www.w3.org/1999/xhtml");
      return $retAttribs;
    }

    // Gets common table attributes.
    /// <include path='items/TableAttribs/*' file='Doc/LJCHTMLBuilder.xml'/>
    public function TableAttribs(int $border = 1, int $cellSpacing = 0
      , int $cellPadding = 2, string $className = null, string $id = null)
      : LJCAttributes
    {
      $retAttribs = $this->Attribs($className, $id);
      $retAttribs->Add("border", strval($border));
      $retAttribs->Add("cellspacing", strval($cellSpacing));
      $retAttribs->Add("cellpadding", strval($cellPadding));
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
        && LJC::HasValue($text))
      {
        if (strlen($text) > 80 - $this->IndentLength())
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

    // Gets the text length if not null.
    // Move to LJC?
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
        $retIndex = LJC::StrRPos($text, " ", $wrapLength);
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
        $nextLength = LJC::StrRPos($tempText, " ", $nextLength);
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
  // Methods: Add(), AddObject(), Retrieve()
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

    // Gets an item by key.
    public function Retrieve($key)
    {
      $retValue = null;

      $retValue = $this->RetrieveItem($key);
      return $retValue;
    }
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
