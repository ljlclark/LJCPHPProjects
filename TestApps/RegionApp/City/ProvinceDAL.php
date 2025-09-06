<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // ProvinceDAL.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  // LJCDBAccessLib: LJCConnectionValues, LJCDbColumns
  // LJCDataManager: LJCDataManager

  /// <summary>The Province Data Access Layer Library</summary>
  /// LibName: ProvinceDAL
  //  Classes:
  //    Province, Provinces, ProvinceManager

  // ***************
  /// <summary>The Province data object class.</summary> 
  class Province
  {
    // ---------------
    // Static Methods

    // *** New Method ***
    /// <summary>
    ///   Creates a new typed object with existing standard object values.
    /// </summary>
    /// <param name="objProvince"></param>
    /// <returns>The new Province object.</returns>
    public static function Copy($objProvince): ?Province
    {
      $retProvince = null;

      if (property_exists($objProvince, "Name"))
      {
        $retProvince = new Province($objProvince->Name);

        foreach ($objProvince as $propertyName => $value)
        {
          if (property_exists($retProvince, $propertyName))
          {
            $retProvince->$propertyName = $value;
          }
        }
      }
      return $retProvince;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->ProvinceID = 0;
      $this->RegionID = 0;
      $this->Name = "";
      $this->Description = null;

      $this->Abbreviation = null;
    }

    // ---------------
    // Data Object Methods

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->ProvinceID = $this->ProvinceID;
      $retValue->RegionID = $this->RegionID;
      $retValue->Name = $this->Name;
      $retValue->Description = $this->Description;

      $retValue->Abbreviation = $this->Abbreviation;
      return $retValue;
    }

    // ---------------
    // Public Properties

    // Primary Keys

    /// <summary>The ID value.</summary> 
    public int $ProvinceID;

    // Parent Keys

    /// <summary>The RegionID value.</summary>
    public int $RegionID;

    // Unique Keys

    // varchar(60)
    /// <summary>The Name value.</summary> 
    public string $Name;

    // varchar(100)
    /// <summary>The Description value.</summary> 
    public ?string $Description;

    // Other Properties

    /// <summary>The Abbreviation value.</summary> 
    public ?string $Abbreviation;

    // ---------------
    // Constants

    public const TableName = "Region";
    public const ColumnID = "ProvinceID";
    public const ColumnRegionID = "RegionID";
    public const ColumnName = "Name";
    public const ColumnDescription = "Description";
    public const ColumnAbbreviation = "Abbreviation";

    /// <summary>The Description column length.</summary>
    public const DescriptionLength = 100;

    /// <summary>The Name column length.</summary>
    public const NameLength = 60;

    /// <summary>The Number column length.</summary>
    public const NumberLength = 5;
  }  // Province
  
  // ***************
  /// <summary>Represents a collection of Province objects.</summary> 
  class Provinces extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    // *** New Method ***
    /// <summary>Create collection from array.</summary>
    /// <param name="items">The items array.</param>
    /// <returns>The collection></returns.
    public static function Collection($items): ?Provinces
    {
      $retProvinces = null;

      if (isset($items)
        && LJC::HasElements($items->Items))
      {
        $retProvinces = new Provinces();
        foreach ($$items->Items as $objDataObject)
        {
          // Create typed object from stdClass.
          $province = Province::Copy($objDataObject[0]);
          $retProvinces->AddObject($province);
        }
      }
      return $retProvinces;
    }

    // ---------------
    // Data Object Methods

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    public function AddObject(Province $item, $key = null)
    {
      if (null == $key)
      {
        $key = $item->Name;
      }
			if ($this->HasKey($key))
			{
				throw new Exception("Key: {$key} already in use.");
			}
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    }
  }  // Provinces

  // ***************
  /// <summary>Contains Province DB Table methods.</summary> 
  class ProvinceManager
  {
    // ---------------
    // Constructor Methods

    // The Constructor function.
    /// <include path='items/construct/*' file='Doc/CityManger/CityManger.xml'/>
    public function __construct($connectionValues, string $tableName = null)
    {
      if (!LJC::HasValue($tableName))
      {
        $tableName = Region::TableName;
      }
      $this->OrderByNames = null;
      $this->DataManager = new LJCDataManager($connectionValues, $tableName);
    }
  
    // ---------------
    // Data Methods

    /// <summary>Adds a new record for the provided values.</summary>
    /// <param name="$dataColumns"></parm>
    /// <returns>The added record data object.</returns>
    public function Add(LJCDbColumns $dataColumns): int
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Add($dataColumns);
      return $retValue;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/CityManger/CityManger.xml'/>
    public function Delete(LJCDbColumns $keyColumns): int
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Delete($keyColumns);
      return $retValue;
    }

    // Loads the records for the provided values.
    /// <include path='items/Load/*' file='Doc/CityManger/CityManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , ?string $filter = null): ?Provinces
    {
      $retValue = null;
      
      $rows = $this->LoadResult($keyColumns, $propertyNames, $filter);

      $provinces = new Provinces();
      $province = new Province();
      $retValue = $this->DataManager->CreateDataCollection($provinces, $province
        , $rows);
      return $retValue;
    }

    // Loads the result data.
    public function LoadResult(?LJCDbColumns $keyColumns
      , array $propertyNames = null, ?string $filter = null): ?array
    {
      $retValue = null;

      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      if ($this->Limit > 0)
      {
        $this->DataManager->Limit = $this->Limit;
      }

      $retValue = $this->DataManager->Load($keyColumns, $propertyNames
        , filter: $filter);
      return $retValue;
    }

    // Retrieves the record for the provided values.
    /// <include path='items/Retrieve/*' file='Doc/CityManger/CityManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns
      , array $propertyNames = null): ?Region
    {
      $retValue = null;
      
      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames);

      $retValue = $this->DataManager->CreateDataObject(new Province(), $row);
      return $retValue;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/CityManger/CityManger.xml'/>
    public function Update(LJCDbColumns $keyColumns, LJCDbColumns $dataColumns)
      : int
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Update($keyColumns, $dataColumns);
      return $retValue;
    }

    // ---------------
    // Class Methods

    // Get the column definitions that match the property names.
    public function Columns(array $propertyNames = null): ?LJCDbColumns
    {
      $retValue = $this->DataManager->Columns($propertyNames);
      return $retValue;
    }

    // Creates a PropertyNames list from the data definition.
    public function PropertyNames(): array
    {
      $retNames = $this->DataManager->PropertyNames();
      return $retNames;
    }

    /// <summary>The created SQL statement.</summary> 
    public function SQL(): ?string
    {
      return $this->DataManager->SQL;
    }

    // ---------------
    // Public Properties

    /// <summary>The Data Manager object.</summary>
    public LJCDataManager $DataManager;

    // 
    public ?array $OrderByNames;
  }  // ProvinceManager
?>