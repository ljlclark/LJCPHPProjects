<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCDBAccessLib.php
  declare(strict_types=1);
  $webCommonPath = "c:/inetpub/wwwroot/LJCPHPCommon";

  /// <summary>The PDO Data Access Library</summary>
  /// LibName: LJCDBAccessLib

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
    }

    // ---------------
    // Properties

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
  // <summary>Provides standard PDO Data Access.</summary>
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
    }

    // Initializes a class instance.
    /// <include path='items/construct/*' file='Doc/LJCDbAccess.xml'/>
    public function __construct(LJCConnectionValues $connectionValues)
    {
      $this->SetConnectionValues($connectionValues->DbServer
        , $connectionValues->DbName, $connectionValues->UserID
        , $connectionValues->Password);
    }

    // ---------------
    // Public Data Methods

    // Executes a non-query sql statement.
    /// <include path='items/Execute/*' file='Doc/LJCDbAccess.xml'/>
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
    }

    // Loads the records using the provided SQL statement.
    /// <include path='items/Load/*' file='Doc/LJCDbAccess.xml'/>
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
    }

    // Retrieves a record using the provided SQL statement.
    /// <include path='items/Retrieve/*' file='Doc/LJCDbAccess.xml'/>
    public function Retrieve(string $sql) : ?array
    {
      $retValue = null;

      $rows = $this->Load($sql);
      if ($rows != null)
      {
        $retValue = $rows[0];
      }
      return $retValue;
    }

    // ---------------
    // Other Public Methods

    // Creates the PDO connection.
    /// <include path='items/GetConnection/*' file='Doc/LJCDbAccess.xml'/>
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
        Writer::WriteLine("Connection failed: ".$e->getMessage());
      }
      return $retValue;
    }

    // Returns the Table Schema LJCDbColumns collection.
    /// <include path='items/LoadTableSchema/*' file='Doc/LJCDbAccess.xml'/>
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
    }

    // Sets the $ConnectionValues property.
    /// <include path='items/SetConnectionValues/*' file='Doc/LJCDbAccess.xml'/>
    public function SetConnectionValues(string $dbServer, string $dbName
      , string $userID, string $password)
    {
      $this->ConnectionValues = new LJCConnectionValues($dbServer, $dbName
        , $userID, $password);
    }

    // ---------------
    // Private Methods

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
    }

    /// <summary>The Database Connection values.</summary>
    public LJCConnectionValues $ConnectionValues;
  }  // LJCDbAccess

  // ***************
  // Represents a DB Column definition.
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
    }

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
    }

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
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
      return $retValue;
    }

    // ---------------
    // Properties

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
  /// <include path='items/LJCDbColumns/*' file='Doc/LJCDbColumns.xml'/>
  class LJCDbColumns extends LJCCollectionBase
  {
    // ---------------
    // Static Functions

    // Deserializes the data from an LJCDocDataFile XML file.
    /// <include path='items/Deserialize/*' file='Doc/LJCDocDataFile.xml'/>
    public static function Deserialize(string $xmlFileSpec) : ?LJCDbColumns
    {
      $retValue = null;

      $dbColumnsXML = new DbColumnsXML($xmlFileSpec);
      $retValue = $dbColumnsXML->Deserialize();
      return $retValue;
    }

    // ---------------
    // Public Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCDbColumns.xml'/>
    public function Add(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, string $dataTypeName = "string"
      , ?string $value = null, $key = null) : ?LJCDbColumn
    {
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
      return $retValue;
    }

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    public function AddObject(LJCDbColumn $item, $key = null) : ?LJCDbColumn
    {
      if (null == $key)
      {
        $key = $item->PropertyName;
      }
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    }

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);
      return $retValue;
    }

    // Get the item by Key value.
    /// <include path='items/Get/*' file='Doc/LJCDbColumns.xml'/>
    public function Get($key, bool $throwError = true) : ?LJCDbColumn
    {
      $retValue = $this->GetItem($key, $throwError);
      return $retValue;
    }

    // Get the column definitions that match the property names.
    /// <include path='items/GetColumns/*' file='Doc/LJCDbColumns.xml'/>
    public function GetColumns(array $propertyNames = null) : self
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
    }

    // Sets the PropertyName, RenameAs and Caption values for a column.
    /// <include path='items/MapNames/*' file='Doc/LJCDbColumns.xml'/>
    public function MapNames(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, ?string $caption = null)
    {
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
    }

    // Sets the Where Clause operators.
    /// <include path='items/SetWhereOperators/*' file='Doc/LJCDbColumns.xml'/>
    public function SetWhereOperators($key, string $compareOperator
      ,  string $boolOperator = "and") : void
    {
      $item = $this->Get($key);
      if ($item != null)
      {
        $item->WhereBoolOperator = $boolOperator;
        $item->WhereCompareOperator = $compareOperator;
      }
    }
  }  // LJCDbColumns

  // ***************
  // Provides methods to Serialize and Deserialize LJCDbColumns.
  class DbColumnsXML
  {
    /// <summary>Initializes a class instance with the provided values.</summary>
    /// <param name="$xmlFileSpec"></param>
    public function __construct(string $xmlFileSpec)
    {
      $this->XMLFileSpec = $xmlFileSpec;
      if (false == file_exists($xmlFileSpec))
      {
        throw new Exception("Input file '$xmlFileSpec' was not found.");
      }
      $this->DebugWriter = new LJCDebugWriter("DbColumnsXML");
    }

    /// <summary>Deserializes the XML file.</summary>
    /// <returns>The LJCDbColumns object.</returns>
    public function Deserialize() : ?LJCDbColumns
    {
      $retValue = null;

      $docNode = simplexml_load_file($this->XMLFileSpec);
      if (null != $docNode)
      {
        $retValue = $this->CreateDbColumns($docNode);
      }
      return $retValue;
    }

    // Deserialize columns from the Doc node.
    private function CreateDbColumns(SimpleXMLElement $docNode) : ?LJCDbColumns
    {
      $retValue = null;

      $dbColumnNodes = $docNode->children();
      if (null != $dbColumnNodes)
      {
        $this->Debug("dbColumnNodes");
        $retValue = new LJCDbColumns();
        foreach ($dbColumnNodes as $dbColumnNode)
        {
          $columnName = $this->Value($dbColumnNode->ColumnName);
          $this->Debug("columnName: $columnName");
          $propertyName = $this->Value($dbColumnNode->PropertyName);
          $renameAs = $this->Value($dbColumnNode->RenameAs);
          $dataTypeName = $this->Value($dbColumnNode->DataTypeName);
          $value = $this->Value($dbColumnNode->Value);
          $dbColumn = new LJCDbColumn($columnName, $propertyName, $renameAs
            , $dataTypeName, $value);
          $retValue->AddObject($dbColumn);
          //$dbColumn->AllowDbNull = $this->Value($dbColumnNode->AllowDbNull);
          //$dbColumn->AutoIncrement = $this->Value($dbColumnNode->AutoIncrement);
          $dbColumn->DefaultValue = $this->Value($dbColumnNode->DefaultValue);
          $dbColumn->MaxLength = $this->Value($dbColumnNode->MaxLength);
          $dbColumn->MySQLTypeName = $this->Value($dbColumnNode->MySQLTypeName);
          $dbColumn->WhereBoolOperator
            = $this->Value($dbColumnNode->WhereBoolOperator);
          $dbColumn->WhereCompareOperator
            = $this->Value($dbColumnNode->WhereCompareOperator);
        }
      }
      return $retValue;
    }  // CreateDbColumns()
  
    // Get the value from the XML value.
    // Potential Common function?
    private function Value(SimpleXMLElement $xmlValue, bool $trim = true)
      : ?string
    {
      $retValue = null;

      if ($xmlValue != null)
      {
        $retValue = (string)$xmlValue;
        if (true == $trim)
        {
          $retValue = trim($retValue);
        }
      }
      return $retValue;
    }

    public function SerializeToString(LJCDbColumns $dbColumns
      , $xmlFileName = null) : string
    {
      $builder = new LJCStringBuilder();

      $builder->AppendLine("<?xml version=\"1.0\"?>");
      $builder->Append("<!-- Copyright (c) Lester J. Clark 2022 -");
      $builder->AppendLine(" All Rights Reserved -->");
      if (null != $xmlFileName)
      {
        $builder->AppendLine("<!-- $xmlFileName -->");
      }
      $builder->Append("<LJCDocDataFile xmlns:xsd=");
      $builder->AppendLine("'http://www.w3.org/2001/XMLSchema'");
      $builder->Append("  xmlns:xsi=");
      $builder->AppendLine("'http://www.w3.org/2001/XMLSchema-instance'>");

      foreach ($dbColumns as $dbColumns)
      {

      }
    }

    // Output the debug value.
    private function Debug(string $text, bool $addLine = true) : void
    {
      $this->DebugWriter->Debug($text, $addLine);
    }
  }  // DbColumnsXML

  // ***************
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
    }

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
    }

    // ---------------
    // Properties

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
  }

  // ***************
  /// <summary>Represents a collection of LJCJoin objects.</summary>
  class LJCJoins extends LJCCollectionBase
  {
    // ---------------
    // Public Methods

    /// <summary>Creates an object and adds it to the collection.</summary>
    /// <include path='items/Add/*' file='Doc/LJCJoins.xml'/>
    public function Add(string $tableName, string $tableAlias = null
      , $key = null) : ?LJCJoin
    {
      $retValue = null;

      if (null == $key)
      {
        $key = $tableName;
      }

      $item = new LJCJoin($tableName, $tableAlias);
      $retValue = $this->AddObject($item , $key);
      return $retValue;
    }

    /// <summary>Adds an object and key value.</summary>
    /// <include path='items/AddObject/*' file='Doc/LJCJoins.xml'/>
    public function AddObject(LJCJoin $item, $key = null) : ?LJCJoin
    {
      if (null == $key)
      {
        $key = $item->TableName;
      }
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    }

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);
      return $retValue;
    }

    /// <summary>Get the item by Key value.</summary>
    /// <include path='items/Get/*' file='Doc/LJCJoins.xml'/>
    public function Get($key, bool $throwError = true) : ?LJCJoin
    {
      $retValue = $this->GetItem($key, $throwError);
      return $retValue;
    }
  }

  // ***************
  /// <summary>Represents a SQL JoinOn.</summary>
  class LJCJoinOn
  {
    /// <summary>Initializes a class instance.</summary>
    /// <include path='items/construct/*' file='Doc/LJCJoinOn.xml'/>
    public function __construct(string $fromColumnName, string $toColumnName)
    {
      $this->BooleanOperator = "and";
      $this->FromColumnName = $fromColumnName;
      $this->JoinOnOperator = "=";
      $this->JoinOns = null;
      $this->ToColumnName = $toColumnName;
    }

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->BooleanOperator = $this->BooleanOperator;
      $retValue->FromColumnName = $this->FromColumnName;
      $retValue->JoinOnOperator = $this->JoinOnOperator;
      $retValue->JoinOns = $this->JoinOns;
      $retValue->ToColumnName = $this->ToColumnName;
      return $retValue;
    }

    // ---------------
    // Properties

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
  }

  // ***************
  /// <summary>Represents a collection of LJCJoin objects.</summary>
  class LJCJoinOns extends LJCCollectionBase
  {
    // ---------------
    // Public Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/LJCJoinOns.xml'/>
    public function Add(string $fromColumnName, string $toColumnName
      , $key = null) : ?LJCJoinOn
    {
      $retValue = null;

      if (null == $key)
      {
        $key = $fromColumnName;
      }

      $item = new LJCJoinOn($fromColumnName, $toColumnName);
      $retValue = $this->AddObject($item , $key);
      return $retValue;
    }

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCJoinOns.xml'/>
    public function AddObject(LJCJoinOn $item, $key = null) : ?LJCJoinOn
    {
      if (null == $key)
      {
        $key = $item->FromColumnName;
      }
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    }

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);
      return $retValue;
    }

    /// <summary>Get the item by Key value.</summary>
    /// <include path='items/Get/*' file='Doc/LJCJoinOns.xml'/>
    public function Get($key, bool $throwError = true) : ?LJCJoinOn
    {
      $retValue = $this->GetItem($key, $throwError);
      return $retValue;
    }
  }
?>