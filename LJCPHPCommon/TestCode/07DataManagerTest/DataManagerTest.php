<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // DBAccessTest.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextBuilderLib.php";
  include_once "$prefix/LJCPHPCommon/LJCHTMLLib.php";
  // LJCCommonLib: LJC
  // LJCDBAccessLib: LJCConnectionValues, LJCDbAccess, LJCTextState
  // LJCTextBuilderLib: LJCTextBuilder, LJCTextState
  // LJCHTMLLib: LJCHTML

  /// <summary>The HTML Section Test Class Library</summary>
  /// LibName: DBAccessTest

  class Item
  {
    public function __construct()
    {
      $this->ID = 0;
      $this->Name = "";
    }

    /// <summary>Creates an object clone.</summary>
    public function Clone(): self
    {
      $retValue = new self();

      $retValue->ID = $this->ID;
      $retValue->Name = $this->Name;
      return $retValue;
    } // Clone()

    public int $ID;
    public string $PropertyName;
  }

  class Items extends LJCCollectionBase
  {
    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(Item $item, $key = null): ?Item
    {
      $methodName = "AddObject()";

      if (null == $key)
      {
        $key = $item->Name;
      }

      // AddItem() is in LJCCollectionBase.
      $retItem = $this->AddItem($item, $key);
      return $retItem;
    } // AddObject()

    // Retrieves the item by index.
    public function RetrieveAtIndex($index)
    {
      $retItem = $this->RetrieveItemAtIndex($index);
      return $retItem;
    }
  }

  $testDataManager = new DataManagerTest();
  $testDataManager->Run();

  // ********************
  /// <summary>The HTML Section Test Class</summary>
  /// <include path='items/LJCHTML/*' file='Doc/LJCHTML.xml'/>
  class DataManagerTest
  {
    /// <summary>Runs the LJCHTML tests.</summary>
    public static function Run()
    {
      // Setup static debug to output.
      $className = "LJCDbAccess";
      $methodName = "Run()";

      echo("\r\n");
      echo("*** LJCDataManager ***");

      // Constructor Methods
      $manager = self::Construct();

      // Data Methods
      self::Add($manager);
      self::Delete($manager);
      self::DeleteSQL($manager);
      self::Load($manager);
      self::LoadSQL($manager);
      self::Retrieve($manager);
      self::RetrieveSQL($manager);
      self::Update($manager);
      self::UpdateSQL($manager);
      self::SQLExecute($manager);
      self::SQLLoad($manager);
      self::SQLRetrieve($manager);

      // Schema Methods
      self::Columns($manager);
      self::MapNames($manager);
      self::PropertyNames($manager);

      // ORM Methods
      self::CreateDataCollection($manager);
      self::CreateDataObject($manager);

      // Other Methods
      self::CreateResultKeys($manager);
    }

    // --------------------
    // Constructor Methods

    private static function Construct()
    {
      $dbServer = "localhost";
		  $dbName = "TestData";
		  $userID = "root";
		  $password = "Unifies1";
		  $values = new LJCConnectionValues($dbServer, $dbName, $userID
			  , $password);
      if (null == $values
        || $values->DbServer != "localhost"
        || $values->DbName != "TestData"
        || $values->UserID != "root"
        || $values->Password != "Unifies1")
      {
        echo("\r\nConstruct() Connection values not set correctly.");
      }

      $tableName = "Person";
      $retManager = new LJCDataManager($values, $tableName);
      if (null == $retManager)
      {
        echo("\r\nConstruct() Data Manager was not created.");
      }
      return $retManager;
    } // Construct()

    // --------------------
    // Data Methods

    // Adds the record for the provided values.
    private static function Add(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "Add()";

      // Add the test data.
      // See constructor for how to create $manager.
		  $data = new LJCDbColumns();
		  $data->Add("Name", value: $nameValue);
		  $affectedCount = $manager->Add($data);
      if ($affectedCount < 1)
      {
        echo("{$methodName}\r\n{$manager->SQL}");
      }
      $result = strval($affectedCount);

      $compare = "1";
      LJC::OutputLogCompare($methodName, $result, $compare);

      self::TestRetrieve($manager, $methodName, $nameValue);
      self::TestDelete($manager, $methodName, $nameValue);
    } // Add()

    // Deletes the records for the provided values.
    private static function Delete(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "Delete()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Delete the test data.
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $affectedCount = $manager->Delete($keys);
      if ($affectedCount < 1)
      {
        echo("{$methodName}\r\n{$manager->SQL}");
      }
      $result = strval($affectedCount);

      $compare = "1";
      LJC::OutputLogCompare("$methodName", $result, $compare);
    } // Delete

    // Creates the Delete SQL.
    private static function DeleteSQL(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "DeleteSQL()";

      // Create the Delete with SQL statement.
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $result = $manager->DeleteSQL($keys);

      $compare = "delete from Person \r\n";
      $compare .= "where Person.Name = 'NameValue'";
      LJC::OutputLogCompare("$methodName", $result, $compare);
    } // DeleteSQL()

    // Loads the records for the provided values.
    private static function Load(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "Load()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Load the test data.
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $rows = $manager->Load($keys);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\n{$methodName} No data retrieved.");
      }

      self::TestDelete($manager, $methodName, $nameValue);
    } // Load()

    // Creates the Load SQL.
    private static function LoadSQL(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "LoadSQL()";

      // Load with SQLLoad().
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $result = $manager->LoadSQL($keys);

      $compare = "select \r\n";
      $compare .= "  Person.ID, \r\n";
      $compare .= "  Person.Name, \r\n";
      $compare .= "  Person.PrincipleFlag, \r\n";
      $compare .= "  Person.TitleID \r\n";
      $compare .= "from Person \r\n";
      $compare .= "where Person.Name = 'NameValue'";
      LJC::OutputLogCompare("$methodName", $result, $compare);
    } // LoadSQL()

    // Retrieves the record for the provided values.
    private static function Retrieve(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "Retrieve()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Retrieve the test data.
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $rows = $manager->Retrieve($keys);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\n{$methodName} No data retrieved.");
      }

      self::TestDelete($manager, $methodName, $nameValue);
    } // Retrieve()

    // Creates the Retrieve SQL.
    private static function RetrieveSQL(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "RetrieveSQL()";

      // Retrieve with SQLLoad().
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value: $nameValue);
      $result = $manager->RetrieveSQL($keyColumns);

      $compare = "select \r\n";
      $compare .= "  Person.ID, \r\n";
      $compare .= "  Person.Name, \r\n";
      $compare .= "  Person.PrincipleFlag, \r\n";
      $compare .= "  Person.TitleID \r\n";
      $compare .= "from Person \r\n";
      $compare .= "where Person.Name = 'NameValue'";
      LJC::OutputLogCompare("$methodName", $result, $compare);
    } // RetrieveSQL()

    // Updates the records for the provided values.
    private static function Update(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $updateValue = "Updated";
      $methodName = "Update()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Update the test data.
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $data = new LJCDbColumns();
		  $data->Add("Name", value: $updateValue);
      $affectedCount = $manager->Update($keys, $data);
      $result = strval($affectedCount);

      $compare = "1";
      LJC::OutputLogCompare($methodName, $result
        , $compare);

      self::TestRetrieve($manager, $methodName, $updateValue);
      self::TestDelete($manager, $methodName, $updateValue);
    } // Update()

    // Creates the Update SQL.
    private static function UpdateSQL(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $updateValue = "Updated";
      $methodName = "UpdateSQL()";

      // Get the Update SQL statement.
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $data = new LJCDbColumns();
		  $data->Add("Name", value: $updateValue);
      $result = $manager->UpdateSQL($keys, $data);

      $compare = "update Person set \r\n";
      $compare .= "  Name = 'Updated' \r\n";
      $compare .= "where Person.Name = 'NameValue'";
      LJC::OutputLogCompare("$methodName", $result, $compare);
    } // UpdateSQL()

    // Executes an Add, Delete or Update SQL statement.
    private static function SQLExecute(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $updateValue = "Updated";
      $methodName = "SQLExecute()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Update with SQLExecute().
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $data = new LJCDbColumns();
		  $data->Add("Name", value: $updateValue);
      $sql = $manager->UpdateSQL($keys, $data);
      $affectedCount = $manager->SQLExecute($sql);
      $result = strval($affectedCount);

      $compare = "1";
      LJC::OutputLogCompare("$methodName", $result, $compare);

      self::TestRetrieve($manager, $methodName, $updateValue);
      self::TestDelete($manager, $methodName, $updateValue);
    } // SQLExecute()

    // Executes a Select SQL statement.
    private static function SQLLoad(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "SQLLoad()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Retrieve with SQLLoad().
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $sql = $manager->LoadSQL($keys);
      $rows = $manager->SQLLoad($sql);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\n{$methodName} No data retrieved.");
      }

      self::TestDelete($manager, $methodName, $nameValue);
    } // SQLLoad()

    // Executes a Select SQL statement.
    private static function SQLRetrieve(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "SQLRetrieve()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Retrieve with SQLRetrieve().
      // See constructor for how to create $manager.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $sql = $manager->RetrieveSQL($keys);
      $rows = $manager->SQLRetrieve($sql);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\n{$methodName} No data retrieved.");
      }

      self::TestDelete($manager, $methodName, $nameValue);
    } // SQLRetrieve()

    // --------------------
    // Schema Methods

    // Get the schema columns that match the property names.
    private static function Columns(LJCDataManager $manager)
    {
      $propertyNames = [
        "ID",
        "Name",
      ];

      // See constructor for how to create $manager.
      $columns = $manager->Columns($propertyNames);
      $value = count($columns);
      $result = strval($value);

      $compare = "2";
      LJC::OutputLogCompare("SQLRetrieve() - Add()", $result, $compare);

      $result = "";
      foreach ($columns as $column)
      {
        if (strlen(trim($result)) > 0)
        {
          $result .= ",";
        }
        $result .= $column->PropertyName;
      }

      $compare = "ID,Name";
      LJC::OutputLogCompare("SQLRetrieve() - Add()", $result, $compare);
    }

    // Sets PropertyName, RenameAs and Caption values for a schema column.
    private static function MapNames(LJCDataManager $manager)
    {
      $methodName = "MapNames";

      $propertyNames = [
        "Name",
      ];
      // See constructor for how to create $manager.
      $columns = $manager->Columns($propertyNames);
      if ($columns != null
        && count($columns) > 0)
      {
        // Change schema column PropertyName value.
        // Matches by key which is the current PropertyName value.
        $manager->MapNames("Name", "NewName", "", "Caption");

        // Get changed column.
        $propertyNames = [
          "NewName",
        ];
        $columns = $manager->Columns($propertyNames);
        if (null == $columns
          || 0 == count($columns))
        {
          echo("{$methodName}: PropertyName change failed.");
        }

        // Check changes.
        if ($columns != null
          && count($columns) > 0)
        {
          // Get column by key which is the PropertyName value.
          $column = $columns->Retrieve("NewName");
          $result = "{$column->PropertyName},{$column->RenameAs}";
          $result .= ",{$column->Caption}";

          $compare = "NewName,,Caption";
          LJC::OutputLogCompare($methodName, $result, $compare);
        }

        // Reset original column properties.
        // Matches by key which is the current PropertyName value.
        $manager->MapNames("NewName", "Name", "", "Caption");
      }
    }

    // Creates a PropertyNames list from the data definition.
    private static function PropertyNames(LJCDataManager $manager)
    {
      // See constructor for how to create $manager.
      $methodName = "PropertyNames()";
      $propertyNames = $manager->PropertyNames();
      LJC::RemoveString($propertyNames, "PrincipleFlag");
      LJC::RemoveString($propertyNames, "TitleID");

      $result = "";
      foreach ($propertyNames as $propertyName)
      {
        if (strlen(trim($result)) > 0)
        {
          $result .= ",";
        }
        $result .= $propertyName;
      }

      $compare = "ID,Name";
      LJC::OutputLogCompare("\r\n{$methodName}", $result, $compare);
    }

    // --------------------
    // ORM Methods

    // Creates an array of Data Objects from a Data Result rows array.
    private static function CreateDataCollection(LJCDataManager $manager)
    {
      $methodName = "CreateDataCollection";

      $rows = $manager->Load();
      $row = $rows[0];
      $id = LJCDbAccess::GetValue($row, "ID");
      $name = LJCDbAccess::GetValue($row, "Name");
      $result = "{$id},{$name}";

      // See constructor for how to create $manager.
      $items = new Items();
      $item = new Item();
      $items = $manager->CreateDataCollection($items, $item, $rows);
      if (null == $items
        || 0 == count($items))
      {
        echo("\$items was not created.");
      }
      else
      {
        $item = $items->RetrieveAtIndex(0);
        $compare = "{$item->ID},{$item->Name}";
        LJC::OutputLogCompare("\r\n{$methodName}", $result, $compare);
      }
    }

    // Populates a Data Object with values from a Data Result row.
    private static function CreateDataObject(LJCDataManager $manager)
    {
      $methodName = "CreateDataObject";

      $rows = $manager->Load();
      $row = $rows[0];
      $id = LJCDbAccess::GetValue($row, "ID");
      $name = LJCDbAccess::GetValue($row, "Name");
      $result = "{$id},{$name}";

      // See constructor for how to create $manager.
      $item = new Item();
      $item = $manager->CreateDataObject($item, $row);
      if (null == $item)
      {
        echo("\$item was not created.");
      }
      else
      {
        $compare = "{$item->ID},{$item->Name}";
        LJC::OutputLogCompare("\r\n{$methodName}", $result, $compare);
      }
    }

    // --------------------
    // Other Methods

    // Create the keys from the result.
    private static function CreateResultKeys(LJCDataManager $manager)
    {
      $methodName = "CreateResultKeys";

      // See constructor for how to create $manager.
      $rows = $manager->Load();

      // Get primary key and unique key.
      $keyNames = [
        "ID",
        "Name",
      ];
      $resultKeys = $manager->CreateResultKeys($rows, $keyNames);
      if (null == $resultKeys
        || 0 == count($resultKeys))
      {
        echo("{$methodName}: \$resultKeys were not created.");
      }
    }

    // --------------------
    // Helper Methods

    // Show the first row from a Load().
    private static function ShowRow(LJCDataManager $manager)
    {
      $rows = $manager->Load();
      $row = $rows[0];
      foreach ($row as $key => $value)
      {
        echo("\r\n\$key = {$key} \$value = {$value}");
      }
    }

    // Add record for testing.
    private static function TestAdd(LJCDataManager $manager
      , string $parentMethod, string $nameValue)
    {
      $methodName = "TestAdd()";

      // Add the test data.
		  $data = new LJCDbColumns();
		  $data->Add("Name", value: $nameValue);
		  $affectedCount = $manager->Add($data);
      $result = strval($affectedCount);

      $compare = "1";
      LJC::OutputLogCompare("{$parentMethod} - {$methodName}", $result, $compare);
    }

    // Delete record for testing.
    private static function TestDelete(LJCDataManager $manager
      , string $parentMethod, string $nameValue)
    {
      $methodName = "TestDelete()";

		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $value = $manager->Delete($keys);
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("{$parentMethod} - {$methodName}", $result
        , $compare);
    }

    // Retrieve record for testing.
    private static function TestRetrieve(LJCDataManager $manager
      , string $parentMethod, string $nameValue)
    {
      $methodName = "TestRetrieve()";

		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $rows = $manager->Retrieve($keys);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo("\r\nTestRetrieve");
        echo("\r\n{$manager->SQL}");
        echo("\r\nTestRetrieve() No data retrieved.");
      }
    }

    // Update record for testing.
    private static function TestUpdate(LJCDataManager $manager
      , string $parentMethod, string $nameValue, string $updateProperty 
      , string $updateValue)
    {
      $methodName = "TestUpdate()";

		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      
      $data = new LJCDbColumns();
		  $data->Add($updateProperty, value: $updateValue);
      $value = $manager->Update($keys, $data);
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("{$parentMethod} - {$methodName}", $result
        , $compare);
    }
  }
