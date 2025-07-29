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
    public function Run(): void
    {
      // Parameters are passed from a POST in JSON.
      header("Content-Type: application/json; charset=UTF-8");
      $value = file_get_contents('php://input');
      $data = json_decode($value);

      $this->BeginID = $data->BeginID;
      $this->ConfigName = $data->ConfigName;
      $this->EndID = $data->EndID;
      $this->Limit = $data->Limit;
      $this->TableName = "City";
      $_SESSION["tableName"] = $this->TableName;

      $htmlTable = $this->CreateHTMLTable();
      echo($htmlTable);
    }

    /// <summary>Gets the HTML Table.</summary>
    public function CreateHTMLTable()
    {
      $retHTMLTable = "";

      // Setup Load.
      $connectionValues = $this->GetConnectionValues($this->ConfigName);  
      $cityManager = new CityManager($connectionValues, $this->TableName);
      $keyColumns = null;
      $cityManager->OrderByNames = array("ProvinceID", "Name");
      // *** Begin ***
      if ($this->Limit > 0)
      {
        $cityManager->Limit = $this->Limit;
      }
      // *** Begin ***

      // Get Load data results.
      $result = $cityManager->LoadResult($keyColumns);
      if ($result != null)
      {
        // Root TextState object.
        $textState = new LJCTextState();

        // Create table object with column property names.
        //$propertyNames = $cityManager->PropertyNames();
        $propertyNames = [
          "ProvinceID",
          "Name",
          "Description",
          "CityFlag",
          "ZipCode",
          "District",
        ];
        $tableBuilder = $this->HTMLTableBuilder($propertyNames);

        // Setup attributes.
        $tableBuilder->TableAttribs = $this->GetTableAttribs();
        $tableBuilder->HeadingAttribs = $this->GetHeadingAttribs();

        // Create HTML table with data collection.
        $textState->IndentCount = 2;
        $retHTMLTable = $tableBuilder->ResultHTML($result, $textState
          , $propertyNames);
      }
      return $retHTMLTable;
    }

    // ---------------
    // Helper Methods

    // Create the LJCHTMLTable object.
    //private function CreateHTMLTable(?array $propertyNames)
    private function HTMLTableBuilder(?array $propertyNames)
    {
      // Create table object with column property names.
      $retTableBuilder = new LJCHTMLTable();
      //$retTableBuilder = new LJCHTMLTableBuilder();
      LJC::RemoveString($propertyNames, "CityID");
      $retTableBuilder->ColumnNames = $propertyNames;
      return $retTableBuilder;
    }

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
  }
?>