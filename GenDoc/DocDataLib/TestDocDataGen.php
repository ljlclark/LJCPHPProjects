<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // TestDocDataGen.php
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/GenDoc/DocDataLib/LJCDocDataGenLib.php";
  include_once "$prefix/GenDoc/GenCodeDoc/LJCGenDocConfigLib.php";
  // LJCDocDataGenLib: LJCDocDataGen
 
  $writeXML = true;
  $docDataGen = new LJCDocDataGen();

  // Create config.
  $genDocConfig = new LJCGenDocConfig();
  $genDocConfig->WriteDocDataXML = false;

  // Read configuration from file list.
  $sourceFileListSpec = "../GenCodeDoc/GenCodeSourceFileList.txt";
  $inputStream = fopen($sourceFileListSpec, "r+");
  while(false == feof($inputStream))
  {
    $line = (string)fgets($inputStream);

    // Sets config properties from file list.
    $isFile = $genDocConfig->SetProperties($line);

    if ($isFile)
    {
      $docDataGen->SetConfig($genDocConfig);
      break;
    }
  }

  $xmlString = $docDataGen->SerializeDocData("../GenCodeDoc/GenCodeDocLib.php"
    , $writeXML);
  echo($xmlString);
?>