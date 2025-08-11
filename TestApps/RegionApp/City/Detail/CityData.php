<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // CityData.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/RegionApp/RegionConfigLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  include_once "$prefix/RegionApp/City/CityDAL.php";

  $main = new LJCMain();
  $main->Run();

  // Manages the City data.
  class LJCMain
  {
    // Initializes a class instance.
    public function __construct()
    {
      $this->CityManager = null;
    }

    // ---------------
    // Public Methods

    /// <summary></summary>
    public function Run()
    {
      $connectionValues = RegionConfig::GetConnectionValues();
      $tableName = $_SESSION["tableName"];
      $this->CityManager = new CityManager($connectionValues, $tableName);

      $city = $this->GetPostValues();
      $listAction = LJC::Scrub($_POST["listAction"]);
      switch ($listAction)
      {
        case "Add":
          $this->Add($city);
          break;

        case "Delete":
          $this->Delete($city->CityID);
          break;

        case "Update":
          $this->Update($city);
          break;
      }
      header ("Location: ../List/CityList.php");
    }

    // ---------------
    // Private Methods

    // 
    private function Add(City $city) : int
    {
      $retValue = 0;

      // Create Data columns.
      $dataColumns = new LJCDbColumns();
      $dataColumns->Add("ProvinceID", dataTypeName: "int"
        , value: $city->ProvinceID);
      $dataColumns->Add("Name", value: $city->Name);
      $dataColumns->Add("Description", value: $city->Description);
      $dataColumns->Add("CityFlag", dataTypeName: "bool", value: $city->CityFlag);
      $dataColumns->Add("ZipCode", value: $city->ZipCode);
      $dataColumns->Add("District", value: $city->District);

      // Add record.
      $retValue = $this->CityManager->Add($dataColumns);
      return $retValue;
    }

    // 
    private function Delete(int $id) : int
    {
      $retValue = 0;

      // Create Key columns.
      $keyColumns = new LJCDbColumns();
      $keyColumns->Add("CityID", dataTypeName: "int", value: strval($id));

      // Delete record.
      $retValue = $this->CityManager->Delete($keyColumns);
      return $retValue;
    }

    // Gets the posted CityDetail cityForm values.
    private function GetPostValues() : City
    {
      $city = new City();
      $city->CityID = (int)$_POST["cityID"];
      $city->ProvinceID = (int)$_POST["provinceID"];
      $city->Name = LJC::Scrub($_POST["name"]);
      $city->Description = LJC::Scrub($_POST["description"]);
      $city->CityFlag = LJC::ToBoolInt($_POST["cityFlag"]);
      $city->ZipCode = LJC::Scrub($_POST["zipCode"]);
      $city->District = (int)$_POST["district"];
      return $city;
    }

    // 
    private function Update(City $city) : int
    {
      $retValue = 0;

      // Create Key columns.
      $keyColumns = new LJCDbColumns();
      $keyColumns->Add("CityID", dataTypeName: "int"
        , value: strval($city->CityID));

      // Create Data columns.
      $dataColumns = new LJCDbColumns();
      $dataColumns->Add("ProvinceID", dataTypeName: "int"
        , value: strval($city->ProvinceID));
      $dataColumns->Add("Name", value: $city->Name);
      $dataColumns->Add("Description", value: $city->Description);
      // *** Testing ***
      //$dataColumns->Add("CityFlag", dataTypeName: "bool"
      //  , value: strval($city->CityFlag));
      $dataColumns->Add("CityFlag", dataTypeName: "int"
        , value: strval($city->CityFlag));
      $dataColumns->Add("ZipCode", value: $city->ZipCode);
      $dataColumns->Add("District", value: strval($city->District));

      // Update record.
      $retValue = $this->CityManager->Update($keyColumns, $dataColumns);
      return $retValue;
    }

    // ---------------
    // Properties

    private ?CityManager $CityManager;
  }
?>