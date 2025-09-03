<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCityDataService.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/ATestForm/CityList/LJCDataConfigs.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/RegionApp/City/CityDAL.php";
  // LJCDataConfigs: DataConfigs 
  // LJCDBAccessLib: LJCConnectionValues
  // CityDAL: City, Cities, CityManager

  $cityItem = new LJCCityDataService();
  $cityItem->Run();

  // ***************
  /// <group name="Entry">Entry Methods</group>
  //    Run()
  /// <group name="Response">Entry Methods</group>
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

      // Convert array of single object arrays to LJCDbColumns
      $dataColumns = $itemData->KeyColumns;
      $keyColumns = new LJCDbColumns();
      foreach ($dataColumns as $objDataColumn)
      {
        // Create typed object from stdClass.
        $keyColumn = LJCCityDataService::Copy($objDataColumn[0]);
        $keyColumns->AddObject($keyColumn);
      }
      $this->KeyColumns = $keyColumns;

      //$this->RequestItems = $itemData->RequestItems;
      $this->OrderByNames = $itemData->OrderByNames;
      $this->PropertyNames = $itemData->PropertyNames;
      $this->TableName = $itemData->TableName;

      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $this->CityManager = new CityManager($connectionValues, $this->TableName);

      $response = $this->CreateResponse();
      echo($response);
    }  // Run()

    public static function Copy($dataColumn)
    {
      $retDataColumn = null;

      if (property_exists($dataColumn, "PropertyName"))
      {
        $retDataColumn = new LJCDbColumn($dataColumn->PropertyName);

        foreach ($dataColumn as $propertyName => $value)
        {
          if (property_exists($dataColumn, $propertyName))
          {
            $retDataColumn->$propertyName = $value;
          }
        }
      }
      return $retDataColumn;
    }

    // Get connection values for a DataConfig name.
    // Called from Run()
    private function GetConnectionValues(string $configName)
    {
      $configFile = $this->ConfigFile;
      $configName = "TestData";
      $retValues = DataConfigs::GetConnectionValues($configFile, $configName);
      return $retValues;
    }  // GetConnectionValues()

    // ---------------
    // Create Response (Main) Methods

    /// <summary>Creates the HTML Table.</summary>
    /// <returns>The response JSON text.</returns.
    /// <ParentGroup>Response</ParentGroup>
    // Called from Run().
    public function CreateResponse()
    {
      $retResponse = "";

      $result = $this->ClearResultValues();
      switch (strtolower($this->Action))
      {
        case "delete":
          break;

        case "insert":
          break;

        case "retrieve":
          $this->Retrieve();
          $result = $this->CreateResult();
          break;

        case "update":
          break;
      }
      if ($result->Action != "")
      {
        $retResponse = json_encode($result);
      }
      return $retResponse;
    }

    /// <summary>Get the requested item.</summary>
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

    /// <summary>Copy collection items to an indexed array.</summary>
    private function ItemsToArray($items)
    {
      $retArray = [];

      foreach ($items as $item)
      {
        $retArray[] = $item;
      }
      return $retArray;
    }

    /// <summary>Clear the Result properties.</summary>
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

    /// <summary>Create the Result object.</summary>
    private function CreateResult()
    {
      $retResult = new stdClass();
      $retResult->Action = $this->Action;
      $retResult->AffectedCount = $this->AffectedCount;
      //$retResult->ResultItems = $this->ResultItems;
      $retResult->ResultItems = $this->ItemsToArray($this->ResultItems);
      $retResult->SQL = $this->SQL;
      return $retResult;
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

    /// <summary>The City request DataObjects.</summary>
    public Cities $RequestItems;

    /// <summary>The OrderBy names.</summary>
    public ?array $OrderByNames;

    /// <summary>The OrderBy names.</summary>
    public ?array $PropertyNames;

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

    // A data object is a user defined data type that contains a group of
    // related values. Each contained value has a unique name and a data type.
    // It is a convenient way to represent a data entity.

    // Data objects simplify the organization of related data, creating a
    // structured and consistent way to manage information within an
    // application.

    // In object-oriented programming, a data object may also contain
    // methods (procedures) that operate on or access the data it contains. 

    // A data object can be reused across different parts of an application,
    // which saves development effort and ensures data consistency. 
  }
?>
