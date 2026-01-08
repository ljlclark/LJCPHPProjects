<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDBAccessLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  // LJCCommonLib: LJC
  // LJCCollectionLib: LJCCollectionBase
  // LJCTextLib: LJCWriter

  // The SQL data access library.
  /// <include path='items/LJCDBAccessLib/*' file='Doc/LJCDbAccess.xml'/>
    
  // The LibName: XML comment triggers the file (library) HTML page generation.
  // It generates a page with the same name as the library.
  // LJCDBAccessLib.html
  /// LibName: LJCDBAccessLib
  //  Classes: LJCConnectionValues, LJCDbAccess,
  //    LJCDataColumn, LJCDataColumns,
  //    LJCJoin, LJCJoins,
  //    LJCJoinOn, LJCJoinOns

  // ***************
  /// <summary>Contains the Connection values.</summary>

  // A class triggers the class HTML page generation.
  // It generates a page with the same name as the class.
  // LJCDBAccess/LJCConnectionValues.html
  class LJCConnectionValues
  {
    // ---------------
    // Constructor Methods - LJCConnectionValues

    // Initializes a class instance with the provided values.
    /// <include path='items/Constructor/*' file='Doc/LJCConnectionValues.xml'/>

    // A method triggers the method HTML page generation.
    // It generates a page with the name: class plus method.
    // LJCConnectionValues/LJCConnectionValuesconstruct.html
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
    // A property triggers the property HTML page generation.
    // It generates a page with the same name as the class plus property.
    // LJCConnectionValues/LJCConnectionValues$DbName.html
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
    // Static Methods - LJCDbAccess

    // Returns a data value if the element exists,	otherwise it returns null.
    /// <include path='items/GetValue/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Static</ParentGroup>
    public static function GetValue(?array $row, string $columnName)
    {
      $retValue = null;

      if ($row != null
        && array_key_exists($columnName, $row)) 
      {
        $retValue = $row[$columnName];
      }
      return $retValue;
    } // GetValue()

    // ---------------
    // Constructor Methods - LJCDbAccess

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

    // Returns the Table Schema LJCDataColumns collection.
    /// <include path='items/LoadTableSchema/*' file='Doc/LJCDbAccess.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function LoadTableSchema(string $dbName, string $tableName)
      : ?LJCDataColumns
    {
      $retValue = null;

      $sql = "SELECT table_schema, table_name, column_name, column_default"
        . ", is_nullable, data_type, character_maximum_length\r\n"
        . " from INFORMATION_SCHEMA.COLUMNS\r\n"
        . " where table_schema = '$dbName' and table_name = '$tableName'\r\n";
      $rows = $this->Load($sql);
      if ($rows != null)
      {
        $retValue = new LJCDataColumns();
        foreach($rows as $row)
        {
          $dbColumn = $this->GetTableSchema($row);
          $retValue->AddObject($dbColumn, $dbColumn->ColumnName);
        }
      }
      $this->Connection = null;
      return $retValue;
    } // LoadTableSchema()

    // Resets the $ConnectionValues property.
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

    // Creates the Table Schema LJCDataColumn object.
    // <include path='items/GetTableSchema/*' file='Doc/LJCDbAccess.xml'/>
    private function GetTableSchema(array $row) : LJCDataColumn
    {
      $retValue = new LJCDataColumn("");

      $retValue->AllowDbNull = (bool)self::GetValue($row, "IS_NULLABLE");
      $retValue->AutoIncrement = false;
      $retValue->ColumnName = self::GetValue($row, "COLUMN_NAME");
      $retValue->DefaultValue = self::GetValue($row, "COLUMN_DEFAULT");
      $retValue->MaxLength = self::GetValue($row, "CHARACTER_MAXIMUM_LENGTH");
      $retValue->MySQLTypeName = self::GetValue($row, "DATA_TYPE");
      $retValue->DataTypeName = LJCDataColumn::GetDataType($retValue->MySQLTypeName);
      $retValue->PropertyName = self::GetValue($row, "COLUMN_NAME");
      $retValue->RenameAs = null;
      $retValue->Value = null;
      return $retValue;
    } // GetTableSchema()

    /// <summary>The Database Connection values.</summary>
    public LJCConnectionValues $ConnectionValues;
  }  // LJCDbAccess

  // ***************
  // Represents a Data Column definition.
  // Static: GetDataType()
  // Methods: Copy(), GetDataType(), Clone()
  /// <include path='items/LJCDataColumn/*' file='Doc/LJCDataColumn.xml'/>
  class LJCDataColumn
  {
    // ---------------
    // Static Methods - LJCDataColumn

    // Creates a new typed object with existing standard object values.
    /// <include path='items/Copy/*' file='Doc/LJCDataColumn.xml'/>
    public static function Copy($objColumn)
    {
      $retColumn = null;

      // Check for required values.
      if (property_exists($objColumn, "PropertyName"))
      {
        $retColumn = new LJCDataColumn($objColumn->PropertyName);

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
    /// <include path='items/GetDataType/*' file='Doc/LJCDataColumn.xml'/>
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

    // ---------------
    // Constructor Methods - LJCDataColumn

    // Initializes a class instance.
    /// <include path='items/Constructor/*' file='Doc/LJCDataColumn.xml'/>
    public function __construct(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, string $dataTypeName = "string"
      , ?string $value = null)
    {
      $this->ColumnName = $columnName;
      if (null == $propertyName)
      {
        $propertyName = $columnName;
      }
      $this->PropertyName = $propertyName;
      $this->RenameAs = $renameAs;
      $this->DataTypeName = $dataTypeName;
      $this->Value = $value;

      $this->AllowDbNull = false;
      $this->AutoIncrement = false;
      $this->Caption = $propertyName;
      $this->DefaultValue = null;
      $this->InsertIndex = 0;
      // ***** 
      //$this->MaxLength = 0;
      $this->MaxLength = -1;
      $this->MySQLTypeName = null;
      $this->Position = 0;
      $this->WhereBoolOperator = "and";
      $this->WhereCompareOperator = "=";
    } // __construct()

    // ---------------
    // Data Class Methods - LJCDataColumn

    // Creates an object clone.
    /// <include path='items/Clone/*' file='Doc/LJCDataColumn.xml'/>
    public function Clone(): self
    {
      $retColumn = new self($this->ColumnName);

      $retColumn->AllowDbNull = $this->AllowDbNull;
      $retColumn->AutoIncrement = $this->AutoIncrement;
      $retColumn->Caption = $this->Caption;
      $retColumn->DataTypeName = $this->DataTypeName;
      $retColumn->DefaultValue = $this->DefaultValue;
      $retColumn->InsertIndex = $this->InsertIndex;
      $retColumn->MaxLength = $this->MaxLength;
      $retColumn->MySQLTypeName = $this->MySQLTypeName;
      $retColumn->PropertyName = $this->PropertyName;
      $retColumn->RenameAs = $this->RenameAs;
      $retColumn->Value = $this->Value;
      $retColumn->WhereBoolOperator = $this->WhereBoolOperator;
      $retColumn->WhereCompareOperator = $this->WhereCompareOperator;
      return $retColumn;
    } // Clone()

    // ---------------
    // Properties - LJCDataColumn

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

    /// <summary>The insert index used in LJCDataColumns.InsertObject()</summary>
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
  }  // LJCDataColumn

  // ***************
  // Represents a collection of LJCDataColumn objects.
  /// <include path='items/LJCDataColumns/*' file='Doc/LJCDataColumns.xml'/>
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="Static">Static Methods</group>
  //    ToCollection()
  /// <group name="DataClass">Data Class Methods</group>
  //    Clone()
  /// <group name="Data">Content Methods</group>
  //    Add(), AddObject(), Remove(), Retrieve()
  /// <group name="Other">Other Methods</group>
  //    SelectItems(), MapNames(), KeyNames(), SetWhereOperators(), ToArray()
  /// <group name="Debug">Debug Methods</group>
  //    DebugItems(), DebugKeys(), DebugPropertyNames()
  class LJCDataColumns extends LJCCollectionBase
  {
    // ---------------
    // Static Methods - LJCDataColumns

    // Create typed collection from deserialized JavasScript collection.
    /// <include path='items/ToCollection/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Static</ParentGroup>
    public static function ToCollection($items)
    {
      $retCollection = new LJCDataColumns();

      // ReadItems is in the JavaScript collection.
      if (isset($items)
        && $items->Count > 0)
      {
        foreach ($items->ReadItems as $objItem)
        {
          // Create typed object from stdClass.
          $item = LJCDataColumn::Copy($objItem);
          $retCollection->AddObject($item);
        }
      }
      return $retCollection;
    } // ToCollection()

    // ---------------
    // Constructor Methods - LJCDataColumns

    // Initializes a class instance.
    /// <include path='items/ToCollection/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
      $this->ClassName = "LJCDataColumns";
    } // __construct()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // ---------------
    // Data Class Methods - LJCDataColumns

    // Creates an object clone.
    /// <include path='items/Clone/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>DataClass</ParentGroup>
    public function Clone(): self
    {
      $retCollection = new self();

      foreach ($this->Items as $key => $item)
      {
        $retCollection->AddObject($item, $key);
      }
      unset($item);
      return $retCollection;
    } // Clone()

    // ---------------
    // Collection Methods - LJCDataColumns

    // Creates a KeyNames list from the collection.
    /// <include path='items/KeyNames/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function KeyNames(): array
    {
      $retKeyNames = [];

      foreach ($this as $item)
      {
        $retKeyNames[] = $item->PropertyName;
      }
      return $retKeyNames;
    } // KeyNames()

    // Get the items that match the key names array values.
    /// <include path='items/SelectItems/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function SelectItems(array $keyNames = null): self
    {
      $retItems = null;

      if (null == $keyNames)
      {
        $retItems = $this->Clone();
      }
      else
      {
        $retItems = new self();
        foreach ($keyNames as $keyName)
        {
          if (array_key_exists($keyName, $this->Items))
          {
            $retItems->AddObject($this->Items[$keyName]);
          }
        }
      }
      return $retItems;
    } // SelectItems()

    // Get an array of item objects.
    /// <include path='items/ToArray/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function ToArray()
    {
      $retArray = [];

      foreach ($this->Items as $item)
      {
        $retArray[] = clone $item;
      }
      return $retArray;
    }

    // ---------------
    // Data Methods - LJCDataColumns

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Add(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, string $dataTypeName = "string"
      , ?string $value = null, $key = null): ?LJCDataColumn
    {
      $methodName = "Add()";
      $retItem = null;

      if (null == $propertyName)
      {
        $propertyName = $columnName;
      }
      $caption = $propertyName;

      if (null == $key)
      {
        $key = $propertyName;
      }

      $item = new LJCDataColumn($columnName, $propertyName, $renameAs
        , $dataTypeName, $value);
      $item->Caption = $caption;
      $retItem = $this->AddObject($item, $key);
      return $retItem;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(LJCDataColumn $item, $key = null): ?LJCDataColumn
    {
      $methodName = "AddObject()";

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
      $retItem = $this->AddItem($item, $key);
      return $retItem;
    } // AddObject()

    // Adds another collection of objects to this collection.
    /// <include path='items/AddObjects/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObjects(LJCDataColumns $items)
    {
      foreach ($items as $item)
      {
        $this->AddObject($item);
      }
    }

    // Inserts an object at the provided insert index.
    /// <include path='items/InsertObject/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function InsertObject(LJCDataColumn $insertItem, int $insertIndex
      , $key = null): ?LJCDataColumn
    {
      $methodName = "InsertObject()";
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
      if ($insertIndex < 0)
      {
        $insertIndex = 0;
      }

      if ($process)
      {
        // Create new items with inserted item.
        $tempItems = [];
        for ($index = 0; $index < count($this->Items); $index++)
        {
          // RetrieveWithIndex() is in LJCCollectionBase.
          $item = $this->RetrieveWithIndex($index);

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
    } // InsertObject()

    // Removes the item by Key value.
    /// <include path='items/Remove/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Remove($key, bool $throwError = true): void
    {
      // DeleteItem() is in LJCCollectionBase.
      $this->DeleteItem($key, $throwError);
    }

    // Retrieves the item by Key value.
    /// <include path='items/Retrieve/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve($key, bool $throwError = true): ?LJCDataColumn
    {
      // RetrieveItem() is in LJCCollectionBase.
      $retItem = $this->RetrieveItem($key, $throwError);
      return $retItem;
    } // Retrieve()

    // ---------------
    // Other Methods - LJCDataColumns

    // Sets the PropertyName, RenameAs and Caption values for a column.
    /// <include path='items/MapNames/*' file='Doc/LJCDataColumns.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function MapNames(string $key, ?string $propertyName = null
      , ?string $renameAs = null, ?string $caption = null): void
    {
      $methodName = "MapNames()";

      $dbColumn = $this->Retrieve($key);
      if ($dbColumn != null)
      {
        if ($propertyName != null)
        {
          // Change Key
          $dbColumn = $dbColumn->Clone();
          $dbColumn->PropertyName = $propertyName;
          $this->DeleteItem($key);
          $this->AddObject($dbColumn);
          // ***** 
          //self::DebugItems($this, $methodName);
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

    // Sets the Where Clause operators.
    /// <include path='items/SetWhereOperators/*' file='Doc/LJCDataColumns.xml'/>
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
    // Debug Methods - LJCDataColumns

    // Output LJCDataColumns information.
    /// <ParentGroup>Debug</ParentGroup>
    public static function DebugItems(LJCDataColumns $dbColumns
      , string $location = null): void
    {
      $output = new Output("LJCDataColumns");

      $text = "LJCDataColumns.DebugItems:";
      if ($location != null)
      {
        $text .= " {$location}";
      }
      echo("\r\n");
      $output->Log(__line__, $text, "-");
      foreach ($dbColumns as $item)
      {
        $output->Log(__line__, "\$item-ColumnName", $item->ColumnName);
        $output->Log(__line__, "\$item-PropertyName", $item->PropertyName);
        if ($item->Value != null)
        {
          $output->Log(__line__, "\$item-Value", $item->Value);
        }
      }
    } // DebugItems()

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
      LJC::OutputDebugValue(0, $text);
      $keys = $collection->GetKeys();
      foreach ($keys as $key)
      {
        LJC::OutputDebugValue(0, "key", $key);
      }
      LJC::OutputDebugValue();
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
      LJC::OutputDebugValue(0, $text);
      foreach ($propertyNames as $propertyName)
      {
        LJC::OutputDebugValue(0, "propertyName", $propertyName);
      }
      LJC::OutputDebugValue();
    }

    // ---------------
    // Properties - LJCDataColumns

    /// <summary>The debug text.</summary>
    public string $DebugText;
  }  // LJCDataColumns

  // ***************
  // Method: Clone()
  // Represents a SQL Join.
  /// <include path='items/construct/*' file='Doc/LJCJoin.xml'/>
  class LJCJoin
  {
    // ---------------
    // Constructor Methods - LJCJoin

    // Initializes a class instance.
    /// <include path='items/Constructor/*' file='Doc/LJCJoin.xml'/>
    public function __construct(string $tableName, ?string $tableAlias = null)
    {
      $this->TableName = $tableName;
      $this->TableAlias = $tableAlias;

      $this->Columns = new LJCDataColumns();
      $this->JoinOns = new LJCJoinOns();
      $this->JoinType = "left";
      $this->SchemaName = null;
    } // __construct()

    // ---------------
    // Data Class Methods - LJCJoin

    /// <summary>Creates an object clone.</summary>
    /// <include path='items/Clone/*' file='Doc/LJCJoin.xml'/>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->LJCDataColumns = $this->LJCDataColumns->Clone();
      $retValue->JoinOns = $this->JoinOns->Clone();
      $retValue->JoinType = $this->JoinType;
      $retValue->TableAlias = $this->TableAlias;
      $retValue->TableName = $this->TableName;
      return $retValue;
    } // Clone()

    // ---------------
    // Properties - LJCJoin

    /// <summary>The included join table columns.</summary>
    public LJCDataColumns $Columns;

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
  // Represents a collection of LJCJoin objects.
  /// <include path='items/LJCJoins/*' file='Doc/LJCJoins.xml'/>
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="DataClass">Data Class Methods</group>
  //    Clone()
  /// <group name="Data">Content Methods</group>
  //    Add(), AddObject(), Retrieve()
  class LJCJoins extends LJCCollectionBase
  {
    // ---------------
    // Constructor Methods - LJCJoins

    // Initializes a class instance.
    /// <include path='items/Constructor/*' file='Doc/LJCJoins.xml'/>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
      $this->ClassName = "LJCJoins";
    } // __construct()

    // ---------------
    // Data Class Methods - LJCJoins

    // Creates an object clone.
    /// <include path='items/Clone/*' file='Doc/LJCJoins.xml'/>
    /// <ParentGroup>DataClass</ParentGroup>
    public function Clone(): self
    {
      $retCollection = new self();

      foreach ($this->Items as $key => $item)
      {
        $retCollection->AddObject($item, $key);
      }
      unset($item);
      return $retCollection;
    } // Clone()

    // ---------------
    // Data Methods - LJCJoins

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCJoins.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Add(string $tableName, string $tableAlias = null
      , $key = null): ?LJCJoin
    {
      $retItem = null;

      if (null == $key)
      {
        $key = $tableName;
      }

      $item = new LJCJoin($tableName, $tableAlias);
      $retItem = $this->AddObject($item , $key);
      return $retItem;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCJoins.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(LJCJoin $item, $key = null): ?LJCJoin
    {
      $methodName = "AddObject()";

      if (null == $key)
      {
        $key = $item->TableName;
      }

      $retItem = $this->AddItem($item, $key);
      return $retItem;
    } // AddObject()

    // Retrieves the item by Key value.
    /// <include path='items/Retrieve/*' file='Doc/LJCJoins.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve($key, bool $throwError = true): ?LJCJoin
    {
      $retValue = $this->RetrieveItem($key, $throwError);
      return $retValue;
    } // Retrieve()

    // ---------------
    // Properties - LJCJoins

    /// <summary>The debug text.</summary>
    public string $DebugText;
  } // LJCJoins

  // ***************
  // Represents a SQL JoinOn
  /// <include path='items/LJCJoinOn/*' file='Doc/LJCJoinOn.xml'/>
  class LJCJoinOn
  {
    // Initializes a class instance.
    /// <include path='items/Constructor/*' file='Doc/LJCJoinOn.xml'/>
    public function __construct(string $fromColumnName, string $toColumnName)
    {
      $this->BooleanOperator = "and";
      $this->FromColumnName = $fromColumnName;
      $this->JoinOnOperator = "=";
      $this->JoinOns = null;
      $this->ToColumnName = $toColumnName;
    } // __construct()

    // Creates an object clone.
    /// <include path='items/Constructor/*' file='Doc/LJCJoinOn.xml'/>
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
  // Represents a collection of LJCJoin objects.
  /// <include path='items/LJCJoinOns/*' file='Doc/LJCJoinOn.xml'/>
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="DataClass">Data Class Methods</group>
  //    Clone()
  /// <group name="Data">Content Methods</group>
  //    Add(), AddObject(), Retrieve()
  class LJCJoinOns extends LJCCollectionBase
  {
    // Initializes a class instance.
    /// <include path='items/Constructor/*' file='Doc/LJCJoinOn.xml'/>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
      $this->ClassName = "LJCJoinOns";
    } // __construct()

    // ---------------
    // Data Class Methods - LJCJoinOns

    // Creates an object clone.
    /// <include path='items/Clone/*' file='Doc/LJCJoinOn.xml'/>
    /// <ParentGroup>DataClass</ParentGroup>
    public function Clone(): self
    {
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item, $key);
      }
      unset($item);
      return $retValue;
    } // Clone()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // ---------------
    // Data Methods - LJCJoinOns

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCJoinOns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Add(string $fromColumnName, string $toColumnName
      , $key = null): ?LJCJoinOn
    {
      $methodName = "Add()";
      $retItem = null;

      if (null == $key)
      {
        $key = $fromColumnName;
      }

      $item = new LJCJoinOn($fromColumnName, $toColumnName);
      $retItem = $this->AddObject($item, $key);
      return $retItem;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCJoinOns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(LJCJoinOn $item, $key = null): ?LJCJoinOn
    {
      $methodName = "AddObject()";

      if (null == $key)
      {
        $key = $item->FromColumnName;
      }

      // AddItem() is in LJCCollectionBase.
      $retItem = $this->AddItem($item, $key);
      return $retItem;
    } // AddObject()

    // Retrieves the item by Key value.
    /// <include path='items/Retrieve/*' file='Doc/LJCJoinOns.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve($key, bool $throwError = true): ?LJCJoinOn
    {
      // RetrieveItem() is in LJCCollectionBase.
      $retItem = $this->RetrieveItem($key, $throwError);
      return $retItem;
    } // Retrieve()

    // ---------------
    // Properties - LJCJoinOns

    /// <summary>The debug text.</summary>
    public string $DebugText;
  } // LJCJoinOns
?>
