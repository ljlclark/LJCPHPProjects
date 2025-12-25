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
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Add()", $result, $compare);

      // Retrieve the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $rows = $manager->Retrieve($keyColumns);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nAdd() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Add() - Delete()", $result, $compare);
    } // Add()

    // Deletes the records for the provided values.
    private static function Delete(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Delete() - Add()", $result, $compare);

      // Delete the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Delete()", $result, $compare);
    } // Delete

    // Creates the Delete SQL.
    private static function DeleteSQL(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("DeleteSQL() - Add()", $result, $compare);

      // Delete the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $sql = $manager->DeleteSQL($keyColumns);
      $value = $manager->SQLExecute($sql);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("DeleteSQL()", $result, $compare);
    } // DeleteSQL()

    // Loads the records for the provided values.
    private static function Load(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Load() - Add()", $result, $compare);

      // Load the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $rows = $manager->Load($keyColumns);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nLoad() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Load() - Delete()", $result, $compare);
    } // Load()

    // Creates the Load SQL.
    private static function LoadSQL(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Load() - Add()", $result, $compare);

      // Load the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $sql = $manager->LoadSQL($keyColumns);
      $rows = $manager->SQLLoad($sql);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nLoadSQL() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("LoadSQL() - Delete()", $result, $compare);
    } // LoadSQL()

    // Retrieves the record for the provided values.
    private static function Retrieve(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Retrieve() - Add()", $result, $compare);

      // Retrieve the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $rows = $manager->Retrieve($keyColumns);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nRetrieve() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Retrieve() - Delete()", $result, $compare);
    } // Retrieve()

    // Creates the Retrieve SQL.
    private static function RetrieveSQL(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("RetrieveSQL() - Add()", $result, $compare);

      // Retrieve the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $sql = $manager->RetrieveSQL($keyColumns);
      $rows = $manager->SQLLoad($sql);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nRetrieveSQL() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("RetrieveSQL() - Delete()", $result, $compare);
    } // RetrieveSQL()

    // Updates the records for the provided values.
    private static function Update(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Update() - Add()", $result, $compare);

      // Update the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");

      $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"UpdatedNameValue");
      $value = $manager->Update($keyColumns, $dataColumns);
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Update()", $result, $compare);

      // Retrieve the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"UpdatedNameValue");
      $rows = $manager->Retrieve($keyColumns);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nUpdate() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("Update() - Delete()", $result, $compare);
    } // Update()

    // Creates the Update SQL.
    private static function UpdateSQL(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("UpdateSQL() - Add()", $result, $compare);

      // Update the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");

      $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"UpdatedNameValue");
      $sql = $manager->UpdateSQL($keyColumns, $dataColumns);
      $value = $manager->SQLExecute($sql);
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("UpdateSQL() - SQLExecute()", $result, $compare);

      // Retrieve the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"UpdatedNameValue");
      $rows = $manager->Retrieve($keyColumns);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nUpdate() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("UpdateSQL() - Delete()", $result, $compare);
    } // UpdateSQL()

    // Executes an Add, Delete or Update SQL statement.
    private static function SQLExecute(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("SQLExecute() - Add()", $result, $compare);

      // Update the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");

      $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"UpdatedNameValue");
      $sql = $manager->UpdateSQL($keyColumns, $dataColumns);
      $value = $manager->SQLExecute($sql);
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("SQLExecute()", $result, $compare);

      // Retrieve the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"UpdatedNameValue");
      $rows = $manager->Retrieve($keyColumns);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nSQLExecute() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("SQLExecute() - Delete()", $result, $compare);
    } // SQLExecute()

    // Executes a Select SQL statement.
    private static function SQLLoad(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("SQLLoad() - Add()", $result, $compare);

      // Retrieve the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $sql = $manager->LoadSQL($keyColumns);
      $rows = $manager->SQLLoad($sql);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nSQLLoad() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("SQLLoad() - Delete()", $result, $compare);
    } // SQLLoad()

    // Executes a Select SQL statement.
    private static function SQLRetrieve(LJCDataManager $manager)
    {
      // Add the test data.
		  $dataColumns = new LJCDbColumns();
		  $dataColumns->Add("Name", value:"NameValue");
		  $value = $manager->Add($dataColumns);
      if ($value < 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("SQLRetrieve() - Add()", $result, $compare);

      // Retrieve the test data.
		  $keyColumns = new LJCDbColumns();
		  $keyColumns->Add("Name", value:"NameValue");
      $sql = $manager->RetrieveSQL($keyColumns);
      $rows = $manager->SQLRetrieve($sql);
      if (null == $rows
        || !is_array($rows)
        || 0 == count($rows))
      {
        echo($manager->SQL);
        echo("\r\nSQLRetrieve() No data retrieved.");
      }

      // Delete the test data.
      $value = $manager->Delete($keyColumns);
      if ($value > 1)
      {
        echo($manager->SQL);
      }
      $result = strval($value);

      $compare = "1";
      LJC::OutputLogCompare("SQLRetrieve() - Delete()", $result, $compare);
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
      $propertyNames = [
        "Name",
      ];
      // ***** 
      echo("\r\n\r\nMapNames()");
      self::ShowRow($manager);

      $columns = $manager->Columns($propertyNames);
      if ($columns != null
        && count($columns) > 0)
      {
        $manager->MapNames("Name", "PropertyName", "RenameAs", "Caption");
        $propertyNames = [
          "PropertyName",
        ];

        $columns = $manager->Columns($propertyNames);
        if (null == $columns
          || 0 == count($columns))
        {
          echo("MapNames: PropertyName change failed.");
        }

        if ($columns != null
          && count($columns) > 0)
        {
          $column = $columns->Retrieve("PropertyName");
          $result = "{$column->PropertyName},{$column->RenameAs}";
          $result .= ",{$column->Caption}";

          $compare = "PropertyName,RenameAs,Caption";
          LJC::OutputLogCompare("MapNames()", $result, $compare);
        }
      }
    }

    // Creates a PropertyNames list from the data definition.
    private static function PropertyNames(LJCDataManager $manager)
    {
      $propertyNames = $manager->PropertyNames();
      //LJC::RemoveString($propertyNames, "PrincipleFlag");
      //LJC::RemoveString($propertyNames, "TitleID");

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
      LJC::OutputLogCompare("PropertyNames()", $result, $compare);
    }

    // --------------------
    // ORM Methods

    // Creates an array of Data Objects from a Data Result rows array.
    private static function CreateDataCollection(LJCDataManager $manager)
    {
      $rows = $manager->Load();
      $row = $rows[0];
      // ***** 
      echo("\r\n\r\nCreateDataCollection()");
      self::ShowRow($manager);

      $id = LJCDbAccess::GetValue($row, "ID");
      // Name changed to PropertyName in MapNames().
      $propertyName = LJCDbAccess::GetValue($row, "PropertyName");
      $result = "{$id},{$propertyName}";

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
        LJC::OutputLogCompare("PropertyNames()", $result, $compare);
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

    private static function ShowRow(LJCDataManager $manager)
    {
      $rows = $manager->Load();
      $row = $rows[0];
      foreach ($row as $key => $value)
      {
        echo("\r\n\$key = {$key} \$value = {$value}");
      }
    }
  }
