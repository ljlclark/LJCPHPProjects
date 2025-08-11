<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // CityDetail.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  include_once "$prefix/RegionApp/RegionConfigLib.php";
  include_once "$prefix/RegionApp/City/CityDAL.php";
  // LJCCommonLib: LJC
  // LJCDBAccessLib: LJCDbColumns
  // LJCTextLib: LJCWriter
  // RegionConfigLib: RegionConfig
  // CityDAL: Cities, City, CityManager

  echo file_get_contents("CityDetailHead.html");
  $main = new LJCMain();
  $main->Run();
  echo file_get_contents("CityDetailTail.html");

  /// <summary>Contains methods to create the CityDetail HTML.</summary>
  class LJCMain
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->CityManager = null;
    }

    // ---------------
    // Public Methods

    /// <summary>Creates the CityDetail HTML.</summary>
    public function Run()
    {
      // Get form values.
      $verify = LJC::Scrub($_POST["verify"]);
      if ($verify != null)
      {
        exit();
      }

      $connectionValues = RegionConfig::GetConnectionValues();
      $tableName = $_SESSION["tableName"];
      $this->CityManager = new CityManager($connectionValues, $tableName);

      $city = null;
      $cityID = (int)$_POST["cityID"];
      $listAction = LJC::Scrub($_POST["listAction"]);
      $commitType = null;
      switch ($listAction)
      {
        case "Add":
          $commitType = "Add";
          break;

        case "Delete":
          $commitType = "Delete";
          $city = $this->Retrieve($cityID);
          break;

        case "Update":
          $commitType = "Update";
          $city = $this->Retrieve($cityID);
          break;
      }
      $this->SetFormValues($city, $commitType);
    }

    // ---------------
    // Private Methods

    // Retrieve the City record.
    private function Retrieve(int $id) : City
    {
      $retValue = null;

      // Create Key columns.
      $keyColumns = new LJCDbColumns();
      $keyColumns->Add("CityID", dataTypeName: "int", value: strval($id));

      // Retrieve record.
      $retValue = $this->CityManager->Retrieve($keyColumns);
      return $retValue;
    }

    // Creates the Javascript to set the cityForm values.
    private function SetFormValues(?City $city, string $commitType)
    {
      $listAction = LJC::Scrub($_POST["listAction"]);
      LJCWriter::WriteLine("<script>");
      LJCWriter::WriteLine("  SetValue('listAction', '$listAction');");
      LJCWriter::WriteLine("  SetValue('commit', '$commitType');");
      if ($city != null)
      {
        LJCWriter::WriteLine("  SetValue('cityID', '$city->CityID');");
        LJCWriter::WriteLine("  SetValue('provinceID', '$city->ProvinceID');");
        LJCWriter::WriteLine("  SetValue('name', '$city->Name');");
        LJCWriter::WriteLine("  SetValue('description', '$city->Description');");
        LJCWriter::WriteLine("  SetValue('cityFlag', '$city->CityFlag');");
        LJCWriter::WriteLine("  SetValue('zipCode', '$city->ZipCode');");
        LJCWriter::WriteLine("  SetValue('district', '$city->District');");
      }
      // *** Testing ***
      LJCWriter::WriteLine("  debug.innerText = `|$city->CityFlag|-|\${cityFlag.value}|`;");

      LJCWriter::WriteLine("</script>");
    }

    // ---------------
    // Properties

    private ?CityManager $CityManager;
  }
?>