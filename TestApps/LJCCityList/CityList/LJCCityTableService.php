<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCityTableService.php
  declare(strict_types=1);
  //header("Access-Control-Allow-Origin: *");
  session_start();
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/ATestForm/CityList/LJCDataConfigs.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLTableLib.php";
  include_once "$prefix/RegionApp/City/RegionTablesDAL.php";
  // LJCDataConfigs: DataConfigs 
  // LJCDBAccessLib: LJCConnectionValues
  // LJCHTMLBuilderLib: LJCAttributes, LJCHTMLBuilder, LJCTextState
  // LJCHTMLTableLib: LJCHTMLTable
  // CityDAL: City, CityManager

  $cityTableService = new LJCCityTableService();
  $cityTableService->Request();

  // ***************
  /// <group name="Entry">Entry Methods</group>
  //    Request()
  /// <group name="Response">Response Methods</group>
  //    CreateResponse(), 
  /// <summary>Web Service to Create an HTML table from City data.
  class LJCCityTableService
  {
    // ---------------
    // Entry Methods

    /// <summary>Service start method.</summary>
    /// <returns>The service response JSON text.</returns.
    /// <ParentGroup>Entry</ParentGroup>
    public function Request(): void
    {
      $this->ClassName = "LJCCityTableService";
      $methodName = "Request()";

      $this->InitResponseProperties();

      // Parameters are passed from a POST with JSON data.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      $request = LJC::ParseJSON($value);

      // Set class properties from request data.
      $this->SetRequestProperties($request);
      $_SESSION["tableName"] = $this->TableName;

      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $this->CityManager = new CityManager($connectionValues, $this->TableName);

      $manager = $this->CityManager;
      if ($this->Limit > 0)
      {
        $manager->Limit = $this->Limit;
      }

      // Create table columns.
      $this->TableColumns = new LJCDbColumns();
      $columns = $manager->Columns($this->TableColumnNames);
      $this->TableColumns->AddObjects($columns);

      // Insert join table columns.
      if (LJC::HasElements($this->AddColumns))
      {
        foreach ($this->AddColumns as $column)
        {
          $dataColumn = LJCDbColumn::Copy($column);
          $insertIndex = $dataColumn->InsertIndex;
          $this->TableColumns->InsertObject($dataColumn, $insertIndex);
        }
      }

      $response = $this->GetResponse();
      echo($response);
    } // Request()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = "null")
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // Get connection values for a DataConfig name.
    // Called from Request()
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
      $this->DebugText = "";
      $this->HTMLTable = "";
      $this->Keys = [];
      $this->ServiceName = "LJCCityTableService";
      $this->SQL = "";
      $this->TableColumnsArray = [];
    }

    // Sets the request property values.
    private function SetRequestProperties($pageData)
    {
      $methodName = "SetRequestProperties";

      $this->Action = $pageData->Action;
      if (isset($pageData->AddTableColumns)
        && LJC::HasElements($pageData->AddTableColumns))
      {
        $this->AddColumns = $pageData->AddTableColumns;
      }
      $this->BeginKeyData = $pageData->BeginKeyData;
      $this->CityTableID = $pageData->HTMLTableID;
      $this->ConfigFile = $pageData->ConfigFile;
      $this->ConfigName = $pageData->ConfigName;
      $this->EndKeyData = $pageData->EndKeyData;
      $this->Limit = $pageData->Limit;

      $headingAttribs = $pageData->HeadingAttributes;
      $attribs = new LJCAttributes();
      $headingAttributes = LJCAttributes::ToCollection($headingAttribs);
      $this->HeadingAttributes = $headingAttributes;

      $this->PropertyNames = $pageData->PropertyNames;

      $tableAttribs = $pageData->TableAttributes;
      $attribs = new LJCAttributes();
      $tableAttributes = LJCAttributes::ToCollection($tableAttribs);
      $this->TableAttributes = $tableAttributes;

      $this->TableName = $pageData->TableName;
      $this->TableColumnNames = $pageData->TableColumnNames;
      if (null == $pageData->TableColumnNames)
      {
        $this->TableColumnNames = $this->DefaultTableColumnNames();
      }
    }

    // Creates the default HTML table column property names.
    // Called from: SetRequestProperties()
    private function DefaultTableColumnNames(): array
    {
      $methodName = "TableColumnNames()";

      $retPropertyNames = [
        City::PropertyProvinceName,
        City::PropertyName,
        City::PropertyDescription,
        City::PropertyCityFlag,
        City::PropertyZipCode,
        City::PropertyDistrict,
      ];
      return $retPropertyNames;
    } // TableColumnNames()

    // ---------------
    // Response Methods

    /// <summary>Creates the HTML Table.</summary>
    /// <returns>The response JSON text.</returns.
    /// <ParentGroup>Response</ParentGroup>
    // Called from Request().
    public function GetResponse()
    {
      $methodName = "GetResponse()";
      $retResponse = "";

      $response = $this->InitResponse();
      $result = $this->RetrieveData();
      if ($result != null)
      {
        // Create table builder with column property names.
        $tableColumnNames = $this->TableColumnNames;
        $tableBuilder = $this->HTMLTableBuilder($tableColumnNames);

        // Setup attributes.
        $tableBuilder->HeadingAttribs = $this->HeadingAttributes;
        $tableBuilder->TableAttribs = $this->TableAttributes;

        // Create HTML table with data collection.
        $textState = new LJCTextState();
        $textState->setIndentCount(2);
        $response->HTMLTable = $tableBuilder->ResultHTML($result, $textState
          , $tableColumnNames);
        // Add DebugText from called object.
        $this->DebugText .= $tableBuilder->DebugText;

        // Create Key array.
        $keyNames = $this->KeyPropertyNames();
        $keyArray = $this->ResultKeyArray($result, $keyNames);
        $response->Keys = $keyArray;

        // Create TableColumns.
        $response->TableColumnsArray = LJC::ToArray($this->TableColumns);
      }

      $response->DebugText = $this->DebugText;
      $response->SQL = $this->SQL;
      $retResponse = LJC::CreateJSON($response);
      return $retResponse;
    } // GetResponse()

    // Create the LJCHTMLTable object.
    // Called from: GetResponse()
    private function HTMLTableBuilder(?array $propertyNames)
    {
      $methodName = "HTMLTableBuilder()";

      // Create table object with column property names.
      $retTableBuilder = new LJCHTMLTable();
      //$retTableBuilder = new LJCHTMLTableBuilder();
      LJC::RemoveString($propertyNames, City::PropertyCityID);
      $retTableBuilder->ColumnNames = $propertyNames;
      return $retTableBuilder;
    } // HTMLTableBuilder()

    // Initializes the response object.
    // Called from GetResponse()
    private function InitResponse()
    {
      $methodName = "InitResponse()";

      // The definition order sets the serialization order.
      $retResponse = new stdClass();
      $retResponse->ServiceName = $this->ServiceName;
      $retResponse->Keys = [];
      $retResponse->SQL = "";
      $retResponse->HTMLTable = "";
      $retResponse->TableColumnsArray = [];
      $retResponse->DebugText = "";
      return $retResponse;
    } // SetResponse()

    // Creates the retrieve property names.
    // Called from GetResponse()
    private function KeyPropertyNames(): array
    {
      $methodName = "KeyPropertyNames()";

      $retKeyNames = [
        City::PropertyCityID,
        City::PropertyProvinceID,
        City::PropertyName,
      ];
      return $retKeyNames;
    } // KeyPropertyNames()

    // Gets the results key array.
    // Called from: GetResponse()
    private function ResultKeyArray($result, $keyNames)
    {
      $methodName = "ResultKeyArray()";

      // Create key values array.
      $dataManager = $this->CityManager->DataManager;
      $retKeyArray = $dataManager->CreateResultKeys($result, $keyNames);
      return $retKeyArray;
    } // ResultKeyArray()

    // ---------------
    // Retrieve Data Methods
    // region Retrieve Data Methods

    // Create the "Next" filter.
    // Called from RetrieveData()
    private function NextFilter($keyData, $backward = false): string
    {
      $methodName = "NextFilter()";

      $retFilter = "where";
      $retFilter .= "\r\n  (City.ProvinceID >= {$keyData->ProvinceID}";
      $filter = "\r\n   and City.Name > '{$keyData->Name}')";
      if ($backward)
      {
        $filter = "\r\n   and City.Name >= '{$keyData->Name}')";
      }
      $retFilter .= $filter;
      $retFilter .= "\r\n   or City.ProvinceID > {$keyData->ProvinceID}";
      return $retFilter;
    } // NextFilter()

    // Create the "Previous" filter.
    // Called from RetrieveData()
    private function PreviousFilter($beginKeyData): string
    {
      $methodName = "PreviousFilter()";

      $retFilter = "where";
      $retFilter .= "\r\n  (City.ProvinceID <= {$beginKeyData->ProvinceID}";
      $retFilter .= "\r\n   and City.Name < '{$beginKeyData->Name}')";
      $retFilter .= "\r\n   or City.ProvinceID < {$beginKeyData->ProvinceID}";
      return $retFilter;
    } // PreviousFilter()

    // Create the "Previous" filter.
    // Called from RetrieveData()
    private function RefreshFilter($keyData)
    {
      $methodName = "RefreshFilter()";

      $retFilter = "where";
      $retFilter .= "\r\n  (City.ProvinceID >= {$keyData->ProvinceID}";
      $filter = "\r\n   and City.Name >= '{$keyData->Name}')";
      $retFilter .= $filter;
      $retFilter .= "\r\n   or City.ProvinceID > {$keyData->ProvinceID}";
      return $retFilter;
    } // RefreshFilter()

    // Retrieve data result.
    // Called from GetResponse().
    private function RetrieveData()
    {
      $methodName = "RetrieveData()";
      $retResult = null;

      $manager = $this->CityManager;
      $joins = $manager->CreateJoins();
      $propertyNames = $this->PropertyNames;

      switch ($this->Action)
      {
        case "Next":
          $filter = $this->NextFilter($this->EndKeyData);
          $manager->OrderByNames = array("ProvinceID", "Name");
          $retResult = $manager->LoadResult(null, $propertyNames, $joins
            , $filter);
          $this->SQL = $manager->DataManager->SQL;
          break;

        case "Previous":
          // Load descending.
          $filter = $this->PreviousFilter($this->BeginKeyData);
          $manager->OrderByNames = array("ProvinceID desc"
            , "Name desc");
          $retResult = $manager->LoadResult(null, $propertyNames, $joins
            , $filter);
          $this->SQL = $manager->DataManager->SQL;

          // Flip result.
          $flipResult = [];
          $count = count($retResult);
          for ($index = $count - 1; $index >= 0; $index--)
          {
            $flipResult[] = $retResult[$index];
          }
          $retResult = $flipResult;
          break;

        default:
          $filter = "";
          if ($this->BeginKeyData->ProvinceID != 0)
          {
            $filter = $this->RefreshFilter($this->BeginKeyData);
          }
          $manager->OrderByNames = array("ProvinceID", "Name");
          $retResult = $manager->LoadResult(null, $propertyNames, $joins
            , $filter);
          $this->SQL = $manager->DataManager->SQL;
          break;
      }
      return $retResult;
    } // RetrieveData()
    // endregion

    // ---------------
    // Request Properties

    /// <summary>The data retrieve action.</summary>
    /// <remarks>
    ///   Values: "Next", "Previous", "Top"?, "Bottom"?, "First"?, "Last"?
    /// </remarks>
    public string $Action;

    /// <summary>Column definitions to add to the data manager.</summary>
    public array $AddColumns = [];

    /// <summary>The find key values for the first table row data.</summary>
    /// <remarks> Properties: ProvinceID, Name</remarks>
    public object $BeginKeyData;

    /// <summary>The HTML city table element ID.</summary>
    public string $CityTableID;

    /// <summary>The data config file name.</summary>
    public string $ConfigFile;

    /// <summary>The data config name.</summary>
    public string $ConfigName;

    /// <summary>The find key values for the last table row data.</summary>
    /// <remarks> Properties: ProvinceID, Name</remarks>
    public object $EndKeyData;

    /// <summary>The heading attributes.</summary>
    public object $HeadingAttributes;

    /// <summary>The number of rows per page.</summary>
    public int $Limit;

    /// <summary>The data object property names.</summary>
    public ?array $PropertyNames;

    /// <summary>The table attributes.</summary>
    public object $TableAttributes;

    /// <summary>The HTML table column property names.</summary>
    public array $TableColumnNames;

    /// <summary>The source table name.</summary>
    public string $TableName;

    // ---------------
    // Response Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;

    /// <summary>The HTML table text.</summary>
    public string $HTMLTable;

    /// <summary>The table item keys.</summary>
    public array $Keys;

    /// <summary>The service name.</summary>
    public string $ServiceName;

    /// <summary>The SQL statement.</summary>
    public string $SQL;

    /// <summary>The HTML Table column definitions.
    public array $TableColumnsArray;

    // ---------------
    // Other Properties

    /// <summary>The CityManager object.</summary>
    public CityManager $CityManager;

    /// <summary>The HTML Table column definition collection.
    public LJCDbColumns $TableColumns;
  }
?>