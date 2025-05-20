<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // GenCodeDocLib.php
  declare(strict_types=1);
  // Must refer to exact same file everywhere in codeline.
  // Path: LJCPHPProjectsDev/GenDoc/GenCodeDoc
  include_once "../../LJCPHPCommon/LJCDebugLib.php";
  include_once "../DocDataLib/LJCDocDataGenLib.php";
  include_once "../GenDataLib/LJCGenDataGenLib.php";
  include_once "../../GenTextLib/TextGenLib.php";
  // LJCDebugLib: LJCDebug
  // LJCDocDataGenLib: LJCDocDataGen
  // LJCGenDataGenLib: LJCGenDataGen

  /// <summary>
  ///   Contains classes to generate HTML doc for files listed
  ///   in GenCodeSourceFileList.txt.
  /// </summary>
  /// LibName: GenCodeDocLib
  // GenCodeDoc

  // Calling Code
  // GenCodeDocFiles.php

  // Main Call Tree
  // CreateFromList() public
  //   CreateFilePages() public
  //     LJCDocDataGen->CreateDocDataXMLString()
  //     LJCGenDataGen->CreateLibXMLString()

  // ***************
  // Generates HTML doc for files in GenCodeSourceFileList.txt 
  // Public: CreateFromList(), CreateFilePages()
  /// <summary>
  ///   Generates HTML doc for files in GenCodeSourceFileList.txt
  /// </summary>
  class GenCodeDoc
  {
    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("GenCodeDocLib", "GenCodeDoc"
        , "w", false);
      $this->Debug->IncludePrivate = true;
      
      $this->DocDataGen = new LJCDocDataGen();
      $this->GenDataGen = new LJCGenDataGen();
    } // __construct()

    /// <summary>Creates CodeDoc pages from source file list.</summary>
    public function CreateFromList()
    {
      $enabled = false;
      $this->Debug->BeginMethod("CreateFromList", $enabled);

      // Gets the list of files to read.
      $sourceFileListSpec = "GenCodeSourceFileList.txt";
      $inputStream = fopen($sourceFileListSpec, "r+");
      while(false == feof($inputStream))
      {
        $line = (string)fgets($inputStream);
        $this->CreateFilePages($line);
      }

      $this->Debug->EndMethod($enabled);
    }

    /// <summary>Create the HTML files.</summary>
    /// <param name="$fileSpecLine">The source file spec.</param>
    public function CreateFilePages($fileSpecLine) : void
    {
      $enabled = false;
      $this->Debug->BeginMethod("CreateFilePages", $enabled);

      $writeDocDataXML = false;
      $writeGenDataXML = false;

      $fileSpec = trim($fileSpecLine);
      if (TextGenLib::HasValue($fileSpec))
      {
        $docXMLString = $this->DocDataGen->CreateDocDataXMLString($fileSpec
          , $writeDocDataXML);
        if ($docXMLString != null)
        {
          $genXMLString = $this->GenDataGen->CreateLibXMLString($docXMLString
            , $fileSpec, $writeGenDataXML);
        }
      }

      $this->Debug->EndMethod($enabled);
    }

    // The Generate DocData XML object.
    private LJCDocDataGen $DocDataGen;

    // The Generate GenData XML and HTML object.
    private LJCGenDataGen $GenDataGen;
  }
?>