<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCGenTextLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  include_once "$prefix/GenTextLib/LJCGenTextSectionLib.php";
  // LJCCommonLib: LJCCommon
  // LJCTextLib: LJCStringBuilder
  // LJCDebugLib: LJCDebug

  // The utility to generate text from a template and custom GenData.
  /// <include path='items/LJCGenTextLib/*' file='Doc/LJCGenTextLib.xml'/>
  /// LibName: LJCGenTextLib
  // LJCGenText

  // ***************
  // The GenText text generator class.
  // Public: ProcessTemplate()
  // Private: ManageSections(), ProcessIfDirectives()
  //   , ProcessReplacements(), ProcessSection()
  /// <summary>The GenText text generator class.</summary>
  /// <remarks>Main Function: ProcessTemplate()</remarks>
  class LJCGenText
  {
    /// <summary>Initializes an object instance.</summary>
    /// <param name="$debugFileSuffix">The data file specification.</param>
    public function __construct(?string $debugFileSuffix = "GenData")
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCGenTextLib", "LJCGenText"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->ActiveSections = [];
      $this->CurrentSection = null;
    }  // construct()

    // ---------------
    // Public Methods

    // Create the items in group order.
    public function OrderGroupItems(LJCItems $items, $groups)
    {
      $retItems = $items->Clone();

      $items = $items->Clone();
      $groups = $this->CurrentSection->Groups;
      if (LJCCommon::HasElements($groups))
      {
        $orderedItems = new LJCItems();

        // Add grouped items.
        $groupCount = count($groups);
        for ($groupIndex = 0; $groupIndex < $groupCount; $groupIndex++)
        {
          $group = $groups[$groupIndex];
          do
          {
            // Find by item->MemberGroup.
            $item = LJCItems::FindGroupItem($items, $group);
            if ($item != null)
            {
              $orderedItems->Add($item);
              $items->Remove($item->Name);
            }
          } while ($item != null);
        }

        // Add remaining ungrouped items.
        while ($items->Count() > 0)
        {
          $item = $items->Item(0);
          $orderedItems->Add($item);
          $count = strval($items->Count());
          $items->Remove($item->Name);
        }
        $retItems = $orderedItems->Clone();
      }
      return $retItems;
    } // OrderGroupItems

    // Processes the Template and Data to produce the output file.
    /// <include path='items/ProcessTemplate/*' file='Doc/LJCGenText.xml'/>
    public function ProcessTemplate(string $templateFileSpec
      , LJCSections $sections) : ?string
    {
      $enabled = false;
      $this->Debug->BeginMethod("ProcessTemplate", $enabled);
      $retValue = null;

      // Instantiate properties with Pascal case.
      $this->Sections = $sections;
      $this->CurrentSection = null;
      $this->ActiveSections = [];
      $builder = new LJCStringBuilder();

      $this->Stream = fopen($templateFileSpec, "r+");
      while(false == feof($this->Stream))
      {
        $this->Line = (string)fgets($this->Stream);
        if (null == trim($this->Line))
        {
          continue;
        }

        // Adds or removes an Active Section.
        // Sets $this->CurrentSection.
        // The Line is set to null if it is a Directive.
        $directive = $this->ManageSections(0, 0);
        if (null == $this->Line)
        {
          continue;
        }

        // Has ActiveSections and Line contains a potential Replacement.
        $writeLine = true;
        if (count($this->ActiveSections) > 0
          && LJCCommon::StrPos($this->Line, "_") >= 0)
        {
          $writeLine = false;
          $lines = $this->ProcessSection();
          $builder->Text($lines);
        }
        if ($writeLine)
        {
          $builder->Text($this->Line);
        }
      }
      fclose($this->Stream);
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // ProcessTemplate()

    // ---------------
    // Private Methods

    // Adds or removes an Active Section.
    // <include path='items/ManageSections/*' file='Doc/LJCGenText.xml'/>
    private function ManageSections(int $prevLineBegin, int $itemIndex)
      : ?LJCDirective
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ManageSections", $enabled);
      $retValue = null;

      if (null == $this->Line)
      {
        return $retValue;				
      }

      $retValue = LJCDirective::GetDirective($this->Line, "<!--");
      if ($retValue != null)
      {
        switch (strtolower($retValue->Type))
        {
          case "#sectionbegin":
            $section = $this->Sections->Retrieve($retValue->Name, false);
            if ($section != null)
            {
              // Set CurrentSection if Section Data exists.
              $this->CurrentSection = $section;

              if (count($this->CurrentSection->RepeatItems) > 1
                && null == $this->CurrentSection->Begin)
              {
                $this->CurrentSection->Begin = $prevLineBegin;
              }

              // Push active section.
              $this->ActiveSections[] = $this->CurrentSection;
            }
            $this->Line = null;
            break;

          case "#sectionend":
            $activeSectionsCount = count($this->ActiveSections);
            if ($activeSectionsCount > 0)
            {
              $section = $this->Sections->Retrieve($retValue->Name, false);							
              if ($section != null)
              {
                // Only pop active section if there is Section data
                // and if there are no more items.
                $count = count($this->CurrentSection->RepeatItems);
                if ($itemIndex >= $count - 1)
                {
                  $section = array_pop($this->ActiveSections);

                  $activeSectionsCount = count($this->ActiveSections);
                  $this->CurrentSection->Begin = null;
                  $this->CurrentSection = null;
                  if ($activeSectionsCount > 0)
                  {
                    $this->CurrentSection = $this->ActiveSections[$activeSectionsCount - 1];
                  }
                }
              }
            }
            $this->Line = null;
            break;

          case "#ifbegin":
          case "#ifelse":
          case "#ifend":
          case "#value":
            $this->Line = null;
            break;
        }
      } // if ($retValue != null)

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // ManageSections()

    // Processes the If directives.
    private function ProcessIfDirectives(LJCDirective $directive
      , string $saveLine) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessIfDirectives", $enabled);
      $retValue = $this->DoOutput;

      switch (strtolower($directive->Type))
      {
        case "#ifbegin":
          $this->IfOperation = "else";
          if ($directive->Value != null)
          {
            $ifOperation = "if";
            $retValue = true;
            $doElse = false;

            $value = null;
            $replacement = $this->GetReplacement($saveLine, $directive->Name);
            if ($replacement != null)
            {
              $name = $replacement->Name;
              $value = $replacement->Value;
            }

            if ("notnull" == strtolower($directive->Value))
            {
              if (null == $value)
              {
                $doElse = true;
              }
            }
            else
            {
              if (null == $replacement
                || $directive->Value != $value)
              {
                $doElse = true;
              }
            }
            if ($doElse)
            {
              $this->IfOperation = "else";
              $retValue = false;
            }
          }
          break;

        case "#ifelse":
          $retValue = false;
          if ("else" == $this->IfOperation)
          {
            $retValue = true;
          }
          break;

        case "#ifend":
          $this->IfOperation = null;
          $retValue = true;
          break;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // ProcessDirective()

    // Processes the Replacement items.
    private function ProcessReplacements() : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessReplacements", $enabled);

      // Start with most recent.
      $outerBreak = false;
      $count = count($this->ActiveSections);
      for ($index = $count - 1; $index >= 0; $index--)
      {
        $activeSection = $this->ActiveSections[$index];
        if (isset($activeSection->CurrentItem))
        {
          $item = $activeSection->CurrentItem;
          $replacements = $item->Replacements;
          foreach ($replacements as $replacement)
          {
            $position = LJCCommon::StrPos($this->Line, $replacement->Name);
            if ($position >= 0)
            { 
              $this->Line = str_replace($replacement->Name, $replacement->Value
                , $this->Line);
            }
            if (-1 == LJCCommon::StrPos($this->Line, "_"))
            {
              $outerBreak = true;
              break;
            }
          }
          if ($outerBreak)
          {
            break;
          }
        }
      }

      $this->Debug->EndMethod($enabled);
    }  // ProcessReplacements()

    // Processes the current Section.
    private function ProcessSection() : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ProcessSection", $enabled);
      $retValue = null;

      if (null == $this->CurrentSection)
      {
        return null;
      }

      $builder = new LJCStringBuilder();

      $items = $this->CurrentSection->RepeatItems;
      // *** Begin *** Add
      $groups = $this->CurrentSection->Groups;
      $orderedItems = $this->OrderGroupItems($items, $groups);
      $prevMemberGroup = "";
      // *** End   *** Add

      // Process Items
      $getLine = false;
      $prevLineBegin =  0;
      $itemCount = count($orderedItems);
      for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++)
      {
        $item = $orderedItems->Item($itemIndex);
        $this->CurrentSection->CurrentItem = $item;

        $this->IfOperation = null;
        $this->DoOutput = true;
        while (true)
        {
          if ($getLine)
          {
            $this->Line = fgets($this->Stream);
            $saveLine = $this->Line;

            // If the line is a Directive, it will be set to null.
            // Adds or pops Active Section, sets the Current Section and Begin position.
            $directive = $this->ManageSections($prevLineBegin, $itemIndex);
            $prevLineBegin = ftell($this->Stream);
            if ($directive != null)
            {
              if ("#sectionbegin" == strtolower($directive->Type))
              {
                // Recursive processing of nested section.
                $lines = $this->ProcessSection();
                $builder->Text($lines);
                $prevLineBegin = ftell($this->Stream);
                continue;
              }

              $this->DoOutput = $this->ProcessIfDirectives($directive, $saveLine);

              // Set to beginning of Current Section if Section End
              // and more Items.
              if ($this->ResetPosition($directive, $itemIndex))
              {
                break;
              }
            }
          }
          $getLine = true;

          if ($this->DoOutput
            && $this->Line != null)
          {
            if (LJCCommon::StrPos($this->Line, "_") >= 0)
            {
              $this->ProcessReplacements($item);
            }

            // *** Begin ***
            // Write group heading.
            $name = $this->CurrentSection->Name;
            if (($name == "Class"
              || $name == "Function")
              && $item->MemberGroup != $prevMemberGroup)
            {
              $text = $this->GroupHeading($item->MemberGroup);
              $builder->Text($text);
            }
            $prevMemberGroup = $item->MemberGroup;
            // *** End   ***

            $builder->Text($this->Line);
          }
        }  // while(true)
      }
      $retValue = $builder->ToString();

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // ProcessSection()

    // Retrieves the Replacement object.
    private function GetReplacement(string $line, string $replacementName)
      : ?LJCReplacement
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetReplacement", $enabled);
      $retValue = null;

      // Start with most recent.
      $outerBreak = false;
      $count = count($this->ActiveSections);
      for ($index = $count - 1; $index >= 0; $index--)
      {
        $activeSection = $this->ActiveSections[$index];
        if (isset($activeSection->CurrentItem))
        {
          $replacements = $activeSection->CurrentItem->Replacements;
          foreach ($replacements as $replacement)
          {
            $position = LJCCommon::StrPos($line, $replacement->Name);
            if ($position >= 0)
            { 
              $retValue = $replacement;
              $outerBreak = true;
              break;
            }
          }
          if ($outerBreak)
          {
            break;
          }
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // GetReplacement()

    // Creates the Group heading.
    private function GroupHeading(string $heading)
    {
      $retHeading = "";

      $textState = new LJCTextState();
      $textState->IndentCount = 3;
      $hb = new LJCHTMLBuilder($textState);
      $hb->End("table", $textState);
      $attribs = $hb->Attribs("Title2");
      $hb->Create("div", $textState, $heading, $attribs);
      $attribs = $hb->Attribs("ListTable");
      $hb->Begin("table", $textState, $attribs);
      $hb->AddLine();
      $retHeading = $hb->ToString();
      return $retHeading;
    }

    // Resets the Stream position to the beginning of the Section.
    private function ResetPosition(LJCDirective $directive, int $itemIndex) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ResetPosition", $enabled);
      $retValue = false;

      if ($directive != null && "#sectionend" == strtolower($directive->Type))
      {
        $retValue = true;

        // Only reset position if there are more items.
        if ($this->CurrentSection != null)
        {
          $count = count($this->CurrentSection->RepeatItems);
          if ($itemIndex < $count - 1)
          {
            $begin = $this->CurrentSection->Begin;
            fseek($this->Stream, $begin, SEEK_SET);
            $this->Line = fgets($this->Stream);
          }
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // ---------------
    // Private Write Helper Methods

    // Indicates if it is a SectionBegin or SectionEnd directive.
    private function IsBeginOrEnd(?LJCDirective $directive) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("IsBeginOrEnd", $enabled);
      $retValue = false;

      if ($directive != null)
      {
        $directiveType = strtolower($directive->Type);
        if ("#sectionbegin" == $directiveType
          || "#sectionend" == $directiveType)
        {
          $retValue = true;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }

    // ---------------
    // Properties

    // The current Active Sections.
    private array $ActiveSections;

    // Indicates if the line should be output.
    private bool $DoOutput;

    // The current Section.
    private ?LJCSection $CurrentSection;

    // The current If operation.
    private ?string $IfOperation;

    // The current Line.
    private ?string $Line;

    // The Data Sections.
    private LJCSections $Sections;

    // The File Stream.
    private $Stream;
  }
?>
