<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // DBAccessTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLLib.php";
  // LJCCommonLib: LJC
  // LJCDBAccessLib: LJCConnectionValues, LJCDbAccess, LJCTextState
  // LJCTextBuilderLib: LJCTextBuilder, LJCTextState
  // LJCHTMLLib: LJCHTML

  /// <summary>The HTML Section Test Class Library</summary>
  /// LibName: DBAccessTest

  $testDBAccess = new DBAccessTest();
  $testDBAccess->Run();

  // ********************
  /// <summary>The HTML Section Test Class</summary>
  /// <include path='items/LJCHTML/*' file='Doc/LJCHTML.xml'/>
  class DBAccessTest
  {
    /// <summary>Runs the LJCHTML tests.</summary>
    public static function Run()
    {
      // Setup static debug to output.
      $className = "LJCDbAccess";
      $methodName = "Run()";

      echo("\r\n");
      echo("*** LJCDbAccess ***");

      $connectionValues = self::construct();

      // Static Methods
      self::GetValue($connectionValues);

      // Data Methods
      self::Execute($connectionValues);
      self::Load($connectionValues);
      self::Retrieve($connectionValues);

      // Other Methods
      self::GetConnection($connectionValues);
      self::LoadTableSchema($connectionValues);
      self::SetConnectionValues($connectionValues);
    }

    // --------------------
    // Static Methods

    // Returns a data value if the element exists.
    private static function GetValue(LJCConnectionValues $connectionValues)
    {
		  // See constructor for how to create $connectionValues.
		  $dbAccess = new LJCDbAccess($connectionValues);

		  $tableName = "Person";
		  $sql = "select ID, Name from {$tableName} where ID = 345";
		  $row = $dbAccess->Retrieve($sql);
		  $id = LJCDbAccess::GetValue($row, "ID");
		  $name = LJCDbAccess::GetValue($row, "Name");
      $result = "{$id}, {$name}";

      $compare = "345, Update Name";
      LJC::OutputLogCompare("GetValue()", $result, $compare);
    }

    // Initializes a class instance.
    private static function construct(): LJCConnectionValues
    {
      $dbServer = "localhost";
		  $dbName = "TestData";
		  $userID = "root";
		  $password = "Unifies1";
		  $retValues = new LJCConnectionValues($dbServer, $dbName, $userID
			  , $password);
      if (null == $retValues
        || $retValues->DbServer != "localhost"
        || $retValues->DbName != "TestData"
        || $retValues->UserID != "root"
        || $retValues->Password != "Unifies1")
      {
        echo("\r\nSetConnectionValues() Connection values not set correctly.");
      }
      return $retValues;
    }

    // --------------------
    // Data Methods
    
    // Executes a non-query sql statement.
    private static function Execute(LJCConnectionValues $connectionValues)
    {
		  // See constructor for how to create $connectionValues.
		  $dbAccess = new LJCDbAccess($connectionValues);

		  $tableName = "Person";

      // Insert test record.
      $name = "Test";
      $sql = "insert into {$tableName}\r\n ";
      $sql .= "(Name)\r\n ";
      $sql .= "values('{$name}');";
		  $affectedCount = $dbAccess->Execute($sql);
      if (0 == $affectedCount)
      {
        echo("\r\nExecute() No record inserted.");
      }

      // Retrieve assigned ID.
      $sql = "select\r\n ";
      $sql .= "ID, Name\r\n ";
      $sql .= "from {$tableName}\r\n ";
      $sql .= "where Name = '{$name}';";
      $row = $dbAccess->Retrieve($sql);
      if ($row == null)
      {
        echo("\r\nExecute() select row not found");
      }
      $id = LJCDbAccess::GetValue($row, "ID");
      if ($id == null)
      {
        echo("\r\nExecute() ID not found");
      }

      // Delete test record.
      if ($id != null)
      {
		    $sql = "delete from {$tableName}\r\n ";
        $sql .= "where ID = {$id};";
		    $affectedCount = $dbAccess->Execute($sql);
        if (0 == $affectedCount)
        {
          echo("\r\nExecute() No record found to delete.");
        }
      }
    }
    
    // Loads the records for the provided SQL statement.
    private static function Load(LJCConnectionValues $connectionValues)
    {
		  // See constructor for how to create $connectionValues.
		  $dbAccess = new LJCDbAccess($connectionValues);

		  $tableName = "Person";
      $sql = "select * from {$tableName};";
      $rows = $dbAccess->Load($sql);
      if (null == $rows
        || (is_array($rows)
        && count($rows) == 0))
      {
        echo("\r\nLoad() rows not found");
      }
    }
    
    // Retrieves a record for the provided SQL statement.
    private static function Retrieve(LJCConnectionValues $connectionValues)
    {
		  // See constructor for how to create $connectionValues.
		  $dbAccess = new LJCDbAccess($connectionValues);
		  $tableName = "Person";

      // Insert test record.
      $name = "Test";
      $sql = "insert into {$tableName}\r\n ";
      $sql .= "(Name)\r\n ";
      $sql .= "values('{$name}');";
		  $affectedCount = $dbAccess->Execute($sql);
      if (0 == $affectedCount)
      {
        echo("\r\nRetrieve() No record inserted.");
      }

      // Retrieve assigned ID.
      $sql = "select\r\n ";
      $sql .= "ID, Name\r\n ";
      $sql .= "from {$tableName}\r\n ";
      $sql .= "where Name = '{$name}';";
      $row = $dbAccess->Retrieve($sql);
      if ($row == null)
      {
        echo("\r\nRetrieve() select row not found");
      }
      $id = LJCDbAccess::GetValue($row, "ID");
      if ($id == null)
      {
        echo("\r\nRetrieve() ID not found");
      }

      // Delete test record.
      if ($id != null)
      {
		    $sql = "delete from {$tableName}\r\n ";
        $sql .= "where ID = {$id};";
		    $affectedCount = $dbAccess->Execute($sql);
        if (0 == $affectedCount)
        {
          echo("\r\nRetrieve() No record found to delete.");
        }
      }
    }

    // --------------------
    // Other Methods
    
    // Retrieves a record for the provided SQL statement.
    private static function GetConnection(LJCConnectionValues $connectionValues)
    {
		  // See constructor for how to create $connectionValues.
		  $dbAccess = new LJCDbAccess($connectionValues);

      $connection = $dbAccess->GetConnection();
      if (null == $connection)
      {
        echo("\r\nGetConnection() The Connection was not created.");
      }
    }
    
    // Returns the Table Schema DbColumns collection.
    private static function LoadTableSchema(
        LJCConnectionValues $connectionValues)
    {
		  // See constructor for how to create $connectionValues.
		  $dbAccess = new LJCDbAccess($connectionValues);

		  $dbName = "TestData";
		  $tableName = "person";
      $schema = $dbAccess->LoadTableSchema($dbName, $tableName);
      if (null == $schema
        || !$schema instanceof LJCDbColumns
        || count($schema) == 0)
      {
        echo("\r\nLoadTableSchema() The Schema was not retrieved.");
      }
    }
    
    // Resets the $ConnectionValues property.
    private static function SetConnectionValues(
      LJCConnectionValues $connectionValues)
    {
		  // See constructor for how to create $connectionValues.
		  $dbAccess = new LJCDbAccess($connectionValues);

      $dbServer = "localhost";
		  $dbName = "TestData";
		  $userID = "root";
		  $password = "Unifies1";
      $dbAccess->SetConnectionValues($dbServer, $dbName, $userID, $password);
      $values = $dbAccess->ConnectionValues;
      if (null == $values
        || $values->DbServer != "localhost"
        || $values->DbName != "TestData"
        || $values->UserID != "root"
        || $values->Password != "Unifies1")
      {
        echo("\r\nSetConnectionValues() Connection values not set correctly.");
      }
    }
  }
