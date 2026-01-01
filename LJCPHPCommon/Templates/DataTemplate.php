<?php
  // #SectionBegin Main
  // #Value _ItemObjectName_ LJCCity;
  // #Value _ItemObjectVar_ City
  // #Value _ItemObject_ city
  // #Value _LibName_ LJCCityDAL
  // #Value _IDColumnName_ CityID
  // #Value _IDColumnLocal_ cityID
  // #Value _ParentColumnName_ ProvinceID
  // #Value _ParentColumnLocal_ provinceID
  // #Value _ParentName_ ProvinceName
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

  /// <summary>The _ItemObjectName_ Data Access Layer Library</summary>
  /// LibName: _LibName_
  //  Classes:
  //    _ItemObjectName_
  
  // ***************
  /// <summary>The _ItemObjectName_ data object class.</summary> 
  class _ItemObjectName_
  {
    // ---------------
    // Static Methods

    /// <summary>
    ///   Creates a new typed object with existing standard object values.
    /// </summary>
    /// <param name="objCity"></param>
    /// <returns>The new City object.</returns>
    public static function Copy($obj_ItemObjectVar_): ?_ItemObjectName_
    {
      $ret_ItemObjectVar_ = null;

      // Check for required values.
      if (property_exists($obj_ItemObjectVar_, "Name"))
      {
        $ret_ItemObjectVar_ = new _ItemObjectName_($obj_ItemObjectVar_->Name);

        // Look for properties of standard object in typed object.
        foreach ($obj_ItemObjectVar_ as $propertyName => $value)
        {
          // Check if object property exists in the typed item.
          if (property_exists($ret_ItemObjectVar_, $propertyName))
          {
            // Update new typed object property from the standard object.
            $success = false;
            $_ItemObject_Value = $ret_ItemObjectVar_->$propertyName;
            $objValue = $obj_ItemObjectVar_->$propertyName;
            if (is_int($_ItemObject_Value))
            {
              $ret_ItemObjectVar_->$propertyName = (int)$objValue;
              $success = true;
            }
            if (!$success
              && is_float($_ItemObject_Value))
            {
              $ret_ItemObjectVar_->$propertyName = (float)$objValue;
              $success = true;
            }
            if (!$success)
            {
              $ret_ItemObjectVar_->$propertyName = $objValue;
            }
          }
        }
      }
      return $ret_ItemObjectVar_;
    }

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    public function __construct($name = "", $_ParentColumnLocal_ = 0)
    {
      $this->ClassName = "_ItemObjectName_";

      $this->Name = $name;
      $this->_ParentColumnName_ = $_ParentColumnLocal_;

      $this->_IDColumnName_ = 0;
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

      $ret_ItemObjectVar_ = new self();
      $ret_ItemObjectVar_->_IDColumnName_ = $this->_IDColumnName_;
      $ret_ItemObjectVar_->_ParentColumnName_ = $this->_ParentColumnName_;
      $ret_ItemObjectVar_->Name = $this->Name;
      $ret_ItemObjectVar_->Description = $this->Description;
      return $ret_ItemObjectVar_;
    }

    // ---------------
    // Public Properties

    // Primary Keys

    /// <summary>The city primary key.</summary>
    public int $_IDColumnName_;

    // Parent Keys

    /// <summary>The province parent key and partial unique key.</summary>
    public int $_ParentColumnName_;

    // varchar(60)
    /// <summary>The province parent key value.</summary>
    public string $_ParentName_ = "";

    // Unique Keys

    // varchar(60)
    /// <summary>The partial unique key.</summary>
    public string $Name;

    // varchar(100)
    /// <summary>The city description.</summary>
    public ?string $Description;

    // Class Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;

    // ---------------
    // Constants

    public const TableName = "_ItemObjectVar_";
    public const Property_IDColumnName_ = "_IDColumnName_";
    public const Property_ParentColumnName_ = "_ParentColumnName_";
    public const Property_ParentName_ = "_ParentName_";
    public const PropertyName = "Name";
    public const PropertyDescription = "Description";

    public const DescriptionLength = 100;
    public const NameLength = 60;
    public const _ParentName_Length = 60;
  }  // _ItemObjectName_
