<?php
	// Copyright (c) Lester J. Clark 2022 - All Rights Reserved
	// TextRegionsLib.php
	declare(strict_types=1);
	$devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
	require_once "$devPath/LJCPHPCommon/LJCCollectionLib.php";
	require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";

	/// <summary>The PHP Text Regions Class Library</summary>
	/// LibName: LJCTextRegionsLib

	// ***************
	/// <summary>Represents the text region.</summary>
	class TextRegion
	{
		/// <summary>
		///		Initializes a class instance with the provided values.
		/// </summary>
		public function __construct(int $beginIndex, int $endIndex)
		{
			$this->BeginIndex = $beginIndex;
			$this->EndIndex = $endIndex;
		}

		/// <summary>Creates an object clone.</summary>
		public function Clone() : self
		{
			$retValue = new self($this->BeginIndex, $this->EndIndex);
			return $retValue;
		}

		// ---------------
		// Properties

		/// <summary>The region beginning index.</summary>
		public int $BeginIndex;

		/// <summary>The region ending index.</summary>
		public string $EndIndex;
	}

	// ***************
	/// <summary>Represents a collection of TextRegion objects.</summary>
	class TextRegions extends LJCCollectionBase
	{
		/// <summary>
		///		Initializes a class instance with the provided values.
		/// </summary>
		public function __construct(string $fieldDelimiter = ","
			, string $valueDelimiter = "\"")
		{
			$this->FieldDelimiter = $fieldDelimiter;
			$this->ValueDelimiter = $valueDelimiter;
		}

		// ---------------
		// Public Collection Methods

		// Creates an object and adds it to the collection.
		// <include path='items/Add/*' file='Doc/TextRegions.xml'/>
		public function Add(int $beginIndex, int $endIndex, $key = null)
			: ?TextRegion
		{
			$retValue = null;

			if (null == $key)
			{
				$key = $beginIndex;
			}

			$item = new TextRegion($beginIndex, $endIndex, $renameAs);
			$retValue = $this->AddObject($item , $key);
			return $retValue;
		}

		// Adds an object and key value.
		// <include path='items/AddObject/*' file='Doc/TextRegions.xml'/>
		public function AddObject(TextRegion $item, $key = null) : ?TextRegion
		{
			if (null == $key)
			{
				$key = $item->PropertyName;
			}
			$retValue = $this->AddItem($item, $key);
			return $retValue;
		}

		/// <summary>Creates an object clone.</summary>
		public function Clone() : self
		{
			$retValue = new self();
			foreach ($this->Items as $key => $item)
			{
				$retValue->AddObject($item);
			}
			unset($item);
			return $retValue;
		}

		// Get the item by Key value.
		// <include path='items/Get/*' file='Doc/TextRegions.xml'/>
		public function Get($key, bool $throwError = true) : ?TextRegion
		{
			$retValue = $this->GetItem($key, $throwError);
			return $retValue;
		}

		// ---------------
		// Public Other Methods

		// Sets regions and returns true if a region was defined.
		public function SetRegions(string $text) : bool
		{
			$retValue = false;

			$this->Clear();
			$currentIndex = 0;
			$beginIndex = LJCCommon::StrPos($text, $this->ValueDelimiter);
			while ($beginIndex > -1)
			{
				$currentIndex = $eginIndex + 1;
				$endIndex = LJCCommon::StrPos($text, $this->ValueDelimiter
					, $currentIndex);
				if (-1 == $endIndex)
				{
					$beginIndex = -1;
					continue;
				}
				$retValue = true;
				$this->Add($beginIndex, $endIndex);
				$currentIndex = $endIndex + 1;
				$endIndex = -1;
				$beginIndex = LJCCommon::StrPos($text, $this->ValueDelimiter
					, $currentIndex);
			}
			return $retValue;
		}

		/// <summary>Determines if a delimiter is in a text region.</summary>
		public function IsInRegion(int $index) : bool
		{
			$retValue = false;

			foreach ($this->Items as $region)
			{
				if ($region->BeginIndex <= $index && $region->EndIndex >= $index)
				{
					$retValue = true;
					break;
				}
			}
			return $retValue;
		}

		/// <summary>
		/// Splits a line of text on the delimiters not enclosed in text regions.
		/// </summary>
		public function Split(string $line) : array
		{
			$retValue = [];

			if (false == $this->SetRegions($line))
			{
				$retValue = explode($this->FieldDelimiter, $line);
			}
			else
			{
				$currentIndex;
				$beginIndex = -1;
				$lineLength = strlen($line);
				if ($line != null && $lineLength > 0)
				{
					$beginIndex = 0;
				}
				while ($beginIndex > -1 && $beginIndex < $lineLength)
				{
					// Skip value delimiter at beginning of field.
					$field = substr($line, $beginIndex);
					if (str_starts_with(trim($field), $this->ValueDelimiter))
					{
						$index = LJCCommon::StrPos($field, $this->ValueDelimiter);
						$beginIndex += $index + 1;
					}

					// Find the end of field.
					$currentIndex = $beginIndex + 1;
					$endIndex = LJCCommon::StrPos($line, $this->FieldDelimiter, $currentIndex);
					if (-1 == $endIndex)
					{
						// No more field delimiters so use length to get last field.
						$endIndex = $lineLength;
					}
					if ($endIndex > -1)
					{
						// Get delimiter that is not already in a region?
						while ($this->IsInRegion($endIndex))
						{
							$currentIndex = $endIndex + 1;
							$endIndex = LJCCommon::StrPos($line, $this->FieldDelimiter
								, $currentIndex);
							if (-1 == $endIndex)
							{
								// No more field delimiters so use length to get last field.
								$endIndex = $lineLength;
							}
						}

						// Get the field value.
						if ($endIndex > -1)
						{
							$currentIndex = $endIndex + 1;
							$value = trim(substr($line, $beginIndex, $endIndex - $beginIndex));

							// Remove trailing delimiter.
							if (str_ends_with($value, $this->ValueDelimiter))
							{
								$value = substr($value, 0, strlen($value) - 1);
							}

							$retValue[] = $value;
						}
					}

					// Set beginning of next field.
					$beginIndex = $currentIndex;
				}
			}
			return $retValue;
		}

		// ---------------
		// Properties

		/// <summary>The field delimiter.</summary>
		public string $FieldDelimiter;

		/// <summary>The text region delimiter.</summary>
		public string $ValueDelimiter;
	}
?>