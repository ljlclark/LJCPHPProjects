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
  // DataConfigs: DataConfigs 
  // LJCDBAccessLib: LJCConnectionValues
  // LJCHTMLBuilderLib: LJCAttribute, LJCAttributes, LJCHTMLBuilder
  //   , LJCTextState

  $cityList = new LJCCityList();
  $cityList->Run();

  /// <summary>Service to Create an HTML table from City data.
  class LJCCityList
  {
    // ---------------
    // Entry Method

    public function Run(): void
    {
      // Parameters are passed from a POST in JSON.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      $pageData = json_decode($value);

      // *** Testing ***
      $this->ReloadBeginArray = null;
      $this->ReloadLastLoaded = null;
      // *** End   ***

      // Parse input data.
      $this->Action = $pageData->Action;
      $this->BeginKeyData = $pageData->BeginKeyData;
      $this->ConfigName = $pageData->ConfigName;
      $this->EndKeyData = $pageData->EndKeyData;
      $this->Limit = $pageData->Limit;

      $this->TableName = City::TableName;
      $this->SQL = null;
      $_SESSION["tableName"] = $this->TableName;

      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $this->CityManager = new CityManager($connectionValues, $this->TableName);
      if ($this->Limit > 0)
      {
        $this->CityManager->Limit = $this->Limit;
      }
      $response = $this->CreateResponse();
      echo($response);
    }

    // ---------------
    // Public Main Methods

    /// <summary>Gets the HTML Table.</summary>
    // Called from Run().
    public function CreateResponse()
    {
      $retValue = "";

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
        $retObject = new stdClass();
        $keyNames = $this->KeyPropertyNames();
        $keyArray = $this->ResultKeyArray($result, $keyNames);

        // *** Testing ***
        $retObject->ReloadBeginArray = $this->ReloadBeginArray;
        $retObject->ReloadLastLoaded = $this->ReloadLastLoaded;
        // *** End   ***
        $retObject->SQL = $this->SQL;
        $retObject->Keys = $keyArray;
        $retObject->HTMLTable = $tableBuilder->ResultHTML($result, $textState
          , $propertyNames);
        $retResponse = json_encode($retObject);
      }
      return $retResponse;
    } // CreateHTMLTable()

    // ---------------
    // Private Main Methods

    // Create DbKeys from keyData object.
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

    // Creates the retrieve property names.
    private function KeyPropertyNames()
    {
      $retKeyNames = [
        City::ColumnCityID,
        City::ColumnProvinceID,
        City::ColumnName,
      ];
      return $retKeyNames;
    }

    // Gets the results key array.
    private function ResultKeyArray($result, $keyNames)
    {
      // Create key values array.
      $dataManager = $this->CityManager->DataManager;
      $retKeyArray = $dataManager->CreateResultKeys($result, $keyNames);
      return $retKeyArray;
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
          $keyColumns = $this->DbColumnKeys($this->EndKeyData);
          $this->CityManager->OrderByNames = array("ProvinceID", "Name");
          $retResult = $this->CityManager->LoadResult($keyColumns);
          $this->SQL = $this->CityManager->DataManager->SQL;
          break;

        case "Previous":
          // Load descending.
          $keyColumns = $this->DbColumnKeys($this->BeginKeyData, true);
          // *** Change ***
          $this->CityManager->OrderByNames = array("ProvinceID desc"
            , "Name desc");
          $retResult = $this->CityManager->LoadResult($keyColumns);
          $this->SQL = $this->CityManager->DataManager->SQL;

          // Flip result.
          //$flipResult = [];
          //$count = count($result);
          //for ($index = $count - 1; $index >= 0; $index--)
          //{
          //  $flipResult[] = $result[$index];
          //}
          //$result = $flipResult;

          // Reload ascending from last record.
          $retResult = $this->RetrieveDataReload($retResult);
          break;

        default:
          // *** Begin *** Add
          if ($this->BeginKeyData->ProvinceID != 0)
          {
            $keyColumns = $this->DbColumnKeys($this->BeginKeyData, true);
          }
          $this->CityManager->OrderByNames = array("ProvinceID", "Name");
          // *** End   ***
          $retResult = $this->CityManager->LoadResult($keyColumns);
          $this->SQL = $this->CityManager->DataManager->SQL;
          break;
      }
      return $retResult;
    } // RetrieveData()

    // Reload ascending from last record.
    private function RetrieveDataReload($result)
    {
      // Get the last element of array of named arrays.
      $keyNames = [
        City::ColumnProvinceID,
        City::ColumnName,
      ];
      $resultKeyArray = $this->ResultKeyArray($result, $keyNames);
      // *****
      $this->ReloadBeginArray = $resultKeyArray;

      // Get last loaded record.
      $keysArray = $resultKeyArray[count($resultKeyArray) - 1];
      $keyData = new stdClass();
      $keyData->ProvinceID = $keysArray["ProvinceID"];
      $keyData->Name = $keysArray["Name"];
      // *****
      $this->ReloadLastLoaded = $keyData;

      // Create LJCDbColumns 
      $keyColumns = $this->DbColumnKeys($keyData, $this->CityManager);
      $this->CityManager->OrderByNames = array("ProvinceID", "Name");
      $retResult = $this->CityManager->LoadResult($keyColumns);
      return $retResult;
    } // RetrieveDataReload()

    // Creates the table property names.
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
    }

    // ---------------
    // Helper Methods

    // Get connection values for a DataConfig name.
    private function GetConnectionValues(string $configName)
    {
      $dataConfigs = "DataConfigs.xml";
      $configName = "TestData";
      $retValues = DataConfigs::GetConnectionValues($dataConfigs, $configName);
      return $retValues;
    }

    // Gets the heading attributes.
    private function GetHeadingAttribs(): LJCAttributes
    {
      $retAttribs = new LJCAttributes();
      $style = "background-color: lightsteelblue";
      $retAttribs->Add("style", $style);
      return $retAttribs;
    }

    // Gets the table attributes.
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
    }

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
    }
  }
?>