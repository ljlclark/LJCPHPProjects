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

  $cityTable = new LJCCityTableService();
  $cityTable->Run();

  // ***************
  /// <group name="Entry">Entry Methods</group>
  //    Run()
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
    public function Run(): void
    {
      $this->ClassName = "LJCCityTableService";
      $methodName = "Run()";

      // Initialize response properties.
      $this->DebugText = "";

      // Parameters are passed from a POST with JSON data.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      $pageData = LJC::ParseJSON($value);

      // Initialize Request properties.
      $this->Action = $pageData->Action;
      $this->BeginKeyData = $pageData->BeginKeyData;
      $this->ConfigFile = $pageData->ConfigFile;
      $this->ConfigName = $pageData->ConfigName;
      $this->EndKeyData = $pageData->EndKeyData;
      $this->Limit = $pageData->Limit;
      // *** Begin *** Add
      $this->PropertyNames = $pageData->PropertyNames;
      if (null == $pageData->PropertyNames)
      {
        $this->PropertyNames = $this->TablePropertyNames();
      }
      // *** End ***

      $this->CityTableID = "cityTableItem";
      $this->TableName = City::TableName;
      $this->SQL = "";
      $_SESSION["tableName"] = $this->TableName;

      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $this->CityManager = new CityManager($connectionValues, $this->TableName);
      $manager = $this->CityManager;
      if ($this->Limit > 0)
      {
        $manager->Limit = $this->Limit;
      }
      // *** Add ***
      // Get table column definitions.
      $this->DataColumns = $manager->Columns($this->PropertyNames);

      $response = $this->GetResponse();
      echo($response);
    } // Run()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = "null")
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

    // ---------------
    // Response Methods

    /// <summary>Creates the HTML Table.</summary>
    /// <returns>The response JSON text.</returns.
    /// <ParentGroup>Response</ParentGroup>
    // Called from Run().
    public function GetResponse()
    {
      $methodName = "GetResponse()";
      $retResponse = "";

      $response = $this->InitResponse();
      $result = $this->RetrieveData();
      if ($result != null)
      {
        // Create table builder with column property names.
        // *** Change ***
        $propertyNames = $this->PropertyNames;
        $tableBuilder = $this->HTMLTableBuilder($propertyNames);

        // Setup attributes.
        $tableBuilder->TableAttribs = $this->GetTableAttribs();
        $tableBuilder->HeadingAttribs = $this->GetHeadingAttribs();

        // Create HTML table with data collection.
        $textState = new LJCTextState();
        $textState->IndentCount = 2;
        $response->HTMLTable = $tableBuilder->ResultHTML($result, $textState
          , $propertyNames);

        // Create Key array.
        $keyNames = $this->KeyPropertyNames();
        $keyArray = $this->ResultKeyArray($result, $keyNames);

        $response->Keys = $keyArray;
        // *** Begin *** Add
        $response->DebugText = $this->DebugText;
        $response->TableColumns = LJC::ItemsToArray($this->DataColumns);
        // *** End ***
        $response->SQL = $this->SQL;
      }
      $retResponse = LJC::CreateJSON($response);
      return $retResponse;
    } // GetResponse()

    // Gets the heading attributes.
    // Called from: GetResponse()
    private function GetHeadingAttribs(): LJCAttributes
    {
      $methodName = "GetHeadingAttribs()";

      $retAttribs = new LJCAttributes();
      $style = "background-color: lightsteelblue";
      $retAttribs->Add("style", $style);
      return $retAttribs;
    } // GetHeadingAttribs()

    // Gets the table attributes.
    // Called from: GetResponse()
    private function GetTableAttribs()
    {
      $methodName = "GetTableAttribs()";

      // Root TextState object.
      $textState = new LJCTextState();

      // Setup table attributes.
      $hb = new LJCHTMLBuilder($textState);
      $className = null;
      $id = $this->CityTableID;
      $retAttribs = $hb->Attribs($className, $id);

      // Centers to page.
      $style = "margin: auto";
      $retAttribs->Add("style", $style);

      // border = 1, cellSpacing = 0, cellPadding = 2, className = null
      //   , id = null
      $retAttribs->Append($hb->TableAttribs());
      return $retAttribs;
    } // GetTableAttribs()

    // Create the LJCHTMLTable object.
    // Called from: GetResponse()
    private function HTMLTableBuilder(?array $propertyNames)
    {
      $methodName = "HTMLTableBuilder()";

      // Create table object with column property names.
      $retTableBuilder = new LJCHTMLTable();
      //$retTableBuilder = new LJCHTMLTableBuilder();
      LJC::RemoveString($propertyNames, City::ColumnCityID);
      $retTableBuilder->ColumnNames = $propertyNames;
      return $retTableBuilder;
    } // HTMLTableBuilder()

    // Creates the retrieve property names.
    // Called from GetResponse()
    private function KeyPropertyNames(): array
    {
      $methodName = "KeyPropertyNames()";

      $retKeyNames = [
        City::ColumnCityID,
        City::ColumnProvinceID,
        City::ColumnName,
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

    // Initializes the response object.
    // Called from GetResponse()
    private function InitResponse()
    {
      $methodName = "InitResponse()";

      $retResponse = new stdClass();
      $retResponse->ServiceName = "LJCCityTable";
      $retResponse->Keys = [];
      $retResponse->SQL = "";
      $retResponse->HTMLTable = "";
      // *** Begin *** Add
      $retResponse->DebugText = "";
      $retResponse->TableColumns = [];
      // *** End ***
      return $retResponse;
    } // SetResponse()

    // Creates the table property names.
    // Called from: GetResponse()
    private function TablePropertyNames(): array
    {
      $methodName = "TablePropertyNames()";

      $retPropertyNames = [
        City::ColumnProvinceID,
        City::ColumnName,
        City::ColumnDescription,
        City::ColumnCityFlag,
        City::ColumnZipCode,
        City::ColumnDistrict,
      ];
      return $retPropertyNames;
    } // TablePropertyNames()

    // ---------------
    // Retrieve Data Methods

    // Create the "Next" filter.
    // Called from RetrieveData()
    private function NextFilter($keyData, $backward = false): string
    {
      $methodName = "NextFilter()";

      $retFilter = "where";
      $retFilter .= "\r\n  (ProvinceID >= {$keyData->ProvinceID}";
      $filter = "\r\n   and Name > '{$keyData->Name}')";
      if ($backward)
      {
        $filter = "\r\n   and Name >= '{$keyData->Name}')";
      }
      $retFilter .= $filter;
      $retFilter .= "\r\n  or ProvinceID > {$keyData->ProvinceID}";
      return $retFilter;
    } // NextFilter()

    // Create the "Previous" filter.
    // Called from RetrieveData()
    private function PreviousFilter($beginKeyData): string
    {
      $methodName = "PreviousFilter()";

      $retFilter = "where";
      $retFilter .= "\r\n  (ProvinceID <= {$beginKeyData->ProvinceID}";
      $retFilter .= "\r\n   and Name < '{$beginKeyData->Name}')";
      $retFilter .= "\r\n  or ProvinceID < {$beginKeyData->ProvinceID}";
      return $retFilter;
    } // PreviousFilter()

    // Create the "Previous" filter.
    // Called from RetrieveData()
    private function RefreshFilter($keyData)
    {
      $methodName = "RefreshFilter()";

      $retFilter = "where";
      $retFilter .= "\r\n  (ProvinceID >= {$keyData->ProvinceID}";
      $filter = "\r\n   and Name >= '{$keyData->Name}')";
      $retFilter .= $filter;
      $retFilter .= "\r\n  or ProvinceID > {$keyData->ProvinceID}";
      return $retFilter;
    } // RefreshFilter()

    // Retrieve data result.
    // Called from GetResponse().
    private function RetrieveData()
    {
      $methodName = "RetrieveData()";
      $retResult = null;

      //$keyColumns = null;
      $manager = $this->CityManager;
      switch ($this->Action)
      {
        case "Next":
          $filter = $this->NextFilter($this->EndKeyData);
          $manager->OrderByNames = array("ProvinceID", "Name");
          $retResult = $manager->LoadResult(null, filter: $filter);
          $this->SQL = $manager->DataManager->SQL;
          break;

        case "Previous":
          // Load descending.
          $filter = $this->PreviousFilter($this->BeginKeyData);
          $manager->OrderByNames = array("ProvinceID desc"
            , "Name desc");
          $retResult = $manager->LoadResult(null, filter: $filter);
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
          $retResult = $manager->LoadResult(null, filter: $filter);
          $this->SQL = $manager->DataManager->SQL;
          break;
      }
      return $retResult;
    } // RetrieveData()

    // ---------------
    // Request Properties

    /// <summary>The data retrieve action.</summary>
    /// <remarks>
    ///   Values: "Next", "Previous", "Top", "Bottom", "First"?, "Last"?
    /// </remarks>
    public string $Action;

    /// <summary>The find key values for the first table row data.</summary>
    /// <remarks> Properties: ProvinceID, Name</remarks>
    public object $BeginKeyData;

    /// <summary>The CityManager object.</summary>
    public CityManager $CityManager;

    /// <summary>The data config file name.</summary>
    public string $ConfigFile;

    /// <summary>The data config name.</summary>
    public string $ConfigName;

    /// <summary>The find key values for the last table row data.</summary>
    /// <remarks> Properties: ProvinceID, Name</remarks>
    public object $EndKeyData;

    /// <summary>The number of rows per page.</summary>
    public int $Limit;

    /// <summary>The db table name.</summary>
    public string $TableName;

    // ---------------
    // Result Properties

    // *** Add ***
    /// <summary>The HTML Table column definitions.
    public LJCDbColumns $DataColumns;

    /// <summary>The debug text.</summary>
    public string $DebugText;

    /// <summary>The SQL statement.</summary>
    public string $SQL;

    // *** Add ***
    /// <summary>The table columns array.</summary>
    public array $TableColumns;
  }
?>