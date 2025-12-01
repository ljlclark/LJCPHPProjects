<?php
  // Copyright (c) Lester J.Clark and Contributors.
  // Licensed under the MIT License.
  // RegionDAL.php
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

    /// <summary>
    ///   Creates a new typed object with existing standard object values.
    /// </summary>
    /// <param name="objRegion"></param>
    /// <returns>The new Region object.</returns>
    public static function Copy($objRegion): ?Region

    {
      $retRegion = null;

      // Check for required values.
      if (property_exists($objRegion, "Name"))
      {
        $retRegion = new Region($objRegion->Name);

        // Look for properties of standard object in typed object.
        foreach ($objRegion as $propertyName => $value)
        {
          // Check if object property exists in the typed item.
          if (property_exists($retRegion, $propertyName))
          {
            // Update new typed object properties from the standard object.
            $success = false;
            $regionValue = $retRegion->$propertyName;
            $objValue = $objRegion->$propertyName;
            if (is_int($regionValue))
            {
              $retRegion->$propertyName = (int)$objValue;
              $success = true;
            }
            if (!$success
              && is_float($regionValue))
            {
              $retRegion->$propertyName = (float)$objValue;
              $success = true;
            }
            if (!$success)
            {
              $retRegion->$propertyName = $objValue;
            }
          }
        }
      }
      return $retRegion;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    public function __construct(string $number = "", string $name = "")
    {
      $this->ClassName = "Region";

      $this->Number = $number;
      $this->Name = $name;

      $this->RegionID = 0;
      $this->Description = null;

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
    /// <summary>The unique key.</summary> 
    public string $Name;

    // varchar(100)
    /// <summary>The Description value.</summary> 
    public ?string $Description;

    // ---------------
    // Constants

    public const TableName = "Region";
    public const PropertyRegionID = "RegionID";
    public const PropertyNumber = "Number";
    public const PropertyName = "Name";
    public const PropertyDescription = "Description";

    /// <summary>The Description column length.</summary>
    public const DescriptionLength = 100;

    /// <summary>The Name column length.</summary>
    public const NameLength = 60;

    /// <summary>The Number column length.</summary>
    public const NumberLength = 5;
  }  // Region
  
  // ***************
  /// <summary>Represents a collection of Region objects.</summary> 
  /// <group name="Static">Static Methods</group>
  //    ToCollection()
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="Data">Content Methods</group>
  //    AddObject()
  class Regions extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    /// <summary>
    ///   Create typed collection from deserialized JavasScript object.
    /// </summary>
    /// <param name="items">The items object.</param>
    /// <returns>The collection></returns.
    /// <ParentGroup>Static</ParentGroup>
    public static function ToCollection($items): ?Regions
    {
      $retRegions = null;

      // ReadItems is in the JavaScript collection.
      if (isset($items)
        && LJC::HasElements($items->ReadItems))
      {
        $retRegions = new Regions();
        foreach ($items->ReadItems as $objItem)
        {
          // Create typed object from stdClass.
          $region = Region::Copy($objItem);
          $retRegions->AddObject($region);
        }
      }
      return $retRegions;
    } // ToCollection()

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
      $this->ClassName = "Regions";
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
    /// <include path='items/AddObject/*' file='Doc/Regions.xml'/>
    public function AddObject(Region $item, $key = null)
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
  }  // Regions

  // ***************
  /// <summary>Contains Region DB Table methods.</summary> 
  class RegionManager
  {
    // ---------------
    // Constructor Methods

    // The Constructor function.
    /// <include path='items/construct/*' file='Doc/RegionManger.xml'/>
    public function __construct($connectionValues, string $tableName = null)
    {
      $this->ClassName = "RegionManager";
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
    /// <include path='items/Delete/*' file='Doc/RegionManger.xml'/>
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
    /// <include path='items/Load/*' file='Doc/RegionManger.xml'/>
    public function Load(?LJCDbColumns $keyColumns, array $propertyNames = null
      , ?string $filter = null): ?Regions
    {
      $methodName = "Load()";
      $retRegions = null;
      
      $this->DataManager->OrderByNames = $this->OrderByNames;
      $rows = $this->LoadResult($keyColumns, $propertyNames, $filter);

      $regions = new Regions();
      $region = new Region();
      $retRegions = $this->DataManager->CreateDataCollection($regions, $region
        , $rows);
      $this->DebugText .= $this->DataManager->DebugText;
      $this->OrderByNames = null;
      return $retRegions;
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
    /// <include path='items/Retrieve/*' file='Doc/RegionManger.xml'/>
    public function Retrieve(LJCDbColumns $keyColumns
      , array $propertyNames = null): ?Region
    {
      $methodName = "Retrieve()";
      $retRegion = null;
      
      $this->DataManager->SQL = "";
      $row = $this->DataManager->Retrieve($keyColumns, $propertyNames);

      $retRegion = $this->DataManager->CreateDataObject(new Region(), $row);
      $this->OrderByNames = null;
      $this->DebugText .= $this->DataManager->DebugText;
      return $retRegion;
    }

    // Updates the records for the provided values.
    /// <include path='items/Update/*' file='Doc/RegionManger.xml'/>
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
  }  // RegionManager
?>
