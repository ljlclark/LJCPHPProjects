<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCommentsLib.php
  declare(strict_types=1);
  // Must refer to exact same file everywhere in codeline.
  // Path: LJCPHPProjectsDev/GenDoc/DocDataLib
  include_once "../../LJCPHPCommon/LJCCommonLib.php";
  include_once "../../GenTextLib/LJCGenTextSectionLib.php";
  include_once "LJCDebugLib.php";
  include_once "LJCIncludeLib.php";
  include_once "LJCParamCommentLib.php";
  // LJCCommonLib: LJCCommon
  // LJCGenTextSectionLib: LJCDirective, LJCSection, LJCSections
  //   , LJCItem, LJCReplacement, LJCReplacements
  // LJCDebugLib: LJCDebug
  // LJCIncludeLib: LJCInclude
  // LJCParamCommentLib: LJCParamComment

  // Contains Classes to parse code XML comments.
  /// <include path='items/LJCCommentsLib/*' file='Doc/LJCCommentsLib.xml'/>
  /// LibName: LJCCommentsLib
  // LJCComments

  // Calling Code
  // LJCDocDataGenLib.php

  // Main Call Tree
  // SetComment() public
  //   GetComment()
  //     LJCInclude.SetComments()
  //   SaveComment()
  
  // ***************
  // Provides methods to parse code XML comment values.
  // Public: ClearComments(), SetComment()
  /// <include path='items/LJCComments/*' file='Doc/LJCComments.xml'/>
  class LJCComments
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCCommentsLib", "LJCComments"
        , "w",  true);
      $this->Debug->IncludePrivate = true;

      $this->CurrentTagName = null;
      $this->Code = null;
      $this->LibName = null;
      $this->Params = null;
      $this->Remarks = null;
      $this->Returns = null;
      $this->Summary = null;

      $this->BeginTags = null;
      $this->CodeFileSpec = null;
      $this->DebugWriter = null;
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
    public function ClearComments() : void
    {
      $enabled = false;
      $this->Debug->BeginMethod("ClearComments", $enabled);

      $this->ClearComment("code");
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
      $enabled = false;
      $this->Debug->BeginMethod("SetComment", $enabled);

      if ($codeFileSpec != null)
      {
        $this->CodeFileSpec = $codeFileSpec;
      }

      if (false == $this->IsContinue)
      {
        // New comment.
        $this->CurrentTagName = $this->GetBeginTagName($line);

        // Critical to handle multiple params.
        if($this->CurrentTagName != "param")
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
    private function ClearComment(?string $tagName = null) : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("ClearComment", $enabled);

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
            $this->Code = "";
            break;

          case "include":
            $this->IncludeXMLPath = null;
            break;

          case "param":					
            $this->Params = new LJCDocDataParams();
            break;

          case "remarks":
            $this->Remarks = "";
            break;

          case "returns":
            $this->Returns = "";
            break;

          case "summary":
            $this->Summary = "";
            break;
        }
      }

      $this->Debug->EndMethod($enabled);
    } // ClearComment()

    // Gets the comment for the current comment tag.
    private function GetComment(string $line) : ?string
    {
      $enabled = true;
      $this->Debug->BeginPrivateMethod("GetComment", $enabled);
      $retValue = null;

      // Get using $CurrentTagName.
      $beginTag = $this->GetBeginTag();
      $endTag = $this->GetEndTag();

      $positionBegin = LJCCommon::StrPos($line, $beginTag);
      if ($positionBegin < 0)
      {
        // No BeginTag so set for start of comment.
        $beginTag = "///";
      }

      $isSimpleComment = true;
      if ("<include" == $beginTag)
      {
        $isSimpleComment = false;
        $this->IncludeFile->SetComments($line, $this->CodeFileSpec);
        // *****
        $this->Debug->Write(__LINE__." CodeFileSpec = $this->CodeFileSpec");

        // Process the include comment lines through SetComment().
        foreach ($this->IncludeFile->Comments as $comment)
        {
          // *****
          $this->Debug->Write(__LINE__." <include comment = $comment");
          $this->SetComment($comment);
        }

        // Remove extra line from code.
        $this->Code = rtrim($this->Code);

        // Indicate that Include comment processing is done.
        $this->CurrentTagName = null;
      }

      if ("<param" == $beginTag)
      {
        $isSimpleComment = false;
        $paramComment = new LJCParamComment();
        // *****
        foreach ($this->Params as $temp)
        {
          $this->Debug->Write(__LINE__." param.Name = $temp->Name");
        }
        $param = $paramComment->GetParam($line);
        // *****
        $this->Debug->Write(__LINE__." line = $line");
        $this->Debug->Write(__LINE__." param.Name = $param->Name");
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
        $retValue = LJCCommon::GetDelimitedString($line, $beginTag, $endTag
          , false, $rTrim);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    }  // GetComment();

    // Saves the comment for the current comment tag.
    private function SaveComment(?string $comment) : void
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SaveComment", $enabled);

      switch ($this->CurrentTagName)
      {
        case "code":
          if ($this->Code != "")
          {
            $this->Code .= "\r\n";
          }
          $this->Code .= htmlspecialchars($comment);
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
    private function GetBeginTagName(string $line) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetBeginTagName", $enabled);
      $retValue = null;
      
      if ($this->CurrentTagName != null)
      {
        $retValue = $this->CurrentTagName;
      }

      // Get XML Comment tag name.
      foreach ($this->BeginTags as $beginTagName => $beginTag)
      {
        if (LJCCommon::StrPos($line, $beginTag) >= 0)
        {
          $retValue = $beginTagName;
          break;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetBeginTagName()

    // Gets the BeginTag for the specified comment tag.
    private function GetBeginTag(?string $tagName = null) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetBeginTag", $enabled);
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
    private function GetEndTag(?string $tagName = null) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetEndTag", $enabled);
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
    private function GetLengthBeginTag(?string $tagName = null) : int
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetLengthBeginTag", $enabled);
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
    private function GetLengthEndTag(?string $tagName = null) : int
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetLengthEndTag", $enabled);
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
    private function HasCurrentEndTag(string $line) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("HasCurrentEndTag", $enabled);
      $retValue = false;

      $endTag = $this->GetEndTag();
      if ($endTag != null
        && LJCCommon::StrRPos($line, $endTag) >= 0)
      {
        $retValue = true;
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // HasCurrentEndTag()

    // Sets the comment tag values
    private function SetCommentTags() : void
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SetCommentTags", $enabled);

      $this->BeginTags["code"] = "<code>";
      $this->BeginTags["include"] = "<include";
      $this->BeginTags["param"] = "<param";
      $this->BeginTags["remarks"] = "<remarks>";
      $this->BeginTags["returns"] = "<returns>";
      $this->BeginTags["summary"] = "<summary>";
      $this->EndTags["code"] = "</code>";
      $this->EndTags["param"] = "</param>";
      $this->EndTags["remarks"] = "</remarks>";
      $this->EndTags["returns"] = "</returns>";
      $this->EndTags["summary"] = "</summary>";

      $this->Debug->EndMethod($enabled);
    } // GetCommentTag()

    // ---------------
    // Private Output Methods - LJCComments

    // Writes an output line.
    private function Output($text = null, $value = null)
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("Output", $enabled);

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

    /// <summary>The Param comments.</summary>
    //public ?array $Params;
    public ?LJCDocDataParams $Params;

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
