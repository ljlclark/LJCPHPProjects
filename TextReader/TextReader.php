<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.-->
  // TextReader.php
  declare(strict_types=1);
  // Path: Codeline/TextReader
  require_once "LJCTextReaderLib.php";
  // LJCTextReaderLib: LJCTextReader

  /// <summary>The LJCTextReader test program.</summary>
  /// LibName: TextReader

  // Instantiate properties with Pascal case.
  $Debug = new LJCDebug("TextReader", "Main"
    , "w", false);
  $Debug->IncludePrivate = true;
  $enabled = false;
  $Debug->BeginMethod("SetConfig", $enabled);

  // Get parameters.
  parse_str(implode('&', array_slice($argv, 1)), $args);
  $fileSpec = $args["fileSpec"];

  $textReader = new LJCTextReader($fileSpec);
  //$textReader->SetFieldConfig("test.xml");
  $textReader->SetFieldConfig();

  while ($textReader->Read())
  {
    if ($textReader->ValueCount > 0)
    {
      $name = new Name();
      $textReader->FillDataObject($name);
      echo "$name->FirstName\r\n";
      echo "$name->MiddleInitial\r\n";
      echo "$name->LastName\r\n";
    }
  }

  $Debug->EndMethod($enabled);

  class Name
  {
    public ?string $FirstName;
    public ?string $MiddleInitial;
    public ?string $LastName;
  }
?>