<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDataManagerLib.php
  declare(strict_types=1);
  // Path: Codeline/LJCPHPCommon
  include_once "LJCDebugLib.php";
  include_once "LJCDBAccessLib.php";
  include_once "LJCTextLib.php";
  // LJCDBAccessLib: LJCConnectionValues, LJCDbAccess
  //   , LJCDbColumn, LJCDbColumns
  //   , LJCJoin, LJCJoins
  //   , LJCJoinOn, LJCJoinOns
  // LJCTextLib: LJCStringBuilder, LJCHTMLTableColumn, LJCHTMLWriter, LJCWriter
  //   , LJCDebugWriter

  /// <summary>The PDO Data Manager Library</summary>
  /// LibName: LJCDataManagerLib
  //  Classes: LJCDataManager, LJCSQLBuilder

  // ***************
  // Provides Standard DB Table methods.
  // Methods: Add(), Delete(), Load(), Retrieve(), Update()
  //   , SQLExecute(), SQLLoad(), SQLRetrieve()
  //   , CreateDataCollection(), CreateDataObject()
  /// <include path='items/LJCDataManager/*' file='Doc/LJCDataManager.xml'/>
  class LJCDataManager
  {
    // Initializes a class instance with the provided values.
    /// <include path='items/construct/*' file='Doc/LJCDataManager.xml'/>
    public function __construct($connectionValues, string $tableName)
    {
      $this->Debug = new LJCDebug("LJCDataManagerLib", "LJCDataManager"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->DbAccess= new LJCDbAccess($connectionValues);
      $this->TableName = $tableName;
      $dbName= $connectionValues->DbName;
      $this->SchemaColumns = $this->DbAccess->LoadTableSchema($dbName
        , $tableName);
      $this->Joins = null;
      $this->OrderByNames = null;
      $this->SQL = null;
    } // __construct()

    // ---------------
    // Public Data Methods - LJCDataManager

    // Adds the record for the provided values.
    /// <include path='items/Add/*' file='Doc/LJCDataManager.xml'/>
    public function Add(LJCDbColumns $dataColumns) : int
    {
      $enabled = false;
      $this->Debug->BeginMethod("Add", $enabled);
      $retValue = 0;
      
      $this->SQL = LJCSQLBuilder::CreateInsert($this->TableName
        , $dataColumns);
      $retValue = $this->DbAccess->Execute($this->SQL);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Add()
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/LJCDataManager.xml'/>
    public function Delete(LJCDbColumns $keyColumns) : int
    {
      $enabled = false;
      $this->Debug->BeginMethod("Delete", $enabled);
      $retValue = 0;
      
      if (null == $keyColumns || 0 == count($keyColumns))
      {
        throw new Exception("LJCDataManager-Delete: keyColumns cannot be null.");
      }
      $this->SQL = LJCSQLBuilder::CreateDelete($this->TableName, $keyColumns);
      $retValue = $this->DbAccess->Execute($this->SQL);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Delete()

    // Loads the records for the provided values.
    /// <include path='items/Load/*' file='Doc/LJCDataManager.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , LJCJoins $joins = null)	: ?array
    {
      $enabled = false;
      $this->Debug->BeginMethod("Load", $enabled);
      $retValue = null;
      
      $this->Joins = $joins;
      $this->SQL = LJCSQLBuilder::CreateSelect($this->TableName
        , $this->SchemaColumns, $keyColumns, $propertyNames, $joins);
      $this->SQL .= LJCSQLBuilder::GetOrderBy($this->OrderByNames);
      $retValue = $this->DbAccess->Load($this->SQL);
      return $retValue;
    } // Load()

    // Retrieves the record for the provided values.
    /// <include path='items/Retrieve/*' file='Doc/LJCDataManager.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns
      , array $propertyNames = null, LJCJoins $joins = null) : ?array
    {
      $enabled = false;
      $this->Debug->BeginMethod("Retrieve", $enabled);
      $retValue = null;

      $this->Joins = $joins;
      $this->SQL = LJCSQLBuilder::CreateSelect($this->TableName
        , $this->SchemaColumns, $keyColumns, $propertyNames, $joins);
      $this->SQL .= LJCSQLBuilder::GetOrderBy($this->OrderByNames);
      $retValue = $this->DbAccess->Retrieve($this->SQL);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Retrieve()

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/LJCDataManager.xml'/>
    public function Update(LJCDbColumns $keyColumns, LJCDbColumns $dataColumns)
      : int
    {
      $enabled = false;
      $this->Debug->BeginMethod("Update", $enabled);
      $retValue = 0;
      
      if (null == $keyColumns || 0 == count($keyColumns))
      {
        throw new Exception("LJCDataManager-Update: keyColumns cannot be null.");
      }
      $this->SQL = LJCSQLBuilder::CreateUpdate($this->TableName, $keyColumns
        , $dataColumns);
      $retValue = $this->DbAccess->Execute($this->SQL);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // Update()

    // Executes an Add, Delete or Update SQL statement.
    /// <include path='items/SQLExecute/*' file='Doc/LJCDataManager.xml'/>
    public function SQLExecute(string $sql) : int
    {
      $enabled = false;
      $this->Debug->BeginMethod("SQLExecute", $enabled);

      $this->SQL = $sql;
      $retValue = $this->DbAccess->Execute($this->SQL);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SQLExecute()

    // Executes a Select SQL statement.
    /// <include path='items/SQLLoad/*' file='Doc/LJCDataManager.xml'/>
    public function SQLLoad() : ?array
    {
      $enabled = false;
      $this->Debug->BeginMethod("SQLLoad", $enabled);

      $this->SQL = $sql;
      $retValue = $this->DbAccess->Load($this->SQL);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SQLLoad()

    // Executes a Select SQL statement.
    /// <include path='items/SQLRetrieve/*' file='Doc/LJCDataManager.xml'/>
    public function SQLRetrieve() : ?array
    {
      $enabled = false;
      $this->Debug->BeginMethod("SQLRetrieve", $enabled);

      $this->SQL = $sql;
      $retValue = $this->DbAccess->Retrieve($this->SQL);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // SQLRetrieve()

    // ---------------
    // Public Methods - LJCDataManager

    // Creates an array of Data Objects from a Data Result rows array.
    /// <include path='items/CreateDataCollection/*' file='Doc/LJCDataManager.xml'/>
    public function CreateDataCollection(object $collection
      , object $dataObject, array $rows)
    {
      $enabled = false;
      $this->Debug->BeginMethod("CreateDataCollection", $enabled);
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

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // CreateDataCollection()

    // Populates a Data Object with values from a Data Result row.
    /// <include path='items/CreateDataObject/*' file='Doc/LJCDataManager.xml'/>
    public function CreateDataObject($dataObject, array $row)
    {
      $enabled = false;
      $this->Debug->BeginMethod("CreateDataObject", $enabled);
      $retValue = $dataObject;

      $this->SetData($this->SchemaColumns, $dataObject, $row);
      $this->CreateJoinData($retValue, $row);

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // CreateDataObject()

    // Populates a Data Object with Join values from a Data Result row.
    private function CreateJoinData($dataObject, array $row)
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("CreateJoinData", $enabled);

      if ($this->Joins != null && count($this->Joins) > 0)
      {
        foreach ($this->Joins as $join)
        {
          $this->SetData($join->Columns, $dataObject, $row);
        }
      }
      $this->Debug->EndMethod($enabled);
    } // CreateJoinData()

    // Sets Data Object values from the Data Result row.
    private function SetData(LJCDbColumns $columns, $dataObject, array $row)
    {
      $enabled = false;
      $this->Debug->BeginPrivateMethod("SetData", $enabled);

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
              $dataObject->$propertyName = (bool)$value;
            }
            else
            {
              $dataObject->$propertyName = $value;
            }
          }
        }
      }
      $this->Debug->EndMethod($enabled);
    } // SetData()

    // ---------------
    // Public Properties - LJCDataManager

    /// <summary>The DbAccess object.</summary>
    public LJCDbAccess $DbAccess;

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
  // Static: CreateDelete(), CreateInsert(), CreateSelect(), CreateUpdate()
  //   , GetJoinOns(), GetJoinStatement(), GetJoinTableString(), GetOrderBy()
  //   , SqlColumns(), SQLJoinColumns(), SQLValueColumns() 
  /// <include path='items/LJCSQLBuilder/*' file='Doc/LJCSQLBuilder.xml'/>
  class LJCSQLBuilder
  {
    // ---------------
    // Public Static Functions

    // Creates a Delete SQL statement.
    /// <include path='items/CreateDelete/*' file='Doc/LJCSQLBuilder.xml'/>
    public static function CreateDelete(string $tableName
      , LJCDbColumns $keyColumns) : string
    {
      $retValue = "delete from $tableName \r\n";
      $retValue .= self::WhereClause($tableName,$keyColumns);
      return $retValue;
    } // CreateDelete()

    // Creates a Select SQL statement.
    /// <include path='items/CreateInsert/*' file='Doc/LJCSQLBuilder.xml'/>
    public static function CreateInsert(string $tableName
      , LJCDbColumns $dataColumns) : string
    {
      $retValue = "insert into $tableName\r\n";
      $retValue .= self::SqlColumns($tableName, $dataColumns, true);
      $retValue .= " values \r\n" . self::SQLValueColumns($dataColumns, false
        , true);
      return $retValue;
    } // CreateInsert()

    // Creates a Select SQL statement.
    /// <include path='items/CreateSelect/*' file='Doc/LJCSQLBuilder.xml'/>
    // *** Next Statement *** Change
    public static function CreateSelect(string $tableName
      , LJCDbColumns $schemaColumns, ?LJCDbColumns $keyColumns
      , array $propertyNames = null, ?LJCJoins $joins = null) : string
    {
      $sqlColumns = $schemaColumns;
      if ($propertyNames != null)
      {
        $sqlColumns = $schemaColumns->GetColumns($propertyNames);
      }

      $retValue = "select\r\n";
      $retValue .= self::SQLColumns($tableName, $sqlColumns, joins: $joins);
      $retValue .= "from $tableName \r\n";
      // *** Next Statement *** Add
      $retValue .= self::GetJoinStatement($tableName, $joins);
      $retValue .= self::WhereClause($tableName, $keyColumns);
      return $retValue;
    } // CreateSelect()

    // Creates an Update SQL statement.
    /// <include path='items/CreateUpdate/*' file='Doc/LJCSQLBuilder.xml'/>
    public static function CreateUpdate(string $tableName
      , ?LJCDbColumns $keyColumns, LJCDbColumns $dataColumns) : string
    {
      $retValue = "update $tableName set\r\n";
      $retValue .= self::SQLValueColumns($dataColumns, true);
      $retValue .= self::WhereClause($tableName, $keyColumns);
      return $retValue;
    } // CreateUpdate()

    // Get the JoinOn statements.
    /// <include path='items/GetJoinOns/*' file='Doc/LJCSQLBuilder.xml'/>
    public static function GetJoinOns(string $tableName, LJCJoin $join
      , bool $recursive = false) : ?string
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
          $builder->Append("(");
        }
        else
        {
          if (false == $recursive)
          {
            $builder->Append(")");
          }
          $builder->Append("\r\n $joinOn->BooleanOperator ");
          if (false == $recursive)
          {
            $builder->Append("(");
          }
        }
        $first = false;

        // Begin the JoinOn grouping.
        $builder->Append("(");

        $fromColumnName = self::GetQualifiedColumnName($joinOn->FromColumnName
          , $tableName);
        $toColumnName = self::GetQualifiedColumnName($joinOn->ToColumnName
          , $join->TableName, $join->TableAlias);
        $builder->Append("$fromColumnName $joinOn->JoinOnOperator $toColumnName");

        // End the JoinOn grouping.
        $builder->Append(")");

        // Recursive JoinOns.
        if ($joinOn->JoinOns != null && count($joinOn->JoinOns) > 0)
        {
          $builder->Append(self::GetJoinOns($tableName, $join, true));
        }
      }

      // End the Join grouping.
      if (false == $recursive)
      {
        $builder->Append(")");
      }
      $retValue = $builder->ToString();
      return $retValue;
    } // GetJoinOns()

    // Creates the join statement.
    /// <include path='items/GetJoinStatement/*' file='Doc/LJCSQLBuilder.xml'/>
    public static function GetJoinStatement(string $tableName
      , ?LJCJoins $joins) : ?string
    {
      $retValue = null;

      if ($joins != null && count($joins) > 0)
      {
        $builder = new LJCStringBuilder();
        foreach ($joins as $join)
        {
          // Begin the Join.
          if ($builder->Length() > 0)
          {
            $builder->AppendLine(" ");
          }
          $builder->Append("$join->JoinType join");
          $builder->Append(self::GetJoinTableString($join));
          $builder->Append(" on ");
          $builder->Append(self::GetJoinOns($tableName, $join));
        }
        $builder->AppendLine(" ");
        $retValue = $builder->ToString();
      }
      return $retValue;
    } // GetJoinStatement()

    // Get the full join table string.
    /// <include path='items/GetJoinTableString/*' file='Doc/LJCSQLBuilder.xml'/>
    private static function GetJoinTableString(LJCJoin $join) : string
    {
      $retValue = null;

      $builder = new LJCStringBuilder();
      $builder->Append(" ");
      if ($join->SchemaName != null && strlen($join->SchemaName) > 0)
      {
        $builder->Append("$join->SchemaName.");
      }
      $builder->Append("$join->TableName");
      if ($join->TableAlias != null)
      {
        $builder->Append(" as $join->TableAlias");
      }
      $builder->AppendLine(" ");
      $retValue = $builder->ToString();
      return $retValue;
    } // GetJoinTableString()

    // Creates an OrderBy clause.
    /// <include path='items/GetOrderBy/*' file='Doc/LJCSQLBuilder.xml'/>
    public static function GetOrderBy(?array $orderByNames) : string
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
    public static function SQLColumns(string $tableName
      , LJCDbColumns $sqlColumns, bool $includeParens = false
      , LJCJoins $joins = null) : string
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
    public static function SQLJoinColumns(?LJCJoins $joins) : ?string
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
    public static function SQLValueColumns(LJCDbColumns $dataColumns
      , bool $isUpdate = false, bool $includeParens = false) : string
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

    // ---------------
    // Private Static Functions - LJCSQLBuilder

    // Qualify with the table name or alias unless already qualified.
    // <include path='items/GetQualifiedColumnName/*' file='Doc/LJCSQLBuilder.xml'/>
    private static function GetQualifiedColumnName(string $columnName
      , string $tableName, ?string $alias = null) : string
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
        if (LJCCommon::StrPos($columnName, ".") > -1)
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

    // Creates the Where clause.
    private static function WhereClause(string $tableName
      , ?LJCDbColumns $keyColumns) : ?string
    {
      $retValue = null;

      if ($keyColumns != null && count($keyColumns) > 0)
      {
        $retValue = "where ";

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
  }  // LJCSQLBuilder
?>