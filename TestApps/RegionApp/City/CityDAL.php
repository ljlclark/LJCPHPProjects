<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // CityManager.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  // LJCDBAccessLib: LJCConnectionValues, LJCDataManager, LJCDbColumns
  // LJCDataManager: LJCDataManager

  /// <summary>The Region Data Access Layer Library</summary>
  /// LibName: CityDAL
  //  Classes:
  //    City, Cities, CityManager
  //    CitySection, CitySections, CitySectionManager
  //    Region, Regions, RegionManager

  // ***************
  /// <summary>The City data object class.</summary> 
  class City
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->CityID = 0;
      $this->ProvinceID = 0;
      $this->Name = "";
      $this->Description = null;
      $this->CityFlag = 0;
      $this->ZipCode = null;
      $this->District = 0;
    }

    // ---------------
    // Public Methods

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->CityID = $this->CityID;
      $retValue->ProvinceID = $this->ProvinceID;
      $retValue->Name = $this->Name;
      $retValue->Description = $this->Description;
      $retValue->CityFlag = $this->CityFlag;
      $retValue->ZipCode = $this->ZipCode;
      $retValue->District = $this->District;
      return $retValue;
    }

    // ---------------
    // Public Properties

    /// <summary>The ID value.</summary> 
    public int $CityID;

    /// <summary>The ProvinceID value.</summary> 
    public int $ProvinceID;

    // varchar(60)
    /// <summary>The Name value.</summary> 
    public string $Name;

    // varchar(100)
    /// <summary>The Description value.</summary>
    public ?string $Description;

    // bit(1)
    /// <summary>The CityFlag value.</summary> 
    public int $CityFlag;

    // char(4) ?
    /// <summary>The ZipCode value.</summary> 
    public ?string $ZipCode;

    // smallint
    /// <summary>The District value.</summary> 
    public int $District;

    // ---------------
    // Constants

    public const TableName = "City";
    public const ColumnCityID = "CityID";
    public const ColumnProvinceID = "ProvinceID";
    public const ColumnName = "Name";
    public const ColumnDescription = "Description";
    public const ColumnCityFlag = "CityFlag";
    public const ColumnZipCode = "ZipCode";
    public const ColumnDistrict = "District";
    public const NameLength = 60;
    public const DescriptionLength = 100;
    public const ZipCodeLength = 4; // ?
  }  // City

  // ***************
  /// <summary>Represents a collection of City objects.</summary> 
  class Cities extends LJCCollectionBase
  {
    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    public function AddObject(City $item, $key = null)
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
  }  // Cities

  // ***************
  /// <summary>Contains City DB Table methods.</summary> 
  class CityManager
  {
    // The Constructor function.
    /// <include path='items/construct/*' file='Doc/CityManger/CityManger.xml'/>
    public function __construct($connectionValues, string $tableName = null)
    {
      // ****
      $this->DebugText = "";
      if (!LJC::HasValue($tableName))
      {
        $tableName = City::TableName;
      }
      $this->OrderByNames = null;
      $this->DataManager = new LJCDataManager($connectionValues, $tableName);
    }
  
    // ---------------
    // Data Methods

    /// <summary>Adds a new record for the provided values.</summary>
    /// <param name="$dataColumns"></parm>
    /// <returns>The added record data object.</returns>
    public function Add(LJCDbColumns $dataColumns)
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Add($dataColumns);
      return $retValue;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/CityManger/CityManger.xml'/>
    public function Delete(LJCDbColumns $keyColumns)
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Delete($keyColumns);
      return $retValue;
    }

    // Loads the data and creates the records for the provided values.
    /// <include path='items/Load/*' file='Doc/CityManger/CityManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , ?string $filter = null)
    {
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
      , array $propertyNames = null, ?string $filter = null)
    {
      $retValue = null;

      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      if ($this->Limit > 0)
      {
        $this->DataManager->Limit = $this->Limit;
      }

      $rows = $this->DataManager->Load($keyColumns, $propertyNames
        , filter: $filter);
      $retValue = $rows;
      return $retValue;
    }

    // Retrieves the record for the provided values.
    /// <include path='items/Retrieve/*' file='Doc/CityManger/CityManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns, array $propertyNames = null)
    {
      $retValue = null;
      
      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames);

      $retValue = $this->DataManager->CreateDataObject(new City(), $row);
      return $retValue;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/CityManger/CityManger.xml'/>
    public function Update(LJCDbColumns $keyColumns, LJCDbColumns $dataColumns)
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Update($keyColumns, $dataColumns);
      return $retValue;
    }

    // ---------------
    // Class Methods

    // Get the column definitions that match the property names.
    public function Columns(array $propertyNames = null): LJCDbColumns
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
    public function SQL()
    {
      return $this->DataManager->SQL;
    }

    // ---------------
    // Public Properties

    /// <summary>The Data Manager object.</summary>
    public LJCDataManager $DataManager;

    // 
    public ?array $OrderByNames;
  }  // CityManager

  // ***************
  /// <summary>The CitySection data object class.</summary> 
  class CitySection
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->ID = 0;
      $this->CityID = 0;
      $this->Name = "";
      $this->Description = null;
      $this->ZoneType = null;
      $this->Contact = null;
    }

    // ---------------
    // Public Methods

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->ID = $this->ID;
      $retValue->CityID = $this->CityID;
      $retValue->Name = $this->Name;
      $retValue->Description = $this->Description;
      $retValue->ZoneType = $this->ZoneType;
      $retValue->Contact = $this->Contact;
      return $retValue;
    }

    // ---------------
    // Public Properties

    /// <summary>The ID value.</summary> 
    public int $ID;

    /// <summary>The CityID value.</summary> 
    public int $CityID;

    // varchar(60)
    /// <summary>The Name value.</summary> 
    public string $Name;

    // varchar(100)
    /// <summary>The Description value.</summary> 
    public ?string $Description;

    // varchar(25)
    /// <summary>The ZoneType value.</summary> 
    public ?string $ZoneType;
    
    // varchar(60)
    /// <summary>The Contact value.</summary> 
    public ?string $Contact;

    // ---------------
    // Constants

    public const TableName = "CitySection";
    public const ColumnID = "ID";
    public const ColumnCityID = "CityID";
    public const ColumnName = "Name";
    public const ColumnDescription = "Description";
    public const ColumnZoneType = "ZoneType";
    public const ColumnContact = "Contact";

    /// <summary>The Name column length.</summary>
    public const NameLength = 60;

    /// <summary>The Description column length.</summary>
    public const DescriptionLength = 100;

    /// <summary>The ZoneType column length.</summary>
    public const ZoneTypeLength = 25;

    /// <summary>The Contact column length.</summary>
    public const ContactLength = 60;
  }  // CitySection
  
  // ***************
  /// <summary>Represents a collection of CitySection objects.</summary> 
  class CitySections extends LJCCollectionBase
  {
    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    public function AddObject(City $item, $key = null)
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
  }  // CitySections

  // ***************
  /// <summary>Contains CitySection DB Table methods.</summary> 
  class CitySectionManager
  {
    // The Constructor function.
    /// <include path='items/construct/*' file='Doc/CityManger/CityManger.xml'/>
    public function __construct($connectionValues, string $tableName = null)
    {
      if (!LJC::HasValue($tableName))
      {
        $tableName = CitySection::TableName;
      }
      $this->OrderByNames = null;
      $this->DataManager = new LJCDataManager($connectionValues, $tableName);
    }
  
    // ---------------
    // Data Methods

    /// <summary>Adds a new record for the provided values.</summary>
    /// <param name="$dataColumns"></parm>
    /// <returns>The added record data object.</returns>
    public function Add(LJCDbColumns $dataColumns)
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Add($dataColumns);
      return $retValue;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/CityManger/CityManger.xml'/>
    public function Delete(LJCDbColumns $keyColumns)
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Delete($keyColumns);
      return $retValue;
    }

    // Loads the records for the provided values.
    /// <include path='items/Load/*' file='Doc/CityManger/CityManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , ?string $filter = null)
    {
      $retValue = null;
      
      $rows = $this->LoadResult($keyColumns, $propertyNames, $filter);

      $citySections = new CitySections();
      $citySection = new CitySection();
      $retValue = $this->DataManager->CreateDataCollection($citySections
        , $citySection, $rows);
      return $retValue;
    }

    // Loads the result data.
    public function LoadResult(?LJCDbColumns $keyColumns
      , array $propertyNames = null, ?string $filter = null)
    {
      $retValue = null;

      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      if ($this->Limit > 0)
      {
        $this->DataManager->Limit = $this->Limit;
      }

      $rows = $this->DataManager->Load($keyColumns, $propertyNames
        , filter: $filter);
      $retValue = $rows;
      return $retValue;
    }

    // Retrieves the record for the provided values.
    /// <include path='items/Retrieve/*' file='Doc/CityManger/CityManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns, array $propertyNames = null)
    {
      $retValue = null;
      
      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames);

      $retValue = $this->DataManager->CreateDataObject(new CitySection(), $row);
      return $retValue;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/CityManger/CityManger.xml'/>
    public function Update(LJCDbColumns $keyColumns, LJCDbColumns $dataColumns)
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Update($keyColumns, $dataColumns);
      return $retValue;
    }

    // ---------------
    // Class Methods

    // Get the column definitions that match the property names.
    public function Columns(array $propertyNames = null): LJCDbColumns
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
    public function SQL()
    {
      return $this->DataManager->SQL;
    }

    // ---------------
    // Public Properties

    /// <summary>The Data Manager object.</summary>
    public LJCDataManager $DataManager;

    // 
    public ?array $OrderByNames;
  }  // CitySectionManager

  // ***************
  /// <summary>The Region data object class.</summary> 
  class Region
  {
    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->CityID = 0;
      $this->Number = "";
      $this->Name = "";
      $this->Description = null;
    }

    // ---------------
    // Public Methods

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->ID = $this->ID;
      $retValue->Number = $this->Number;
      $retValue->Name = $this->Name;
      $retValue->Description = $this->Description;
      return $retValue;
    }

    // ---------------
    // Public Properties

    /// <summary>The ID value.</summary> 
    public int $ID;

    // varchar(5)
    /// <summary>The Number value.</summary> 
    public string $Number;

    // varchar(60)
    /// <summary>The Name value.</summary> 
    public string $Name;

    // varchar(100)
    /// <summary>The Description value.</summary> 
    public ?string $Description;

    // ---------------
    // Constants

    public const TableName = "Region";
    public const ColumnID = "ID";
    public const ColumnNumber = "Number";
    public const ColumnName = "Name";
    public const ColumnDescription = "Description";

    /// <summary>The Number column length.</summary>
    public const NumberLength = 5;

    /// <summary>The Name column length.</summary>
    public const NameLength = 60;

    /// <summary>The Description column length.</summary>
    public const DescriptionLength = 100;
  }  // Region
  
  // ***************
  /// <summary>Represents a collection of Region objects.</summary> 
  class Regions extends LJCCollectionBase
  {
    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/LJCDbColumns.xml'/>
    public function AddObject(City $item, $key = null)
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
  }  // CitySections

  // ***************
  /// <summary>Contains Region DB Table methods.</summary> 
  class RegionManager
  {
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
    public function Add(LJCDbColumns $dataColumns)
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Add($dataColumns);
      return $retValue;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/CityManger/CityManger.xml'/>
    public function Delete(LJCDbColumns $keyColumns)
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Delete($keyColumns);
      return $retValue;
    }

    // Loads the records for the provided values.
    /// <include path='items/Load/*' file='Doc/CityManger/CityManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , ?string $filter = null)
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
      , array $propertyNames = null, ?string $filter = null)
    {
      $retValue = null;

      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      if ($this->Limit > 0)
      {
        $this->DataManager->Limit = $this->Limit;
      }

      $rows = $this->DataManager->Load($keyColumns, $propertyNames
        , filter: $filter);
      $retValue = $rows;
      return $retValue;
    }

    // Retrieves the record for the provided values.
    /// <include path='items/Retrieve/*' file='Doc/CityManger/CityManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns, array $propertyNames = null)
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
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Update($keyColumns, $dataColumns);
      return $retValue;
    }

    // ---------------
    // Class Methods

    // Get the column definitions that match the property names.
    public function Columns(array $propertyNames = null): LJCDbColumns
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
    public function SQL()
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