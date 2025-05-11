<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // GenCodeDocLib.php
  declare(strict_types=1);
  // Must refer to exact same file everywhere in codeline.
  require_once "../../LJCPHPCommon/LJCTextLib.php";
  include_once "../DocDataLib/LJCDebugLib.php";
  require_once "../DocDataLib/LJCDocDataGenLib.php";
  require_once "../GenDataLib/LJCGenDataGenLib.php";

  // Classes
  // File
  //   GenCodeDoc

  // Contains classes to generate HTML doc for files listed
  // in GenCodeSourceFileList.txt 

  // Calling Code
  // GenCodeDocFiles.php

  // Main Call Tree
  // CreateFromList() public
  //   CreateFilePages() public
  //     LJCDocDataGen->CreateDocDataXMLString()
  //     LJCGenDataGen->CreateLibXMLString()

  // ***************
  class GenCodeDoc
  {
    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $enabled = false;
      $this->Debug = new LJCDebug("GenCodeDocLib", "GenCodeDoc"
        , "w", $enabled);
      $this->Debug->IncludePrivate = true;
      
      // GenDoc/DocDataLib/LJCDocDataGenLib.php
      $this->DocDataGen = new LJCDocDataGen();
      //GenDoc/GenDataLib/LJCGenDataGenLib.php
      $this->GenDataGen = new LJCGenDataGen();
    } // __construct()

    /// <summary>Creates CodeDoc pages from source file list.</summary>
    public function CreateFromList()
    {
      $this->Debug->WriteStartText("CreateFromList");

      // Gets the list of files to read.
      $sourceFileListSpec = "GenCodeSourceFileList.txt";
      $inputStream = fopen($sourceFileListSpec, "r+");
      while(false == feof($inputStream))
      {
        $line = (string)fgets($inputStream);
        $this->CreateFilePages($line);
      }

      $this->Debug->AddIndent(-1);
    }

    /// <summary>Create the HTML files.</summary>
    /// <param name="$fileSpecLine">The source file spec.</param>
    public function CreateFilePages($fileSpecLine) : void
    {
      $this->Debug->WriteStartText("CreateFilePages");

      //global $path;
      $writeDocDataXML = false;
      $writeGenDataXML = false;

      $tokens = LJCCommon::GetTokens($fileSpecLine);
      if (count($tokens) > 1)
      {
        switch ($tokens[0])
        {
          case "path":
            // *** Change *** 5/11/25
            //$fileSpec = "$devPath";
            $fileSpec = "../../";
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

      $this->Debug->AddIndent(-1);
    }

    // The Generate DocData XML object.
    private LJCDocDataGen $DocDataGen;

    // The Generate GenData XML and HTML object.
    private LJCGenDataGen $GenDataGen;
  }
?>