<?php
	// Copyright (c) Lester J. Clark 2022 - All Rights Reserved
	// TextReader.php
	declare(strict_types=1);
	$devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
	require_once "LJCTextReaderLib.php";

	parse_str(implode('&', array_slice($argv, 1)), $args);
	$fileSpec = $args["fileSpec"];

	$textReader = new LJCTextReader($fileSpec);
	$textReader->SetConfig("test.xml");
	foreach ($textReader->FieldNames as $fieldName)
	{
		$fieldName = rTrim($fieldName);
		echo "fieldName: $fieldName\r\n";
	}

	while ($textReader->Read())
	{
		if ($textReader->ValueCount > 0)
		{
			foreach ($textReader->FieldNames as $fieldName)
			{
				if ($fieldName != null)
				{
					$fieldValue = $textReader->GetString($fieldName);
					if ($fieldValue != null)
					{
						echo "fieldValue: $fieldValue\r\n";
					}
				}
			}
			$firstName = $textReader->GetString("FirstName");
			if ($firstName != null)
			{
				echo "firstName: $firstName\r\n";
			}
		}
	}
?>