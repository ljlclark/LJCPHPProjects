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
      // Name changed to PropertyName in MapNames().
      $this->PropertyName = "";
    }

    /// <summary>Creates an object clone.</summary>
    public function Clone(): self
    {
      $retValue = new self();

      $retValue->ID = $this->ID;
      $retValue->PropertyName = $this->PropertyName;
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
        $key = $item->PropertyName;
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
      self::CreateResultKeys();
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
		  $data = new LJCDbColumns();
		  $data->Add("Name", value: $nameValue);
		  $value = $manager->Add($data);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

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
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $value = $manager->Delete($keys);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("$methodName", $result, $compare);
    } // Delete

    // Creates the Delete SQL.
    private static function DeleteSQL(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "DeleteSQL()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Delete with SQLExecute().
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $sql = $manager->DeleteSQL($keys);
      $value = $manager->SQLExecute($sql);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("$methodName", $result, $compare);
    } // DeleteSQL()

    // Loads the records for the provided values.
    private static function Load(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "Load()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Load the test data.
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
      self::TestAdd($manager, $methodName, $nameValue);

      // Load with SQLLoad().
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
    } // LoadSQL()

    // Retrieves the record for the provided values.
    private static function Retrieve(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $methodName = "Retrieve()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Retrieve the test data.
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
      self::TestAdd($manager, $methodName, $nameValue);

      // Retrieve with SQLLoad().
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value: $nameValue);
      $sql = $manager->RetrieveSQL($keyColumns);
      $rows = $manager->SQLLoad($sql);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\n{$methodName} No data retrieved.");
      }

      self::TestDelete($manager, $methodName, $nameValue);
    } // RetrieveSQL()

    // Updates the records for the provided values.
    private static function Update(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $updateValue = "Updated";
      $methodName = "Update()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Update the test data.
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $data = new LJCDbColumns();
		  $data->Add("Name", value: $updateValue);
      $value = $manager->Update($keys, $data);
      $result = strval($value);

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
      self::TestAdd($manager, $methodName, $nameValue);

      // Update with SQLExecute().
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $data = new LJCDbColumns();
		  $data->Add("Name", value: $updateValue);
      $sql = $manager->UpdateSQL($keys, $data);
      $value = $manager->SQLExecute($sql);
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("{$methodName} - SQLExecute()", $result, $compare);

      self::TestRetrieve($manager, $methodName, $updateValue);
      self::TestDelete($manager, $methodName, $updateValue);
    } // UpdateSQL()

    // Executes an Add, Delete or Update SQL statement.
    private static function SQLExecute(LJCDataManager $manager)
    {
      $nameValue = "NameValue";
      $updateValue = "Updated";
      $methodName = "SQLExecute()";
      self::TestAdd($manager, $methodName, $nameValue);

      // Update with SQLExecute().
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $data = new LJCDbColumns();
		  $data->Add("Name", value: $updateValue);
      $sql = $manager->UpdateSQL($keys, $data);
      $value = $manager->SQLExecute($sql);
      $result = strval($value);

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

    // Get the column definitions that match the property names.
    private static function Columns(LJCDataManager $manager)
    {
      $propertyNames = [
        "ID",
        "Name",
      ];

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

    // Sets the PropertyName, RenameAs and Caption values for a column.
    private static function MapNames(LJCDataManager $manager)
    {
      $methodName = "MapNames";

      $propertyNames = [
        "Name",
      ];
      $columns = $manager->Columns($propertyNames);
      if ($columns != null
        && count($columns) > 0)
      {
        $manager->MapNames("Name", "PropertyName", "", "Caption");
        $propertyNames = [
          "PropertyName",
        ];
        $columns = $manager->Columns($propertyNames);
        if (null == $columns
          || 0 == count($columns))
        {
          echo("{$methodName}: PropertyName change failed.");
        }

        if ($columns != null
          && count($columns) > 0)
        {
          $column = $columns->Retrieve("PropertyName");
          $result = "{$column->PropertyName},{$column->RenameAs}";
          $result .= ",{$column->Caption}";

          $compare = "PropertyName,,Caption";
          LJC::OutputLogCompare($methodName, $result, $compare);
        }
      }
    }

    // Creates a PropertyNames list from the data definition.
    private static function PropertyNames(LJCDataManager $manager)
    {
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

      // Name changed to PropertyName in MapNames().
      $compare = "ID,PropertyName";
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
        // Name changed to PropertyName in MapNames().
        $compare = "{$item->ID},{$item->PropertyName}";
        LJC::OutputLogCompare("\r\n{$methodName}", $result, $compare);
      }
    }

    // Populates a Data Object with values from a Data Result row.
    private static function CreateDataObject(LJCDataManager $manager)
    {

    }

    // --------------------
    // Other Methods

    // Create the keys from the result.
    private static function CreateResultKeys()
    {

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
      // Add the test data.
		  $data = new LJCDbColumns();
		  $data->Add("Name", value: $nameValue);
		  $value = $manager->Add($data);
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("{$parentMethod} - TestAdd()", $result, $compare);
    }

    // Delete record for testing.
    private static function TestDelete(LJCDataManager $manager
      , string $parentMethod, string $nameValue)
    {
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $value = $manager->Delete($keys);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("{$parentMethod} - TestDelete()", $result
        , $compare);
    }

    // Retrieve record for testing.
    private static function TestRetrieve(LJCDataManager $manager
      , string $parentMethod, string $nameValue)
    {
		  $keys = new LJCDbColumns();
		  $keys->Add("Name", value: $nameValue);
      $rows = $manager->Retrieve($keys);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nSQLExecute() No data retrieved.");
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
