<?php
  // #SectionBegin Main
  // #Value _CollectionObjectName_ LJCCities
  // #Value _CollectionObjectVar_ Cities
  // #Value _CollectionObject_ cities
  // #Value _ItemObjectName_ LJCCity
  // #Value _ItemObjectVar_ City
  // #Value _ItemObject_ city
  // #Value _LibName_ LJCCityDAL
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // _LibName_.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  // LJCCommon: LJC
  // LJCCollectionLib: LJCCollectionBase
  // LJCDBAccessLib: LJCConnectionValues, LJCDbColumns
  // LJCDataManager: LJCDataManager

  /// <summary>The _ItemObjectName_ Data Access Layer Library</summary>
  /// LibName: _LibName_
  //  Classes:
  //    _ItemObjectName_Manager

  // ***************
  /// <summary>Contains City DB Table methods.</summary> 
  /// <include path='items/_CollectionName_/*' file='Doc/_CollectionName_.xml'/>
  /// <group name="Static">Static Methods</group>
  //    ToCollection()
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="Data">Content Methods</group>
  //    AddObject()
  class _ItemObjectName_Manager
  {
    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct($connectionValues, string $tableName = null)
    {
      $this->ClassName = "_ItemObjectName_Manager";
      $this->DebugText = "";

      if (!LJC::HasValue($tableName))
      {
        $tableName = _ItemObjectName_::TableName;
      }
      $this->DataManager = new LJCDataManager($connectionValues, $tableName);
      $this->DebugText .= $this->DataManager->DebugText;
      $this->Limit = 0;
      $this->OrderByNames = null;
    }

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = "null"
      , $line = 0)
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      if ($line > 0)
      {
        $location .= " ({$line}) ";
      }
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()
  
    // ---------------
    // Data Methods

    /// <summary>Adds a new record for the provided values.</summary>
    /// <param name="$dataColumns"></parm>
    /// <returns>The added record data object.</returns>
    public function Add(LJCDbColumns $dataColumns): ?_ItemObjectName_
    {
      $methodName = "Add()";
      $retCount = 0;

      $this->DataManager->SQL = "";
      $retCount = $this->DataManager->Add($dataColumns);
      $this->DebugText .= $this->DataManager->DebugText;
      return $retCount;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/_ItemObjectName_Manger.xml'/>
    public function Delete(LJCDbColumns $keyColumns): int
    {
      $methodName = "Delete()";
      $retCount = 0;

      $this->DataManager->SQL = "";
      $retCount = $this->DataManager->Delete($keyColumns);
      $this->DebugText .= $this->DataManager->DebugText;
      return $retCount;
    }

    // Loads the data and creates the records for the provided values.
    /// <include path='items/Load/*' file='Doc/CityManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , LJCJoins $joins = null, ?string $filter = null): ?_CollectionObjectName_
    {
      $methodName = "Load()";
      $ret_CollectionObjectVar_ = null;
      
      $this->DataManager->OrderByNames = $this->OrderByNames;
      $rows = $this->LoadResult($keyColumns, $propertyNames, $joins, $filter);

      $_CollectionObject_ = new _CollectionObjectName_();
      $_ItemObject_ = new _ItemObjectName_();
      $ret_CollectionObjectVar_ = $this->DataManager->CreateDataCollection($_CollectionObject_
        , $_ItemObject_, $rows);
      $this->OrderByNames = null;
      $this->DebugText .= $this->DataManager->DebugText;
      return $ret_CollectionObjectVar_;
    }

    // Loads the result data.
    public function LoadResult(?LJCDbColumns $keyColumns
      , array $propertyNames = null, LJCJoins $joins = null
      , ?string $filter = null): ?array
    {
      $methodName = "LoadResult()";
      $retRows = null;

      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      if ($this->Limit > 0)
      {
        $this->DataManager->Limit = $this->Limit;
      }

      $retRows = $this->DataManager->Load($keyColumns, $propertyNames, $joins
        , $filter);
      $this->OrderByNames = null;
      $this->DebugText .= $this->DataManager->DebugText;
      return $retRows;
    }

    // Retrieves the record for the provided values.
    /// <include path='items/Retrieve/*' file='Doc/CityManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns
      , array $propertyNames = null, LJCJoins $joins = null): ?City
    {
      $methodName = "Retrieve()";
      $ret_ItemObjectVar_ = null;
      
      $this->DataManager->SQL = "";
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames, $joins);

      $ret_ItemObjectVar_ = $this->DataManager->CreateDataObject(new _ItemObjectName()
        , $row);
      $this->OrderByNames = null;
      $this->DebugText .= $this->DataManager->DebugText;
      return $ret_ItemObjectVar_;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/CityManger.xml'/>
    public function Update(LJCDbColumns $keyColumns, LJCDbColumns $dataColumns)
      : int
    {
      $methodName = "Update()";
      $retCount = 0;

      $this->DataManager->SQL = "";
      $retCount = $this->DataManager->Update($keyColumns, $dataColumns);
      $this->DebugText .= $this->DataManager->DebugText;
      return $retCount;
    }

    // ---------------
    // Class Methods

    // Get the column definitions that match the property names.
    public function Columns(array $propertyNames = null): ?LJCDbColumns
    {
      $retDataColumns = $this->DataManager->Columns($propertyNames);
      $this->DebugText .= $this->DataManager->DebugText;
      return $retDataColumns;
    }

    /// <summary>Gets the Joins.</summary>
    public function CreateJoins(): LJCJoins
    {
      $retJoins = new LJCJoins();

      // #Value _JoinTableName_ Province
      $join = $retJoins->Add(_JoinTableName_);

      $joinOns = new LJCJoinOns();
      // #Value _JoinFromColumnName_ City::PropertyProvinceID
      // #Value _JoinToColumnName_ Province::ColumnID
      $joinOn = $joinOns->Add(_JoinFromColumnName_, _JoinToPropertyName_);
      $join->JoinOns = $joinOns;

      $dataColumns = new LJCDbColumns();
      // #Value _JoinColumnName_ Province::ColumnName
      // #Value _JoinPropertyName_ "ProvinceName"
      // #Value _JoinRenameAs_ "ProvinceName"
      $dataColumn = $dataColumns->Add(_JoinColumnName_, _JoinPropertyName_
        , _JoinRenameAs_);
      $join->Columns = $dataColumns;
      return $retJoins;
    }

    // Creates a PropertyNames list from the data definition.
    public function PropertyNames(): array
    {
      $retNames = $this->DataManager->PropertyNames();
      $this->DebugText .= $this->DataManager->DebugText;
      return $retNames;
    }

    /// <summary>The created SQL statement.</summary> 
    public function SQL(): ?string
    {
      return $this->DataManager->SQL;
    }

    // ---------------
    // Public Properties

    /// <summary>The class name for debugging.</summary>
    public string $ClassName;

    /// <summary>The Data Manager object.</summary>
    public LJCDataManager $DataManager;

    /// <summary>The debug text.</summary>
    public string $DebugText;

    /// <summary>The load limit.</summary>
    public int $Limit;

    /// <summary>The order names array.</summary> 
    public ?array $OrderByNames;
  }  // CityManager
  // #SectionEnd Main
?>
