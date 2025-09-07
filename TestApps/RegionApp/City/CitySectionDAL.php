<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // CitySectionDAL.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDataManagerLib.php";
  // LJCDBAccessLib: LJCConnectionValues, LJCDbColumns
  // LJCDataManager: LJCDataManager

  /// <summary>The CitySection Data Access Layer Library</summary>
  /// LibName: CitySectionDAL
  //  Classes:
  //    CitySection, CitySections, CitySectionManager

  // ***************
  /// <summary>The CitySection data object class.</summary> 
  class CitySection
  {
    // ---------------
    // Static Methods

    /// <summary>
    ///   Creates a new typed object with existing standard object values.
    /// </summary>
    /// <param name="objCitySection"></param>
    /// <returns>The new CitySection object.</returns>
    public static function Copy($objCitySection): ?CitySection
    {
      $retCitySection = null;

      if (property_exists($objCitySection, "Name"))
      {
        $retCitySection = new CitySection($objCitySection->Name);

        foreach ($objCitySection as $propertyName => $value)
        {
          if (property_exists($retCitySection, $propertyName))
          {
            $retCitySection->$propertyName = $value;
          }
        }
      }
      return $retCitySection;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->ID = 0;
      $this->CityID = 0;
      $this->Name = "";
      $this->Description = null;

      $this->Contact = null;
      $this->ZoneType = null;
    }

    // ---------------
    // Data Object Methods

    /// <summary>Creates an object clone.</summary>
    public function Clone() : self
    {
      $retValue = new self();
      $retValue->ID = $this->ID;
      $retValue->CityID = $this->CityID;
      $retValue->Name = $this->Name;
      $retValue->Description = $this->Description;

      $retValue->Contact = $this->Contact;
      $retValue->ZoneType = $this->ZoneType;
      return $retValue;
    }

    // ---------------
    // Public Properties

    // Primary Keys

    /// <summary>The ID value.</summary> 
    public int $ID;

    // Parent Keys

    /// <summary>The CityID value.</summary> 
    public int $CityID;

    // Unique Keys

    // varchar(60)
    /// <summary>The Name value.</summary> 
    public string $Name;

    // varchar(100)
    /// <summary>The Description value.</summary> 
    public ?string $Description;

    // Other Properties
    
    // varchar(60)
    /// <summary>The Contact value.</summary> 
    public ?string $Contact;

    // varchar(25)
    /// <summary>The ZoneType value.</summary> 
    public ?string $ZoneType;

    // ---------------
    // Constants

    public const TableName = "CitySection";
    public const ColumnID = "ID";
    public const ColumnCityID = "CityID";
    public const ColumnName = "Name";
    public const ColumnDescription = "Description";

    public const ColumnContact = "Contact";
    public const ColumnZoneType = "ZoneType";

    /// <summary>The Contact column length.</summary>
    public const ContactLength = 60;

    /// <summary>The Description column length.</summary>
    public const DescriptionLength = 100;

    /// <summary>The Name column length.</summary>
    public const NameLength = 60;

    /// <summary>The ZoneType column length.</summary>
    public const ZoneTypeLength = 25;
  }  // CitySection
  
  // ***************
  /// <summary>Represents a collection of CitySection objects.</summary> 
  class CitySections extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    /// <summary>
    ///   Create typed collection from deserialized JavasScript object.
    /// </summary>
    /// <param name="items">The items object.</param>
    /// <returns>The collection></returns.
    public static function Collection($items): ?CitySections
    {
      $retCities = new CitySections();

      if (isset($items)
        && LJC::HasElements($items->Items))
      {
        foreach ($items->Items as $objDataObject)
        {
          // Create typed object from stdClass.
          $city = CitySection::Copy($objDataObject);
          $retCities->AddObject($city);
        }
      }
      return $retCities;
    }

    // ---------------
    // Collection Methods

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/CitySections.xml'/>
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
    // ---------------
    // Constructor Methods

    // The Constructor function.
    /// <include path='items/construct/*' file='Doc/CitySectionManger.xml'/>
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
    public function Add(LJCDbColumns $dataColumns): int
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Add($dataColumns);
      return $retValue;
    }
  
    // Deletes the records for the provided values.
    /// <include path='items/Delete/*' file='Doc/CitySectionManger.xml'/>
    public function Delete(LJCDbColumns $keyColumns): int
    {
      $retValue = 0;

      $this->DataManager->SQL = "";
      $retValue = $this->DataManager->Delete($keyColumns);
      return $retValue;
    }

    // Loads the records for the provided values.
    /// <include path='items/Load/*' file='Doc/CitySectionManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , ?string $filter = null): ?CitySections
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
      , array $propertyNames = null, ?string $filter = null): array
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
    /// <include path='items/Retrieve/*' file='Doc/CitySectionManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns
      , array $propertyNames = null): ?CitySection
    {
      $retValue = null;
      
      $this->DataManager->SQL = "";
      $this->DataManager->OrderByNames = $this->OrderByNames;
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames);

      $retValue = $this->DataManager->CreateDataObject(new CitySection(), $row);
      return $retValue;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/CitySectionManger.xml'/>
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
  }  // CitySectionManager
?>
