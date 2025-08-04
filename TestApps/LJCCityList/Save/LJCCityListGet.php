<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCityListGet.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/ATestForm/LJCDataConfigs.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLTableLib.php";
  include_once "$prefix/RegionApp/City/CityDAL.php";
  // DataConfigs: DataConfigs 
  // LJCDBAccessLib: LJCConnectionValues
  // LJCHTMLBuilderLib: LJCAttribute, LJCAttributes, LJCHTMLBuilder
  //   , LJCTextState

  $cityListGet = new LJCCityListGet();
  $cityListGet->Run();

  /// <summary>Create an HTML table from City data.
  class LJCCityListGet
  {
    public function Run(): void
    {
      $this->ConfigName = $_GET["configName"];
      $this->BeginID = $_GET["beginID"];
      $this->EndID = $_GET["endID"];
      $this->RowCount = $_GET["rowCount"];

      $text = "GET ConfigName: {$this->ConfigName}";
      $text .= ", &nbsp;BeginID: {$this->BeginID}";
      $text .= ", &nbsp;EndID: {$this->EndID}";
      $text .= ", &nbsp;RowCount: {$this->RowCount}";
      echo($text);
    }

    public function Results()
    {

    }
  }
?>