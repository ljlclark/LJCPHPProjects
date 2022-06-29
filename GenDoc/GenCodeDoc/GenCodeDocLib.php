<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // XMLGenTest.php
  declare(strict_types=1);
  $webCommonPath = "c:/inetpub/wwwroot/LJCPHPCommon";
  $devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
  require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
  require_once "$devPath/LJCPHPCommon/LJCTextLib.php";
  require_once "$devPath/GenDoc/DocDataLib/LJCDocDataGenLib.php";
  require_once "$devPath/GenDoc/GenDataLib/LJCGenDataGenLib.php";

  class GenCodeDoc
  {
    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      $this->DocDataGen = new LJCDocDataGen();
      $this->GenDataGen = new LJCGenDataGen();
    }

    /// <summary>Creates CodeDoc pages from source file list.</summary>
    public function CreateFromList()
    {
      $sourceFileListSpec = "GenCodeSourceFileList.txt";
      $inputStream = fopen($sourceFileListSpec, "r+");
      while(false == feof($inputStream))
      {
        $line = (string)fgets($inputStream);
        $this->CreateFilePages($line);
      }
    }

    /// <summary>Create the HTML files.</summary>
    /// <param name="$fileSpecLine">The source file spec.</param>
    public function CreateFilePages($fileSpecLine) : void
    {
      global $webCommonPath;
      global $devPath;
      $writeDocDataXML = false;
      $writeGenDataXML = false;

      $tokens = LJCCommon::GetTokens($fileSpecLine);
      if (count($tokens) > 1)
      {
        switch ($tokens[0])
        {
          case "webCommonPath":
            $fileSpec = "$webCommonPath";
            break;
          case "devPath":
            $fileSpec = "$devPath";
            break;
          default:
            $fileSpec = $tokens[0];
            break;
        }
        $fileSpec .= trim($tokens[1]);

        $docXMLString = $this->DocDataGen->CreateDocDataXMLString($fileSpec
          , $writeDocDataXML);
        if ($docXMLString != null)
        {
          $genXMLString = $this->GenDataGen->CreateLibXMLString($docXMLString
            , $fileSpec, $writeGenDataXML);
        }
      }
    }

    // The Generate DocData XML object.
    private LJCDocDataGen $DocDataGen;

    // The Generate GenData XML and HTML object.
    private LJCGenDataGen $GenDataGen;
  }
?>