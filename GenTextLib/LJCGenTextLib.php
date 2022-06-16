<?php
	// Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCGenTextLib.php
	declare(strict_types=1);
	$webCommonPath = "c:/inetpub/wwwroot/LJCPHPCommon";
	$devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
	require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
	require_once "$devPath/LJCPHPCommon/LJCTextLib.php";
	require_once "$devPath/GenTextLib/LJCGenTextSectionLib.php";

	// The utility to generate text from a template and custom GenData.
	/// <include path='items/LJCGenTextLib/*' file='Doc/LJCGenTextLib.xml'/>
	/// LibName: LJCGenTextLib

	// ***************
	/// <summary>The GenText text generator class.</summary>
	/// <remarks>Main Function: ProcessTemplate()</remarks>
	class LJCGenText
	{
		/// <summary>Initializes an object instance.</summary>
		/// <param name="$debugFileSuffix">The data file specification.</param>
		public function __construct(?string $debugFileSuffix = "GenData")
		{
			$this->ActiveSections = [];
			$this->CurrentSection = null;

			$debug = false;
			if ($debug)
			{
				$fileName = LJCCommon::GetFileName($debugFileSuffix);
				$fileName = LJCCommon::GetDebugFileName("Debug", $fileName);
				$debugOutputStream = fopen($fileName, "w");
				$this->DebugWriter = new LJCWriter($debugOutputStream);
			}
		}  // construct()

		// ---------------
		// Public Methods

		// Processes the Template and Data to produce the output file.
		/// <include path='items/ProcessTemplate/*' file='Doc/LJCGenText.xml'/>
		public function ProcessTemplate(string $templateFileSpec
			, LJCSections $sections) : ?string
		{
			$retValue = null;

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
					$builder->Append($lines);
				}
				if ($writeLine)
				{
					$text = rtrim($this->Line);
					$this->Debug("-$text");

					//LJCWriter::Write($this->Line);
					$builder->Append($this->Line);
				}
			}
			fclose($this->Stream);
			$retValue = $builder->ToString();
			return $retValue;
		}  // ProcessTemplate()

		// ---------------
		// Private Methods

		// Adds or removes an Active Section.
		// <include path='items/ManageSections/*' file='Doc/LJCGenText.xml'/>
		private function ManageSections(int $prevLineBegin, int $itemIndex)
			: ?LJCDirective
		{
			$retValue = null;

			if (null == $this->Line)
			{
				return $retValue;				
			}

			$retValue = LJCDirective::Find($this->Line);
			if ($retValue != null)
			{
				switch (strtolower($retValue->Type))
				{
					case "#sectionbegin":
						$section = $this->Sections->Get($retValue->Name, false);
						if ($section != null)
						{
							// Set CurrentSection if Section Data exists.
							$this->CurrentSection = $section;

							if (count($this->CurrentSection->Items) > 1
								&& null == $this->CurrentSection->Begin)
							{
								$this->CurrentSection->Begin = $prevLineBegin;
							}

							// Push active section.
							$this->ActiveSections[] = $this->CurrentSection;
							//$this->ShowActive();
						}
						$this->Line = null;
						break;

					case "#sectionend":
						$activeSectionsCount = count($this->ActiveSections);
						if ($activeSectionsCount > 0)
						{
							$section = $this->Sections->Get($retValue->Name, false);							
							if ($section != null)
							{
								// Only pop active section if there is Section data
								// and if there are no more items.
								$count = count($this->CurrentSection->Items);
								if ($itemIndex >= $count - 1)
								{
									$section = array_pop($this->ActiveSections);
									//$this->ShowActive();

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
			}  // if ($retValue != null)
			return $retValue;
		}  // ManageSections()

		// Processes the If directives.
		private function ProcessIfDirectives(LJCDirective $directive
			, string $saveLine) : bool
		{
			$retValue = $this->DoOutput;

			switch (strtolower($directive->Type))
			{
				case "#ifbegin":
					$this->Debug("");
					$this->Debug("IfBegin:");
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
							$this->Debug("Replacement: $name, $value");
						}

						$this->Debug("Directive: $directive->Type, $directive->Name, $directive->Value");
						if ("notnull" == strtolower($directive->Value))
						{
							if (null == $value)
							{
								$doElse = true;
								$this->Debug("Replacement Value: IsNull");
							}
						}
						else
						{
							if (null == $replacement
								|| $directive->Value != $value)
							{
								$doElse = true;
								$this->Debug("Directive Value != Replacement Value:");
							}
						}
						if ($doElse)
						{
							$this->IfOperation = "else";
							$retValue = false;
							$this->Debug("doElse:");
						}
						else
						{
							$this->Debug("doIf:");
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
					$this->Debug("IfEnd:");
					$this->Debug("");
					break;
			}
			return $retValue;
		}  // ProcessDirective()

		// Processes the Replacement items.
		private function ProcessReplacements() : void
		{
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
		}  // ProcessReplacements()

		// Processes the current Section.
		private function ProcessSection() : ?string
		{
			$retValue = null;

			if (null == $this->CurrentSection)
			{
				return null;
			}

			$builder = new LJCStringBuilder();

			// Process Items
			$getLine = false;
			$prevLineBegin =  0;
			$items = $this->CurrentSection->Items;
			$itemCount = count($items);

			for ($itemIndex = 0; $itemIndex < $itemCount; $itemIndex++)
			{
				$item = $items[$itemIndex];
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
								$builder->Append($lines);
								$prevLineBegin = ftell($this->Stream);
								continue;
							}

							$this->DoOutput = $this->ProcessIfDirectives($directive, $saveLine);
							$this->Debug("ProcessSection() DoOutput: $this->DoOutput");

							// Set to beginning of Current Section if Section End
							// and more Items.
							if ($this->ResetPosition($directive, $itemIndex))
							{
								break;
							}
						}
					}
					$getLine = true;

					if ($this->DoOutput && $this->Line != null)
					{
						if (LJCCommon::StrPos($this->Line, "_") >= 0)
						{
							$this->ProcessReplacements($item);
						}
						$text = rtrim($this->Line);
						$this->Debug("-$text");

						//LJCWriter::Write($this->Line);
						$builder->Append($this->Line);
					}
				}  // while(true)
			}
			$retValue = $builder->ToString();
			return $retValue;
		}  // ProcessSection()

		// Retrieves the Replacement object.
		private function GetReplacement(string $line, string $replacementName)
			: ?LJCReplacement
		{
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
			return $retValue;
		}  // GetReplacement()

		// Resets the Stream position to the beginning of the Section.
		private function ResetPosition(LJCDirective $directive, int $itemIndex) : bool
		{
			$retValue = false;

			if ($directive != null && "#sectionend" == strtolower($directive->Type))
			{
				$retValue = true;

				// Only reset position if there are more items.
				if ($this->CurrentSection != null)
				{
					$count = count($this->CurrentSection->Items);
					if ($itemIndex < $count - 1)
					{
						$begin = $this->CurrentSection->Begin;
						fseek($this->Stream, $begin, SEEK_SET);
						$this->Line = fgets($this->Stream);
					}
				}
			}
			return $retValue;
		}

		// ---------------
		// Private Write Helper Methods

		// Writes a Debug line.
		private function Debug(string $text, bool $addLine = true) : void
		{
			if (isset($this->DebugWriter))
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

		// Indicates if it is a SectionBegin or SectionEnd directive.
		private function IsBeginOrEnd(?LJCDirective $directive) : bool
		{
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
			return $retValue;
		}

		// Show the Active Sections.
		private function ShowActive() : void
		{
			if (is_array($this->ActiveSections)
				&& count($this->ActiveSections) > 0)
			{
				foreach ($this->ActiveSections as $activeSection)
				{
					$this->Debug("Active Section: $activeSection->Name");
				}
			}
		}

		// ---------------
		// Properties

		// The current Active Sections.
		private array $ActiveSections;

		// Indicates if the line should be output.
		private bool $DoOutput;

		// The current Section.
		private ?LJCSection $CurrentSection;

		// The Debug writer.
		private LJCWriter $DebugWriter;

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
