<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCCityListPost.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/ATestForm/LJCDataConfigs.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLTableLib.php";
  include_once "$prefix/RegionApp/City/CityDAL.php";
  // DataConfigs: DataConfigs 
  // LJCDBAccessLib: LJCConnectionValues
  // LJCTextBuilderLib: LJCAttribute, LJCAttributes, LJCTextBuilder
  //   , LJCTextState

  $cityListPost = new LJCCityListPost();
  $cityListPost->Run();

  /// <summary>Create an HTML table from City data.
  class LJCCityListPost
  {
    public function Run(): void
    {
      $this->ConfigName = $_POST["configName"];
      $this->BeginID = $_POST["beginID"];
      $this->EndID = $_POST["endID"];
      $this->RowCount = $_POST["rowCount"];

      $text = "POST ConfigName: {$this->ConfigName}";
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