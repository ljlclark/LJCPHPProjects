<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCRegionConfigLib.php
  declare(strict_types=1);
  session_start();
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  require_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  // LJCDBAccessLib: LJCConnectionValues

  /// <summary>The Region DB config Library.</summary>
  /// LibName: RegionConfigLib
  //  Classes: RegionConfig

  // ***************
  /// <summary>Contains method to retrieve the DB config values.</summary>
  class RegionConfig
  {
    /// <summary>Retrieves the DB connection values.</summary>
    /// <returns>The DB connection values object.</returns>
    public static function GetConnectionValues() : LJCConnectionValues
    {
      $retValue = null;

      // Set connection values.
      $dbServer = "localhost";
      $dbName = "TestData";
      $userID = "root";
      $password = "Unifies1";
      $tableName = "City";

      // Set Session values.
      $_SESSION["dbServer"] = $dbServer;
      $_SESSION["dbName"] = $dbName;
      $_SESSION["userID"] = $userID;
      $_SESSION["password"] = $password;
      $_SESSION["tableName"] = $tableName;

      $retValue = new LJCConnectionValues($dbServer, $dbName, $userID
        , $password);
      return $retValue;
    }
  }
?>