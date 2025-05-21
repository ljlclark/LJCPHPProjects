<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCIncludeLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  include_once "$prefix/GenTextLib/LJCGenTextSectionLib.php";
  // LJCCommonLib: LJCCommon
  // LJCTextLib: LJCWriter
  // LJCGenTextSectionLib: LJCDirective, LJCSection, LJCSections, LJCItem
  //   , LJCReplacement, LJCReplacements
  // LJCDebugLib: LJCDebug

  // #01 Correct XMLFileName - 5/4/25

  // Contains Classes to retrieve data from include XML files.
  /// <include path='items/LJCIncludeLib/*' file='Doc/LJCIncludeLib.xml'/>
  /// LibName: LJCIncludeLib
  //  LJCInclude

  // Main Call Tree
  // SetComments() public
  //   SetIncludeValues()
  //   GetComment()

  // ***************
  // Retrieves the Include file XML comment values.
  // Public: SetComments()
  /// <include path='items/LJCInclude/*' file='Doc/LJCInclude.xml'/>
  class LJCInclude
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCIncludeLib", "LJCInclude"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->Comments = null;
      $this->LibName = null;
      $this->XMLFile = null;
      $this->CurrentTagName = null;
    } // __construct()

    // ---------------
    // Public Methods - LJCInclude

    // Sets the XML comments from the specified include file.
    /// <include path='items/SetComments/*' file='Doc/LJCInclude.xml'/>
    public function SetComments(string $includeLine, string $codeFileSpec)
      : void
    {
      $enabled = false;
      $this->Debug->BeginMethod("SetComments", $enabled);

      // Sets LibName, XMLFile and itemTag.
      if ($this->SetIncludeValues($includeLine, $codeFileSpec, $itemTag))
      {
        $isItem = false;
        $isContinue = false;
        $stream = fopen($this->XMLFile, "r");

        // Potentially get multiple comment lines.
        while (false == feof($stream))
        {
          $line = (string)fgets($stream);
          $trimLine = trim($line);

          // Do not start processing until the itemTag is found.
          if (LJCCommon::StrPos($trimLine, "<$itemTag>") >= 0)
          {
            $isItem = true;
          }
          else
          {
            if ($isItem)
            {
              $endTag = $this->GetLineEndTag($trimLine);
              if (false === $isContinue)
              {
                // New comment.
                $this->CurrentTagName = $this->GetLineBeginTag($line);
                $comment = $this->GetComment($line);
                if ($this->InvalidCommentEndTag($trimLine))
                {
                  fclose($stream);
                  break;
                }
                if (null === $endTag)
                {
                  // No end tag so start Continue comment.
                  $isContinue = true;
                }
              }
              else
              {
                // Continue comment.
                if ($endTag)
                {
                  // Has end tag so end Continue comment.
                  $isContinue = false;
                }
                $comment = $this->GetComment($line);
                if ($this->InvalidCommentEndTag($comment))
                {
                  fclose($stream);
                  break;
                }
              }
            }
          }
        }
      }

      $this->Debug->EndMethod($enabled);
    } // SetComments();

    // ---------------
    // Private Methods - LJCInclude

    // Gets the comment for the specified code line.
    private function GetComment(string $line) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetComment", $enabled);
      $retValue = null;

      $beginTag = $this->GetLineBeginTag($line);
      $endTag = $this->GetLineEndTag($line);

      if (null == $beginTag)
      {
        // No BeginTag so set tag for start of comment.
        $line = "/$line";
        $beginTag = "/";
      }

      $rTrim = false;
      if ("<code>" == $this->CurrentTagName
        || $endTag != null)
      {
        // Is Code or Has EndTag then remove cr/lf.
        $rTrim = true;
      }
      $comment = LJCCommon::GetDelimitedString($line, $beginTag, $endTag
        , false, $rTrim);

      $success = true;
      if ($this->InvalidCommentEndTag($comment))
      {
        $success = false;
      }

      if ($success)
      {
        if ($beginTag == "/")
        {
          $beginTag = null;
        }

        // Build an XML Comment.
        $retValue = "/// ";
        if ($beginTag != null)
        {
          $retValue .= $beginTag;
        }
        $retValue .= $comment;
        if ($endTag != null)
        {
          $retValue .= $endTag;
        }
      }

      if ($retValue != null)
      {
        // Left Trim and Save potentially partial comment.
        $retValue = $this->LTrimXMLComment($retValue);
        if (false === $this->InvalidCommentEndTag($retValue))
        {
          $this->Comments[] = $retValue;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetComment()

    // Gets the begin tag.
    private  function GetLineBeginTag(string $line) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetLineBeginTag", $enabled);
      $retValue = null;

      $beginTag = LJCCommon::GetDelimitedString($line, "<", ">");
      if ($this->IsCommentTag($beginTag))
      {
        $retValue = "<$beginTag>";								
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetLineBeginTag()

    // Gets the end tag.
    private function GetLineEndTag(string $line) : ?string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetLineEndTag", $enabled);
      $retValue = null;

      $endTag = LJCCommon::GetDelimitedString($line, "</", ">");
      if ($this->IsCommentTag($endTag))
      {
        $retValue = "</$endTag>";								
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetLineEndTag()

    // Checks for an invalid end comment tag.
    private function InvalidCommentEndTag(?string $comment) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("InvalidCommentEndTag", $enabled);
      $retValue = false;

      if ($comment != null)
      {
        $endTag = LJCCommon::GetDelimitedString($comment, "</", ">");

        if ($endTag != null
          && false === $this->IsCommentTag($endTag))
        {
          // End tag was found and not a comment tag.
          $retValue = true;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // InvalidCommentEndTag()

    // Checks for a valid comment tag.
    private function IsCommentTag(?string $tag) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("IsCommentTag", $enabled);
      $retValue = false;

      if ($tag != null)
      {
        $tag = strtolower($tag);
        switch ($tag)
        {
          case "code":
          case "param":
          case "remarks":
          case "returns":
          case "summary":
            $retValue = true;
            break;
        }
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // IsCommentTag()

    // Replaces tabs with spaces and removes extra leading spaces
    private function LTrimXMLComment(string $comment) : string
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("LTrimXMLComment", $enabled);

      // Convert comment tabs to spaces.
      $retValue = str_replace("\t", "  ", $comment);

      // Start after /// and get count chars.
      $count = 6;
      $check = substr($retValue, 3, $count);

      // If at least count spaces, left trim the count spaces.
      if ($check == "      ")
      {
        $retValue = "///" . substr($retValue, $count + 3);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // LTrimXMLComment()

    // Sets the Class include file values: LibName, XMLFile.
    private function SetIncludeValues(string $includeLine, string $codeFileSpec
      , ?string &$itemTag) : bool
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SetIncludeValues", $enabled);
      $retValue = true;

      $itemTag = null;
      $this->LibName = LJCCommon::GetFileName($codeFileSpec);
      $this->Comments = [];
      $xmlPath = LJCCommon::GetDelimitedString($includeLine, "path='", "'");
      if (null == $xmlPath)
      {
        $retValue = false;
      }

      if ($retValue)
      {
        $itemTag = LJCCommon::GetDelimitedString($xmlPath, "items/", "/*");
        $this->XMLFile = LJCCommon::GetDelimitedString($includeLine, "file='", "'");
        if (null == $this->XMLFile)
        {
          $retValue = false;
        }

        // Add code file path to doc file path to create XML file spec.
        $fileSpecPath = LJCCommon::GetFileSpecPath($codeFileSpec);
        // *** Begin *** #01
        // ToDo: This is causing problems?
        if ($fileSpecPath != "")
        {
          $this->XMLFile = "$fileSpecPath/$this->XMLFile";
        }
        // *** End   *** #01
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SetIncludeValues()

    // ---------------
    // Private Output Methods

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
    // Public Properties - LJCInclude

    /// <summary>The Incude comments.</summary>
    public ?array $Comments;

    /// <summary>The Code File base (Library) name.</summary>
    public ?string $LibName;

    /// <summary>The Include file spec.</summary>
    public ?string $XMLFile;

    // ---------------
    // Private Properties

    /// <summary>The Current tag name.</summary>
    public ?string $CurrentTagName;
  } // LJCInclude
?>