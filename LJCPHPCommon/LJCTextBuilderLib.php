<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCTextBuilderLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  // LJCCommonLib: LJC
  // LJCCollectionLib: LJCCollectionBase

  // The Text Builder Class Library
  /// <include path='members/LJCTextBuilderLib/*' file='Doc/LJCTextBuilder.xml'/>
  /// LibName: LJCTextBuilderLib

  // Represents a node or element attribute.
  /// <include path='members/LJCAttribute/*' file='Doc/LJCAttribute.xml'/>
  class LJCAttribute
  {
    // #region Properties - LJCAttribute

    /// <summary>The class name for debugging.</summary>
    public string $ClassName = "";

    /// <summary>The debug text.</summary>
    public string $LogText = "";

    /// <summary>The item name.</summary>
    public ?string $Name = null;

    /// <summary>The item value.</summary>
    public ?string $Value = null;
    // #endregion

    // #region Static Methods - LJCAttribute

    // Creates a typed data object from a standard object.
    /// <include path='members/Copy/*' file='Doc/LJCAttribute.xml'/>
    public static function Copy($objItem)
    {
      // Static method output logging.
      $className = "LJCAttribute";
      $methodName = "Copy()";

      $retAttrib = null;

      if ($objItem != null)
      {
        $properties = get_object_vars($objItem);
        $retAttrib = new LJCAttribute();

        // Look for properties of simple object in object.
        foreach ($properties as $propertyName => $value)
        {
          if (property_exists($objItem, $propertyName))
          {
            // Update new object properties from the simple object.
            $retAttrib->$propertyName = $value;
          }
        }
      }
      return $retAttrib;
    } // Copy()
    // #endregion

    // #region Constructor Methods - LJCAttribute

    // Initializes an object instance.
    /// <include path='members/construct/*' file='Doc/LJCAttribute.xml'/>
    public function __construct(string $name = null, string $value = null)
    {
      // Set logging values.
      $this->ClassName = "LJCAttribute";
      $methodName = "constructor";
      // Output logging.
      //LJC::OutputDebugObject(__line__, $this->ClassName, $methodName
      //  , "\$object", $object);

      // Property logging.
      $this->LogText = "";
      //$this->AddLogText(__line__, $this->ClassName, $methodName, "\$value"
      //  , $value);

      $this->Name = $name;
      $this->Value = $value;
    } // construct()

    // Add to logging property.
    private function AddLogText(int $lineNumber, $methodName, $valueName
      , $value = null)
    {
      $methodName = "{$methodName}()";
      $this->LogText .= LJC::OutputDebugObject($lineNumber, $this->ClassName
        , $methodName, $valueName, $value);
    } // AddLogText()
    // #endregion
    
    // #region Data Class Methods - LJCAttribute

    // Creates an object clone.
    /// <include path='members/Clone/*' file='Doc/LJCAttribute.xml'/>
    public function Clone()
    {
      $retAttrib = new LJCAttribute($this->Name, $this->Value);
      return $retAttrib;
    } // Clone()
    // #endregion
  } // LJCAttribute

  // Represents a collection of node or element attributes.
  /// <include path='members/LJCAttributes/*' file='Doc/LJCAttributes.xml'/>
  class LJCAttributes extends LJCCollectionBase
  {
    // #region Properties - LJCAttributes

    /// <summary>The class name for debugging.</summary>
    public string $ClassName = "";

    /// <summary>The debug text.</summary>
    public string $LogText = "";
    // #endregion

    // #region Static Methods - LJCAttributes

    // Creates a typed collection from an array of objects.
    /// <include path='members/ToCollection/*' file='Doc/LJCAttributes.xml'/>
    public static function ToCollection(array $arrItems)
    {
      // Static method output logging.
      $className = "LJCAttributes";
      $methodName = "ToCollection";
      $retAttribs = null;

      if (LJC::HasElements($arrItems))
      {
        $retAttribs = new LJCAttributes();
        $key = 1;
        foreach ($arrItems as $objItem)
        {
          // Create object from simple object.
          $attrib = LJCAttribute::Copy($objItem);
          $retAttribs->AddObject($attrib, $key);
          $key++;
        }
      }
      return $retAttribs;
    } // ToCollection()
    // #endregion

    // #region Constructor Methods - LJCAttributes

    // Initializes an object instance.
    /// <include path='members/construct/*' file='Doc/LJCAttributes.xml'/>
    public function __construct()
    {
      // Set logging values.
      $this->ClassName = "LJCAttributes";
      $methodName = "constructor";
      // Output logging.
      //LJC::OutputDebugObject(__line__, $this->ClassName, $methodName
      //  , "\$object", $object);

      // Property logging.
      $this->LogText = "";
      //$this->AddLogText(__line__, $this->ClassName, $methodName, "\$value"
      //  , $value);
    } // construct()

    // Add to logging property.
    private function AddLogText(int $lineNumber, $methodName, $valueName
      , $value = null)
    {
      // Method property logging.
      $methodName = "{$methodName}()";
      $this->LogText .= LJC::OutputDebugObject($lineNumber, $this->ClassName
        , $methodName, $valueName, $value);
    } // AddLogText()
    // #endregion

    // #region Collection Methods - LJCAttributes

    // Creates an object and adds it to the collection.
    /// <include path='members/Add/*' file='Doc/LJCAttributes.xml'/>
    public function Add(string $name, string $value = null, $key = null)
      : ?LJCAttribute
    {
      $methodName = "Add";
      $retAttrib = null;

      if (null == $key)
      {
        $key = $name;
      }

      $attrib = new LJCAttribute($name, $value);
      $retAttrib = $this->AddObject($attrib, $key);
      return $retAttrib;
    } // Add()

    // Adds an object and key value.
    /// <include path='members/AddObject/*' file='Doc/LJCAttributes.xml'/>
    public function AddObject(LJCAttribute $attrib, $key = null): ?LJCAttribute
    {
      $methodName = "AddObject";
      $retAttrib = null;

      if (null == $key)
      {
        $key = $attrib->Name;
      }

      $process = true;

      // Merge new styles with existing styles.
      if ("style" == strtolower($key)
        && $this->HasKey($key))
      {
        $foundAttrib = $this->Retrieve($key);
        if ($foundAttrib != null)
        {
          $process = false;
          $mergedValue = $this->MergeStyle($foundAttrib, $attrib);
          if (LJC::HasValue($mergedValue))
          {
            $foundAttrib->Value = $mergedValue;
          }
        }
      }

      if ($process)
      {
        $retAttrib = $this->AddItem($attrib, $key);
      }
      return $retAttrib;
    }// AddObject()

    // Appends items.
    /// <include path='members/Append/*' file='Doc/LJCAttributes.xml'/>
    public function Append(LJCAttributes $attribs): void
    {
      foreach ($attribs as $attrib)
      {
        $this->AddObject($attrib);
      }
    } // Append()

    // Removes the item by Key value.
    /// <include path='members/Remove/*' file='Doc/LJCAttributes.xml'/>
    public function Remove($key, bool $throwError = true): void
    {
      // DeleteItem() is in LJCCollectionBase.
      $this->DeleteItem($key, $throwError);
    } // Remove()

    // Gets an item by key.
    /// <include path='members/Retrieve/*' file='Doc/LJCAttributes.xml'/>
    public function Retrieve($key)
    {
      $retValue = null;

      // RetrieveItem() is in LJCCollectionBase.
      $retValue = $this->RetrieveItem($key);
      return $retValue;
    } // Retrieve()
    // #endregion

    // #region Other Methods - LJCAttributes

    // Merges "style" attrib rules.
    /// <include path='members/MergeStyle/*' file='Doc/LJCAttributes.xml'/>
    public function MergeStyle($foundAttrib, $newAttrib)
    {
      $methodName = "MergeStyle";
      $retMergedRules = $this->SingleValue($foundAttrib, $newAttrib);

      if (!LJC::HasValue($retMergedRules))
      {
        // Get existing style rules.
        $foundValue = trim($foundAttrib->Value);
        $foundRules = explode(";", $foundValue);

        // Get new style rules.
        $newValue = trim($newAttrib->Value);
        $newRules = explode(";", $newValue);

        // Save previous rule unless overriden by new rule.
        foreach ($foundRules as $foundRule)
        {
          if ($foundRule != null)
          {
            // 0 = Property, 1 = Value.
            $values = explode(":", $foundRule);
            $property = self::TrimElement($values, 0);

              // Check for override.
            $newRule = $this->FindRule($newRules, $property);
            if ($newRule != null)
            {
              $values = explode(":", $newRule);
              $index = $this->GetRuleIndex($newRules, $property);
              unset($newRules[index]);
            }

            $property = self::TrimElement($values, 0);
            $value = self::TrimElement($values, 1);
            $retMergedRules .= "{$property}: {$value}; ";
          }
        }

        // Add remaining new rules.
        foreach ($newRules as $newRule)
        {
          $values = explode(":", $newRule);
          $property = self::TrimElement($values, 0);
          if (LJC::HasValue($property))
          {
            $value = self::TrimElement($values, 1);
            $retMergedRules .= "{$property}: {$value}; ";
          }
        }
      }
      return $retMergedRules;
    } // MergeStyle()
    // #endregion

    // #region Private Methods - LJCAttributes

    // Trims element value or if null, returns null.
    private static function TrimElement($values, $index)
    {
      // Static method output logging.
      $className = "LJCAttribute";
      $methodName = "TrimElement";
      $retValue = null;

      if ($values != null)
      {
        if (count($values) > $index)
        {
          $retValue = trim($values[$index]);
        }
      }
      return $retValue;
    } // TrimElement()

    // Finds the rule with the supplied property name.
    private function FindRule($rules, $property)
    {
      $methodName = "FindRule";
      $retRule = "";

      $property = trim($property);
      foreach ($rules as $rule)
      {
        $values = explode(":", $rule);
        if (trim($values[0]) == $property)
        {
          $retRule = $rule;
          break;
        }
      }
      return $retRule;
    } // FindRule()

    // Finds the rule index with the supplied property name.
    private function GetRuleIndex($rules, $property)
    {
      $methodName = "GetRuleIndex";
      $retIndex = -1;

      for ($index = 0; $index < count($rules); $index++)
      {
        $rule = $rules[$index];
        $values = explode(":", $rule);
        if (trim($values[0]) == $property)
        {
          $retIndex = $index;
          break;
        }
      }
      return $retIndex;
    } // GetRuleIndex()

    // Returns the existing value if only one exists.
    // Otherwise returns an empty string.
    private function SingleValue($foundAttrib, $newAttrib)
    {
      $methodName = "SingleValue";
      $retRules = "";

      if (null == $foundAttrib
        && $newAttrib != null)
      {
        $retRules = $newAttrib->Value;
      }
      if (null == $newAttrib
        && $existingAttrib != null)
      {
        $retRules = $foundAttrib->Value;
      }
      return $retRules;
    } // SingleValue()
    // #endregion
  } // LJCAttributes

  // ********************
  // Represents a built string value.
  /// <include path='members/LJCTextBuilder/*' file='Doc/LJCTextBuilder.xml'/>
  class LJCTextBuilder
  {
    // #region Properties - LJCTextBuilder

    /// <summary>The class name for debugging.</summary>
    public string $ClassName = "";

    /// <summary>The debug text.</summary>
    public string $LogText = "";

    // <summary>The indent character count.</summary>
    public int $IndentCharCount = 0;

    /// <summary>Gets the current length.</summary>
    public int $LineLength = 0;

    /// <summary>Gets or sets the character limit.</summary>
    public int $LineLimit = 0;

    /// <summary>Indicates if the wrap functionality is enabled.</summary>
    public bool $WrapEnabled = false;

    // The built string value.
    private ?string $BuilderValue = "";

    // The current indent count.
    private int $IndentCount = 0;
    // #endregion

    // #region Constructor Methods - LJCTextBuilder

    // Initializes a class instance.
    /// <include path='members/construct/*' file='Doc/LJCTextBuilder.xml'/>
    public function __construct(?LJCTextState $textState = null)
    {
      // Set logging values.
      $this->ClassName = "LJCTextBuilder";
      $methodName = "constructor";
      // Output logging.
      //LJC::OutputDebugObject(__line__, $this->ClassName, $methodName
      //  , "\$object", $object);

      // Property logging.
      $this->LogText = "";
      //$this->AddLogText(__line__, $this->ClassName, $methodName, "\$value"
      //  , $value);

      $this->BuilderValue = "";
      $this->IndentCharCount = 2;
      $this->setIndentCount(0);
      if ($textState != null)
      {
        $this->AddIndent($textState->getIndentCount());
      }
      $this->LineLength = 0;
      $this->LineLimit = 80;
      $this->WrapEnabled = false;
    } // construct()

    // Add to logging property.
    private function AddLogText(int $lineNumber, $methodName, $valueName
      , $value = null)
    {
      // Method property logging.
      $methodName = "{$methodName}()";
      $this->LogText .= LJC::OutputDebugObject($lineNumber, $this->ClassName
        , $methodName, $valueName, $value);
    } // AddLogText()
    // #endregion

    // #region Data Class Methods - LJCTextBuilder

    // Gets the built string.
    /// <include path='members/ToString/*' file='Doc/LJCTextBuilder.xml'/>
    public function ToString(): string
    {
      return $this->BuilderValue;
    } // ToString()
    // #endregion

    // #region Add Text Methods - LJCTextBuilder
    
    // Appends a text line without modification.
    /// <include path='members/AddLine/*' file='Doc/LJCTextBuilder.xml'/>
    public function AddLine(string $text = null): string
    {
      $methodName = "Add";
      $retText = "{$text}\r\n";

      $this->BuilderValue .= $retText;
      return $retText;
    } // AddLine()

    // Appends text without modification.
    /// <include path='members/AddText/*' file='Doc/LJCTextBuilder.xml'/>
    public function AddText(string $text): void
    {
      $methodName = "AddText";

      if ($this->TextLength($text) > 0)
      {
        $this->BuilderValue .= $text;
      }
    } // AddText()
    // #endregion

    // #region Append Text Methods - LJCTextBuilder

    // Appends a potentially indented text line to the builder.
    /// <include path='members/Line/*' file='Doc/LJCTextBuilder.xml'/>
    public function Line(?string $text = null, bool $addIndent = true
      , bool $allowNewLine = true): string
    {
      $methodName = "Line";
      $retText = $this->GetLine($text, $addIndent, $allowNewLine);

      $this->BuilderValue .= $retText;
      return $retText;
    } // Line()

    // Appends the potentially indented text.
    /// <include path='members/Text/*' file='Doc/LJCTextBuilder.xml'/>
    public function Text(string $text, bool $addIndent = true
      , bool $allowNewLine = true): string
    {
      $methodName = "Text";
      $retText = $this->GetText($text, $addIndent, $allowNewLine);

      if ($this->TextLength($retText) > 0)
      {
        $this->BuilderValue .= $retText;
      }
      return $retText;
    } // Text()
    // #endregion

    // #region Get Text Methods - LJCTextBuilder

    // Gets a modified text line.
    /// <include path='members/GetLine/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetLine(string $text = null, bool $addIndent = true
      , bool $allowNewLine = true): string
    {
      $methodName = "GetLine";
      $retLine = $this->GetText($text, $addIndent, $allowNewLine);

      $retLine .= "\r\n";
      return $retLine;
    } // GetLine()

    // Gets the potentially indented and wrapped text.
    /// <include path='members/GetText/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetText(?string $text, bool $addIndent = true
      , bool $allowNewLine = true): string
    {
      $methodName = "GetText";
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
    } // GetText()
    // #endregion

    // #region Other Get Text Methods - LJCTextBuilder

    // Gets a new potentially indented line.
    /// <include path='members/GetIndented/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetIndented(string $text): string
    {
      $methodName = "GetIndented";
      $retText = "";

      // Allow add of blank characters.
      if ($text != null)
      {
        $retText = $this->GetIndentString();
        $retText .= $text;
      }
      return $retText;
    } // GetIndented()

    // Gets the current indent string.
    /// <include path='members/GetIndentString/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetIndentString(): string
    {
      $methodName = "GetIndentString";
      $retValue = str_repeat(" ", $this->IndentLength());
      return $retValue;
    } // GetIndentString()

    // Appends added text and new wrapped line.
    /// <include path='members/GetWrapped/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetWrapped(string $text): string
    {
      $methodName = "GetWrapped";
      $retText = $text;

      $buildText = "";
      $workText = $text;
      $lineLength = $this->LineLength;
      $lineLimit = $this->LineLimit;
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
    } // GetWrapped()
    // #endregion

    // #region Get Attribs Methods - LJCTextBuilder

    // Gets common element attributes.
    /// <include path='members/Attribs/*' file='Doc/LJCTextBuilder.xml'/>
    public function Attribs(string $className = null, string $id = null)
      : LJCAttributes
    {
      $methodName = "Attribs";
      $retAttribs = new LJCAttributes();

      if (LJC::HasValue($id))
      {
        $retAttribs->Add("id", $id);
      }
      if (LJC::HasValue($className))
      {
        $retAttribs->Add("class", $className);
      }

      $this->LogText .= $retAttribs->LogText;
      return $retAttribs;
    } // Attribs()

    // Gets the attributes text.
    /// <include path='members/GetAttribs/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetAttribs(?LJCAttributes $attribs, LJCTextState $textState)
      : string
    {
      $methodName = "GetAttribs";
      $retText = "";

      if (LJC::HasItems($attribs))
      {
        $tb = new LJCTextBuilder($textState);
        foreach ($attribs as $attrib)
        {
          $name = $attrib->Name;
          $value = $attrib->Value;

          if ($tb->HasText())
          {
            // Wrap line for large attribute value.
            if (LJC::HasValue($value)
              && strlen($value) > 35)
            {
              $tb->AddText("\r\n{$this->GetIndentString()}");
            }
          }

          // [ AttribName="Value"]
          $tb->AddText(" {$name}");
          if (LJC::HasValue($value))
          {
            $tb->AddText("=\"{$value}\"");
          }
        }

        $this->LogText .= $attribs->LogText;
        $retText = $tb->ToString();
      }
      return $retText;
    } // GetAttribs()

    // Creates the HTML element attributes.
    /// <include path='members/StartAttribs/*' file='Doc/LJCTextBuilder.xml'/>
    public function StartAttribs(): LJCAttributes
    {
      $methodName = "StartAttribs";
      $retAttribs = new LJCAttributes();

      $retAttribs->Add("lang", "en");
      //$retAttribs->Add("xmlns", "http://www.w3.org/1999/xhtml");

      $this->LogText .= $retAttribs->LogText;
      return $retAttribs;
    } // StartAttribs()

    // Creates the XML element attributes.
    /// <include path='members/StartXMLAttribs/*' file='Doc/LJCTextBuilder.xml'/>
    public function StartXMLAttribs(): LJCAttributes
    {
      $methodName = "StartXMLAttribs";
      $retAttribs = new LJCAttributes();

      $retAttribs->Add("xmlns:xsd", "http://www.w3.org/2001/XMLSchema");
      $retAttribs->Add("xmlns:xsi"
        , "http://www.w3.org/2001/XMLSchema-instance");

      $this->LogText .= $retAttribs->LogText;
      return $retAttribs;
    } // StartXMLAttribs()

    // Gets common table attributes.
    /// <include path='members/TableAttribs/*' file='Doc/LJCTextBuilder.xml'/>
    public function TableAttribs(int $border = 1, int $borderSpacing = 0
      , int $cellPadding = 2, string $className = null, string $id = null)
      : LJCAttributes
    {
      $methodName = "TableAttribs";
      $retAttribs = $this->Attribs($className, $id);

      $value = strval($border);
      $style = "border: {$value}px solid;";
      $value = strval($borderSpacing);
      $style .= " borderspacing: {$value}px;";
      $value = strval($cellPadding);
      $style .= " cellpadding: {$value}px;";

      $retAttribs->Add("style", $style);

      $this->LogText .= $retAttribs->LogText;
      return $retAttribs;
    } // TableAttribs()
    // #endregion

    // #region Append Element Methods - LJCTextBuilder

    // Appends the element begin tag.
    /// <include path='members/Begin/*' file='Doc/LJCTextBuilder.xml'/>
    public function Begin(string $name, LJCTextState $textState
      , LJCAttributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true): string
    {
      $methodName = "Begin";

      $createText = $this->GetBegin($name, $textState, $attribs, $addIndent
        , $childIndent);
      $this->Text($createText, addIndent: false);

      // Use AddChildIndent after beginning an element.
      if ($childIndent)
      {
        $this->AddChildIndent($createText, $textState);
      }

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    } // Begin()

    // Appends an element.
    /// <include path='members/Create/*' file='Doc/LJCTextBuilder.xml'/>
    public function Create(string $name, LJCTextState $textState
      , string $text = "", LJCAttributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true, bool $isEmpty = false, bool $close = true)
      : string
    {
      $methodName = "Create";

      // Adds the indent string.
      $createText = $this->GetCreate($name, $text, $textState, $attribs
        , $addIndent, $childIndent, $isEmpty, $close);
      $this->Text($createText, addIndent: false);
      if (!$close)
      {
        // Use AddChildIndent after beginning an element.
        $this->AddChildIndent($createText, $textState);
      }

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    } // Create()

    // Appends the element end tag.
    /// <include path='members/End/*' file='Doc/LJCTextBuilder.xml'/>
    public function End(string $name, LJCTextState $textState
      , bool $addIndent = true): string
    {
      $methodName = "End";

      $createText = $this->GetEnd($name, $textState, $addIndent);
      $this->Text($createText, addIndent: false);

      // Append Method
      $this->UpdateState($textState);
      return $createText;
    } // End()
    // #endregion

    // #region Get Element Methods - LJCTextBuilder

    // Gets the element begin tag.
    /// <include path='members/GetBegin/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetBegin(string $name, LJCTextState $textState
      , LJCAttributes $attribs = null, bool $addIndent = true
      , bool $childIndent = true): string
    {
      $methodName = "GetBegin";

      $tb = new LJCTextBuilder($textState);

      $createText = $this->GetCreate($name, "", $textState, $attribs
        , $addIndent, $childIndent, close: false);
      $tb->Text($createText, addIndent: false);

      // Only use AddChildIndent() if additional text is added in this method.
      $retValue = $tb->ToString();
      return $retValue;
    } // GetBegin()

    // Gets an element.
    /// <include path='members/GetCreate/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetCreate(string $name, string $text
      , LJCTextState $textState, LJCAttributes $attribs = null
      , bool $addIndent = true, bool $childIndent = true, bool $isEmpty = false
      , bool $close = true): string
    {
      $methodName = "GetCreate";

      $textState->ChildIndentCount = 0; // ?
      $tb = new LJCTextBuilder($textState);

      // Start text with the opening tag.
      $tb->Text("<{$name}", $addIndent);
      $getText = $this->GetAttribs($attribs, $textState);
      $tb->AddText($getText);
      if ($isEmpty)
      {
        $tb->AddText(" /");
        $close = false;
      }
      $tb->AddText(">");

      // Content is added if not an empty element.
      $isWrapped = false;
      if (!$isEmpty
        && LJC::HasValue($text))
      {
        $content = $this->Content($text, $textState, $isEmpty, $isWrapped);
        $tb->AddText($content);
      }

      // Close the element.
      if ($close)
      {
        if ($isWrapped)
        {
          $tb->Line();
          $tb->AddText($this->GetIndentString());
        }
        $tb->AddText("</{$name}>");
      }

      // Increment ChildIndentCount if not empty and not closed.
      if (!$isEmpty
        && !$close
        && $childIndent)
      {
        $textState->ChildIndentCount++;
      }

      $retElement = $tb->ToString();
      return $retElement;
    } // GetCreate()

    // Gets the element end tag.
    /// <include path='members/GetEnd/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetEnd(string $name, LJCTextState $textState
      , bool $addIndent = true): string
    {
      $methodName = "GetEnd";

      $tb = new LJCTextBuilder($textState);

      $this->AddSyncIndent($tb, $textState, -1);
      $tb->Text("</{$name}>", $addIndent);

      $retElement = $tb->ToString();
      return $retElement;
    } // GetEnd()
    // #endregion

    // Other Methods - LJCTextBuilder

    // Adds the new (child) indents.
    /// <include path='members/AddChildIndent/*' file='Doc/LJCTextBuilder.xml'/>
    public function AddChildIndent(string $createText, LJCTextState $textState)
      : void
    {
      $methodName = "AddChildIndent";

      $childIndentCount = $textState->ChildIndentCount;

      if ($this->TextLength($createText) > 0
        && $childIndentCount > 0)
      {
        $this->AddIndent($childIndentCount);
        $indentCount = $textState->getIndentCount() + $childIndentCount;
        $textState->setIndentCount($indentCount);
        $textState->ChildIndentCount = 0;
      }
    } // AddChildIndent()

    // Changes the IndentCount by the provided value.
    /// <include path='members/AddIndent/*' file='Doc/LJCTextBuilder.xml'/>
    public function AddIndent($increment = 1): int
    {
      $methodName = "AddIndent";

      $indentCount = $this->getIndentCount() + $increment;
      $this->setIndentCount($indentCount);
      return $this->getIndentCount();
    } // AddIndent()

    // Indicates if the builder text ends with a newline.
    /// <include path='members/EndsWithNewLine/*' file='Doc/LJCTextBuilder.xml'/>
    public function EndsWithNewLine(): bool
    {
      $methodName = "EndsWithNewLine";
      $retValue = false;

      $builderValue = $this->BuilderValue;
      $length = strlen($builderValue);
      if ($length > 0)
      {
        if ("\n" == $builderValue[$length - 1])
        {
          $retValue = true;
        }
      }
      return $retValue;
    } // EndsWithNewLine()

    // Gets a current LJCTextState object.
    /// <include path='members/GetTextState/*' file='Doc/LJCTextBuilder.xml'/>
    public function GetTextState(): LJCTextState
    {
      $methodName = "GetTextState";

      $indentCount = $this->getIndentCount();
      $retState = new LJCTextState($indentCount);
      return $retState;
    } // GetTextState()

    // Indicates if the builder has text.
    /// <include path='members/HasText/*' file='Doc/LJCTextBuilder.xml'/>
    public function HasText(): bool
    {
      $methodName = "HasText";
      $retValue = false;

      if (strlen($this->BuilderValue) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    } // HasText()

    // Gets the current indent length.
    /// <include path='members/IndentLength/*' file='Doc/LJCTextBuilder.xml'/>
    public function IndentLength(): int
    {
      return $this->getIndentCount() * $this->IndentCharCount;
    } // IndentLength()

    // Checks if the text can start with a newline.
    /// <include path='members/StartWithNewLine/*' file='Doc/LJCTextBuilder.xml'/>
    public function StartWithNewLine(bool $allowNewLine): bool
    {
      $methodName = "StartWithNewLine";
      $retValue = false;

      if ($allowNewLine
        && $this->HasText()
        && !$this->EndsWithNewLine())
      {
        $retValue = true;
      }
      return $retValue;
    } // StartWithNewLine()
    // #endregion

    // #region Private Methods - LJCTextBuilder

    // Adds indent to builders and sync object.
    private function AddSyncIndent(LJCTextBuilder $tb, LJCTextState $state
      , int $value = 1): void
    {
      $methodName = "AddSyncIndent";

      $this->AddIndent($value);
      $tb->AddIndent($value);
      $indentCount = $state->getIndentCount() + $value;
      $state->setIndentCount($indentCount);
    }

    // Creates the content text.
    private function Content(string $text, LJCTextState $textState, bool $isEmpty
      , bool &$isWrapped): string
    {
      $methodName = "Content";
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
    private function GetAddText(string $text, int $addLength): string
    {
      $methodName = "GetAddText";
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
    private function TextLength(?string $text): int
    {
      $methodName = "TextLength";
      $retLength = 0;

      if ($text != null)
      {
        $retLength = strlen($text);
      }
      return $retLength;
    }

    // Updates the text state values.
    private function UpdateState(?LJCTextState $textState): void
    {
      if ($textState != null)
      {
        $this->setIndentCount($textState->getIndentCount());
      }
    }

    // Calculates the index at which to wrap the text.
    private function WrapIndex(string $text): int
    {
      $methodName = "WrapIndex";
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
    private function WrapText(string $text, int $wrapIndex): string
    {
      $methodName = "WrapText";
      $retText = "";

      $nextLength = strlen($text) - $wrapIndex;

      // Leave room for prepend text.
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
        $nextLength = $this->LineLimit - $this->IndentLength;
        $nextLength = LJC::StrRPos($tempText, " ", $nextLength);
        $retText = substr($text, $startIndex, $nextLength);
      }
      return $retText;
    }
    // #endregion

    // #region Getters and Setters - LJCTextBuilder

    // Gets the indent count.
    /// <include path='members/getIndentCount/*' file='Doc/LJCTextBuilder.xml'/>
    public function getIndentCount(): int
    {
      return $this->IndentCount;
    }

    // Sets the indent count.
    private function setIndentCount(int $count): void
    {
      if ($count >= 0)
      {
        $this->IndentCount = $count;
      }
    }
    // #endregion
  }

  // Represents the text state.
  /// <include path='items/LJCTextState/*' file='Doc/LJCTextState.xml'/>
  class LJCTextState
  {
    // #region Properties - LJCTextState

    /// <summary>The class name for debugging.</summary>
    public string $ClassName = "";

    /// <summary>The debug text.</summary>
    public string $LogText = "";

    // <summary>The current Child IndentCount value.</summary>
    public int $ChildIndentCount;

    // <summary>Indicates if the current builder has text.</summary>
    public bool $HasText;

    // <summary>The current IndentCount value.</summary>
    private int $IndentCount;
    // #endregion

    // #region Constructor Methods - LJCTextState

    // Initializes an object instance.
    /// <include path='items/construct/*' file='Doc/LJCTextState.xml'/>
    public function __construct(int $indentCount = 0, bool $hasText = false)
    {
      // Set logging values.
      $this->ClassName = "LJCTextState";
      $methodName = "constructor";

      // Property logging.
      $this->LogText = "";
      //$this->AddLogText(__line__, $this->ClassName, $methodName, "\$value"
      //  , $value);

      $this->setIndentCount($indentCount);
      $this->HasText = $hasText;
      $this->ChildIndentCount = 0;
    } // __construct()

    // Add to logging property.
    private function AddLogText(int $lineNumber, $methodName, $valueName
      , $value = null)
    {
      // Method property logging.
      $methodName = "{$methodName}()";
      $this->LogText .= LJC::OutputDebugObject($lineNumber, $this->ClassName
        , $methodName, $valueName, $value);
    } // AddLogText()
    // #endregion

    // #region Getters and Setters - LJCTextState

    // Gets the indent count.
    /// <include path='items/getIndentCount/*' file='Doc/LJCTextState.xml'/>
    public function getIndentCount(): int
    {
      return $this->IndentCount;
    }

    // Sets the indent count.
    /// <include path='items/setIndentCount/*' file='Doc/LJCTextState.xml'/>
    public function setIndentCount(int $count): void
    {
      $methodName = "setIndentCount";

      if ($count >= 0)
      {
        $this->IndentCount = $count;
      }
    }
    // #endregion
  }

