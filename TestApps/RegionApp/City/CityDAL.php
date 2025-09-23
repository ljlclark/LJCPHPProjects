<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // CityDAL.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  // LJCDBAccessLib: LJCConnectionValues, LJCDbColumns
  // LJCDataManager: LJCDataManager

  /// <summary>The City Data Access Layer Library</summary>
  /// LibName: CityDAL
  //  Classes:
  //    City, Cities, CityManager

  // ***************
  /// <summary>The City data object class.</summary> 
  class City
  {
    // ---------------
    // Static Methods

    /// <summary>
    ///   Creates a new typed object with existing standard object values.
    /// </summary>
    /// <param name="objCity"></param>
    /// <returns>The new City object.</returns>
    public static function Copy($objCity): ?City
    {
      $className = "City";
      $methodName = "Copy()";
      $retCity = null;

      // Check for required values.
      if (property_exists($objCity, "Name"))
      {
        $retCity = new City($objCity->Name);

        // Look for properties of standard object in typed object.
        foreach ($objCity as $propertyName => $value)
        {
          if (property_exists($retCity, $propertyName))
          {
            // Update new typed object properties from the standard object.
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
              $retCity->$propertyName = $objValue;
            }
          }
        }
      }
      return $retCity;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    public function __construct($name = "", $provinceID = 0)
    {
      $this->ClassName = "City";

      $this->Name = $name;
      $this->ProvinceID = $provinceID;

      $this->CityID = 0;
      $this->Description = null;

      $this->CityFlag = 0;
      $this->District = 0;
      $this->ZipCode = null;

      $this->DebugText = "";
    }

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $retDebugText = "";

      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // ---------------
    // Data Object Methods

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $methodName = "Clone()";

      $retValue = new self();
      $retValue->CityID = $this->CityID;
      $retValue->ProvinceID = $this->ProvinceID;
      $retValue->Name = $this->Name;
      $retValue->Description = $this->Description;

      $retValue->CityFlag = $this->CityFlag;
      $retValue->District = $this->District;
      $retValue->ZipCode = $this->ZipCode;
      return $retValue;
    }

    // ---------------
    // Public Properties

    // Primary Keys

    /// <summary>The ID value.</summary> 
    public int $CityID;

    // Parent Keys

    /// <summary>The ProvinceID value.</summary> 
    public int $ProvinceID;

    // varchar(60)
    /// <summary>The province name.</summary>
    public string $ProvinceName;

    // Unique Keys

    // varchar(60)
    /// <summary>The Name value.</summary> 
    public string $Name;

    // varchar(100)
    /// <summary>The Description value.</summary>
    public ?string $Description;

    // Other Properties

    // bit(1)
    /// <summary>The CityFlag value.</summary> 
    public int $CityFlag;

    // smallint
    /// <summary>The District value.</summary> 
    public int $District;

    // char(4) ?
    /// <summary>The ZipCode value.</summary> 
    public ?string $ZipCode;

    // Class Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;

    // ---------------
    // Constants

    public const TableName = "City";
    public const ColumnCityID = "CityID";
    public const ColumnProvinceID = "ProvinceID";
    public const ColumnName = "Name";
    public const ColumnDescription = "Description";

    public const ColumnCityFlag = "CityFlag";
    public const ColumnDistrict = "District";
    public const ColumnZipCode = "ZipCode";

    public const DescriptionLength = 100;
    public const NameLength = 60;
    public const ProvinceNameLength = 60;
    public const ZipCodeLength = 4; // ?
  }  // City

  // ***************
  /// <summary>Represents a collection of City objects.</summary> 
  class Cities extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    /// <summary>
    ///   Create typed collection from deserialized JavasScript object.
    /// </summary>
    /// <param name="items">The items object.</param>
    /// <returns>The collection></returns.
    public static function Collection($items): ?Cities
    {
      $className = "Cities";
      $methodName = "Collection()";
      $retCities = new Cities();

      if (isset($items)
        && $items->Count > 0)
      {
        foreach ($items->ReadItems as $objDataObject)
        {
          // Create typed object from stdClass.
          $city = City::Copy($objDataObject);
          $retCities->AddObject($city);
        }
      }
      return $retCities;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->ClassName = "Cities";
    }

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $retDebugText = "";

      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // ---------------
    // Collection Methods

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/Cities.xml'/>
    public function AddObject(City $item, $key = null)
    {
      $methodName = "AddObject()";

      if (null == $key)
      {
        $key = $this->count();
      }
			if ($this->HasKey($key))
			{
				throw new Exception("Key: {$key} already in use.");
			}
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    }
  }  // Cities

  // ***************
  /// <summary>Contains City DB Table methods.</summary> 
  class CityManager
  {
    // ---------------
    // Constructor Methods

    // The Constructor function.
    /// <include path='items/construct/*' file='Doc/CityManger.xml'/>
    public function __construct($connectionValues, string $tableName = null)
    {
      $this->ClassName = "CityManager";

      if (!LJC::HasValue($tableName))
      {
        $tableName = City::TableName;
      }
      $this->DataManager = new LJCDataManager($connectionValues, $tableName);
      $this->DebugText = "";
      $this->Limit = 0;
      $this->OrderByNames = null;
    }

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = "null")
    {
      $retDebugText = "";

      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()
  
    // ---------------
    // Data Methods

    /// <summary>Adds a new record for the provided values.</summary>
    /// <param name="$dataColumns"></parm>
    /// <returns>The added record data object.</returns>
    public function Add(LJCDbColumns $dataColumns): ?City
    {
      $methodName = "Add()";
      $retCount = 0;

        // ***** Begin
      $this->AddDebug($methodName, "Here");
      $this->AddDebug($methodName, "\$dataColumns", $dataColumns);
        // ***** End
      $this->DataManager->SQL = "";
      $retCount = $this->DataManager->Add($dataColumns);
      return $retCount;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/CityManger.xml'/>
    public function Delete(LJCDbColumns $keyColumns): int
    {
      $methodName = "Delete()";
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Delete($keyColumns);
      return $retValue;
    }

    // Loads the data and creates the records for the provided values.
    /// <include path='items/Load/*' file='Doc/CityManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , ?string $filter = null): ?Cities
    {
      $methodName = "Load()";
      $retValue = null;
      
      $rows = $this->LoadResult($keyColumns, $propertyNames, $filter);

      $cities = new Cities();
      $city = new City();
      $retValue = $this->DataManager->CreateDataCollection($cities, $city
        , $rows);
      return $retValue;
    }

    // Loads the result data.
    public function LoadResult(?LJCDbColumns $keyColumns
      , array $propertyNames = null, ?string $filter = null): ?array
    {
      $methodName = "LoadResult()";
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
    /// <include path='items/Retrieve/*' file='Doc/CityManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns
      , array $propertyNames = null, LJCJoins $joins = null): ?City
    {
      $methodName = "Retrieve()";
      $retValue = null;
      
      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      // *** Change ***
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames, $joins);

      $retValue = $this->DataManager->CreateDataObject(new City(), $row);
      return $retValue;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/CityManger.xml'/>
    public function Update(LJCDbColumns $keyColumns, LJCDbColumns $dataColumns)
      : int
    {
      $methodName = "Update()";
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

    /// <summary>Gets the Joins.</summary>
    public function CreateJoins(): LJCJoins
    {
      $retJoins = new LJCJoins();
      $join = $retJoins->Add("Province");

      $joinOns = new LJCJoinOns();
      $joinOn = $joinOns->Add(City::ColumnProvinceID, Province::ColumnID);
      $join->JoinOns = $joinOns;

      $dataColumns = new LJCDbColumns();
      $dataColumn = $dataColumns->Add(Province::ColumnName, "ProvinceName"
        , "ProvinceName");
      $join->Columns = $dataColumns;
      return $retJoins;
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

    /// <summary>The debug text.</summary>
    public string $DebugText;

    /// <summary>The load limit.</summary>
    public int $Limit;

    /// <summary>The order names array.</summary> 
    public ?array $OrderByNames;
  }  // CityManager
?>
