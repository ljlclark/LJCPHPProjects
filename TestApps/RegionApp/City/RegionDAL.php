<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // RegionDAL.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  // LJCDBAccessLib: LJCConnectionValues, LJCDbColumns
  // LJCDataManager: LJCDataManager

  /// <summary>The Region Data Access Layer Library</summary>
  /// LibName: RegionDAL
  //  Classes:
  //    Region, Regions, RegionManager

  // ***************
  /// <summary>The Region data object class.</summary> 
  class Region
  {
    // ---------------
    // Static Methods

    // *** New Method ***
    /// <summary>
    ///   Creates a new typed object with existing standard object values.
    /// </summary>
    /// <param name="objRegion"></param>
    /// <returns>The new Region object.</returns>
    public static function Copy($objRegion): ?Region
    {
      $retRegion = null;

      if (property_exists($objRegion, "Name"))
      {
        $retRegion = new Region($objRegion->Name);

        foreach ($objRegion as $propertyName => $value)
        {
          if (property_exists($retRegion, $propertyName))
          {
            $retRegion->$propertyName = $value;
          }
        }
      }
      return $retRegion;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->RegionID = 0;
      $this->Number = "";
      $this->Name = "";
      $this->Description = null;
    }

    // ---------------
    // Data Object Methods

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->RegionID = $this->RegionID;
      $retValue->Number = $this->Number;
      $retValue->Name = $this->Name;
      $retValue->Description = $this->Description;
      return $retValue;
    }

    // ---------------
    // Public Properties

    // Primary Keys

    /// <summary>The ID value.</summary> 
    public int $RegionID;

    // Natural Keys

    // varchar(5)
    /// <summary>The Number value.</summary> 
    public string $Number;

    // Unique Keys

    // varchar(60)
    /// <summary>The Name value.</summary> 
    public string $Name;

    // varchar(100)
    /// <summary>The Description value.</summary> 
    public ?string $Description;

    // ---------------
    // Constants

    public const TableName = "Region";
    public const ColumnRegionID = "RegionID";
    public const ColumnNumber = "Number";
    public const ColumnName = "Name";
    public const ColumnDescription = "Description";

    /// <summary>The Description column length.</summary>
    public const DescriptionLength = 100;

    /// <summary>The Name column length.</summary>
    public const NameLength = 60;

    /// <summary>The Number column length.</summary>
    public const NumberLength = 5;
  }  // Region
  
  // ***************
  /// <summary>Represents a collection of Region objects.</summary> 
  class Regions extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    // *** New Method ***
    /// <summary>Create collection from array.</summary>
    /// <param name="items">The items array.</param>
    /// <returns>The collection></returns.
    public static function Collection($items): ?Regions
    {
      $retRegions = null;

      if (isset($items)
        && LJC::HasElements($items->Items))
      {
        $retRegions = new Regions();
        foreach ($$items->Items as $objDataObject)
        {
          // Create typed object from stdClass.
          $region = Region::Copy($objDataObject[0]);
          $retRegions->AddObject($region);
        }
      }
      return $retRegions;
    }

    // ---------------
    // Data Object Methods

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    public function AddObject(Region $item, $key = null)
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
  }  // Regions

  // ***************
  /// <summary>Contains Region DB Table methods.</summary> 
  class RegionManager
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
      , ?string $filter = null): ?Regions
    {
      $retValue = null;
      
      $rows = $this->LoadResult($keyColumns, $propertyNames, $filter);

      $regions = new Regions();
      $region = new Region();
      $retValue = $this->DataManager->CreateDataCollection($regions, $region
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

      $retValue = $this->DataManager->CreateDataObject(new Region(), $row);
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
  }  // RegionManager
?>
