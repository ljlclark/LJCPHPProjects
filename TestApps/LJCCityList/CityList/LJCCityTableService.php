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
  include_once "$prefix/RegionApp/City/CityDAL.php";
  // LJCDataConfigs: DataConfigs 
  // LJCDBAccessLib: LJCConnectionValues
  // LJCHTMLBuilderLib: LJCAttribute, LJCAttributes, LJCHTMLBuilder
  //   , LJCTextState
  // CityDAL: 

  $cityTable = new LJCCityTableService();
  $cityTable->Run();

  // ***************
  /// <group name="Entry">Entry Methods</group>
  //    Run()
  /// <group name="Response">Entry Methods</group>
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
      // Parameters are passed from a POST with JSON data.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      $pageData = json_decode($value);

      // Parse input data.
      $this->Action = $pageData->Action;
      $this->BeginKeyData = $pageData->BeginKeyData;
      $this->ConfigFile = $pageData->ConfigFile;
      $this->ConfigName = $pageData->ConfigName;
      $this->EndKeyData = $pageData->EndKeyData;
      $this->Limit = $pageData->Limit;

      $this->CityTableID = "cityTableItem";
      $this->TableName = City::TableName;
      $this->SQL = "";
      $_SESSION["tableName"] = $this->TableName;

      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $this->CityManager = new CityManager($connectionValues, $this->TableName);
      if ($this->Limit > 0)
      {
        $this->CityManager->Limit = $this->Limit;
      }

      $response = $this->CreateResponse();
      echo($response);
    }  // Run()

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

    /// <summary>Creates the Response data.</summary>
    /// <returns>The response JSON text.</returns.
    /// <ParentGroup>Response</ParentGroup>
    // Called from Run().
    public function CreateResponse()
    {
      $retValue = "";

      $response = $this->SetResponse();
      $result = $this->RetrieveData();
      if ($result != null)
      {
        // Create table builder with column property names.
        $propertyNames = $this->TablePropertyNames();
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
        $response->SQL = $this->SQL;
      }
      $retResponse = json_encode($response);
      return $retResponse;
    } // CreateResponse()

    // Gets the heading attributes.
    // Called from: CreateResponse()
    private function GetHeadingAttribs(): LJCAttributes
    {
      $retAttribs = new LJCAttributes();
      $style = "background-color: lightsteelblue";
      $retAttribs->Add("style", $style);
      return $retAttribs;
    } // GetHeadingAttribs()

    // Gets the table attributes.
    // Called from: CreateResponse()
    private function GetTableAttribs()
    {
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
    // Called from: CreateResponse()
    private function HTMLTableBuilder(?array $propertyNames)
    {
      // Create table object with column property names.
      $retTableBuilder = new LJCHTMLTable();
      //$retTableBuilder = new LJCHTMLTableBuilder();
      LJC::RemoveString($propertyNames, City::ColumnCityID);
      $retTableBuilder->ColumnNames = $propertyNames;
      return $retTableBuilder;
    } // HTMLTableBuilder()

    // Creates the retrieve property names.
    // Called from CreateResponse()
    private function KeyPropertyNames(): array
    {
      $retKeyNames = [
        City::ColumnCityID,
        City::ColumnProvinceID,
        City::ColumnName,
      ];
      return $retKeyNames;
    } // KeyPropertyNames()

    // Gets the results key array.
    // Called from: CreateResponse()
    private function ResultKeyArray($result, $keyNames)
    {
      // Create key values array.
      $dataManager = $this->CityManager->DataManager;
      $retKeyArray = $dataManager->CreateResultKeys($result, $keyNames);
      return $retKeyArray;
    } // ResultKeyArray()

    // Initializes the response object.
    // Called from CreateResponse()
    private function SetResponse()
    {
      $retResponse = new stdClass();
      $retResponse->Keys = [];
      $retResponse->SQL = "";
      $retResponse->HTMLTable = "";
      return $retResponse;
    } // SetResponse()

    // Creates the table property names.
    // Called from: CreateResponse()
    private function TablePropertyNames(): array
    {
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
      $retFilter = "where";
      $retFilter .= "\r\n  (ProvinceID >= {$keyData->ProvinceID}";
      $filter = "\r\n   and Name >= '{$keyData->Name}')";
      $retFilter .= $filter;
      $retFilter .= "\r\n  or ProvinceID > {$keyData->ProvinceID}";
      return $retFilter;
    } // RefreshFilter()

    // Retrieve data result.
    // Called from CreateResponse().
    private function RetrieveData()
    {
      $retResult = null;

      $keyColumns = null;
      switch ($this->Action)
      {
        case "Next":
          $filter = $this->NextFilter($this->EndKeyData);
          $this->CityManager->OrderByNames = array("ProvinceID", "Name");
          $retResult = $this->CityManager->LoadResult(null, filter: $filter);
          $this->SQL = $this->CityManager->DataManager->SQL;
          break;

        case "Previous":
          // Load descending.
          $filter = $this->PreviousFilter($this->BeginKeyData);
          $this->CityManager->OrderByNames = array("ProvinceID desc"
            , "Name desc");
          $retResult = $this->CityManager->LoadResult(null, filter: $filter);
          $this->SQL = $this->CityManager->DataManager->SQL;

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
          $this->CityManager->OrderByNames = array("ProvinceID", "Name");
          $retResult = $this->CityManager->LoadResult(null, filter: $filter);
          $this->SQL = $this->CityManager->DataManager->SQL;
          break;
      }
      return $retResult;
    } // RetrieveData()

    // ---------------
    // Properties

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

    /// <summary>The SQL statement.</summary>
    public string $SQL;

    /// <summary>The db table name.</summary>
    public string $TableName;
  }
?>