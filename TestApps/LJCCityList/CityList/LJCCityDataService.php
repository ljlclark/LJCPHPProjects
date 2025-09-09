<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCityDataService.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/ATestForm/CityList/LJCDataConfigs.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/RegionApp/City/RegionTablesDAL.php";
  // LJCDataConfigs: DataConfigs 
  // LJCDBAccessLib: LJCConnectionValues
  // CityDAL: City, Cities, CityManager

  $cityItem = new LJCCityDataService();
  $cityItem->Run();

  // ***************
  /// <group name="Entry">Entry Methods</group>
  //    Run()
  /// <group name="Response">Response Methods</group>
  //    CreateResponse(), 
  /// <summary>Web Service for City table data.</summary>
  class LJCCityDataService
  {
    // ---------------
    // Entry Methods

    /// <summary>Service start method.</summary>
    /// <returns>The service response JSON text.</returns.
    /// <ParentGroup>Entry</ParentGroup>
    public function Run(): void
    {
      // Parameters are passed from a POST with JSON data.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      //print_r("CityDataService input: {$value}");
      $itemData = json_decode($value);

      // Parse input data.
      $this->Action = $itemData->Action;
      $this->ConfigFile = $itemData->ConfigFile;
      $this->ConfigName = $itemData->ConfigName;
      $this->KeyColumns = LJCDbColumns::Collection($itemData->KeyColumns);
      $this->RequestItems = Cities::Collection($itemData->RequestItems);
      $this->OrderByNames = $itemData->OrderByNames;
      $this->PropertyNames = $itemData->PropertyNames;
      $this->TableName = $itemData->TableName;

      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $this->CityManager = new CityManager($connectionValues, $this->TableName);

      $response = $this->CreateResponse();
      echo($response);
    }  // Run()

    // Get connection values for a DataConfig name.
    private function GetConnectionValues(string $configName)
    {
      $configFile = $this->ConfigFile;
      $configName = "TestData";
      $retValues = DataConfigs::GetConnectionValues($configFile, $configName);
      return $retValues;
    }  // GetConnectionValues()

    // ---------------
    // Response Methods

    /// <summary>Creates the HTML Table.</summary>
    /// <returns>The response JSON text.</returns.
    /// <ParentGroup>Response</ParentGroup>
    public function CreateResponse()
    {
      $retResponse = "";

      $result = $this->ClearResultValues();
      switch (strtolower($this->Action))
      {
        case "delete":
          $this->Delete();
          //$result = $this->CreateResult();
          break;

        case "insert":
          $this->Add();
          //$result = $this->CreateResult();
          break;

        case "retrieve":
          $this->Retrieve();
          $result = $this->CreateResult();
          break;

        case "update":
          $this->Update();
          $result = $this->CreateResult();
          break;
      }
      if ($result->Action != "")
      {
        $retResponse = json_encode($result);
      }
      return $retResponse;
    }

    // Inserts the new items.
    private function Add()
    {

    }

    // Deletes the selected items.
    private function Delete()
    {

    }

    // Get the requested item.
    private function Retrieve()
    {
      if ($this->OrderByNames != null)
      {
        $this->CityManager->OrderByNames = $this->OrderByNames;
      }
      $resultItem = $this->CityManager->Retrieve($this->KeyColumns
        , $this->PropertyNames);
      if ($resultItem != null)
      {
        $this->ResultItems->AddObject($resultItem);
      }
      $this->SQL = $this->CityManager->DataManager->SQL;
    }

    // Updates the requested items.
    private function Update()
    {
      $this->SQL = "";
      foreach ($this->RequestItems as $city)
      {
        $keyColumns = $this->KeyColumns($city);
        $dataColumns = $this->DataColumns($city);
        $this->AffectedCount = $this->CityManager->Update($keyColumns
          , $dataColumns);
        $this->SQL .= "\r\n{$this->CityManager->DataManager->SQL}";
      }
    }

    // ---------------
    // Other Methods

    // Create the Result object.
    private function CreateResult()
    {
      $retResult = new stdClass();
      $retResult->Action = $this->Action;
      $retResult->AffectedCount = $this->AffectedCount;
      $items = LJC::ItemsToArray($this->ResultItems);
      $retResult->ResultItems = $items;
      $retResult->SQL = $this->SQL;
      return $retResult;
    }

    // Clear the Result properties.
    private function ClearResultValues()
    {
      $action = $this->Action;
      $this->Action = "";
      $this->AffectedCount = 0;
      $this->ResultItems = new Cities();
      $this->SQL = "";
      $retResult = $this->CreateResult();
      $this->Action = $action;
      return $retResult;
    }

    // Create the data columns.
    private function DataColumns($city)
    {
      $retDataColumns = new LJCDbColumns();

      $retDataColumns->Add("CityID", dataTypeName: "int"
        , value: strval($city->CityID));
      $retDataColumns->Add("ProvinceID", dataTypeName: "int"
        , value: strval($city->ProvinceID));
      $retDataColumns->Add("Name", value: $city->Name);
      $retDataColumns->Add("Description", value: $city->Description);
      // *** Testing ***
      //$retDataColumns->Add("CityFlag", dataTypeName: "bool"
      //  , value: strval($city->CityFlag));
      $retDataColumns->Add("CityFlag", dataTypeName: "int"
        , value: strval($city->CityFlag));
      $retDataColumns->Add("ZipCode", value: $city->ZipCode);
      $retDataColumns->Add("District", value: strval($city->District));
      return $retDataColumns;
    }

    // Create the key columns.
    private function KeyColumns($city)
    {
      $retKeyColumns = new LJCDbColumns();

      $retKeyColumns->Add("CityID", dataTypeName: "int"
        , value: strval($city->CityID));
      return $retKeyColumns;
    }

    // ---------------
    // Request Properties

    /// <summary>The data retrieve action.</summary>
    /// <remarks>
    ///   Values: "Delete", "Insert", "Retrieve", "Update"
    /// </remarks>
    public string $Action;

    /// <summary>The data config file name.</summary>
    public string $ConfigFile;

    /// <summary>The data config name.</summary>
    public string $ConfigName;

    /// <summary>The item unique keys.</summary>
    public LJCDbColumns $KeyColumns;

    /// <summary>The OrderBy names.</summary>
    public ?array $OrderByNames;

    /// <summary>The OrderBy names.</summary>
    public ?array $PropertyNames;

    /// <summary>The City request DataObjects.</summary>
    public Cities $RequestItems;

    /// <summary>The table name.</summary>
    public string $TableName;

    // ---------------
    // Result Properties

    /// <summary>The affected count for "Delete" and "Update".</summary>
    public int $AffectedCount;

    /// <summary>The City result DataObjects.</summary>
    public Cities $ResultItems;

    /// <summary>The executed SQL statement.</summary>
    public string $SQL;
  }
?>
