<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDBAccessLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  // LJCCollectionLib: LJCCollectionBase
  // LJCTextLib: LJCWriter

  /// <summary>The PDO Data Access Library</summary>
  /// LibName: LJCDBAccessLib
  //  Classes: LJCConnectionValues, LJCDbAccess
  //    , LJCDbColumn, LJCDbColumns
  //    , LJCJoin, LJCJoins
  //    , LJCJoinOn, LJCJoinOns

  // ***************
  /// <summary>Contains the Connection values.</summary>
  class LJCConnectionValues
  {
    // Initializes a class instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCConnectionValues.xml'/>
    public function __construct(string $dbServer
      , string $dbName, string $userID, string $password)
    {
      $this->DbServer = $dbServer;
      $this->DbName = $dbName;
      $this->UserID = $userID;
      $this->Password = $password;
    } // __construct()

    // ---------------
    // Properties - LJCConnectionValues

    /// <summary>The Database name.</summary>
    public string $DbName;

    /// <summary>The DB Server name.</summary>
    public string $DbServer;

    /// <summary>The user Password.</summary>
    public string $Password;

    /// <summary>The User ID.</summary>
    public string $UserID;
  }  // LJCConnectionValues

  // ***************
  // Provides standard PDO Data Access.
  /// <include path='items/LJCDbAccess/*' file='Doc/LJCDbAccess.xml'/>
  /// <group name="Static">Static Methods</group>
  //    GetValue()
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="Data">Data Methods</group>
  //    Execute(), Load(), Retrieve()
  /// <group name="Other">Other Methods</group>
  //    GetConnection(), LoadTableSchema(), SetConnectionValues()
  class LJCDbAccess
  {
    // ---------------
    // Static Methods

    // Returns a data value if the element exists,	otherwise it returns null.
    /// <include path='items/GetValue/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Static</ParentGroup>
    public static function GetValue(array $row, string $columnName)
    {
      $retValue = null;

      if (array_key_exists($columnName, $row)) 
      {
        $retValue = $row[$columnName];
      }
      return $retValue;
    } // GetValue()

    // Initializes a class instance.
    /// <include path='items/construct/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct(LJCConnectionValues $connectionValues)
    {
      $this->SetConnectionValues($connectionValues->DbServer
        , $connectionValues->DbName, $connectionValues->UserID
        , $connectionValues->Password);
    } // __construct()

    // ---------------
    // Data Methods - LJCDbAccess

    // Executes a non-query sql statement.
    /// <include path='items/Execute/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Execute(string $sql) : int
    {
      $retValue = 0;

      $connection = $this->GetConnection();
      if ($connection != null)
      {
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $statement = $connection->prepare($sql);
        $statement->execute();
        $retValue = $statement->rowCount();
        $connection = null;
      }
      return $retValue;
    } // Execute()

    // Loads the records using the provided SQL statement.
    /// <include path='items/Load/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Load(string $sql) : ?array
    {
      $retValue = null;

      $connection = $this->GetConnection();
      if ($connection != null)
      {
        $result = $connection->query($sql);
        if ($result != null)
        {
          $retValue = $result->fetchAll(PDO::FETCH_ASSOC);
        }
        $connection = null;
      }
      return $retValue;
    } // Load()

    // Retrieves a record using the provided SQL statement.
    /// <include path='items/Retrieve/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve(string $sql) : ?array
    {
      $retValue = null;

      $rows = $this->Load($sql);
      if ($rows != null)
      {
        $retValue = $rows[0];
      }
      return $retValue;
    } // Retrieve()

    // ---------------
    // Other Methods - LJCDbAccess

    // Creates the PDO connection.
    /// <include path='items/GetConnection/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function GetConnection()
    {
      $retValue = null;

      $values = $this->ConnectionValues;
      try
      {
        $dsn = "mysql:host=$values->DbServer;dbname=$values->DbName";
        $retValue = new PDO($dsn, $values->UserID, $values->Password);
        $retValue->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      }
      catch(PDOException $e)
      {
        $retValue = null;
        LJCWriter::WriteLine("Connection failed: ".$e->getMessage());
      }
      return $retValue;
    } // GetConnection()

    // Returns the Table Schema LJCDbColumns collection.
    /// <include path='items/LoadTableSchema/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function LoadTableSchema(string $dbName, string $tableName)
      : ?LJCDbColumns
    {
      $retValue = null;

      $sql = "SELECT table_schema, table_name, column_name, column_default"
        . ", is_nullable, data_type, character_maximum_length\r\n"
        . " from INFORMATION_SCHEMA.COLUMNS\r\n"
        . " where table_schema = '$dbName' and table_name = '$tableName'\r\n";
      $rows = $this->Load($sql);
      if ($rows != null)
      {
        $retValue = new LJCDbColumns();
        foreach($rows as $row)
        {
          $dbColumn = $this->GetTableSchema($row);
          $retValue->AddObject($dbColumn, $dbColumn->ColumnName);
        }
      }
      $this->Connection = null;
      return $retValue;
    } // LoadTableSchema()

    // Sets the $ConnectionValues property.
    /// <include path='items/SetConnectionValues/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function SetConnectionValues(string $dbServer, string $dbName
      , string $userID, string $password)
    {
      $this->ConnectionValues = new LJCConnectionValues($dbServer, $dbName
        , $userID, $password);
    } // SetConnectionValues()

    // ---------------
    // Private Methods - LJCDbAccess

    // Creates the Table Schema LJCDbColumn object.
    // <include path='items/GetTableSchema/*' file='Doc/LJCDbAccess.xml'/>
    private function GetTableSchema(array $row) : LJCDbColumn
    {
      $retValue = new LJCDbColumn("");

      $retValue->AllowDbNull = (bool)self::GetValue($row, "IS_NULLABLE");
      $retValue->AutoIncrement = false;
      $retValue->ColumnName = self::GetValue($row, "COLUMN_NAME");
      $retValue->DefaultValue = self::GetValue($row, "COLUMN_DEFAULT");
      $retValue->MaxLength = self::GetValue($row, "CHARACTER_MAXIMUM_LENGTH");
      $retValue->MySQLTypeName = self::GetValue($row, "DATA_TYPE");
      $retValue->DataTypeName = LJCDbColumn::GetDataType($retValue->MySQLTypeName);
      $retValue->PropertyName = self::GetValue($row, "COLUMN_NAME");
      $retValue->RenameAs = null;
      $retValue->Value = null;
      return $retValue;
    } // GetTableSchema()

    /// <summary>The Database Connection values.</summary>
    public LJCConnectionValues $ConnectionValues;
  }  // LJCDbAccess

  // ***************
  // Represents a DB Column definition.
  // Static: GetDataType()
  // Methods: Clone()
  /// <include path='items/LJCDbColumn/*' file='Doc/LJCDbColumn.xml'/>
  class LJCDbColumn
  {
    // ---------------
    // Static Methods

    // *** New Method ***
    /// <summary>
    ///   Creates a new typed object with existing standard object values.
    /// </summary>
    /// <param name="objColumn"></param>
    /// <returns>The new LJCDbColumn object.</returns>
    public static function Copy($objColumn)
    {
      $retDataColumn = null;

      // Check for required values.
      if (property_exists($objColumn, "PropertyName"))
      {
        $retColumn = new LJCDbColumn($objColumn->PropertyName);

        // Look for properties of standard object in typed object.
        foreach ($objColumn as $propertyName => $value)
        {
          if (property_exists($retColumn, $propertyName))
          {
            // Update new typed object properties from the standard object.
            $success = false;
            $columnValue = $retColumn->$propertyName;
            $objValue = $objColumn->$propertyName;
            if (is_int($columnValue))
            {
              $retColumn->$propertyName = (int)$objValue;
              $success = true;
            }
            if (!$success
              && is_float($columnValue))
            {
              $retColumn->$propertyName = (float)$objValue;
              $success = true;
            }
            if (!$success)
            {
              $retColumn->$propertyName = $objValue;
            }
          }
        }
      }
      return $retColumn;
    }

    // Coverts MySQL type names to PHP type names.
    /// <include path='items/GetDataType/*' file='Doc/LJCDbColumn.xml'/>
    public static function GetDataType(string $mySQLTypeName): string
    {
      $retValue = "string";

      switch ($mySQLTypeName)
      {
        case "bit":
          //$retValue = "bool";
          $retValue = "int";
          break;

        case "int":
        case "smallint":
          $retValue = "int";
          break;
      }
      return $retValue;
    } // GetDataType()

    // Initializes a class instance.
    /// <include path='items/construct/*' file='Doc/LJCDbColumn.xml'/>
    public function __construct(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, string $dataTypeName = "string"
      , ?string $value = null)
    {
      $this->AllowDbNull = false;
      $this->AutoIncrement = false;
      $this->ColumnName = $columnName;
      $this->DataTypeName = $dataTypeName;
      $this->DefaultValue = null;
      $this->InsertIndex = 0;
      $this->MaxLength = 0;
      $this->MySQLTypeName = null;
      $this->Position = 0;
      if (null == $propertyName)
      {
        $propertyName = $columnName;
      }
      $this->Caption = $propertyName;
      $this->PropertyName = $propertyName;
      $this->RenameAs = $renameAs;
      $this->Value = $value;
      $this->WhereBoolOperator = "and";
      $this->WhereCompareOperator = "=";
    } // __construct()

    /// <summary>Creates an object clone.</summary>
    public function Clone(): self
    {
      $retValue = new self($this->ColumnName);

      $retValue->AllowDbNull = $this->AllowDbNull;
      $retValue->AutoIncrement = $this->AutoIncrement;
      $retValue->Caption = $this->Caption;
      $retValue->DataTypeName = $this->DataTypeName;
      $retValue->DefaultValue = $this->DefaultValue;
      $retValue->MaxLength = $this->MaxLength;
      $retValue->MySQLTypeName = $this->MySQLTypeName;
      $retValue->PropertyName = $this->PropertyName;
      $retValue->RenameAs = $this->RenameAs;
      $retValue->Value = $this->Value;
      $retValue->WhereBoolOperator = $this->WhereBoolOperator;
      $retValue->WhereCompareOperator = $this->WhereCompareOperator;
      return $retValue;
    } // Clone()

    // ---------------
    // Properties - LJCDbColumn

    /// <summary>Indicates if the Column allows nulls.</summary>
    public bool $AllowDbNull;

    /// <summary>The AutoIncrement flag.</summary>
    public bool $AutoIncrement;

    /// <summary>The Caption value.</summary>
    public string $Caption;

    /// <summary>The Column name.</summary>
    public string $ColumnName;

    /// <summary>The DataType name.</summary>
    public string $DataTypeName;

    /// <summary>The Default value.</summary>
    public $DefaultValue;

    /// <summary>The insert index used in LJCDbColumns.InsertObject()</summary>
    public ?int $InsertIndex;

    /// <summary>The MaxLength value.</summary>
    public ?int $MaxLength;

    /// <summary>The MySQL Type name.</summary>
    public ?string $MySQLTypeName;

    /// <summary>The fixed length field position value.</summary>
    public ?int $Position;

    /// <summary>The Property name.</summary>
    public string $PropertyName;

    /// <summary>The RenameAs value.</summary>
    public ?string $RenameAs;

    /// <summary>The Column value.</summary>
    public $Value;

    /// <summary>The Where clause boolean operator.</summary>
    public ?string $WhereBoolOperator;

    /// <summary>The Where clause comparison operator.</summary>
    public ?string $WhereCompareOperator;
  }  // LJCDbColumn

  // ***************
  // Represents a collection of LJCDbColumn objects.
  /// <include path='items/LJCDbColumns/*' file='Doc/LJCDbColumns.xml'/>
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="DataClass">Data Class Methods</group>
  //    Clone()
  /// <group name="Data">Data Methods</group>
  //    Add(), AddObject(), Delete(), Retrieve()
  /// <group name="Other">Other Methods</group>
  //    Columns(), MapNames(), PropertyNames(), SetWhereOperators()
  /// <group name="Debug">Debug Methods</group>
  //    DebugDbColumns(), DebugKeys(), DebugPropertyNames()
  class LJCDbColumns extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    // *** New Method ***
    /// <summary>Create collection from array.</summary>
    /// <param name="items">The items array.</param>
    /// <returns>The collection></returns.
    public static function Collection($items)
    {
      $retColumns = new LJCDbColumns();

      if (isset($items)
        && LJC::HasElements($items->Items))
      {
        foreach ($items->Items as $objDataColumn)
        {
          // Create typed object from stdClass.
          $dataColumn = LJCDbColumn::Copy($objDataColumn);
          $retColumns->AddObject($dataColumn);
        }
      }
      return $retColumns;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
    } // __construct()

    // ---------------
    // Data Class Methods

    /// <summary>Creates an object clone.</summary>
    /// <ParentGroup>DataClass</ParentGroup>
    public function Clone(): self
    {
      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item, key: $key);
      }
      unset($item);
      return $retValue;
    } // Clone()

    // ---------------
    // Data Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCDbColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Add(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, string $dataTypeName = "string"
      , ?string $value = null, $key = null): ?LJCDbColumn
    {
      $retValue = null;

      if (null == $propertyName)
      {
        $propertyName = $columnName;
      }

      $caption = $propertyName;
      if (null == $key)
      {
        $key = $propertyName;
      }

      $item = new LJCDbColumn($columnName, $propertyName, $renameAs
        , $dataTypeName, $value);
      $item->Caption = $caption;
      $retValue = $this->AddObject($item , $key);
      return $retValue;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(LJCDbColumn $item, $key = null): ?LJCDbColumn
    {
      if (null == $item->PropertyName)
      {
        $item->PropertyName = $item->ColumnName;
      }
      if (null == $item->Caption)
      {
        $item->Caption = $item->PropertyName;
      }
      if (null == $key)
      {
        $key = $item->PropertyName;
      }
      // AddItem() is in LJCCollectionBase.
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    } // AddObject()

    /// <summary>
    ///   Adds another collection of objects to this collection.
    /// </summary>
    public function AddObjects(LJCDbColumns $items)
    {
      foreach ($items as $item)
      {
        $this->AddObject($item);
      }
    }

    /// <summary>Inserts an object at the provided insert index.</summary>
    /// <param name="$insertItem>The insert item.</param>
    /// <param name="$key">The insert item key.</param>
    /// <returns>The inserted item.</returns>
    public function InsertObject(LJCDbColumn $insertItem, int $insertIndex
      , $key = null): ?LJCDbColumn
    {
      $process = true;
      $retItem = null;

      if (null == $key)
      {
        $key = $insertItem->PropertyName;
      }

      // Just add object if insert index is beyond end of the array.
      if ($insertIndex > count($this->Items) - 1)
      {
        $this->AddObject($insertItem);
        $process = false;
      }

      if ($process
        && $insertIndex >= 0)
      {
        // Create new items with inserted item.
        $tempItems = [];
        for ($index = 0; $index < count($this->Items); $index++)
        {
          // RetrieveIndex() is in LJCCollectionBase.
          $item = $this->RetrieveIndex($index);

          // Insert before insert index.
          if ($index == $insertIndex)
          {
            $key = $insertItem->PropertyName;

            if (isset($this->Items[$key]))
            {
              throw new Exception("Key: {$key} is already in use.");
            }
            $tempItems[$key] = $insertItem;
            $retItem = $insertItem;
          }

          $tempItems[$item->PropertyName] = $item;
        }

        // Replace items with new items.
        $this->Items = $tempItems;
      }
      return $retItem;
    }

    // Delete the item by Key value.
    /// <ParentGroup>Data</ParentGroup>
    public function Delete($key, bool $throwError = true): void
    {
      // DeleteItem() is in LJCCollectionBase.
      $this->DeleteItem($key, $throwError);
    }

    // Retrieves the item by Key value.
    /// <include path='items/Retrieve/*' file='Doc/LJCDbColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve($key, bool $throwError = true): ?LJCDbColumn
    {
      // RetrieveItem() is in LJCCollectionBase.
      $retValue = $this->RetrieveItem($key, $throwError);
      return $retValue;
    } // Retrieve()

    // ---------------
    // Other Methods

    // Get the column definitions that match the property names.
    /// <include path='items/GetColumns/*' file='Doc/LJCDbColumns.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function Columns(array $propertyNames = null): self
    {
      $retValue = null;

      if (null == $propertyNames)
      {
        $retValue = $this->Clone();
      }
      else
      {
        $retValue = new self();
        foreach ($propertyNames as $propertyName)
        {
          if (array_key_exists($propertyName, $this->Items))
          {
            $retValue->AddObject($this->Items[$propertyName]);
          }
        }
      }
      return $retValue;
    } // GetColumns()

    // Sets the PropertyName, RenameAs and Caption values for a column.
    /// <include path='items/MapNames/*' file='Doc/LJCDbColumns.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function MapNames(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, ?string $caption = null): void
    {
      $dbColumn = $this->Retrieve($columnName);
      if ($dbColumn != null)
      {
        if ($propertyName != null)
        {
          // Change Key
          $dbColumn = $dbColumn->Clone();
          $dbColumn->PropertyName = $propertyName;
          $this->DeleteItem($columnName);
          $this->AddObject($dbColumn);
        }
        if ($renameAs != null)
        {
          $dbColumn->RenameAs = $renameAs;
        }
        if ($caption != null)
        {
          $dbColumn->Caption = $caption;
        }
      }
    } // MapNames()

    // Creates a PropertyNames list from the data definition.
    /// <ParentGroup>Other</ParentGroup>
    public function PropertyNames(): array
    {
      $retValue = [];

      foreach ($this as $dbColumn)
      {
        $retValue[] = $dbColumn->PropertyName;
      }
      return $retValue;
    }

    /// <summary>Get a clone of item objects.</summary>
    public function ReadItems()
    {
      $retArray = [];

      foreach ($this->Items as $dbColumn) {
        $retArray[] = clone $dbColumn;
      }
     return $retArray;
    }

    // Sets the Where Clause operators.
    /// <include path='items/SetWhereOperators/*' file='Doc/LJCDbColumns.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function SetWhereOperators($key, string $compareOperator
      ,  string $boolOperator = "and"): void
    {
      $item = $this->Retrieve($key);
      if ($item != null)
      {
        $item->WhereBoolOperator = $boolOperator;
        $item->WhereCompareOperator = $compareOperator;
      }
    } // SetWhereOperators()

    // ---------------
    // Debug Methods

    // Output DbColumn information.
    /// <ParentGroup>Debug</ParentGroup>
    public static function DebugDbColumns(LJCDbColumns $dbColumns
      , string $location = null): void
    {
      $text = "DebugDbColumns:";
      if ($location != null)
      {
        $text .= " {$location}";
      }
      LJC::Debug(0, $text);
      foreach ($dbColumns as $dbColumn)
      {
        LJC::Debug(0, "dbColumn-ColumnName", $dbColumn->ColumnName);
        LJC::Debug(0, "dbColumn-PropertyName", $dbColumn->PropertyName);
        if ($dbColumn->Value != null)
        {
          LJC::Debug(0, "dbColumn-Value", $dbColumn->Value);
        }
      }
      LJC::Debug();
    }

    // Output Collection Keys information.
    /// <ParentGroup>Debug</ParentGroup>
    public static function DebugKeys(LJCCollectionBase $collection
      , string $location = null): void
    {
      $text = "DebugKeys:";
      if ($location != null)
      {
        $text .= " {$location}";
      }
      LJC::Debug(0, $text);
      $keys = $collection->GetKeys();
      foreach ($keys as $key)
      {
        LJC::Debug(0, "key", $key);
      }
      LJC::Debug();
    }

    // Output property names.
    /// <ParentGroup>Debug</ParentGroup>
    public static function DebugPropertyNames(array $propertyNames
      , string $location = null): void
    {
      $text = "DebugPropertyNames:";
      if ($location != null)
      {
        $text .= " {$location}";
      }
      LJC::Debug(0, $text);
      foreach ($propertyNames as $propertyName)
      {
        LJC::Debug(0, "propertyName", $propertyName);
      }
      LJC::Debug();
    }
  }  // LJCDbColumns

  // ***************
  // Method: Clone()
  /// <summary>Represents a SQL Join.</summary>
  class LJCJoin
  {
    // Initializes a class instance.
    /// <include path='items/construct/*' file='Doc/LJCJoin.xml'/>
    public function __construct(string $tableName, ?string $tableAlias = null)
    {
      $this->Columns = new LJCDbColumns();
      $this->JoinOns = new LJCJoinOns();
      $this->JoinType = "left";
      $this->SchemaName = null;
      $this->TableAlias = $tableAlias;
      $this->TableName = $tableName;
    } // __construct()

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->LJCDbColumns = $this->LJCDbColumns->Clone();
      $retValue->JoinOns = $this->JoinOns->Clone();
      $retValue->JoinType = $this->JoinType;
      $retValue->TableAlias = $this->TableAlias;
      $retValue->TableName = $this->TableName;
      return $retValue;
    } // Clone()

    // ---------------
    // Properties - LJCJoin

    // The included join table columns.
    public LJCDbColumns $Columns;

    /// <summary>The JoinOn definintions.</summary>
    public LJCJoinOns $JoinOns;

    /// <summary>The Join type.</summary>
    public string $JoinType;

    /// <summary>The Schema name.</summary>
    public ?string $SchemaName;

    /// <summary>The table alias.</summary>
    public ?string $TableAlias;

    /// <summary>The table name.</summary>
    public string $TableName;
  } // LJCJoin

  // ***************
  /// <summary>Represents a collection of LJCJoin objects.</summary>
  /// <group name="DataClass">Data Class Methods</group>
  //    Clone()
  /// <group name="Data">Data Methods</group>
  //    Add(), AddObject(), Retrieve()
  class LJCJoins extends LJCCollectionBase
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
    } // __construct()

    // ---------------
    // Data Class Methods

    /// <summary>Creates an object clone.</summary>
    /// <ParentGroup>DataClass</ParentGroup>
    public function Clone(): self
    {
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);
      return $retValue;
    } // Clone()

    // ---------------
    // Data Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCJoins.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Add(string $tableName, string $tableAlias = null
      , $key = null): ?LJCJoin
    {
      $retValue = null;

      if (null == $key)
      {
        $key = $tableName;
      }

      $item = new LJCJoin($tableName, $tableAlias);
      $retValue = $this->AddObject($item , $key);
      return $retValue;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCJoins.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(LJCJoin $item, $key = null): ?LJCJoin
    {
      if (null == $key)
      {
        $key = $item->TableName;
      }
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    } // AddObject()

    // Retrieves the item by Key value.
    /// <include path='items/Retrieve/*' file='Doc/LJCJoins.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve($key, bool $throwError = true): ?LJCJoin
    {
      $retValue = $this->RetrieveItem($key, $throwError);
      return $retValue;
    } // Retrieve()
  } // LJCJoins

  // ***************
  // Methods: Clone()
  /// <summary>Represents a SQL JoinOn.</summary>
  class LJCJoinOn
  {
    // Initializes a class instance.
    /// <include path='items/construct/*' file='Doc/LJCJoinOn.xml'/>
    public function __construct(string $fromColumnName, string $toColumnName)
    {
      $this->BooleanOperator = "and";
      $this->FromColumnName = $fromColumnName;
      $this->JoinOnOperator = "=";
      $this->JoinOns = null;
      $this->ToColumnName = $toColumnName;
    } // __construct()

    /// <summary>Creates an object clone.</summary>
    public function Clone(): self
    {
      $retValue = new self();

      $retValue->BooleanOperator = $this->BooleanOperator;
      $retValue->FromColumnName = $this->FromColumnName;
      $retValue->JoinOnOperator = $this->JoinOnOperator;
      $retValue->JoinOns = $this->JoinOns;
      $retValue->ToColumnName = $this->ToColumnName;
      return $retValue;
    } // Clone()

    // ---------------
    // Properties - LJCJoinOn

    /// <summary>The Boolean Operator value.</summary>
    public string $BooleanOperator;

    /// <summary>The 'From' column name.</summary>
    public string $FromColumnName;

    /// <summary>The Join On Operator.</summary>
    public string $JoinOnOperator;

    /// <summary>The contained JoinOns.</summary>
    public ?array $JoinOns;

    /// <summary>The 'To' column name.</summary>
    public string $ToColumnName;
  } // LJCJoinOn

  // ***************
  // Methods: Add(), AddObject(), Retrieve()
  /// <summary>Represents a collection of LJCJoin objects.</summary>
  /// <group name="DataClass">Data Class Methods</group>
  //    Clone()
  /// <group name="Data">Data Methods</group>
  //    Add(), AddObject(), Retrieve()
  class LJCJoinOns extends LJCCollectionBase
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
    } // __construct()

    // ---------------
    // Data Class Methods

    /// <summary>Creates an object clone.</summary>
    /// <ParentGroup>DataClass</ParentGroup>
    public function Clone(): self
    {
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);
      return $retValue;
    } // Clone()

    // ---------------
    // Data Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCJoinOns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Add(string $fromColumnName, string $toColumnName
      , $key = null): ?LJCJoinOn
    {
      $retValue = null;

      if (null == $key)
      {
        $key = $fromColumnName;
      }

      $item = new LJCJoinOn($fromColumnName, $toColumnName);
      $retValue = $this->AddObject($item , $key);
      return $retValue;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCJoinOns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(LJCJoinOn $item, $key = null): ?LJCJoinOn
    {
      if (null == $key)
      {
        $key = $item->FromColumnName;
      }
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    } // AddObject()

    // Retrieves the item by Key value.
    /// <include path='items/Retrieve/*' file='Doc/LJCJoinOns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve($key, bool $throwError = true): ?LJCJoinOn
    {
      $retValue = $this->RetrieveItem($key, $throwError);
      return $retValue;
    } // Retrieve()
  } // LJCJoinOns
?>
