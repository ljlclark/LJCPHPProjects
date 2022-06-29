<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // TextReader.php
  declare(strict_types=1);
  $devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
  require_once "$devPath/LJCPHPCommon/LJCDBAccessLib.php";
  require_once "LJCTextReaderLib.php";

  // Get parameters.
  parse_str(implode('&', array_slice($argv, 1)), $args);
  $fileSpec = $args["fileSpec"];

  $textReader = new LJCTextReader($fileSpec);
  //$textReader->SetConfig("test.xml");
  $textReader->SetConfig();

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

  class Name
  {
    public ?string $FirstName;
    public ?string $MiddleInitial;
    public ?string $LastName;
  }
?>