<?php
	// Copyright (c) Lester J. Clark 2022 - All Rights Reserved
	// LJCIncludeLib.php
	declare(strict_types=1);
	$webCommonPath = "c:/inetpub/wwwroot/LJCPHPCommon";
	$devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
	require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
	require_once "$devPath/GenTextLib/LJCGenTextSectionLib.php";

	// Contains Classes to retrieve data from include XML files.
	/// <include path='items/LJCIncludeLib/*' file='Doc/LJCIncludeLib.xml'/>
	/// LibName: LJCIncludeLib

	// Main Call Tree
	// SetComments()
	//   GetComment()

	// ***************
	// Retrieves the Include file XML comment values.
	/// <include path='items/LJCInclude/*' file='Doc/LJCInclude.xml'/>
	class LJCInclude
	{
		// ---------------
		// Constructors

		/// <summary>Initializes an object instance.</summary>
		public function __construct()
		{
			$this->Comments = null;
			$this->XMLFile = null;

			$this->DebugWriter = null;
			$this->LibName = null;

			$debug = false;
			if ($debug)
			{
				$fileName = LJCCommon::GetDebugFileName("Debug", "IncludeLib");
				$debugOutputStream = fopen($fileName, "w");
				$this->DebugWriter = new Writer($debugOutputStream);
			}
		}

		// ---------------
		// Public Methods

		// Sets the comments from the specified include file.
		/// <include path='items/SetComments/*' file='Doc/LJCInclude.xml'/>
		public function SetComments(string $includeLine, string $codeFileSpec)
			: void
		{
			// Sets LibName, XMLFile and itemTag.
			if ($this->SetValues($includeLine, $codeFileSpec, $itemTag))
			{
				$isItem = false;
				$isContinue = false;
				$stream = fopen($this->XMLFile, "r");
				while (false == feof($stream))
				{
					$line = (string)fgets($stream);
					$trimLine = trim($line);

					if (LJCCommon::StrPos($trimLine, "<$itemTag>") >= 0)
					{
						$isItem = true;
					}
					else
					{
						if ($isItem)
						{
							if (false === $isContinue)
							{
								$comment = $this->GetComment($line);
								if ($comment != null)
								{
									$comment = $this->TrimXMLComment($comment);
									$this->Comments[] = $comment;
								}

								$endTag = $this->GetEndTag($trimLine);
								if ($this->InvalidCommentEndTag($trimLine))
								{
									$isItem = false;
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
								$endTag = $this->GetEndTag($trimLine);
								if ($endTag)
								{
									// Has end tag so end Continue comment.
									$isContinue = false;
								}

								$comment = $this->GetComment($line);
								if ($comment != null)
								{
									$comment = $this->TrimXMLComment($comment);
									if (false === $this->InvalidCommentEndTag($comment))
									{
										$this->Comments[] = $comment;
									}
								}

								if ($this->InvalidCommentEndTag($comment))
								{
									$isItem = false;
									fclose($stream);
									break;
								}
							}
						}
					}
				}
			}
		}  // SetComments();

		// ---------------
		// Private Methods

		// Gets the begin tag.
		private  function GetBeginTag(string $line) : ?string
		{
			$retValue = null;

			$beginTag = LJCCommon::GetDelimitedString($line, "<", ">");
			if ($this->IsCommentTag($beginTag))
			{
				$retValue = "<$beginTag>";								
			}
			return $retValue;
		}

		// Gets the comment for the specified code line.
		private function GetComment(string $line) : ?string
		{
			$retValue = null;

			$beginTag = $this->GetBeginTag($line);
			$endTag = $this->GetEndTag($line);

			if (null == $beginTag)
			{
				// No begin tag so set tag for start of comment.
				$line = "/$line";
				$beginTag = "/";
			}

			$comment = LJCCommon::GetDelimitedString($line, $beginTag, $endTag, false);

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
			return $retValue;
		}

		// Gets the end tag.
		private function GetEndTag(string $line) : ?string
		{
			$retValue = null;

			$endTag = LJCCommon::GetDelimitedString($line, "</", ">");
			if ($this->IsCommentTag($endTag))
			{
				$retValue = "</$endTag>";								
			}
			return $retValue;
		}

		// Checks for a valid comment tag.
		private function IsCommentTag(?string $tag) : bool
		{
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
			return $retValue;
		}

		// Checks for an invalid end comment tag.
		private function InvalidCommentEndTag(?string $comment) : bool
		{
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
			return $retValue;
		}

		// Sets the Class include file values.
		private function SetValues(string $includeLine, string $codeFileSpec
			, ?string &$itemTag) : bool
		{
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
				$this->XMLFile = "$fileSpecPath/$this->XMLFile";
			}
			return $retValue;
		}

		// Replaces tabs with spaces and removes extra leading spaces
		private function TrimXMLComment(string $comment) : string
		{
			$retValue = str_replace("\t", "  ", $comment);
			$count = 6;
			$check = substr($retValue, 3, $count);
			if ($check == "      ")
			{
				$retValue = "///" . substr($retValue, $count + 3);
			}
			return $retValue;
		}

		// Writes a Debug line.
		private function Debug(string $text, bool $addLine = true) : void
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

		// ---------------
		// Public Properties

		/// <summary>The Incude comments.</summary>
		public ?array $Comments;

		/// <summary>The Include file spec.</summary>
		public ?string $XMLFile;

		// ---------------
		// Private Properties

		// The Debug writer.
		private ?Writer $DebugWriter;

		// The Code File base (Library) name.
		private ?string $LibName;
	}
?>