<?php
	// Copyright (c) Lester J. Clark 2022 - All Rights Reserved
	// LJCTextReaderLib.php
	declare(strict_types=1);
	$devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
	require_once "$devPath/LJCPHPCommon/LJCCollectionLib.php";
	require_once "$devPath/LJCPHPCommon/LJCDbAccessLib.php";
	require_once "TextRangesLib.php";

	/// <summary>The PHP Text Reader Class Library</summary>
	/// LibName: LJCTextReaderLib

	// ***************
	// Contains methods to read text files and parse into fields.
	/// <include path='items/LJCTextReader/*' file='Doc/LJCTextReader.xml'/>
	class LJCTextReader
	{
		// Initializes a class instance with the provided values.
		/// <include path='items/construct/*' file='Doc/LJCTextReader.xml'/>
		public function __construct(string $fileSpec)
		{
			if (false == file_exists($fileSpec))
			{
				throw new Exception("Input file '$fileSpec' was not found.");
			}
			$this->FileSpec = $fileSpec;
			$this->ConfigXMLSpec = null;
			$this->FieldDelimiter = ",";
			$this->IsFirstRead = true;
			$this->ValueDelimiter = "\"";
		}

		/// <summary></summary>
		public function SetConfig(string $configXMLSpec = null)
		{
			$this->ConfigXMLSpec = $configXMLSpec;
			$this->TextRanges = new TextRanges($this->FieldDelimiter
				, $this->ValueDelimiter);

			// Open for reading and to allow positioning?
			$this->InputStream = fopen($this->FileSpec, "r+");
			if (null == $configXMLSpec)
			{
				// First line must have the configuration.
				if (feof($this->InputStream))
				{
					throw new Exception("First line configuration was not found.");
				}
				$line = (string)fgets($this->InputStream);
				$this->IsFirstRead = false;
				$names = explode($this->FieldDelimiter, $line);
				$this->SetFieldNames($names);
			}
			else
			{
				if (false == file_exists($configXMLSpec))
				{
					throw new Exception("Config file '$configXMLSpec' was not found.");
				}
				$dbColumns = LJCDbColumns::Deserialize($configXMLSpec);
				foreach($dbColumns as $dbColumn)
				{
					$this->FieldNames[] = $dbColumn->PropertyName;
				}
			}

			$this->FieldCount = 0;
			if (isset($this->FieldNames))
			{
				$this->FieldCount = count($this->FieldNames);
			}
		}  // SetConfig()

		// ---------------
		// Public Methods - LJCTextReader

		/// <summary>Clears the field values.</summary>
		public function Clear() : void
		{
			if (isset($this->FieldValues))
			{
				$valuesLength = count($this->FieldValues);
				if ($valuesLength > 0)
				{
					for ($index = 0; $index < $this->FieldCount; $index++)
					{
						$name = $this->FieldNames[$index];
						$this->FieldValues[$name] = null;
					}
				}
			}
		}  // Clear()

		/// <summary>Reads the next input line.</summary>
		/// <returns>True if the line was read, otherwise false.</returns>
		public function Read() : bool
		{
			$retValue = false;

			$this->Clear();
			if (false == feof($this->InputStream))
			{
				$this->ValueCount = 0;
				$line = (string)fgets($this->InputStream);
				$values = $this->TextRanges->Split($line);

				if ($this->IsFirstRead
					&& null != $this->ConfigXMLSpec
					&& $this->IsConfig($values))
				{
					// Skip Config line if configured with XML file.
					$line = (string)fgets($this->InputStream);
					$values = $this->TextRanges->Split($line);
				}
				$this->IsFirstRead = false;

				$valueLength = count($values);
				if ($valueLength > 0)
				{
					for ($index = 0; $index < $this->FieldCount; $index++)
					{
						if ($valueLength > $index)
						{
							$name = $this->FieldNames[$index];
							$value = $this->TrimCrLf($values[$index]);
							if (null != $value)
							{
								$this->FieldValues[$name] = $value;
								$this->ValueCount++;
							}
						}
					}
					$retValue = true;
				}
			}
			return $retValue;
		}  // Read()

		// Gets the field value.
		/// <include path='items/GetString/*' file='Doc/LJCTextReader.xml'/>
		public function GetString(string $fieldName) : string
		{
			$retValue = null;

			if (isset($this->FieldValues[$fieldName]))
			{
				$retValue = $this->FieldValues[$fieldName];
			}
			return $retValue;
		}  // GetString()

		// ---------------
		// Private Methods - LJCTextReader

		// Checks if the line is a Config line.
		private function IsConfig($names)
		{
			$retValue = false;

			if ("Config" == $names[0])
			{
				$retValue = true;
			}
			return $retValue;
		}

		// Sets the field names from the Config line.
		private function SetFieldNames(array $names)
		{
			if ($this->IsConfig($names))
			{
				foreach ($names as $name)
				{
					if ("Config" != $name)
					{
						$this->FieldNames[] = $name;
					}
				}
			}
			else
			{
				throw new Exception("First line configuration was not found.");
			}
		}

		// Remove the trailing cr/lf or lf without removing anything else.
		// <include path='items/TrimCrLf/*' file='Doc/LJCTextReader.xml'/>
		private function TrimCrLf(string $text) : string
		{
			$retValue = $text;

			if (null != $retValue)
			{
				$length = strlen($retValue) - 2;
				if ($length > 1)
				{
					$end = substr($retValue, $length);
					$success = true;
					if ("\r\n" != $end)
					{
						$length++;
						$end = substr($retValue, $length);
						$success = false;
						if ("\n" == $end)
						{
							$success = true;
						}
					}
					if ($success)
					{
						$retValue = substr($retValue, 0, $length);
					}
				}
			}
			return $retValue;
		}  // TrimCrLf()

		// ---------------
		// Properties - LJCTextReader

		/// <summary>The field count.</summary>
		public int $FieldCount;

		/// <summary>The field count.</summary>
		public string $FieldDelimiter;

		/// <summary>The field names.</summary>
		public array $FieldNames;

		/// <summary>The field values.</summary>
		public array $FieldValues;

		/// <summary>The file specification.</summary>
		public string $FileSpec;

		/// <summary>The input stream.</summary>
		private $InputStream;

		/// <summary>The value count.</summary>
		public string $ValueDelimiter;

		/// <summary>The value count.</summary>
		public int $ValueCount;
	}  // LJCTextReader
?>