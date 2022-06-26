<?php
	// Copyright (c) Lester J. Clark 2022 - All Rights Reserved
	// LJCTextReaderLib.php
	declare(strict_types=1);
	require_once "TextRangesLib.php";

	/// <summary>The PHP Text Reader Class Library</summary>
	/// LibName: LJCTextReaderLib

	// ***************
	/// <summary>
	///		Contains methods to read text files and parse into fields.
	/// </summary>
	class LJCTextReader
	{
		/// <summary>
		///		Initializes a class instance with the provided values.
		/// </summary>
		public function __construct(string $fileSpec)
		{
			$this->FileSpec = $fileSpec;
			$this->FieldCount = 0;
			$this->FieldDelimiter = "\t";
			$this->ValueDelimiter = "\"";
			$this->TextRanges = new TextRanges($this->FieldDelimiter
				, $this->ValueDelimiter);

			// Open for reading and to allow positioning?
			$this->InputStream = fopen($fileSpec, "r+");
			if (false == feof($this->InputStream))
			{
				$line = (string)fgets($this->InputStream);
				$this->FieldNames = explode("\t", $line);
			}
			if (isset($this->FieldNames))
			{
				$this->FieldCount = count($this->FieldNames);
			}
		}

		// ---------------
		// Public Methods - LJCTextReader

		/// <summary>Clear field values.</summary>
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

		/// <summary>Read the next input line.</summary>
		public function Read() : bool
		{
			$retValue = false;

			$this->Clear();
			if (false == feof($this->InputStream))
			{
				$this->ValueCount = 0;
				$line = (string)fgets($this->InputStream);
				$values = $this->TextRanges->Split($line);
				$valueLength = count($values);
				if ($valueLength > 0)
				{
					for ($index = 0; $index < $this->FieldCount; $index++)
					{
						if ($valueLength > $index)
						{
							$name = $this->FieldNames[$index];
							$value = $this->TrimCrLf($values[$index]);
							if ($value != null)
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

		/// <summary>Gets the field value.</summary>
		public function GetString(string $fieldName)
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

		// Remove cr/lf.
		private function TrimCrLf(string $value)
		{
			$retValue = $value;

			if ($retValue != null)
			{
				$length = strlen($retValue) - 2;
				if ($length > 1)
				{
					$end = substr($retValue, $length);
					if ($end == "\r\n")
					{
						// Remove \r\n;
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