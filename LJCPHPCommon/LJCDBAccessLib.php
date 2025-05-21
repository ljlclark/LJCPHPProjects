<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDBAccessLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
  // LJCCollectionLib: LJCCollectionBase
  // LJCDebugLib: LJCDebug

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
      $this->Debug = new LJCDebug("LJCDBAccessLib", "LJCConnectionValues"
        , "w", false);
      $this->Debug->IncludePrivate = true;

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
  // Static: GetValue()
  // Methods: Execute(), Load(), Retrieve(), GetConnection(), LoadTableSchema()
  //   , SetConnectionValues()
  /// <include path='items/LJCDbAccess/*' file='Doc/LJCDbAccess.xml'/>
  class LJCDbAccess
  {
    // ---------------
    // Static Functions

    // Returns a data value if the element exists,	otherwise it returns null.
    /// <include path='items/GetValue/*' file='Doc/LJCDbAccess.xml'/>
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
    public function __construct(LJCConnectionValues $connectionValues)
    {
      $this->Debug = new LJCDebug("LJCDBAccessLib", "LJCDbAccess"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->SetConnectionValues($connectionValues->DbServer
        , $connectionValues->DbName, $connectionValues->UserID
        , $connectionValues->Password);
    } // __construct()

    // ---------------
    // Public Data Methods - LJCDbAccess

    // Executes a non-query sql statement.
    /// <include path='items/Execute/*' file='Doc/LJCDbAccess.xml'/>
    public function Execute(string $sql) : int
    {
      $enabled = false;
      $this->Debug->BeginMethod("Execute", $enabled);
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

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Execute()

    // Loads the records using the provided SQL statement.
    /// <include path='items/Load/*' file='Doc/LJCDbAccess.xml'/>
    public function Load(string $sql) : ?array
    {
      $enabled = false;
      $this->Debug->BeginMethod("Load", $enabled);
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

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Load()

    // Retrieves a record using the provided SQL statement.
    /// <include path='items/Retrieve/*' file='Doc/LJCDbAccess.xml'/>
    public function Retrieve(string $sql) : ?array
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);
      $retValue = null;

      $rows = $this->Load($sql);
      if ($rows != null)
      {
        $retValue = $rows[0];
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Retrieve()

    // ---------------
    // Other Public Methods - LJCDbAccess

    // Creates the PDO connection.
    /// <include path='items/GetConnection/*' file='Doc/LJCDbAccess.xml'/>
    public function GetConnection()
    {
      $enabled = false;
      $this->Debug->BeginMethod("GetConnection", $enabled);
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
        Writer::WriteLine("Connection failed: ".$e->getMessage());
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetConnection()

    // Returns the Table Schema LJCDbColumns collection.
    /// <include path='items/LoadTableSchema/*' file='Doc/LJCDbAccess.xml'/>
    public function LoadTableSchema(string $dbName, string $tableName)
      : ?LJCDbColumns
    {
      $enabled = false;
      $this->Debug->BeginMethod("LoadTableSchema", $enabled);
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

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // LoadTableSchema()

    // Sets the $ConnectionValues property.
    /// <include path='items/SetConnectionValues/*' file='Doc/LJCDbAccess.xml'/>
    public function SetConnectionValues(string $dbServer, string $dbName
      , string $userID, string $password)
    {
      $enabled = false;
      $this->Debug->BeginMethod("SetConnectionValues", $enabled);

      $this->ConnectionValues = new LJCConnectionValues($dbServer, $dbName
        , $userID, $password);

      $this->Debug->EndMethod($enabled);
    } // SetConnectionValues()

    // ---------------
    // Private Methods - LJCDbAccess

    // Creates the Table Schema LJCDbColumn object.
    // <include path='items/GetTableSchema/*' file='Doc/LJCDbAccess.xml'/>
    private function GetTableSchema(array $row) : LJCDbColumn
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("GetTableSchema", $enabled);
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

      $this->Debug->EndMethod($enabled);
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
    // Coverts MySQL type names to PHP type names.
    /// <include path='items/GetDataType/*' file='Doc/LJCDbColumn.xml'/>
    public static function GetDataType(string $mySQLTypeName) : string
    {
      $retValue = "string";

      switch ($mySQLTypeName)
      {
        case "bit":
          $retValue = "bool";
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
      $this->Debug = new LJCDebug("LJCDBAccessLib", "LJCDbColumn"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->AllowDbNull = false;
      $this->AutoIncrement = false;
      $this->ColumnName = $columnName;
      $this->DataTypeName = $dataTypeName;
      $this->DefaultValue = null;
      $this->MaxLength = 0;
      $this->MySQLTypeName = null;
      if (null == $propertyName)
      {
        $propertyName = $columnName;
      }
      $this->PropertyName = $propertyName;
      $this->RenameAs = $renameAs;
      $this->Value = $value;
      $this->WhereBoolOperator = "and";
      $this->WhereCompareOperator = "=";
    } // __construct()

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      $retValue = new self();

      $retValue->AllowDbNull = $this->AllowDbNull;
      $retValue->AutoIncrement = $this->AutoIncrement;
      $retValue->ColumnName = $this->ColumnName;
      $retValue->DataTypeName = $this->DataTypeName;
      $retValue->DefaultValue = $this->DefaultValue;
      $retValue->MaxLength = $this->MaxLength;
      $retValue->MySQLTypeName = $this->MySQLTypeName;
      $retValue->PropertyName = $this->PropertyName;
      $retValue->RenameAs = $this->RenameAs;
      $retValue->Value = $this->Value;
      $retValue->WhereBoolOperator = $this->WhereBoolOperator;
      $retValue->WhereCompareOperator = $this->WhereCompareOperator;

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // ---------------
    // Properties - LJCDbColumn

    /// <summary>Indicates if the Column allows nulls.</summary>
    public bool $AllowDbNull;

    /// <summary>The AutoIncrement flag.</summary>
    public bool $AutoIncrement;

    /// <summary>The Column name.</summary>
    public string $ColumnName;

    /// <summary>The DataType name.</summary>
    public string $DataTypeName;

    /// <summary>The Default value.</summary>
    public $DefaultValue;

    /// <summary>The MaxLength value.</summary>
    public $MaxLength;

    /// <summary>The MySQL Type name.</summary>
    public ?string $MySQLTypeName;

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
  // Methods: Add(), AddObject(), Clone(), Get(), GetColumns(), MapNames()
  //   , Retrieve(), SetWhereOperators()
  /// <include path='items/LJCDbColumns/*' file='Doc/LJCDbColumns.xml'/>
  class LJCDbColumns extends LJCCollectionBase
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->Debug = new LJCDebug("LJCDBAccessLib", "LJCDbColumns"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    } // __construct()

    // ---------------
    // Public Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCDbColumns.xml'/>
    public function Add(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, string $dataTypeName = "string"
      , ?string $value = null, $key = null) : ?LJCDbColumn
    {
      $enabled = false;
      $this->Debug->BeginMethod("Add", $enabled);
      $retValue = null;

      if (null == $propertyName)
      {
        $propertyName = $columnName;
      }
      if (null == $key)
      {
        $key = $propertyName;
      }

      $item = new LJCDbColumn($columnName, $propertyName, $renameAs
        , $dataTypeName, $value);
      $retValue = $this->AddObject($item , $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    public function AddObject(LJCDbColumn $item, $key = null) : ?LJCDbColumn
    {
      $enabled = false;
      $this->Debug->BeginMethod("AddObject", $enabled);

      if (null == $key)
      {
        $key = $item->PropertyName;
      }
      $retValue = $this->AddItem($item, $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // AddObject()

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // Get the item by Key value.
    // Obsolete: Use Retrieve().
    public function Get($key, bool $throwError = true) : ?LJCDbColumn
    {
      return $this->Retrieve($key, $throwError);
    } // Get()

    // Get the column definitions that match the property names.
    /// <include path='items/GetColumns/*' file='Doc/LJCDbColumns.xml'/>
    public function GetColumns(array $propertyNames = null) : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("GetColumns", $enabled);
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

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetColumns()

    // Sets the PropertyName, RenameAs and Caption values for a column.
    /// <include path='items/MapNames/*' file='Doc/LJCDbColumns.xml'/>
    public function MapNames(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, ?string $caption = null)
    {
      $enabled = false;
      $this->Debug->BeginMethod("MapNames", $enabled);

      $dbColumn = $this->Get($columnName);
      if ($dbColumn != null)
      {
        if ($propertyName != null)
        {
          $dbColumn->PropertyName = $propertyName;
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

      $this->Debug->EndMethod($enabled);
    } // MapNames()

    // Get the item by Key value.
    /// <include path='items/Get/*' file='Doc/LJCDbColumns.xml'/>
    public function Retrieve($key, bool $throwError = true) : ?LJCDbColumn
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);
      $retValue = $this->RetrieveItem($key, $throwError);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Retrieve()

    // Sets the Where Clause operators.
    /// <include path='items/SetWhereOperators/*' file='Doc/LJCDbColumns.xml'/>
    public function SetWhereOperators($key, string $compareOperator
      ,  string $boolOperator = "and") : void
    {
      $enabled = false;
      $this->Debug->BeginMethod("SetWhereOperator", $enabled);

      $item = $this->Get($key);
      if ($item != null)
      {
        $item->WhereBoolOperator = $boolOperator;
        $item->WhereCompareOperator = $compareOperator;
      }

      $this->Debug->EndMethod($enabled);
    } // SetWhereOperators()
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
      $this->Debug = new LJCDebug("LJCDBAccessLib", "LJCJoin"
        , "w", false);
      $this->Debug->IncludePrivate = true;

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
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);

      $retValue = new self();
      $retValue->LJCDbColumns = $this->LJCDbColumns->Clone();
      $retValue->JoinOns = $this->JoinOns->Clone();
      $retValue->JoinType = $this->JoinType;
      $retValue->TableAlias = $this->TableAlias;
      $retValue->TableName = $this->TableName;

      $this->Debug->EndMethod($enabled);
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
  // Methods: Add(), AddObject(), Clone(), Retrieve()
  /// <summary>Represents a collection of LJCJoin objects.</summary>
  class LJCJoins extends LJCCollectionBase
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->Debug = new LJCDebug("LJCDBAccessLib", "LJCJoins"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    } // __construct()

    // ---------------
    // Public Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCJoins.xml'/>
    public function Add(string $tableName, string $tableAlias = null
      , $key = null) : ?LJCJoin
    {
      $enabled = false;
      $this->Debug->BeginMethod("Add", $enabled);
      $retValue = null;

      if (null == $key)
      {
        $key = $tableName;
      }

      $item = new LJCJoin($tableName, $tableAlias);
      $retValue = $this->AddObject($item , $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCJoins.xml'/>
    public function AddObject(LJCJoin $item, $key = null) : ?LJCJoin
    {
      $enabled = false;
      $this->Debug->BeginMethod("AddObject", $enabled);

      if (null == $key)
      {
        $key = $item->TableName;
      }
      $retValue = $this->AddItem($item, $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // AddObject()

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // Get the item by Key value.
    // Obsolete: Use Retrieve().
    public function Get($key, bool $throwError = true) : ?LJCJoin
    {
      return $this->Retrieve($key, $throwError);
    } // Get()

    // Get the item by Key value.
    /// <include path='items/Get/*' file='Doc/LJCJoins.xml'/>
    public function Retrieve($key, bool $throwError = true) : ?LJCJoin
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);
      $retValue = $this->RetrieveItem($key, $throwError);

      $this->Debug->EndMethod($enabled);
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
      $this->Debug = new LJCDebug("LJCDBAccessLib", "LJCJoinOn"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->BooleanOperator = "and";
      $this->FromColumnName = $fromColumnName;
      $this->JoinOnOperator = "=";
      $this->JoinOns = null;
      $this->ToColumnName = $toColumnName;
    } // __construct()

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      $retValue = new self();

      $retValue->BooleanOperator = $this->BooleanOperator;
      $retValue->FromColumnName = $this->FromColumnName;
      $retValue->JoinOnOperator = $this->JoinOnOperator;
      $retValue->JoinOns = $this->JoinOns;
      $retValue->ToColumnName = $this->ToColumnName;

      $this->Debug->EndMethod($enabled);
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
  class LJCJoinOns extends LJCCollectionBase
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->Debug = new LJCDebug("LJCDBAccessLib", "LJCJoinOns"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    } // __construct()

    // ---------------
    // Public Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCJoinOns.xml'/>
    public function Add(string $fromColumnName, string $toColumnName
      , $key = null) : ?LJCJoinOn
    {
      $enabled = false;
      $this->Debug->BeginMethod("Add", $enabled);
      $retValue = null;

      if (null == $key)
      {
        $key = $fromColumnName;
      }

      $item = new LJCJoinOn($fromColumnName, $toColumnName);
      $retValue = $this->AddObject($item , $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCJoinOns.xml'/>
    public function AddObject(LJCJoinOn $item, $key = null) : ?LJCJoinOn
    {
      $enabled = false;
      $this->Debug->BeginMethod("AddObject", $enabled);

      if (null == $key)
      {
        $key = $item->FromColumnName;
      }
      $retValue = $this->AddItem($item, $key);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // AddObject()

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $enabled = false;
      $this->Debug->BeginMethod("Clone", $enabled);
      $retValue = new self();

      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Clone()

    // Get the item by Key value.
    // Obsolete: Use Retrieve().
    public function Get($key, bool $throwError = true) : ?LJCJoinOn
    {
      return $this.Retrieve($key,$throwError);
    } // Get()

    // Get the item by Key value.
    /// <include path='items/Get/*' file='Doc/LJCJoinOns.xml'/>
    public function Retrieve($key, bool $throwError = true) : ?LJCJoinOn
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);

      $retValue = $this->RetrieveItem($key, $throwError);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Retrieve()
  } // LJCJoinOns
?>