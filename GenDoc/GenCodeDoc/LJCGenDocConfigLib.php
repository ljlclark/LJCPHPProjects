<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCGenDocConfigLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  // LJCCommonLib: LJCCommon

  // Contains Class for GenDoc configuration.
  /// <include path='items/LJCDocDataGenLib/*' file='Doc/LJCDocDataGenLib.xml'/>
  /// LibName: LJCDocDocConfigLib
  // The contained classes:
  //  LJCGenDocConfig

  // ***************
  // Public: CreateDocDataXMLString(), ProcessCode()
  /// <summary>
  ///		Provides methods to generate DocData XML files from a code file.
  /// </summary>
  class LJCGenDocConfig
  {
    // ---------------
    // Constructors - LJCDocDataGen

    /// <summary>
    ///		Initializes a class instance.
    ///		And More.
    /// </summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCDocDataGenLib", "LJCDocDataGen"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->DocDataXMLPath = "";
      $this->GenDataXMLPath = "";
      $this->OutputPath = "";
      $this->WriteDocDataXML = false;
      $this->WriteGenDataXML = false;
    } // __construct()

    // ---------------
    // Public Methods - LJCGenDocConfig

    // Sets the property values.
    public function SetProperties(string $line) : bool
    {
      $retIsFile = true;

      if (str_contains($line, ":"))
      {
        $retIsFile = false;
        $tokens = explode(":", $line);
        if (2 == count($tokens))
        {
          $name = trim($tokens[0]);
          $value = trim($tokens[1]);
          switch(strtolower($name))
          {
            case "outputpath":
              $this->OutputPath = $value;
              break;
            case "docdataxmlpath":
              $this->DocDataXMLPath = $value;
              break;
            case "gendataxmlpath":
              $this->GenDataXMLPath = $value;
              break;
            case "writedocdataxml":
              $this->WriteDocDataXML = LJC::ToBool($value);
              break;
            case "writegendataxml":
              $this->WriteGenDataXML = LJC::ToBool($value);
            break;
          }
        }
      }
      return $retIsFile;
    }

    // The DocDataXML target path.
    public string $DocDataXMLPath;

    // The GenDataXML target path.
    public string $GenDataXMLPath;

    // The Output target path.
    public string $OutputPath;

    // Indicates if the DocDataXML will be written to a file.
    public bool $WriteDocDataXML;

    // Indicates if the GenDataXML will be written to a file.
    public bool $WriteGenDataXML;
  }
?>
