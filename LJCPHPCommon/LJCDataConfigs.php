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

  // The DB config Library.
  /// <include path='items/LJCDataConfigs/*' file='Doc/DataConfigs.xml'/>

  /// LibName: DataConfigs
  //  Classes: DataConfigs

  // ***************
  // Contains methods to retrieve the DB config values.
  /// <include path='items/DataConfigs/*' file='Doc/DataConfigs.xml'/>
  class DataConfigs
  {
    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = "null")
    {
      $this->ClassName = "DataConfigs";

      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // Retrieves the DB connection values.
    /// <include path='items/GetConnectionValues/*' file='Doc/DataConfigs.xml'/>
    public static function GetConnectionValues(string $configFileSpec
      , string $configName): LJCConnectionValues
    {
      $methodName = "GetConnectionValues()";
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
    } // GetConnectionValues()

    // Gets the config by name.
    private static function GetDataConfig(string $configFileSpec, $configName)
      : SimpleXMLElement
    {
      $methodName = "GetDataConfig()";
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
    } // GetDataConfig()

    // ---------------
    // Result Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;
  }
?>