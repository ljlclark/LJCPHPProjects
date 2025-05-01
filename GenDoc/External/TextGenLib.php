<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // TextGenLib.php
  declare(strict_types=1);
  $devPath = "";
  include_once "LJCGenTextSectionLib.php";

  // Classes
  // LJCGenTextSectionLib
  //   LJCDirective
  //   LJCSection
  //   LJCSections
  //   LJCItem
  //   LJCReplacement
  //   LJCReplacements
  // File
  //   TextGenLib

  // ***************
  /// <summary>Generate output text from a template and data.</summary>
  class TextGenLib
  {
    // ---------------
    // Public Static Functions

    /// <summary>Check for text.</summary>
    public static function HasItems($items) : bool
    {
      $retValue = false;

      if ($items != null
        && count($items) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    } // HasItems()

    /// <summary>Check for text.</summary>
    public static function HasValue($text) : bool
    {
      $retValue = false;

      if ($text != null
        && strlen(trim($text)) > 0)
      {
        $retValue = true;
      }
      return $retValue;
    } // HasValue()

    // 
    public static function Debug(string $context, $value = "")
    {
      echo("*** $context: $value\r\n");
    } // Debug()

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      $this->CommentChars = "//";
      $this->PlaceholderBegin = "_";
      $this->PlaceholderEnd = "_";

      $this->ActiveReplacements = [];
      $this->Output = "";
    } // __construct()

    // ---------------
    // Main Processing Methods

    /// <summary>Generate the Output text.</summary>
    public function TextGen(LJCSections $sections, array $templateLines)
      : string
    {
      $this->Sections = $sections;
      $this->Lines = $templateLines;

      for ($lineIndex = 0; $lineIndex < count($templateLines)
        ; $lineIndex++)
      {
        $line = rtrim($this->Lines[$lineIndex]);
        if ($this->IsConfig($line))
        {
          continue;
        }

        $directive = LJCDirective::GetDirective($line, $this->CommentChars);
        if ($directive != null
          && $directive->IsSectionBegin())
        {
          $section = $this->Sections->Retrieve($directive->Name);
          if (null == $section)
          {
            // No Section data.
            $lineIndex = $this->SkipSection($lineIndex);
          }
          else
          {
            $lineIndex++;
            $section->BeginLineIndex = $lineIndex;
            $this->DoItems($section, $lineIndex);
          }
          continue;
        }
        $this->AddOutput($line);
      }
      return $this->Output;
    } // TextGen()

    // Process the #IfBegin LJCDirective.
    private function DoIf(LJCDirective $directive, LJCReplacements $replacements
      , int &$lineIndex) : void
    {
      // Check replacement value against LJCDirective value.
      $success = true;
      $isIf = false;
      $isMatch = false;
      if (!TextGenLib::HasValue($directive->Value))
      {
        $success = false;
      }

      // Check replacement value against directive value.
      if ($success)
      {
        $replacement = $replacements->Retrieve($directive->Name, false);
        if ("hasvalue" == strtolower($directive->Value))
        {
          $isIf = true;
          if ($replacement != null
            && TextGenLib::HasValue($replacement->Value))
          {
            $isMatch = true;
          }
        }
        else
        {
          $isIf = true;
          if ($replacement != null
            && $replacement->Value == $directive->Value)
          {
            $isMatch = true;
          }
        }
      }

      if ($success)
      {
        // Process to IfEnd.
        $lineIndex++;
        for ($index = $lineIndex; $index < count($this->Lines); $index++)
        {
          $line = rtrim($this->Lines[$index]);
          if ($isMatch
            && !LJCDirective::IfElse($line, $this->CommentChars))
          {
            $this->DoOutput($replacements, $line);
          }
          if ($isIf
            && LJCDirective::IfElse($line, $this->CommentChars))
          {
            $isMatch = !$isMatch;
          }
          if (LJCDirective::IfEnd($line, $this->CommentChars))
          {
            $lineIndex = $index;
            break;
          }
        }
      }
    } // DoIf()

    // Process the RepeatItems.
    private function DoItems(LJCSection $section, int &$lineIndex) : void
    {
      $success = true;
      $items = $section->RepeatItems;

      // No Section data.
      if (!TextGenLib::HasItems($items))
      {
        $success = false;
        $lineIndex = $this->SkipSection($lineIndex);
        $lineIndex++;
      }

      if ($success)
      {
        for ($itemIndex = 0; $itemIndex < count($items); $itemIndex++)
        {
          $item = $items[$itemIndex];

          // No Replacement data.
          if (!TextGenLib::HasItems($item->Replacements))
          {
            $lineIndex = $this->SkipSection($lineIndex);

            // If not last item.
            if ($itemIndex < count($items) - 1)
            {
              // Do section again for following items.
              $lineIndex = $section->BeginLineIndex;
            }
            continue;
          }

          for ($index = $lineIndex; $index < count($this->Lines); $index++)
          {
            $line = rtrim($this->Lines[$index]);
            $lineIndex = $index;

            $directive = LJCDirective::GetDirective($line, $this->CommentChars);
            if ($directive != null)
            {
              if ($directive->IsSectionBegin())
              {
                $nextSection = $this->GetBeginSection($line);
                if (null == $nextSection)
                {
                  // No Section data.
                  $index = $this->SkipSection($lineIndex);
                  continue;
                }

                // RepeatItem processing starts with first line.
                $lineIndex++;
                $nextSection->BeginLineIndex = $lineIndex;

                $this->AddActive($item);
                $this->DoItems($nextSection, $lineIndex);
                $this->RemoveActive();

                // Continue with returned line index after the processed section.
                $index = $lineIndex;
              }

              if ($directive->IsSectionEnd())
              {
                // If not last item.
                if ($itemIndex < count($items) - 1)
                {
                  // Do section again for following items.
                  $lineIndex = $section->BeginLineIndex;
                  break;
                }
              }

              if ($directive->IsIfBegin())
              {
                $this->DoIf($directive, $item->Replacements, $lineIndex);
              }
              $index = $lineIndex;
            }

            // Does not output directives.
            $this->DoOutput($item->Replacements, $line);
          }
        }
      }
    } // DoItems()

    // If not directive, process replacements and add to output.
    private function DoOutput(LJCReplacements $replacements, string $line) : void
    {
      if (!LJCDirective::IsDirective($line, $this->CommentChars))
      {
        if ($line != null
          && strlen(trim($line)) > 0)
        {
          $this->DoReplacements($replacements, $line);
        }
        $this->AddOutput($line);
      }
    } // DoOutput()

    // Perform the line replacements.
    private function DoReplacements(LJCReplacements $replacements, string &$lineItem)
      : void
    {
      if (str_contains($lineItem, $this->PlaceholderBegin))
      {
        $line = $lineItem;

        //$pattern = "$this->PlaceholderBegin.+?$this->PlaceholderEnd";
        //$matches = Regex.Matches($line, $pattern);
        $matches = $this->Matches(trim($line));
        for ($index = 0; $index < count($matches); $index++)
        {
          $match = $matches[$index];
          $replacement = $replacements->Retrieve($match, false);
          if ($replacement != null)
          {
            $lineItem = str_replace($match, $replacement->Value, $lineItem);
          }
          else
          {
            // Replacement not found in current collection.
            // Search active replacements.
            $active = $this->ActiveReplacements;
            for ($activeIndex = count($active) - 1; $activeIndex >= 0
              ; $activeIndex--)
            {
              $replacement = $active[$activeIndex]->Retrieve($match, false);
              if ($replacement != null)
              {
                $lineItem = str_replace($match, $replacement->Value, $lineItem);
                break;
              }
            }
          }
        }
      }
    } DoReplacements()

    // 
    public function Matches(string $text) : array
    {
      $retValue = [];

      $startIndex = 0;
      while ($startIndex >= 0)
      {
        $placeholder = LJCCommon::GetDelimitedString($text, $this->PlaceholderBegin
        , $this->PlaceholderEnd);
        if (!self::HasValue($placeholder))
        {
          $startIndex = -1;
          continue;
        }

        $placeholder = "$this->PlaceholderBegin$placeholder$this->PlaceholderEnd";
        $startIndex = LJCCommon::StrPos($text, $placeholder);
        if ($startIndex >= 0)
        {
          $retValue[] = $placeholder;
          $startIndex += strlen($placeholder);
          $text = substr($text, $startIndex);
        }
      }
      return $retValue;
    } // Matches()

    // Skips to the end of the current section.
    private function SkipSection(int $lineIndex) : int
    {
      $retValue = $lineIndex;

      // Skip to end of section.
      for ($index = $lineIndex; $index < count($this->Lines); $index++)
      {
        $line = $this->Lines[$index];
        if (LJCDirective::SectionEnd($line, $this->CommentChars))
        {
          $retValue = $index++;
          break;
        }
      }
      return $retValue;
    } // SkipSection()

    // ---------------
    // Other Methods

    // Add current replacements to Active array.
    private function AddActive(LJCItem $item) : void
    {
      if (TextGenLib::HasItems($item->Replacements))
      {
        $this->ActiveReplacements[] = $item->Replacements;
        //array_push($this->ActiveReplacements, $item=>Replacements);
      }
    } // AddActive()

    // Add the line to the output.
    private function AddOutput(string $line) : void
    {
      if (strlen($this->Output) > 0)
      {
        $this->Output .= "\r\n";
      }
      $this->Output .= $line;
    } // AddOutput()

    // Gets the begin section.
    private function GetBeginSection(string $line) : LJCSection
    {
      $directive = LJCDirective::GetDirective($line, $this->CommentChars);
      $retValue = $this->Sections->Retrieve($directive->Name);
      return $retValue;
    } // GetBeginSection()

    // Sets the configuration values.
    private function IsConfig(string $line) : bool
    {
      $retValue = false;

      // Sets the configuration.
      if (LJCDirective::IsDirective($line, $this->CommentChars))
      {
        $directive = LJCDirective::GetDirective($line, $this->CommentChars);
        switch (strtolower($directive->ID))
        {
          case "#commentchars":
            $retValue = true;
            $this->CommentChars = $directive->Name;
            break;

          case "#placeholderbegin":
            $retValue = true;
            $this->PlaceholderBegin = $directive->Name;
            break;

          case "#placeholderend":
            $retValue = true;
            $this->PlaceholderEnd = $directive->Name;
            break;
        }
      }
      return $retValue;
    } // IsConfig()

    // Remove Replacements that are no longer active.
    private function RemoveActive() : void
    {
      if (TextGenLib::HasItems($this->ActiveReplacements))
      {
        array_pop($this->ActiveReplacements);
      }
    } // RemoveActive()

    // ---------------
    // private Properties

    // The current Active Sections.
    private array $ActiveReplacements;

    private array $Lines;

    private string $Output;

    private LJCSections $Sections;
  } // TextGenLib
?>