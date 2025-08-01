<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // DataConfigs.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  // LJCCommon: LJC
  // LJCDBAccessLib: LJCConnectionValues

  /// <summary>The DBHTMLTableConfig DB config Library.</summary>
  /// LibName: DBHTMLTableConfig
  //  Classes: DBHTMLTableConfig

  // ***************
  /// <summary>Contains method to retrieve the DB config values.</summary>
  class DataConfigs
  {
    /// <summary>Retrieves the DB connection values.</summary>
    /// <returns>The DB connection values object.</returns>
    public static function GetConnectionValues(string $configFileSpec
      , string $configName): LJCConnectionValues
    {
      $retValue = null;

      // Get connection values.
      if (!file_exists($configFileSpec))
      {
        throw new Exception("File: {$configFileSpec} does not exist.");
      }
      $xmlConfig = self::GetDataConfig($configFileSpec, $configName);
      if ($xmlConfig != null)
      {
        $dbServer = LJC::XMLToString($xmlConfig->dbserver);
        $dbName = LJC::XMLToString($xmlConfig->dbname);
        $userID = LJC::XMLToString($xmlConfig->userid);
        $password = LJC::XMLToString($xmlConfig->password);

        // Set Session values.
        $_SESSION["dbServer"] = $dbServer;
        $_SESSION["dbName"] = $dbName;
        $_SESSION["userID"] = $userID;
        $_SESSION["password"] = $password;

        $retValue = new LJCConnectionValues($dbServer, $dbName, $userID
          , $password);
      }
      return $retValue;
    }

    // Gets the config by name.
    private static function GetDataConfig(string $configFileSpec, $configName)
      : SimpleXMLElement
    {
      $retConfig = null;

      $xmlDoc = simplexml_load_file($configFileSpec);
      $xmlConfigs = $xmlDoc->children();
      foreach($xmlConfigs as $xmlConfig)
      {
        if ($xmlConfig->name == $configName)
        {
          $retConfig = $xmlConfig;
          break;
        }
      }
      return $retConfig;
    }
  }
?>