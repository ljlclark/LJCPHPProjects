<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCityDataService.php
  declare(strict_types=1);
  //header("Access-Control-Allow-Origin: *");
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
  /// <summary>Web Service for City entity data.</summary>
  // Called from CityListEvents.DataRequest() for Delete(), Edit(), New().
  // Called from CityDetailEvents.DataRequest() for CommitClick().
  class LJCCityDataService
  {
    // ---------------
    // Entry Methods

    /// <summary>Service start method.</summary>
    /// <returns>The service response JSON text.</returns.
    /// <ParentGroup>Entry</ParentGroup>
    public function Run(): void
    {
      $this->ClassName = "LJCCityDataService";
      $methodName = "Run()";

      // Initialize response properties.
      $this->ServiceName = "LJCCityData";
      $this->AffectedCount = 0;
      $this->DebugText = "";
      $this->ResultCities = null;
      $this->ResultItems = [];
      $this->SQL = "";

      // Parameters are passed from a POST with JSON data.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      $request = LJC::ParseJSON($value);

      // Initialize Request properties.
      $this->Action = $request->Action;
      $this->ConfigFile = $request->ConfigFile;
      $this->ConfigName = $request->ConfigName;
      $this->KeyColumns = LJCDbColumns::Collection($request->KeyColumns);
      $this->RequestCities = Cities::Collection($request->RequestItems);
      $this->OrderByNames = $request->OrderByNames;
      $this->PropertyNames = $request->PropertyNames;
      $this->TableName = $request->TableName;

      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $this->CityManager = new CityManager($connectionValues, $this->TableName);

      $response = $this->GetResponse();
      echo($response);
    } // Run()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $retDebugText = "";

      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // Get connection values for a DataConfig name.
    private function GetConnectionValues(string $configName)
    {
      $methodName = "GetConnectionValues()";

      $configFile = $this->ConfigFile;
      $configName = "TestData";
      $retValues = DataConfigs::GetConnectionValues($configFile, $configName);
      return $retValues;
    } // GetConnectionValues()

    // ---------------
    // Response (Main) Methods

    /// <summary>Gets the Response data.</summary>
    /// <returns>The response JSON text.</returns.
    /// <ParentGroup>Response</ParentGroup>
    public function GetResponse()
    {
      $methodName = "GetResponse()";
      $retResponse = "";

      $response = $this->ClearResponseValues();
      switch (strtolower($this->Action))
      {
        case "delete":
          $this->Delete();
          $response = $this->CreateResponse();
          break;

        case "insert":
          $this->Add();
          $response = $this->CreateResponse();
          break;

        case "retrieve":
          $this->Retrieve();
          $response = $this->CreateResponse();
          break;

        case "update":
          $this->Update();
          $response = $this->CreateResponse();
          break;
      }
      if ($response->Action != "")
      {
        $retResponse = LJC::CreateJSON($response);
      }
      return $retResponse;
    } // GetResponse()

    // Inserts the new items.
    private function Add()
    {
      $methodName = "Add()";

      $this->SQL = "";
      foreach ($this->RequestCities as $city)
      {
        $dataColumns = $this->DataColumns($city);
        $this->AffectedCount = $this->CityManager->Add($dataColumns);
        $this->SQL .= "\r\n{$this->CityManager->DataManager->SQL}";
        $this->DebugText .= $this->CityManager->DebugText;
      }
    } // Add()

    // Deletes the selected items.
    private function Delete()
    {
      $methodName = "Delete()";

      $this->SQL = "";

      $this->AffectedCount = $this->CityManager->Delete($this->KeyColumns);
      $this->SQL .= "\r\n{$this->CityManager->DataManager->SQL}";
      $this->DebugText .= $this->CityManager->DebugText;
    } // Delete()

    // Get the requested item.
    private function Retrieve()
    {
      $methodName = "Retrieve()";

      $this->SQL = "";
      if ($this->OrderByNames != null)
      {
        $this->CityManager->OrderByNames = $this->OrderByNames;
      }
      $this->ResultCities = new Cities();
      $resultCity = $this->CityManager->Retrieve($this->KeyColumns
        , $this->PropertyNames);
      if ($resultCity != null)
      {
        $this->ResultCities->AddObject($resultCity);
      }
      $this->SQL = $this->CityManager->DataManager->SQL;
      $this->DebugText .= $this->CityManager->DebugText;
    } // Retrieve()

    // Updates the requested items.
    private function Update()
    {
      $methodName = "Update()";

      $this->SQL = "";
      foreach ($this->RequestCities as $city)
      {
        $keyColumns = $this->KeyColumns($city);
        $dataColumns = $this->DataColumns($city);
        $this->AffectedCount = $this->CityManager->Update($keyColumns
          , $dataColumns);
        $this->SQL .= "\r\n{$this->CityManager->DataManager->SQL}";
        $this->DebugText .= $this->CityManager->DebugText;
      }
    } // Update()

    // ---------------
    // Other Methods

    // Create the Result object.
    private function CreateResponse()
    {
      $methodName = "CreateResponse()";

      $retResponse = new stdClass();
      $retResponse->ServiceName = "LJCCityData";
      $retResponse->Action = $this->Action;
      $retResponse->AffectedCount = $this->AffectedCount;
      $retResponse->DebugText = $this->DebugText;
      $items = LJC::ItemsToArray($this->ResultCities);
      $retResponse->ResultItems = $items;
      $retResponse->SQL = $this->SQL;
      return $retResponse;
    } // CreateResponse()

    // Clear the Result properties.
    private function ClearResponseValues()
    {
      $methodName = "ClearResponseValues()";

      $action = $this->Action;
      $this->Action = "";
      $this->AffectedCount = 0;
      $this->ResultCities = new Cities();
      $this->ResultItems = [];
      $this->SQL = "";
      $retResponse = $this->CreateResponse();
      $this->Action = $action;
      return $retResponse;
    } // ClearResponseValues()

    // Create the data columns.
    private function DataColumns($city)
    {
      $methodName = "DataColumns()";

      $retDataColumns = new LJCDbColumns();

      // Insert and Update do not accept synthetic primary key.
      $retDataColumns->Add("ProvinceID", dataTypeName: "int"
        , value: strval($city->ProvinceID));
      $retDataColumns->Add("Name", value: $city->Name);
      $retDataColumns->Add("Description", value: $city->Description);
      $retDataColumns->Add("CityFlag", dataTypeName: "int"
        , value: strval($city->CityFlag));
      $retDataColumns->Add("ZipCode", value: $city->ZipCode);
      $retDataColumns->Add("District", value: strval($city->District));
      return $retDataColumns;
    } // DataColumns()

    // Create the key columns.
    private function KeyColumns($city)
    {
      $methodName = "KeyColumns()";

      $retKeyColumns = new LJCDbColumns();

      $retKeyColumns->Add("CityID", dataTypeName: "int"
        , value: strval($city->CityID));
      return $retKeyColumns;
    } // KeyColumns()

    // ---------------
    // Request Properties

    /// <summary>The data request action.</summary>
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

    /// <summary>The City request data objects.</summary>
    public Cities $RequestCities;

    /// <summary>The table name.</summary>
    public string $TableName;

    // ---------------
    // Result Properties

    /// <summary>The affected count for "Delete" and "Update".</summary>
    public int $AffectedCount;

    /// <summary>The debug text.</summary>
    public string $DebugText;

    /// <summary>The City result data objects.</summary>
    public ?Cities $ResultCities;

    /// <summary>The result data objects.</summary>
    public array $ResultItems;

    /// <summary>The executed SQL statement.</summary>
    public string $SQL;
  }


  // ***************
  /// <summary>Contains CityData web service response data.</summary>
  //  Constructor: constructor(), Clone()
  class LJCCityDataResponse
  {
    // ---------------
    // Constructor methods.

    /// <summary>Initializes the object instance.</summary>
    public function __construct($action = "", $affectedCount = 0)
    {
      $this->ClassName = "LJCCityDataService.LJCCityDataResponse";
      $methodName = "__construct()";

      $this->Action = $action;
      $this->AffectedCount = $affectedCount;
      $this->DebugText = "";
      $this->ResultItems = [];
      $this->SQL = "";
    } // __construct()

    /// <summary>Creates a clone of this object.</summary>
    public function Clone()
    {
      $methodName = "Clone()";
      $retResponse = null;

      $json = LJC::CreateJSON(this);
      $retResponse = LJC::ParseJSON($json);
      return $retResponse;
    } // Clone()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value)
    {
      $retDebugText = "";

      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // ---------------
    // Properties

    /// <summary>The data request action.</summary>
    /// <remarks>
    ///   Values: "Delete", "Insert", "Retrieve", "Update"
    /// </remarks>
    public string $Action;

    /// <summary>The affected count for "Delete" and "Update".</summary>
    public int $AffectedCount;

    /// <summary>The debug text.</summary>
    public string $DebugText;

    /// <summary>The result  data objects.</summary>
    public array $ResultItems;

    /// <summary>The executed SQL statement.</summary>
    public string $SQL;
  }
?>
