<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // CityDAL.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  // LJCCommon: LJC
  // LJCCollectionLib: LJCCollectionBase
  // LJCDBAccessLib: LJCConnectionValues, LJCDataColumns
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
      $retCity = null;

      // Check for required values.
      if (property_exists($objCity, "Name"))
      {
        $retCity = new City($objCity->Name);

        // Look for properties of standard object in typed object.
        foreach ($objCity as $propertyName => $value)
        {
          // Check if object property exists in the typed item.
          if (property_exists($retCity, $propertyName))
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

    /// <summary>The city primary key.</summary>
    public int $CityID;

    // Parent Keys

    /// <summary>The province parent key and partial unique key.</summary>
    public int $ProvinceID;

    // varchar(60)
    /// <summary>The province parent key value.</summary>
    public string $ProvinceName = "";

    // Unique Keys

    // varchar(60)
    /// <summary>The partial unique key.</summary>
    public string $Name;

    // varchar(100)
    /// <summary>The city description.</summary>
    public ?string $Description;

    // Other Properties

    // bit(1)
    /// <summary>The city flag.</summary>
    /// <remarks>1 = city, 0 = municipality.</remarks>
    public int $CityFlag;

    // smallint
    /// <summary>The District value.</summary> 
    public int $District;

    // char(4) ?
    /// <summary>The city zip code.</summary>
    public ?string $ZipCode;

    // Class Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;

    // ---------------
    // Constants

    public const TableName = "City";
    public const PropertyCityID = "CityID";
    public const PropertyProvinceID = "ProvinceID";
    public const PropertyProvinceName = "ProvinceName";
    public const PropertyName = "Name";
    public const PropertyDescription = "Description";

    public const PropertyCityFlag = "CityFlag";
    public const PropertyDistrict = "District";
    public const PropertyZipCode = "ZipCode";

    public const DescriptionLength = 100;
    public const NameLength = 60;
    public const ProvinceNameLength = 60;
    public const ZipCodeLength = 4; // ?
  }  // City

  // ***************
  /// <summary>Represents a collection of City objects.</summary> 
  /// <include path='items/_CollectionName_/*' file='Doc/_CollectionName_.xml'/>
  /// <group name="Static">Static Methods</group>
  //    ToCollection()
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="Data">Content Methods</group>
  //    AddObject()
  class Cities extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    // Create typed collection from deserialized JavasScript collection.
    /// <include path='items/ToCollection/*' file='Doc/Cities.xml'/>
    /// <ParentGroup>Static</ParentGroup>
    public static function ToCollection($items): ?Cities
    {
      $retCities = null;

      // ReadItems is in the JavaScript collection.
      if (isset($items)
        && LJC::HasElements($items->ReadItems))
      {
        $retCities = new Cities();
        foreach ($items->ReadItems as $objItem)
        {
          // Create typed object from stdClass.
          $city = City::Copy($objItem);
          $retCities->AddObject($city);
        }
      }
      return $retCities;
    } // ToCollection()

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
      $this->ClassName = "Cities";
    }

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // ---------------
    // Data Methods

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/Cities.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(City $item, $key = null)
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
      $retItem = $this->AddItem($item, $key);
      return $retItem;
    } // AddObject()

    // ---------------
    // Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;
  }  // Cities

  // ***************
  /// <summary>Contains City DB Table methods.</summary> 
  class CityManager
  {
    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    /// <include path='items/construct/*' file='Doc/CityManger.xml'/>
    public function __construct($connectionValues, string $tableName = null)
    {
      $this->ClassName = "CityManager";
      $this->DebugText = "";

      if (!LJC::HasValue($tableName))
      {
        $tableName = City::TableName;
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
    public function Add(LJCDataColumns $dataColumns): ?City
    {
      $methodName = "Add()";
      $retCount = 0;

      $this->DataManager->SQL = "";
      $retCount = $this->DataManager->Add($dataColumns);
      $this->DebugText .= $this->DataManager->DebugText;
      return $retCount;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/CityManger.xml'/>
    public function Delete(LJCDataColumns $keyColumns): int
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
    public function Load(?LJCDataColumns $keyColumns, array $propertyNames = null
      , LJCJoins $joins = null, ?string $filter = null): ?Cities
    {
      $methodName = "Load()";
      $retCities = null;
      
      $this->DataManager->OrderByNames = $this->OrderByNames;
      $rows = $this->LoadResult($keyColumns, $propertyNames, $joins, $filter);

      $cities = new Cities();
      $city = new City();
      $retCities = $this->DataManager->CreateDataCollection($cities, $city
        , $rows);
      $this->OrderByNames = null;
      $this->DebugText .= $this->DataManager->DebugText;
      return $retCities;
    }

    // Loads the result data.
    public function LoadResult(?LJCDataColumns $keyColumns
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
    public function Retrieve(LJCDataColumns $keyColumns
      , array $propertyNames = null, LJCJoins $joins = null): ?City
    {
      $methodName = "Retrieve()";
      $retCity = null;
      
      $this->DataManager->SQL = "";
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames, $joins);

      $retCity = $this->DataManager->CreateDataObject(new City(), $row);
      $this->OrderByNames = null;
      $this->DebugText .= $this->DataManager->DebugText;
      return $retCity;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/CityManger.xml'/>
    public function Update(LJCDataColumns $keyColumns, LJCDataColumns $dataColumns)
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
    public function Columns(array $propertyNames = null): ?LJCDataColumns
    {
      $retDataColumns = $this->DataManager->Columns($propertyNames);
      $this->DebugText .= $this->DataManager->DebugText;
      return $retDataColumns;
    }

    /// <summary>Gets the Joins.</summary>
    public function CreateJoins(): LJCJoins
    {
      $retJoins = new LJCJoins();
      $join = $retJoins->Add("Province");

      $joinOns = new LJCJoinOns();
      $joinOn = $joinOns->Add(City::PropertyProvinceID, Province::ColumnID);
      $join->JoinOns = $joinOns;

      $dataColumns = new LJCDataColumns();
      $dataColumn = $dataColumns->Add(Province::ColumnName, "ProvinceName"
        , "ProvinceName");
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
?>
