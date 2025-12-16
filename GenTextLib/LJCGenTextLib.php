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
  // LJCCommonLib: LJC
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
      $enabled = false;
      $this->Debug = new LJCDebug("LJCGenTextLib", "LJCGenText"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;

      $this->Groups = [];
      $this->ActiveSections = [];
      $this->CurrentSection = null;
    }  // construct()

    // ---------------
    // Public Methods

    // Create the items in group order.
    public function OrderGroupItems(LJCItems $items, $groups)
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("OrderGroupItems", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retItems = $items->Clone();

      $itemPool = $items->Clone();
      $groups = $this->CurrentSection->Groups;
      if (LJC::HasElements($groups))
      {
        $orderedItems = new LJCItems();

        // Add grouped items.
        foreach ($groups as $key => $value)
        {
          do
          {
            // Find by item->ParentGroup
            $item = LJCItems::FindGroupItem($itemPool, $key);
            if ($item !=null)
            {
              $orderedItems->Add($item);
              $itemPool->Remove($item->Name);
            }
          } while ($item != null);
        }

        // Add remaining ungrouped items.
        while ($itemPool->Count() > 0)
        {
          $item = $itemPool->Item(0);
          $orderedItems->Add($item);
          $itemPool->Remove($item->Name);
        }
        $retItems = $orderedItems->Clone();
      }

      $this->Debug->EndMethod($enabled);
      return $retItems;
    } // OrderGroupItems

    // Processes the Template and Data to produce the output file.
    /// <include path='items/ProcessTemplate/*' file='Doc/LJCGenText.xml'/>
    public function ProcessTemplate(string $templateFileSpec
      , LJCSections $sections) : ?string
    {
      $enabled = false;
      $this->Debug->BeginMethod("ProcessTemplate", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
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
          && LJC::StrPos($this->Line, "_") >= 0)
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
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
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
              $name = $this->CurrentSection->Name;
              $this->SectionName = $name;

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
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
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
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

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
            $position = LJC::StrPos($this->Line, $replacement->Name);
            if ($position >= 0)
            { 
              $this->Line = str_replace($replacement->Name, $replacement->Value
                , $this->Line);
            }
            if (-1 == LJC::StrPos($this->Line, "_"))
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
      // ProcessTemplate()
      // ProcessSection()
      $enabled = true;
      $this->Debug->BeginPrivateMethod("ProcessSection", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      if (null == $this->CurrentSection)
      {
        return null;
      }

      $builder = new LJCStringBuilder();

      $items = $this->CurrentSection->RepeatItems;
      $orderedItems = $items;
      $sectionName = $this->CurrentSection->Name;
      $prevParentGroup = "";
      if ($sectionName == "Class")
      {
        $this->Groups = $this->CurrentSection->Groups;
        $this->HasGroups = false;
        if ($this->Groups != null)
        {
          $this->HasGroups = true;
        }
      }
      if ($sectionName == "Function")
      {
        if ($this->Groups != null)
        {
          $orderedItems = $this->OrderGroupItems($items, $this->Groups);
        }
        $prevParentGroup = "";
      }

      // Process Items
      $getLine = false;
      $prevLineBegin =  0;
      //$itemCount = $items->count();
      $itemCount = $orderedItems->count();
      for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++)
      {
        //$item = $items->Item($itemIndex);
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
            if (LJC::StrPos($this->Line, "_") >= 0)
            {
              $this->ProcessReplacements($item);
            }

            // Write group heading.
            $name = $this->CurrentSection->Name;
            if (($name == "Class"
              || $name == "Function")
              && $item->ParentGroup != $prevParentGroup)
            {
              $heading = "";
              foreach ($this->Groups as $key => $value)
              {
                if ($key == $item->ParentGroup)
                {
                  $heading = $value;
                  break;
                }
              }
              if (LJC::HasValue($heading))
              {
                $text = $this->GroupHeading($heading);
                $builder->Text($text);
              }
              $prevParentGroup = $item->ParentGroup;
            }

            $builder->Text($this->Line);
          }
        }
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
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
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
            $position = LJC::StrPos($line, $replacement->Name);
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
      $textState->setIndentCount(3);
      $hb = new LJCTextBuilder($textState);
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
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
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
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
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
