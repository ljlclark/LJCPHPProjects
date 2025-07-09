<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommentsLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/GenTextLib/LJCGenTextSectionLib.php";
  include_once "$prefix/GenDoc/DocDataLib/LJCIncludeLib.php";
  include_once "$prefix/GenDoc/DocDataLib/LJCParamCommentLib.php";
  // LJCCommonLib: LJC
  // LJCGenTextSectionLib: LJCDirective, LJCSection, LJCSections
  //   , LJCItem, LJCReplacement, LJCReplacements
  // LJCDebugLib: LJCDebug
  // LJCIncludeLib: LJCInclude
  // LJCParamCommentLib: LJCParamComment

  // Contains Classes to parse code XML comments.
  /// <include path='items/LJCCommentsLib/*' file='Doc/LJCCommentsLib.xml'/>
  /// LibName: LJCCommentsLib
  // LJCComments

  // Main Call Tree
  // LJCDocDataGenLib.php
  // LJCDocDataGen.LineProcessed()
  //   SetComment() public
  //     GetComment()
  //       LJCInclude.SetComments()
  //     SaveComment()
  
  // ***************
  // Provides methods to parse code XML comment values.
  // Methods: ClearComments(), SetComment()
  /// <include path='items/LJCComments/*' file='Doc/LJCComments.xml'/>
  class LJCComments
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("LJCCommentsLib", "LJCComments"
        , "w",  $enabled);
      $this->Debug->IncludePrivate = true;

      $this->CurrentTagName = null;
      $this->Code = null;
      $this->Groups = [];
      $this->LibName = null;
      $this->Params = null;
      $this->ParentGroup = null;
      $this->Remarks = null;
      $this->Returns = null;
      $this->Summary = null;

      $this->BeginTags = null;
      $this->CodeFileSpec = null;
      //$this->DebugWriter = null;
      $this->EndTags = null;
      $this->IncludeFile = null;
      $this->IsContinue = false;

      $this->ClearComments();
      $this->SetCommentTags();
      $this->IncludeFile = new LJCInclude();
    } // __construct()

    // ---------------
    // Public Methods - LJCComments

    /// <summary>Clears the XML comments.</summary>
    public function ClearComments(): void
    {
      $enabled = false;
      $this->Debug->BeginMethod("ClearComments", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $this->ClearComment("code");
      $this->ClearComment("group");
      $this->ClearComment("include");
      $this->ClearComment("param");
      $this->ClearComment("remarks");
      $this->ClearComment("returns");
      $this->ClearComment("summary");

      $this->Debug->EndMethod($enabled);
    } // ClearComments()

    // Sets the XML comment value.
    /// <include path='items/SetComment/*' file='Doc/LJCComments.xml'/>
    public function SetComment(string $line, ?string $codeFileSpec = null)
      : void
    {
      // LJCDocDataGen.LineProcessed()
      $enabled = false;
      $this->Debug->BeginMethod("SetComment", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      if ($codeFileSpec != null)
      {
        $this->CodeFileSpec = $codeFileSpec;
      }

      if (false == $this->IsContinue)
      {
        // New comment.
        $this->CurrentTagName = $this->GetBeginTagName($line);

        // Critical to handle multiple tags.
        if($this->CurrentTagName != "param"
          && $this->CurrentTagName != "group")
        {
          $this->ClearComment();
        }

        $comment = $this->GetComment($line);
        if ($this->CurrentTagName != null)
        {
          // Process if Include comment processing is not done.
          $this->SaveComment($comment);
          if (false == $this->HasCurrentEndTag($line))
          {
            // No end tag so start Continue comment.
            $this->IsContinue = true;
          }
        }
      }
      else
      {
        // Continue comment.
        if ($this->HasCurrentEndTag($line))
        {
          // Has end tag so end Continue comment.
          $this->IsContinue = false;
        }
        $comment = $this->GetComment($line);
        $this->SaveComment($comment);
      }

      $this->Debug->EndMethod($enabled);
    } // SetComment();

    // ---------------
    // Private Comment Methods - LJCComments

    // Clears the comments for the specified comment tag.
    private function ClearComment(?string $tagName = null): void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ClearComment", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      if (null == $tagName)
      {
        $tagName = $this->CurrentTagName;
      }

      if ($tagName != null)
      {
        $tagName = strtolower($tagName);
        switch ($tagName)
        {
          case "code":
            // Setup for multiple lines.
            $this->Code = "";
            break;

          case "group":
            // Setup for multiple lines.
            $this->Groups = [];
            break;

          case "include":
            // Setup for single value.
            $this->IncludeXMLPath = null;
            break;

          case "param":					
            // Setup for multiple lines.
            $this->Params = new LJCDocDataParams();
            break;

          case "parentgroup":
            // Setup for single value.
            $this->ParentGroup = null;
            break;

          case "remarks":
            // Setup for multiple lines.
            $this->Remarks = "";
            break;

          case "returns":
            // Setup for multiple lines.
            $this->Returns = "";
            break;

          case "summary":
            // Setup for multiple lines.
            $this->Summary = "";
            break;
        }
      }

      $this->Debug->EndMethod($enabled);
    } // ClearComment()

    // Gets the comment for the current comment tag.
    private function GetComment(string $line): ?string
    {
      // SetComment()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetComment", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      // Get using $CurrentTagName.
      $beginTag = $this->GetBeginTag();
      $endTag = $this->GetEndTag();

      $positionBegin = LJC::StrPos($line, $beginTag);
      if ($positionBegin < 0)
      {
        // No BeginTag so set parse for start of comment.
        $beginTag = "///";
      }

      $isSimpleComment = true;
      if ("<include" == $beginTag)
      {
        $isSimpleComment = false;
        $this->IncludeFile->SetComments($line, $this->CodeFileSpec);

        // Process the include comment lines through SetComment().
        foreach ($this->IncludeFile->Comments as $comment)
        {
          $this->SetComment($comment);
        }

        // Remove extra line from code.
        $this->Code = rtrim($this->Code);

        // Indicate that Include comment processing is done.
        $this->CurrentTagName = null;
      }

      // Save group item.
      if ("<group" == $beginTag)
      {
        $isSimpleComment = false;
        $paramComment = new LJCParamComment();
        $param = $paramComment->GetParam($line);
        $this->Groups[$param->Name] = $param->Summary;
      }

      // Save param item.
      if ("<param" == $beginTag)
      {
        $isSimpleComment = false;
        $paramComment = new LJCParamComment();
        $param = $paramComment->GetParam($line);
        $this->Params->AddObject($param);
      }

      if ($isSimpleComment)
      {
        $rTrim = true;
        if (false == $this->HasCurrentEndTag($line))
        {
          // No EndTag so do not remove cr/lf.
          $rTrim = false;
        }

        // Get to end of string if endTag is null.
        $retValue = LJC::GetDelimitedString($line, $beginTag, $endTag
          , false, $rTrim);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // GetComment();

    // Saves the comment for the current comment tag.
    private function SaveComment(?string $comment): void
    {
      // SetComment()
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SaveComment", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      switch ($this->CurrentTagName)
      {
        case "code":
          if ($this->Code != "")
          {
            $this->Code .= "\r\n";
          }
          $this->Code .= htmlspecialchars($comment);
          break;

        case "parentgroup":
          $this->ParentGroup = htmlspecialchars($comment);
          break;

        case "remarks":
          $this->Remarks .= htmlspecialchars($comment);
          break;

        case "returns":
          $this->Returns .= htmlspecialchars($comment);
          break;

        case "summary":
          $this->Summary .= htmlspecialchars($comment);
          break;
      }

      $this->Debug->EndMethod($enabled);
    } // SaveComment()

    // ---------------
    // Private Tag Methods - LJCComments

    // Returns the current or other BeginTag found in the line.
    private function GetBeginTagName(string $line): ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetBeginTagName", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;
      
      if ($this->CurrentTagName != null)
      {
        $retValue = $this->CurrentTagName;
      }

      // Get XML Comment tag name.
      // beginTagName = "group", beginTag = "<group>"
      foreach ($this->BeginTags as $beginTagName => $beginTag)
      {
        $checkLine = strtolower($line);
        if (LJC::StrPos($checkLine, $beginTag) >= 0)
        {
          $retValue = $beginTagName;
          break;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetBeginTagName()

    // Gets the BeginTag for the specified comment tag.
    private function GetBeginTag(?string $tagName = null): ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetBeginTag", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      if (null == $tagName)
      {
        $tagName = $this->CurrentTagName;
      }

      if (array_key_exists($tagName, $this->BeginTags))
      {
        $retValue = $this->BeginTags[$tagName];
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetBeginTag()

    // Gets the EndTag for the specified or current comment tag.
    private function GetEndTag(?string $tagName = null): ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetEndTag", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      if (null == $tagName)
      {
        $tagName = $this->CurrentTagName;
      }

      if (array_key_exists($tagName, $this->EndTags))
      {
        $retValue = $this->EndTags[$tagName];
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetEndTag()

    // Gets the BeginTag length for the specified or current comment tag.
    private function GetLengthBeginTag(?string $tagName = null): int
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetLengthBeginTag", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = 0;

      if (null == $tagName)
      {
        $tagName = $this->CurrentTagName;
      }

      $value = $this->BeginTags[$this->CurrentTagName];
      if ($value != null)
      {
        $retValue = strlen($value);
      }

      $this->Debug->EndMethohd($enabled);
      return $retValue;
    } // GetLengthBeginTag()

    // Gets the EndTag length for the specified or current comment tag.
    private function GetLengthEndTag(?string $tagName = null): int
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetLengthEndTag", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = 0;

      if (null == $tagName)
      {
        $tagName = $this->CurrentTagName;
      }

      if (array_key_exists($this->CurrentTagName, $this->EndTags))
      {
        $value = $this->EndTags[$this->CurrentTagName];
        if ($value != null)
        {
          $retValue = strlen($value);
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetLengthEndTag()

    // Indicates if the lines has a current EndTag.
    private function HasCurrentEndTag(string $line): bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("HasCurrentEndTag", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = false;

      $endTag = $this->GetEndTag();
      if ($endTag != null
        && LJC::StrRPos($line, $endTag) >= 0)
      {
        $retValue = true;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // HasCurrentEndTag()

    // Sets the comment tag values
    private function SetCommentTags(): void
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SetCommentTags", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      // tagName = "code", tag = "<code>"
      $this->BeginTags["code"] = "<code>";
      $this->BeginTags["group"] = "<group";
      $this->BeginTags["include"] = "<include";
      $this->BeginTags["parentgroup"] = "<parentgroup>";
      $this->BeginTags["param"] = "<param";
      $this->BeginTags["remarks"] = "<remarks>";
      $this->BeginTags["returns"] = "<returns>";
      $this->BeginTags["summary"] = "<summary>";

      $this->EndTags["code"] = "</code>";
      $this->EndTags["group"] = "</group>";
      $this->EndTags["parentgroup"] = "</parentgroup>";
      $this->EndTags["param"] = "</param>";
      $this->EndTags["remarks"] = "</remarks>";
      $this->EndTags["returns"] = "</returns>";
      $this->EndTags["summary"] = "</summary>";

      $this->Debug->EndMethod($enabled);
    } // GetCommentTag()

    // ---------------
    // Private Output Methods - LJCComments

    // Writes an output line.
    private function Output($text = null, $value = null): void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("Output", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);

      $lib = "";
      //$lib = "LJCCommonLib";
      if ("" == $lib
        ||$lib == $this->LibName
        || $lib == $this->IncludeFile->LibName)
      {
        LJCWriter::Write($text);
        if ($value != null)
        {
          LJCWriter::Write(":\r\n|$value|");
        }
        LJCWriter::WriteLine("");
      }

      $this->Debug->EndMethod($enabled);
    } // Output()

    // ---------------
    // Public Properties - LJCComments

    /// <summary>The example Code.</summary>
    public ?string $Code;

    /// <summary>The current tag name.</summary>
    public ?string $CurrentTagName;

    /// <summary>The groups.</summary>
    public ?array $Groups;

    /// <summary>The Param comments.</summary>
    public ?LJCDocDataParams $Params;

    /// <summary>The Parent group name.</summary>
    public ?string $ParentGroup;

    /// <summary>The Remark comment.</summary>
    public ?string $Remarks;

    /// <summary>The Returns comment.</summary>
    public ?string $Returns;

    /// <summary>The Summary comment.</summary>
    public ?string $Summary;

    // ---------------
    // Private Properties - LJCComments

    // The Begin comment tags.
    private ?array $BeginTags;

    // The Code File specification.
    private ?string $CodeFileSpec;

    /// <summary>The Code File base (Library) name.</summary>
    public ?string $LibName;

    // The End comment tags.
    private ?array $EndTags;

    // The IncludeFile object.
    private ?LJCInclude $IncludeFile;

    // The IsContinue flag.
    private bool $IsContinue;
  } // LJCComments
?>
