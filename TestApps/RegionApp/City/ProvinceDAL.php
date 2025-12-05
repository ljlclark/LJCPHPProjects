<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // ProvinceDAL.php
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

        // Look for properties of standard object in typed object.
        foreach ($objProvince as $propertyName => $value)
        {
          // Check if object property exists in the typed item.
          if (property_exists($retProvince, $propertyName))
          {
            // Update new typed object property from the standard object.
            $success = false;
            $cityValue = $retCity->$propertyName;
            $objValue = $objCity->$propertyName;

            if (is_int($cityValue))
            {
              $retCity->$propertyName = (int)$objValue;
              $success = true;
            }
            if (!$success
              && is_float($cityValue))
            {
              $retCity->$propertyName = (float)$objValue;
              $success = true;
            }
            if (!$success)
            {
              $retProvince->$propertyName = $value;
            }
          }
        }
      }
      return $retProvince;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    public function __construct($name = "", $region = 0)
    {
      $this->ClassName = "Province";

      $this->Name = $name;
      $this->RegionID = $regionID;

      $this->ProvinceID = 0;
      $this->Description = null;

      $this->Abbreviation = null;

      $this->DebugText = "";
    }

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

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

    // char(3)
    /// <summary>The Abbreviation value.</summary> 
    public ?string $Abbreviation;

    // Class Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;

    // ---------------
    // Constants

    public const TableName = "Region";
    public const PropertyID = "ProvinceID";
    public const PropertyRegionID = "RegionID";
    public const PropertyName = "Name";
    public const PropertyDescription = "Description";
    public const PropertyAbbreviation = "Abbreviation";

    /// <summary>The Abbreviation column length.</summary>
    public const AbbreviationLength = 3;

    /// <summary>The Description column length.</summary>
    public const DescriptionLength = 100;

    /// <summary>The Name column length.</summary>
    public const NameLength = 60;

    /// <summary>The Number column length.</summary>
    public const NumberLength = 5;
  }  // Province
  
  // ***************
  /// <summary>Represents a collection of Province objects.</summary> 
  /// <group name="Static">Static Methods</group>
  //    ToCollection()
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="Data">Content Methods</group>
  //    AddObject()
  class Provinces extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    /// <summary>
    ///   Create typed collection from deserialized JavasScript object.
    /// </summary>
    /// <param name="items">The items object.</param>
    /// <returns>The collection></returns.
    /// <ParentGroup>Static</ParentGroup>
    public static function Collection($items): ?Provinces
    {
      $retProvinces = null;

      // ReadItems is in the JavaScript collection.
      if (isset($items)
        && LJC::HasElements($items->ReadItems))
      {
        $retProvinces = new Provinces();
        foreach ($items->ReadItems as $objItem)
        {
          // Create typed object from stdClass.
          $province = Province::Copy($objItem);
          $retProvinces->AddObject($province);
        }
      }
      return $retProvinces;
    } // ToCollection()

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
      $this->ClassName = "Provinces";
    }

    // ---------------
    // Data Methods

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/Provinces.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(Province $item, $key = null)
    {
      $methodName = "AddObject()";

      if (null == $key)
      {
        //$key = $item->Name;
        $key = $this->count();
      }

      // HasKey() is in LJCCollectionBase.
			if ($this->HasKey($key))
			{
				throw new Exception("Key: {$key} already in use.");
			}

      // AddItem() is in LJCCollectionBase.
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    } // AddObject()

    // ---------------
    // Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;
  }  // Provinces

  // ***************
  /// <summary>Contains Province DB Table methods.</summary> 
  class ProvinceManager
  {
    // ---------------
    // Constructor Methods

    // The Constructor function.
    /// <include path='items/construct/*' file='Doc/ProvinceManger.xml'/>
    public function __construct($connectionValues, string $tableName = null)
    {
      $this->ClassName = "CityManager";
      $this->DebugText = "";

      if (!LJC::HasValue($tableName))
      {
        $tableName = Region::TableName;
      }
      $this->DataManager = new LJCDataManager($connectionValues, $tableName);
      $this->DebugText .= $this->DataManager->DebugText;
      $this->Limit = 0;
      $this->OrderByNames = null;
    }
  
    // ---------------
    // Data Methods

    /// <summary>Adds a new record for the provided values.</summary>
    /// <param name="$dataColumns"></parm>
    /// <returns>The added record data object.</returns>
    public function Add(LJCDbColumns $dataColumns): int
    {
      $methodName = "Add()";
      $retCount = 0;

      $this->DataManager->SQL = "";
      $retCount = $this->DataManager->Add($dataColumns);
      $this->DebugText .= $this->DataManager->DebugText;
      return $retCount;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/ProvinceManger.xml'/>
    public function Delete(LJCDbColumns $keyColumns): int
    {
      $methodName = "Delete()";
      $retCount = 0;

      $this->DataManager->SQL = "";
      $retCount = $this->DataManager->Delete($keyColumns);
      $this->DebugText .= $this->DataManager->DebugText;
      return $retCount;
    }

    // Loads the records for the provided values.
    /// <include path='items/Load/*' file='Doc/ProvinceManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , LJCJoins $joins = null, ?string $filter = null): ?Provinces
    {
      $methodName = "Load()";
      $retProvinces = null;
      
      $this->DataManager->OrderByNames = $this->OrderByNames;
      $rows = $this->LoadResult($keyColumns, $propertyNames, $joins, $filter);

      $provinces = new Provinces();
      $province = new Province();
      $retProvinces = $this->DataManager->CreateDataCollection($provinces
        , $province, $rows);
      $this->OrderByNames = null;
      $this->DebugText .= $this->DataManager->DebugText;
      return $retProvinces;
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
    /// <include path='items/Retrieve/*' file='Doc/ProvinceManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns
      , array $propertyNames = null, LJCJoins $joins = null): ?Region
    {
      $methodName = "Retrieve()";
      $retProvince = null;
      
      $this->DataManager->SQL = "";
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames, $joins);

      $retProvince = $this->DataManager->CreateDataObject(new Province(), $row);
      $this->OrderByNames = null;
      $this->DebugText .= $this->DataManager->DebugText;
      return $retProvince;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/CityManger/CityManger.xml'/>
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
      return $retDataColumns;
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

    /// <summary>The load limit.</summary>
    public int $Limit;

    /// <summary>The order names array.</summary> 
    public ?array $OrderByNames;
  }  // ProvinceManager
?>