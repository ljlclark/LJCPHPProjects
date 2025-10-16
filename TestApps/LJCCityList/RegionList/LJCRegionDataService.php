<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCRegionDataService.php
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

  $regionDataService = new LJCRegionDataService();
  $regionDataService->Request();

  // ***************
  /// <group name="Entry">Entry Methods</group>
  //    Request()
  /// <group name="Response">Response Methods</group>
  //    CreateResponse(), 
  /// <summary>Web Service for City entity data.</summary>
  // Called from CityListEvents.DataRequest() for RegionDelete(), RegionEdit(), RegionNew().
  // Called from RegionDetailEvents.DataRequest() for CommitClick().
  class LJCRegionDataService
  {
    // ---------------
    // Entry Methods

    /// <summary>Service start method.</summary>
    /// <returns>The service response JSON text.</returns.
    /// <ParentGroup>Entry</ParentGroup>
    public function Request(): void
    {
      $this->ClassName = "LJCRegionDataService";
      $methodName = "Request()";
      $this->DebugText = "";

      $this->InitResponseProperties();

      // Parameters are passed from a POST with JSON data.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      $request = LJC::ParseJSON($value);

      // Set class properties from request data.
      $this->SetRequestProperties($request);

      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $this->RegionManager = new RegionManager($connectionValues, $this->TableName);

      $response = $this->GetResponse();
      echo($response);
    } // Run()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // Get connection values for a DataConfig name.
    // Called from Run()
    private function GetConnectionValues(string $configName)
    {
      $methodName = "GetConnectionValues()";

      $configFile = $this->ConfigFile;
      $configName = "TestData";
      $retValues = DataConfigs::GetConnectionValues($configFile, $configName);
      return $retValues;
    } // GetConnectionValues()

    // Initializes the response properties.
    private function InitResponseProperties()
    {
      $this->AffectedCount = 0;
      $this->ResultRegions = null;
      $this->ResultItems = [];
      $this->ServiceName = "LJCRegionDataService";
      $this->SQL = "";
    }

    // Sets the request property values.
    private function SetRequestProperties($request)
    {
      $methodName = "SetRequestProperties";

      $this->Action = $request->Action;
      $this->ConfigFile = $request->ConfigFile;
      $this->ConfigName = $request->ConfigName;
      $this->KeyColumns = LJCDbColumns::ToCollection($request->KeyColumns);
      $this->OrderByNames = $request->OrderByNames;
      $this->PropertyNames = $request->PropertyNames;
      $this->RequestRegions = Regions::ToCollection($request->RequestItems);
      $this->TableName = $request->TableName;
    }

    // ---------------
    // Response Methods

    /// <summary>Gets the Response data.</summary>
    /// <returns>The response JSON text.</returns.
    /// <ParentGroup>Response</ParentGroup>
    // Called from Request().
    public function GetResponse()
    {
      $methodName = "GetResponse()";
      $retResponse = "";

      // Gets response without data but preserves existing Action.
      $response = $this->ClearResponseValues();
      switch (strtolower($this->Action))
      {
        case "insert":
          $this->Add();
          $response = $this->CreateResponse();
          break;

        case "delete":
          $this->Delete();
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
        $retResponseJSON = LJC::CreateJSON($response);
      }
      return $retResponseJSON;
    } // GetResponse()

    // Inserts the new items.
    // Called from GetResponse().
    private function Add()
    {
      $methodName = "Add()";

      $this->SQL = "";
      foreach ($this->RequestRegions as $region)
      {
        $dataColumns = $this->DataColumns($region);
        $this->AffectedCount = $this->RegionManager->Add($dataColumns);
        $this->SQL .= "\r\n{$this->RegionManager->DataManager->SQL}";
        $this->DebugText .= $this->RegionManager->DebugText;
      }
    } // Add()

    // Deletes the selected items.
    // Called from GetResponse().
    private function Delete()
    {
      $methodName = "Delete()";

      $this->SQL = "";

      $this->AffectedCount = $this->RegionManager->Delete($this->KeyColumns);
      $this->SQL .= "\r\n{$this->RegionManager->DataManager->SQL}";
      $this->DebugText .= $this->RegionManager->DebugText;
    } // Delete()

    // Get the requested item.
    // Called from GetResponse().
    private function Retrieve()
    {
      $methodName = "Retrieve()";

      $this->SQL = "";
      if ($this->OrderByNames != null)
      {
        $this->RegionManager->OrderByNames = $this->OrderByNames;
      }
      $this->ResultRegions = new Regions();
      //$joins = $this->RegionManager->CreateJoins();
      $resultRegion = $this->RegionManager->Retrieve($this->KeyColumns
        , $this->PropertyNames, $joins);
      if ($resultRegion != null)
      {
        $this->ResultRegions->AddObject($resultRegion);
      }
      $this->SQL = $this->RegionManager->DataManager->SQL;
      $this->DebugText .= $this->RegionManager->DebugText;
    } // Retrieve()

    // Updates the requested items.
    // Called from GetResponse().
    private function Update()
    {
      $methodName = "Update()";

      $this->SQL = "";

      // Intended to support updating multiple records.
      $this->ResultRegions = new Regions();
      foreach ($this->RequestRegions as $region)
      {
        $keyColumns = $this->KeyColumns($region);
        $dataColumns = $this->DataColumns($region);
        $this->AffectedCount += $this->RegionManager->Update($keyColumns
          , $dataColumns);
        // ***** ToDo: Why always zero?
        //if ($this->AffectedCount > 0)
        //{
          $this->ResultRegions->AddObject($region);
        //}
        $this->SQL .= "\r\n{$this->RegionManager->DataManager->SQL}";
        $this->DebugText .= $this->RegionManager->DebugText;
      }
    } // Update()

    // ---------------
    // Other Methods

    // Create the Result object.
    private function CreateResponse()
    {
      $methodName = "CreateResponse()";

      $retResponse = new stdClass();
      //$retResponse->ServiceName = "LJCRegionDataService";
      $retResponse->ServiceName = $this->ServiceName;
      $retResponse->Action = $this->Action;
      $retResponse->AffectedCount = $this->AffectedCount;
      $arrItems = LJC::ToArray($this->ResultRegions);
      $retResponse->ResultItems = $arrItems;
      if (LJC::HasValue($this->Action))
      {
      }
      $retResponse->SQL = $this->SQL;
      $retResponse->DebugText = $this->DebugText;
      return $retResponse;
    } // CreateResponse()

    // Clear the Result properties.
    private function ClearResponseValues()
    {
      $methodName = "ClearResponseValues()";

      $action = $this->Action;

      $this->Action = "";
      $this->AffectedCount = 0;
      $this->ResultRegions = new Regions();
      $this->ResultItems = [];
      $this->SQL = "";
      $retResponse = $this->CreateResponse();

      $this->Action = $action;
      return $retResponse;
    } // ClearResponseValues()

    // Create the data columns.
    private function DataColumns($region)
    {
      $methodName = "DataColumns()";

      $retDataColumns = new LJCDbColumns();

      // Insert and Update do not accept synthetic primary key.
      $retDataColumns->Add("Number", value: $region->Number);
      $retDataColumns->Add("Name", value: $region->Name);
      $retDataColumns->Add("Description", value: $region->Description);
      return $retDataColumns;
    } // DataColumns()

    // Create the key columns.
    private function KeyColumns($region)
    {
      $methodName = "KeyColumns()";

      $retKeyColumns = new LJCDbColumns();

      $retKeyColumns->Add("RegionID", dataTypeName: "int"
        , value: strval($region->RegionID));
      return $retKeyColumns;
    } // KeyColumns()

    // ---------------
    // Request Properties

    // The data request action.
    /// <include path='items/Action/*' file='Doc/LJCCityDataService.xml'/>
    public string $Action;

    /// <summary>The class name for debugging.</summary>
    public string $ClassName;

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
    public ?Cities $RequestRegions;

    /// <summary>The table name.</summary>
    public string $TableName;

    // ---------------
    // Result Properties

    /// <summary>The affected count for "Delete" and "Update".</summary>
    public int $AffectedCount;

    /// <summary>The debug text.</summary>
    public string $DebugText;

    /// <summary>The City result data objects.</summary>
    public ?Cities $ResultRegions;

    /// <summary>The result data objects.</summary>
    public array $ResultItems;

    /// <summary>The executed SQL statement.</summary>
    public string $SQL;
  }


  // ***************
  /// <summary>Contains RegionData web service response data.</summary>
  //  Constructor: constructor(), Clone()
  class LJCRegionDataResponse
  {
    // ---------------
    // Constructor methods.

    /// <summary>Initializes the object instance.</summary>
    public function __construct($action = "", $affectedCount = 0)
    {
      $this->ClassName = "LJCRegionDataService.LJCRegionDataResponse";
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
