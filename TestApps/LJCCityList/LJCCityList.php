<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCityList.php
  declare(strict_types=1);
  session_start();
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/ATestForm/CityList/LJCDataConfigs.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLTableLib.php";
  //include_once "$prefix/LJCPHPCommon/LJCHTMLTableBuilderLib.php";
  include_once "$prefix/RegionApp/City/CityDAL.php";
  // LJCDataConfigs: DataConfigs 
  // LJCDBAccessLib: LJCConnectionValues
  // LJCHTMLBuilderLib: LJCAttribute, LJCAttributes, LJCHTMLBuilder
  //   , LJCTextState
  // CityDAL: 

  $cityList = new LJCCityList();
  $cityList->Run();

  /// <summary>Web Service to Create an HTML table from City data.
  //  Entry: Run()
  //  Response: CreateResponse(), 
  class LJCCityList
  {
    // ---------------
    // Entry Methods

    /// <summary>Service start method.</summary>
    /// <returns>The service response JSON text.</returns.
    public function Run(): void
    {
      // Parameters are passed from a POST in JSON.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      $pageData = json_decode($value);

      // Parse input data.
      $this->Action = $pageData->Action;
      $this->BeginKeyData = $pageData->BeginKeyData;
      $this->ConfigName = $pageData->ConfigName;
      $this->EndKeyData = $pageData->EndKeyData;
      $this->Limit = $pageData->Limit;

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
    private function GetConnectionValues(string $configName)
    {
      $dataConfigs = "DataConfigs.xml";
      $configName = "TestData";
      $retValues = DataConfigs::GetConnectionValues($dataConfigs, $configName);
      return $retValues;
    }  // GetConnectionValues()

    // ---------------
    // Create Response (Main) Methods

    /// <summary>Creates the HTML Table.</summary>
    /// <returns>The response JSON text.</returns.
    // Called from Run().
    public function CreateResponse()
    {
      $retValue = "";

      $response = $this->SetResponse();
      $result = $this->RetrieveData();
      if ($result != null)
      {
        // Root TextState object.
        $textState = new LJCTextState();

        // Create table object with column property names.
        $propertyNames = $this->TablePropertyNames();
        $tableBuilder = $this->HTMLTableBuilder($propertyNames);

        // Setup attributes.
        $tableBuilder->TableAttribs = $this->GetTableAttribs();
        $tableBuilder->HeadingAttribs = $this->GetHeadingAttribs();

        // Create HTML table with data collection.
        $textState->IndentCount = 2;

        // Create Key array.
        $keyNames = $this->KeyPropertyNames();
        $keyArray = $this->ResultKeyArray($result, $keyNames);

        $response->Keys = $keyArray;
        $response->SQL = $this->SQL;
        $response->HTMLTable = $tableBuilder->ResultHTML($result, $textState
          , $propertyNames);
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
      $id = "dataTable";
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
    //private function CreateHTMLTable(?array $propertyNames)
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

    // Initializes the response object.
    private function SetResponse()
    {
      $retResponse = new stdClass();
      $retResponse->Keys = [];
      $retResponse->SQL = "";
      $retResponse->HTMLTable = "";
      return $retResponse;
    }

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

    // Create DbKeys from keyData object.
    // Called from RetrieveData(), RetrieveDataReload()
    private function DbColumnKeys($keyData, $backward = false): LJCDbColumns
    {
      $propertyNames = [ "ProvinceID", "Name"];
      $retKeys = $this->CityManager->Columns($propertyNames);

      $dbColumn = $retKeys->Retrieve("ProvinceID");
      $dbColumn->Value = $keyData->ProvinceID;
      $dbColumn->WhereCompareOperator = ">=";
      if ($backward)
      {
        $dbColumn->WhereCompareOperator = "<=";
      }

      $dbColumn = $retKeys->Retrieve("Name");
      $dbColumn->Value = $keyData->Name;
      $dbColumn->WhereCompareOperator = ">";
      if ($backward)
      {
        $dbColumn->WhereCompareOperator = "<";
      }
      return $retKeys;
    } // DbColumnKeys()

    // Create the "Next" filter.
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
    }

    // Create the "Previous" filter.
    private function PreviousFilter($beginKeyData): string
    {
      $retFilter = "where";
      $retFilter .= "\r\n  (ProvinceID <= {$beginKeyData->ProvinceID}";
      $retFilter .= "\r\n   and Name < '{$beginKeyData->Name}')";
      $retFilter .= "\r\n  or ProvinceID < {$beginKeyData->ProvinceID}";
      return $retFilter;
    }

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

          // Reload ascending from last record.
          //$retResult = $this->RetrieveDataReload($retResult);
          break;

        default:
          $filter = "";
          if ($this->BeginKeyData->ProvinceID != 0)
          {
            $filter = $this->NextFilter($this->BeginKeyData);
          }
          $this->CityManager->OrderByNames = array("ProvinceID", "Name");
          $retResult = $this->CityManager->LoadResult(null, filter: $filter);
          $this->SQL = $this->CityManager->DataManager->SQL;
          break;
      }
      return $retResult;
    } // RetrieveData()

    // Reload ascending from last record.
    // Called from: RetrieveData()
    private function RetrieveDataReload($result)
    {
      // Get the last element of array of named arrays.
      $keyNames = [
        City::ColumnProvinceID,
        City::ColumnName,
      ];
      $resultKeyArray = $this->ResultKeyArray($result, $keyNames);

      // Get last loaded record.
      $keysArray = $resultKeyArray[count($resultKeyArray) - 1];
      $keyData = new stdClass();
      $keyData->ProvinceID = $keysArray["ProvinceID"];
      $keyData->Name = $keysArray["Name"];

      $filter = $this->NextFilter($keyData, true);
      $this->CityManager->OrderByNames = array("ProvinceID", "Name");
      $retResult = $this->CityManager->LoadResult(null, filter: $filter);
      //$this->SQL = $this->CityManager->DataManager->SQL;
      return $retResult;
    } // RetrieveDataReload()

    // ---------------
    // Other Methods

    // Gets the results key array.
    // Called from: CreateResponse(), RetrieveDataReload()
    private function ResultKeyArray($result, $keyNames)
    {
      // Create key values array.
      $dataManager = $this->CityManager->DataManager;
      $retKeyArray = $dataManager->CreateResultKeys($result, $keyNames);
      return $retKeyArray;
    } // ResultKeyArray()

    // ---------------
    // Properties

    /// <summary>The data retrieve action.</summary>
    /// <remarks>
    ///   Values: "Next", "Previous", "Top", "Bottom", "First"?, "Last"?
    /// </remarks>
    //public string $Action;

    /// <summary>The find key values for the first table row data.</summary>
    /// <remarks> Properties: ProvinceID, Name</remarks>
    //public object $BeginKeyData;

    /// <summary>The CityManager object.</summary>
    //public CityManager $CityManager;

    /// <summary>The data config name.</summary>
    //public string $ConfigName;

    /// <summary>The find key values for the last table row data.</summary>
    /// <remarks> Properties: ProvinceID, Name</remarks>
    //public object $EndKeyData;

    /// <summary>The number of rows per page.</summary>
    //public int $Limit = 10;

    /// <summary>The db table name.</summary>
    //public string $TableName;

    /// <summary>The SQL statement.</summary>
    //public string $SQL;
  }
?>