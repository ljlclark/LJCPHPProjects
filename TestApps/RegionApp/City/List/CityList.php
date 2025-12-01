<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // CityList.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/RegionApp/RegionConfigLib.php";
  include_once "$prefix/RegionApp/City/List/CityListTable.php";
  // RegionConfigLib: RegionConfig
  // CityListTable: CityListTable

  echo(file_get_contents("CityListHead.html"));
  $main = new LJCMain();
  $main::Run();
  echo(file_get_contents("CityListTail.html"));

  /// <summary>Provides methods to create the CityList HTML.</summary>
  class LJCMain
  {
    // ---------------
    // Public Methods

    /// <summary>Creates the CityList HTML.</summary>
    public static function Run()
    {
      $connectionValues = RegionConfig::GetConnectionValues();
      // Get session value.
      $tableName = $_SESSION["tableName"];

      $cityTable = new CityListTable($connectionValues, $tableName);
      $cityTable->CreateHTMLTable();
    }
  }
?>