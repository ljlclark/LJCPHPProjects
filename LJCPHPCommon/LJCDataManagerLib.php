<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDataManagerLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCTextLib.php";
  // LJCDBAccessLib: LJCConnectionValues, LJCDbAccess
  //   , LJCDbColumn, LJCDbColumns
  //   , LJCJoin, LJCJoins
  //   , LJCJoinOn, LJCJoinOns
  // LJCTextLib: LJCStringBuilder

  /// <summary>The PDO Data Manager Library</summary>
  /// LibName: LJCDataManagerLib
  //  Classes: LJCDataManager, LJCSQLBuilder

  // ***************
  // Provides Standard DB Table methods.
  /// <include path='items/LJCDataManager/*' file='Doc/LJCDataManager.xml'/>
  /// <group name="Construct">Constructor Methods</group>
  //    __construct()
  /// <group name="Data">Data Methods</group>
  //    Add(), Delete(), DeleteSQL(), Load(), LoadSQL(), Retrieve()
  //    RetrieveSQL(), Update(), UpdateSQL(), SQLExecute(), SQLLoad()
  //    SQLRetrieve()
  /// <group name="Schema">Schema Methods</group>
  //    Columns(), MapNames(), PropertyNames()
  /// <group name="ORM">ORM Methods</group>
  //    CreateDataCollection(), CreateDataObject()
  /// <group name="Other">Other Methods</group>
  //    CreateResultKeys()
  class LJCDataManager
  {
    // ---------------
    // Constructor Methods

    // Initializes a class instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Construct</ParentGroup>
    public function __construct($connectionValues, string $tableName)
    {
      $this->ClassName = "LJCDataManager";
      $this->DebugText = "";

      $this->DbAccess= new LJCDbAccess($connectionValues);
      $this->TableName = $tableName;
      $dbName= $connectionValues->DbName;
      $this->SchemaColumns = $this->DbAccess->LoadTableSchema($dbName
        , $tableName);
      $this->Joins = null;
      $this->Limit = 0;
      $this->OrderByNames = null;
      $this->SQL = null;
    } // __construct()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = "null")
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // ---------------
    // Data Methods - LJCDataManager

    // Adds the record for the provided values.
    /// <include path='items/Add/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Add(LJCDbColumns $dataColumns): int
    {
      $retValue = 0;
      
      $this->SQL = LJCSQLBuilder::CreateInsert($this->TableName
        , $dataColumns);
      $retValue = $this->DbAccess->Execute($this->SQL);
      return $retValue;
    } // Add()
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Delete(LJCDbColumns $keyColumns): int
    {
      $retValue = 0;
      
      $this->SQL = $this->DeleteSQL($keyColumns);
      $retValue = $this->DbAccess->Execute($this->SQL);
      return $retValue;
    } // Delete()

    /// <summary>Creates the Delete SQL.</summary>
    /// <ParentGroup>Data</ParentGroup>
    public function DeleteSQL(LJCDbColumns $keyColumns)
    {
      $retSQL = null;

      if (null == $keyColumns || 0 == count($keyColumns))
      {
        throw new Exception("LJCDataManager-Delete: keyColumns cannot be null.");
      }
      $retSQL = LJCSQLBuilder::CreateDelete($this->TableName, $keyColumns);
      return $retSQL;
    }

    // Loads the records for the provided values.
    /// <include path='items/Load/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Load(?LJCDbColumns $keyColumns = null
      , ?array $propertyNames = null, ?LJCJoins $joins = null
      , ?string $filter = null): ?array
    {
      $retValue = null;
      
      $this->SQL = $this->LoadSQL($keyColumns, $propertyNames, $joins
        , $filter);
      $retValue = $this->DbAccess->Load($this->SQL);
      return $retValue;
    } // Load()

    /// <summary>Creates the Load SQL.</summary>
    /// <ParentGroup>Data</ParentGroup>
    public function LoadSQL(?LJCDbColumns $keyColumns = null
      , ?array $propertyNames = null, ?LJCJoins $joins = null
      , ?string $filter = null) : string
    {
      $retSQL = "";

      if (null == $propertyNames)
      {
        $propertyNames = $this->PropertyNames(); 
      }

      if ($filter != null
        && strlen(trim($filter)) > 0)
      {
        $keyColumns = null;
      }

      $this->Joins = $joins;
      $retSQL = LJCSQLBuilder::CreateSelect($this->TableName
        , $this->SchemaColumns, $keyColumns, $propertyNames, $joins);
      if ($filter != null
        && strlen(trim($filter)) > 0)
      {
        $retSQL .= " \r\n{$filter}";
      }

      $retSQL .= LJCSQLBuilder::GetOrderBy($this->OrderByNames);
      if ($this->Limit > 0)
      {
        $retSQL .= "\r\nlimit {$this->Limit}";
      }
      return $retSQL;
    }

    // Retrieves the record for the provided values.
    /// <include path='items/Retrieve/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve(LJCDbColumns $keyColumns
      , array $propertyNames = null, LJCJoins $joins = null): ?array
    {
      $methodName = "Retrieve()";
      $retValue = null;

      $this->SQL = $this->RetrieveSQL($keyColumns, $propertyNames, $joins);
      $retValue = $this->DbAccess->Retrieve($this->SQL);
      return $retValue;
    } // Retrieve()

    /// <summary>Creates the Load SQL.</summary>
    /// <ParentGroup>Data</ParentGroup>
    public function RetrieveSQL(LJCDbColumns $keyColumns
      , array $propertyNames = null, LJCJoins $joins = null)
    {
      $retSQL = "";

      if (null == $propertyNames)
      {
        $propertyNames = $this->PropertyNames(); 
      }
      $this->Joins = $joins;
      $retSQL = LJCSQLBuilder::CreateSelect($this->TableName
        , $this->SchemaColumns, $keyColumns, $propertyNames, $joins);
      $retSQL .= LJCSQLBuilder::GetOrderBy($this->OrderByNames);
      return $retSQL;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Update(LJCDbColumns $keyColumns, LJCDbColumns $dataColumns)
      : int
    {
      $methodName = "Retrieve()";
      $retValue = 0;
      
      $this->SQL = $this->UpdateSQL($keyColumns, $dataColumns);
      $retValue = $this->DbAccess->Execute($this->SQL);
      return $retValue;
    } // Update()

    /// <summary>Creates the Update SQL.</summary>
    /// <ParentGroup>Data</ParentGroup>
    public function UpdateSQL(LJCDbColumns $keyColumns
      , LJCDbColumns $dataColumns)
    {
      $retSQL = "";

      if (null == $keyColumns || 0 == count($keyColumns))
      {
        throw new Exception("LJCDataManager-Update: keyColumns cannot be null.");
      }
      $retSQL = LJCSQLBuilder::CreateUpdate($this->TableName, $keyColumns
        , $dataColumns);
      return $retSQL;
    }

    // Executes an Add, Delete or Update SQL statement.
    /// <include path='items/SQLExecute/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function SQLExecute(string $sql): int
    {
      $this->SQL = $sql;
      $retValue = $this->DbAccess->Execute($this->SQL);
      return $retValue;
    } // SQLExecute()

    // Executes a Select SQL statement.
    /// <include path='items/SQLLoad/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function SQLLoad(sting $sql): ?array
    {
      $this->SQL = $sql;
      $retValue = $this->DbAccess->Load($this->SQL);
      return $retValue;
    } // SQLLoad()

    // Executes a Select SQL statement.
    /// <include path='items/SQLRetrieve/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function SQLRetrieve(string $sql): ?array
    {
      $this->SQL = $sql;
      $retValue = $this->DbAccess->Retrieve($this->SQL);
      return $retValue;
    } // SQLRetrieve()

    // ---------------
    // Schema Methods

    // Get the column definitions that match the property names.
    /// <ParentGroup>Schema</ParentGroup>
    public function Columns(array $propertyNames = null): LJCDbColumns
    {
      $retValue = $this->SchemaColumns->SelectItems($propertyNames);
      return $retValue;
    } // Columns()

    /// <summary>
    ///   Sets the PropertyName, RenameAs and Caption values for a column.
    /// </summary>
    /// <param name="$columnName">The column name.</param>
    /// <param name="$propertyName">The property name.</param>
    /// <param name="$renameAs">The rename as value.</param>
    /// <param name="$caption">The caption value.</param>
    /// <ParentGroup>Schema</ParentGroup>
    public function MapNames(string $columnName, ?string $propertyName = null
      , ?string $renameAs = null, ?string $caption = null)
    {
      $this->SchemaColumns.MapNames($columnName, $propertyName, $renameAs
        , $caption);
    }

    // Creates a PropertyNames list from the data definition.
    /// <ParentGroup>Other</ParentGroup>
    /// <ParentGroup>Schema</ParentGroup>
    public function PropertyNames(): array
    {
      $retNames = $this->SchemaColumns->KeyNames();
      return $retNames;
    } // PropertyNames()

    // ---------------
    // ORM Methods - LJCDataManager

    // Creates an array of Data Objects from a Data Result rows array.
    /// <include path='items/CreateDataCollection/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>ORM</ParentGroup>
    public function CreateDataCollection(object $collection
      , object $dataObject, array $rows)
    {
      $retValue = $collection;

      if ($rows != null && count($rows) > 0)
      {
        foreach ($rows as $row)
        {
          $data = $dataObject->Clone();
          $data = $this->CreateDataObject($data, $row);
          $retValue->AddObject($data);
        }
        $values = $collection->GetValues();
      }
      return $retValue;
    } // CreateDataCollection()

    // Populates a Data Object with values from a Data Result row.
    /// <include path='items/CreateDataObject/*' file='Doc/LJCDataManager.xml'/>
    /// <ParentGroup>ORM</ParentGroup>
    public function CreateDataObject($dataObject, array $row)
    {
      $retValue = $dataObject;

      $this->SetData($this->SchemaColumns, $dataObject, $row);
      $this->CreateJoinData($retValue, $row);
      return $retValue;
    } // CreateDataObject()

    // ---------------
    // Other Methods - LJCDataManager

    // Create the keys from the result.
    /// <ParentGroup>Other</ParentGroup>
    public function CreateResultKeys($rows, $keyNames)
    {
      $methodName = "CreateResultKeys()";

      // Create key columns array.
      $retKeys = [];

      // Each row contains a columns array.
      foreach($rows as $rowKey => $columns)
      {
        // Get each column.
        $keys = [];
        foreach($keyNames as $keyName)
        {
          // *** Add ***
          if (array_key_exists($keyName, $columns))
          {
            $value = $columns[$keyName]; 
            if ($value != null)
            {
              // Create named array element.
              $keys[$keyName] = $value;
            }
          }
        }
        $retKeys[] = $keys;
      }
      return $retKeys;
    }

    // Populates a Data Object with Join values from a Data Result row.
    private function CreateJoinData($dataObject, array $row): void
    {
      if ($this->Joins != null && count($this->Joins) > 0)
      {
        foreach ($this->Joins as $join)
        {
          $this->SetData($join->Columns, $dataObject, $row);
        }
      }
    } // CreateJoinData()

    // Sets Data Object values from the Data Result row.
    private function SetData(LJCDbColumns $columns, $dataObject, array $row)
     : void
    {
      if ($columns != null && count($columns) > 0)
      {
        foreach ($columns as $column)
        {
          $columnName = $column->ColumnName;
          if ($column->RenameAs != null)
          {
            $columnName = $column->RenameAs;
          }
          $propertyName = $column->PropertyName;
          if (property_exists($dataObject, $propertyName)
            && array_key_exists($columnName, $row))
          {
            // Using variable name for object property.
            $value = $row[$columnName];
            if ("bool" == $column->DataTypeName)
            {
              // *** Change ***
              //$dataObject->$propertyName = (bool)$value;
              $dataObject->$propertyName = (int)$value;
            }
            else
            {
              $dataObject->$propertyName = $value;
            }
          }
        }
      }
    } // SetData()

    // ---------------
    // Public Properties - LJCDataManager

    /// <summary>The class name for debugging.</summary>
    public string $ClassName;

    /// <summary>The DbAccess object.</summary>
    public LJCDbAccess $DbAccess;

    /// <summary>The debug text.</summary>
    public string $DebugText;

    /// <summary>The Join definitions.</summary>
    public ?LJCJoins $Joins;

    /// <summary>The OrderBy names.</summary>
    public ?array $OrderByNames;

    /// <summary>The column definitions.</summary>
    public LJCDbColumns $SchemaColumns;

    /// <summary>The last SQL statement.</summary>
    public ?string $SQL;

    /// <summary>The table name.</summary>
    public string $TableName;
  }  // LJCDataManager

  // ***************
  // Provides functions for creating SQL statements.
  /// <include path='items/LJCSQLBuilder/*' file='Doc/LJCSQLBuilder.xml'/>
  /// <group name="Statement">SQL Statement Methods</group>
  //    CreateDelete(), CreateInsert(), CreateSelect(), CreateUpdate()
  /// <group name="Column">SQL Column Methods</group>
  //    GetOrderBy(), SQLColumns(), SQLJoinColumns(), SQLValueColumns()
  /// <group name="Join">Join Methods</group>
  //    GetJoinOns(), GetJoinStatement(), GetJoinTableString()
  class LJCSQLBuilder
  {
    // ---------------
    // Static Statement Methods

    // Creates a Delete SQL statement.
    /// <include path='items/CreateDelete/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Statement</ParentGroup>
    public static function CreateDelete(string $tableName
      , LJCDbColumns $keyColumns): string
    {
      $retValue = "delete from $tableName \r\n";
      $retValue .= self::WhereClause($tableName,$keyColumns);
      return $retValue;
    } // CreateDelete()

    // Creates a Select SQL statement.
    /// <include path='items/CreateInsert/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Statement</ParentGroup>
    public static function CreateInsert(string $tableName
      , LJCDbColumns $dataColumns): string
    {
      $retValue = "insert into $tableName\r\n";
      $retValue .= self::SqlColumns($tableName, $dataColumns, true);
      $retValue .= " values \r\n" . self::SQLValueColumns($dataColumns, false
        , true);
      return $retValue;
    } // CreateInsert()

    // Creates a Select SQL statement.
    /// <include path='items/CreateSelect/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Statement</ParentGroup>
    public static function CreateSelect(string $tableName
      , LJCDbColumns $schemaColumns, ?LJCDbColumns $keyColumns
      , array $propertyNames = null, ?LJCJoins $joins = null): string
    {
      $sqlColumns = $schemaColumns;
      if ($propertyNames != null)
      {
        $sqlColumns = $schemaColumns->SelectItems($propertyNames);
      }

      $retValue = "select\r\n";
      $retValue .= self::SQLColumns($tableName, $sqlColumns, joins: $joins);
      $retValue .= "from $tableName ";
      $retValue .= self::GetJoinStatement($tableName, $joins);
      $retValue .= self::WhereClause($tableName, $keyColumns);
      return $retValue;
    } // CreateSelect()

    // Creates an Update SQL statement.
    /// <include path='items/CreateUpdate/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Statement</ParentGroup>
    public static function CreateUpdate(string $tableName
      , ?LJCDbColumns $keyColumns, LJCDbColumns $dataColumns): string
    {
      $retValue = "update $tableName set\r\n";
      $retValue .= self::SQLValueColumns($dataColumns, true);
      $retValue .= self::WhereClause($tableName, $keyColumns);
      return $retValue;
    } // CreateUpdate()

    // ---------------
    // Static Column Methods - LJCSQLBuilder

    // Creates an OrderBy clause.
    /// <include path='items/GetOrderBy/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Column</ParentGroup>
    public static function GetOrderBy(?array $orderByNames): string
    {
      $retValue = "";

      if ($orderByNames != null && count($orderByNames) > 0)
      {
        $retValue = "\r\norder by ";

        $first = true;
        foreach ($orderByNames as $orderByName)
        {
          if ($orderByName != null)
          {
            if (false == $first)
            {
              $retValue .= ", ";
            }
            $first = false;

            $retValue .= $orderByName;
          }
        }
      }
      return $retValue;
    } // GetOrderBy()

    // Creates the columns for a Select SQL statement.
    /// <include path='items/SQLColumns/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Column</ParentGroup>
    public static function SQLColumns(string $tableName
      , LJCDbColumns $sqlColumns, bool $includeParens = false
      , LJCJoins $joins = null): string
    {
      $retValue = "";

      if ($includeParens)
      {
        $retValue .= " (\r\n";
      }

      $first = true;
      foreach ($sqlColumns as $sqlColumn)
      {
        if (false == $first)
        {
          $retValue .= ", \r\n";
        }
        $first = false;

        $retValue .= "  $tableName.$sqlColumn->ColumnName";
        if ($sqlColumn->RenameAs != null)
        {
          $retValue .= " as $sqlColumn->RenameAs";
        }
      }
      $retValue .= self::SQLJoinColumns($joins);

      $retValue .= " \r\n";
      if ($includeParens)
      {
        $retValue .= " )\r\n";
      }
      return $retValue;
    } // SQLColumns()

    // Creates the Join columns for a Select SQL statement.
    /// <include path='items/SQLJoinColumns/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Column</ParentGroup>
    public static function SQLJoinColumns(?LJCJoins $joins): ?string
    {
      $retValue = null;

      if ($joins != null && count($joins) > 0)
      {
        foreach ($joins as $join)
        {
          if ($join->Columns != null)
          {
            foreach ($join->Columns as $column)
            {
              $qualifier = $join->TableName;
              if ($join->TableAlias != null)
              {
                $qualifier = $join->TableAlias;
              }

              $retValue .= ",\r\n  $qualifier.$column->ColumnName";
              if ($column->RenameAs != null)
              {
                $retValue .= " as $column->RenameAs";
              }
            }
          }
        }
      }
      return $retValue;
    } // SQLJoinColumns()

    // Creates the value columns for an Update SQL statement.
    /// <include path='items/SQLValueColumns/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Column</ParentGroup>
    public static function SQLValueColumns(LJCDbColumns $dataColumns
      , bool $isUpdate = false, bool $includeParens = false): string
    {
      $retValue = "";

      if ($includeParens)
      {
        $retValue .= " (\r\n";
      }

      $first = true;
      foreach ($dataColumns as $dataColumn)
      {
        if ($dataColumn->AutoIncrement)
        //	|| null == $dataColumn->Value)
        {
          continue;
        }

        if (false == $first)
        {
          $retValue .= ", \r\n";
        }
        $first = false;

        $retValue .= "  ";
        if ($isUpdate)
        {
          $retValue .= "$dataColumn->ColumnName = ";
        }

        $value = $dataColumn->Value;
        if ("string" == $dataColumn->DataTypeName)
        {
          if (null == $value)
          {
            $value = "null";
          }
          else
          {
            $value = "'$value'";
          }
          $retValue .= "$value";
        }
        else
        {
          $retValue .= "$value";
        }
      }
      $retValue .= " \r\n";
      if ($includeParens)
      {
        $retValue .= " )\r\n";
      }
      return $retValue;
    } // SQLValueColumns()

    // Creates the Where clause.
    private static function WhereClause(string $tableName
      , ?LJCDbColumns $keyColumns): ?string
    {
      $retValue = null;

      if ($keyColumns != null && count($keyColumns) > 0)
      {
        $retValue = "\r\nwhere ";

        $first = true;
        foreach ($keyColumns as $keyColumn)
        {
          if ($keyColumn->Value != null)
          {
            if (false == $first)
            {
              $retValue .= "\r\n";
              $retValue .= "  $keyColumn->WhereBoolOperator ";
            }
            $first = false;

            // Include quotes if string.
            $value = "$keyColumn->Value";
            if ($keyColumn->DataTypeName == "string")
            {
              $value = "'$keyColumn->Value'";
            }

            // Use RenameAs if set.
            $columnName = $keyColumn->ColumnName;
            if ($keyColumn->RenameAs != null)
            {
              $columnName = $keyColumn->RenameAs;
            }
            $compareOperator = $keyColumn->WhereCompareOperator;
            $retValue .= "$tableName.$columnName $compareOperator $value";
          }
        }
      }
      return $retValue;
    } // WhereClause()

    // ---------------
    // Static Join Methods - LJCSQLBuilder

    // Get the JoinOn statements.
    /// <include path='items/GetJoinOns/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Join</ParentGroup>
    public static function GetJoinOns(string $tableName, LJCJoin $join
      , bool $recursive = false): ?string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $first = true;
      $joinOns = $join->JoinOns;
      foreach ($joinOns as $joinOn)
      {
        // Begin the Join grouping.
        if ($first && false == $recursive)
        {
          $builder->Text("(");
        }
        else
        {
          if (false == $recursive)
          {
            $builder->Text(")");
          }
          $builder->Text("\r\n $joinOn->BooleanOperator ");
          if (false == $recursive)
          {
            $builder->Text("(");
          }
        }
        $first = false;

        // Begin the JoinOn grouping.
        $builder->Text("(");

        $fromColumnName = self::GetQualifiedColumnName($joinOn->FromColumnName
          , $tableName);
        $toColumnName = self::GetQualifiedColumnName($joinOn->ToColumnName
          , $join->TableName, $join->TableAlias);
        $builder->Text("$fromColumnName $joinOn->JoinOnOperator $toColumnName");

        // End the JoinOn grouping.
        $builder->Text(")");

        // Recursive JoinOns.
        if ($joinOn->JoinOns != null && count($joinOn->JoinOns) > 0)
        {
          $builder->Text(self::GetJoinOns($tableName, $join, true));
        }
      }

      // End the Join grouping.
      if (false == $recursive)
      {
        $builder->Text(")");
      }
      $retValue = $builder->ToString();
      return $retValue;
    } // GetJoinOns()

    // Creates the join statement.
    /// <include path='items/GetJoinStatement/*' file='Doc/LJCSQLBuilder.xml'/>
    /// <ParentGroup>Join</ParentGroup>
    public static function GetJoinStatement(string $tableName
      , ?LJCJoins $joins): ?string
    {
      $retValue = null;

      if ($joins != null && count($joins) > 0)
      {
        $builder = new LJCStringBuilder();
        $builder->Line(" ");
        foreach ($joins as $join)
        {
          // Begin the Join.
          $value = trim($builder->ToString());
          if (strlen($value) > 0)
          {
            $builder->Line(" ");
          }
          $builder->Text("$join->JoinType join");
          $builder->Text(self::GetJoinTableString($join));
          $builder->Text(" on ");
          $builder->Text(self::GetJoinOns($tableName, $join));
        }
        $retValue = $builder->ToString();
      }
      return $retValue;
    } // GetJoinStatement()

    // Get the full join table string.
    /// <include path='items/GetJoinTableString/*' file='Doc/LJCSQLBuilder.xml'/>
    private static function GetJoinTableString(LJCJoin $join): string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Text(" ");
      if ($join->SchemaName != null && strlen($join->SchemaName) > 0)
      {
        $builder->Text("$join->SchemaName.");
      }
      $builder->Text("$join->TableName");
      if ($join->TableAlias != null)
      {
        $builder->Text(" as $join->TableAlias");
      }
      $builder->Line(" ");
      $retValue = $builder->ToString();
      return $retValue;
    } // GetJoinTableString()

    // Qualify with the table name or alias unless already qualified.
    // <include path='items/GetQualifiedColumnName/*' file='Doc/LJCSQLBuilder.xml'/>
    private static function GetQualifiedColumnName(string $columnName
      , string $tableName, ?string $alias = null): string
    {
      $qualify = true;
      $retValue = $columnName;

      if (str_starts_with(trim($columnName), "|"))
      {
        // Value is a constant delimited with "|".
        $qualify = false;
        $retValue = trim(retValue);
        $retValue =  substr($retValue, 1, $retValue.Length - 2);
      }

      if ($qualify)
      {
        // Allow user to qualify column name to another table.
        if (LJC::StrPos($columnName, ".") > -1)
        {
          $values = preg_split(".", $columnName, 0, PREG_SPLIT_NO_EMPTY);
          if ($values.Length > 1)
          {
            $tableName = values[0];
            $columnName = values[1];
          }
        }
        else
        {
          if ($alias != null)
          {
            $tableName = alias;
          }
        }
        $retValue = "$tableName.$columnName";
      }
      return $retValue;
    } // GetQualifiedColumnName()
  }  // LJCSQLBuilder
?>
