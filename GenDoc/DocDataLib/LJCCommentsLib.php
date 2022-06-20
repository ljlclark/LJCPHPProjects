<?php
	// Copyright (c) Lester J. Clark 2022 - All Rights Reserved
	// LJCCommentsLib.php
	declare(strict_types=1);
	$webCommonPath = "c:/inetpub/wwwroot/LJCPHPCommon";
	$devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
	require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
	require_once "$devPath/GenTextLib/LJCGenTextSectionLib.php";
	require_once "LJCIncludeLib.php";
	require_once "LJCParamCommentLib.php";

	// Contains Classes to parse code XML comments.
	/// <include path='items/LJCCommentsLib/*' file='Doc/LJCCommentsLib.xml'/>
	/// LibName: LJCCommentsLib

	// Main Call Tree
	// SetComment() public
	//   GetComment()
	//   SaveComment()
	
	// ***************
	// Provides methods to parse code XML comment values.
	/// <include path='items/LJCComments/*' file='Doc/LJCComments.xml'/>
	class LJCComments
	{
		// ---------------
		// Constructors

		/// <summary>Initializes an object instance.</summary>
		public function __construct()
		{
			$this->DebugClass = "LJCComments";
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

			$debug = false;
			if ($debug)
			{
				$fileName = LJCCommon::GetDebugFileName("Debug","LJCCommentsLib");
				$debugOutputStream = fopen($fileName, "w");
				$this->DebugWriter = new Writer($debugOutputStream);
			}
		}

		// ---------------
		// Public Methods - LJCComments

		/// <summary>Clears the XML comments.</summary>
		public function ClearComments() : void
		{
			$this->ClearComment("code");
			$this->ClearComment("include");
			$this->ClearComment("param");
			$this->ClearComment("remarks");
			$this->ClearComment("returns");
			$this->ClearComment("summary");
		}

		// Sets the XML comment value.
		/// <include path='items/SetComment/*' file='Doc/LJCComments.xml'/>
		public function SetComment(string $line, ?string $codeFileSpec = null)
			: void
		{
			$loc = "$this->DebugClass.SetComment";

			if ($codeFileSpec != null)
			{
				$this->CodeFileSpec = $codeFileSpec;
			}

			// Testing
			$this->Output("---------------");
			$this->Output("$loc line", $line);
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
				// Testing
				$this->Output("$loc comment", $comment);

				// Process if Include comment processing is not done.
				if ($this->CurrentTagName != null)
				{
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
				// Testing
				$this->Output("$loc isContinue", $comment);
				$this->SaveComment($comment);
			}
		}  // SetComment();

		// ---------------
		// Private Methods - LJCComments

		// Clears the comments for the specified comment tag.
		private function ClearComment(?string $tagName = null) : void
		{
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
		}

		// Gets the comment for the current comment tag.
		private function GetComment(string $line) : ?string
		{
			$loc = "$this->DebugClass.GetComment";
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
				// *** Next Statement *** Change - 6/19
				//if (null == $endTag)
				if (false == $this->HasCurrentEndTag($line))
				{
					// No EndTag so do not remove cr/lf.
					$rTrim = false;
				}
				$retValue = LJCCommon::GetDelimitedString($line, $beginTag, $endTag
					, false, $rTrim);
			}
			return $retValue;
		}  // GetComment();

		// Saves the comment for the current comment tag.
		private function SaveComment(?string $comment) : void
		{
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
		}

		// ---------------
		// Private Tag Methods- LJCComments

		// Returns the current or other BeginTag found in the line.
		private function GetBeginTagName(string $line) : ?string
		{
			$loc = "$this->DebugClass.GetBeginTagName";
			$retValue = null;
			
			if ($this->CurrentTagName != null)
			{
				$retValue = $this->CurrentTagName;
			}

			foreach ($this->BeginTags as $beginTagName => $beginTag)
			{
				if (LJCCommon::StrPos($line, $beginTag) >= 0)
				{
					$retValue = $beginTagName;
					break;
				}
			}
			return $retValue;
		}

		// Gets the BeginTag for the specified comment tag.
		private function GetBeginTag(?string $tagName = null) : ?string
		{
			$retValue = null;

			if (null == $tagName)
			{
				$tagName = $this->CurrentTagName;
			}

			if (array_key_exists($tagName, $this->BeginTags))
			{
				$retValue = $this->BeginTags[$tagName];
			}
			return $retValue;
		}

		// Gets the EndTag for the specified or current comment tag.
		private function GetEndTag(?string $tagName = null) : ?string
		{
			$retValue = null;

			if (null == $tagName)
			{
				$tagName = $this->CurrentTagName;
			}

			if (array_key_exists($tagName, $this->EndTags))
			{
				$retValue = $this->EndTags[$tagName];
			}
			return $retValue;
		}

		// Gets the BeginTag length for the specified or current comment tag.
		private function GetLengthBeginTag(?string $tagName = null) : int
		{
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
			return $retValue;
		}

		// Gets the EndTag length for the specified or current comment tag.
		private function GetLengthEndTag(?string $tagName = null) : int
		{
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
			return $retValue;
		}

		// Indicates if the lines has a current EndTag.
		private function HasCurrentEndTag(string $line) : bool
		{
			$retValue = false;

			$endTag = $this->GetEndTag();
			if ($endTag != null
				&& LJCCommon::StrRPos($line, $endTag) >= 0)
			{
				$retValue = true;
			}
			return $retValue;
		}

		// Sets the comment tag values
		private function SetCommentTags() : void
		{
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
		}

		// ---------------
		// Private Output Methods

		// Writes a file Debug line.
		private function Debug(string $text, bool $addLine= true) : void
		{
			if ($this->DebugWriter != null)
			{
				if ($addLine)
				{
					$this->DebugWriter->FWriteLine($text);
				}
				else
				{
					$this->DebugWriter->FWrite($text);
				}
			}
		}

		// Writes an output line.
		private function Output($text = null, $value = null)
		{
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
		}

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

		// The Debug writer.
		private ?Writer $DebugWriter;

		// The End comment tags.
		private ?array $EndTags;

		// The IncludeFile object.
		private ?LJCInclude $IncludeFile;

		// The IsContinue flag.
		private bool $IsContinue;
	}
?>
