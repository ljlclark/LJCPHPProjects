<?php
  // #SectionBegin Collection
  // #Value _CollectionName_ Cities
  // #Value _CollectionVar_ Cities
  // #Value _CollectionLocal_ cities
  // #Value _FileName_ CityDAL.php
  // #Value _ItemName_ City
  // #Value _ItemVar_ City
  // #Value _ItemLocal_ city
  // #Value _KeyPropertyName_ Name
  // #Value _KeyPropertyLocal_ name
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // _FileName_
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCollectionLib.php";
  include_once "$prefix/LJCPHPCommon/LJCDBAccessLib.php";
  // LJCCommonLib: LJC
  // LJCCollectionLib: LJCCollectionBase
  // LJCDBAccessLib: LJCConnectionValues

  /// <summary>The _ItemName_ Data Access Layer Library</summary>
  /// LibName: _ItemName_DAL
  //  Classes:
  //    _ItemName_, _CollectionName_, _ItemName_Manager

  // ***************
  /// <summary>Represents a collection of _ItemName_ objects.</summary>
  /// <include path='items/_CollectionName_/*' file='Doc/_CollectionName_.xml'/>
  /// <group name="Static">Static Methods</group>
  //    ToCollection()
  /// <group name="Constructor">Constructor Methods</group>
  /// <group name="DataClass">Data Class Methods</group>
  //    Clone()
  /// <group name="Data">Content Methods</group>
  //    Add(), AddObject(), AddObjects(), Remove(), Retrieve()
  /// <group name="Other">Other Methods</group>
  //    SelectItems(), KeyNames(), ToArray()
  /// <group name="Debug">Debug Methods</group>
  //    DebugItems()
  class _CollectionName_ extends LJCCollectionBase
  {
    // ---------------
    // Static Methods

    // Create typed collection from deserialized JavasScript collection.
    /// <include path='items/ToCollection/*' file='Doc/_CollectionName_.xml'/>
    /// <ParentGroup>Static</ParentGroup>
    public static function ToCollection($items): ?_CollectionName_
    {
      $ret_CollectionName_ = null;

      // ReadItems is in the JavaScript collection.
      if (isset($items)
        && LJC::HasElements($items->ReadItems))
      {
        $ret_CollectionName_ = new _CollectionName_();
        foreach ($items->ReadItems as $objItem)
        {
          // Create typed object from stdClass.
          $_ItemLocal_ = _ItemName_::Copy($objItem);
          $retCollection->AddObject($_ItemLocal_);
        }
      }
      return $ret_CollectionName_;
    } // ToCollection()

    // ---------------
    // Constructor Methods

    /// <summary>Initializes a class instance.</summary>
    /// <ParentGroup>Constructor</ParentGroup>
    public function __construct()
    {
      $this->ClassName = "_CollectionName_";
    } // __construct()

    // Standard debug method for each class.
    private function AddDebug($methodName, $valueName, $value = null)
    {
      $location = LJC::Location($this->ClassName, $methodName
        , $valueName);
      $this->DebugText .= LJC::DebugObject($location, $value);
    } // AddDebug()

    // ---------------
    // Data Class Methods

    /// <summary>Creates an object clone.</summary>
    /// <ParentGroup>DataClass</ParentGroup>
    public function Clone(): self
    {
      $retCollection = new self();

      foreach ($this->Items as $key => $item)
      {
        $retCollection->AddObject($item, $key);
      }
      unset($item);
      return $retCollection;
    } // Clone()

    // ---------------
    // Data Methods

    // Creates an object and adds it to the collection.
    /// <include path='items/Add/*' file='Doc/_CollectionName_.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Add(string $name, ?string $description = null, $key = null)
      : ?_ItemName_
    {
      $methodName = "Add()";
      $retItem = null;

      if (null == $key)
      {
        $key = $_KeyPropertyName_;
        //$key = $this->count();
      }

      $item = new _ItemName_($name, $description);
      $retItem = $this->AddObject($item, $key);
      return $retItem;
    } // Add()

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='Doc/_CollectionName_.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObject(_ItemName_ $item, $key = null): ?_ItemName_
    {
      $methodName = "AddObject()";

      if (null == $key)
      {
        // ToDo: Handle multiple properties?
        //$key = $item->_KeyPropertyName_;
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

    // Adds another collection of objects to this collection.
    /// <include path='items/AddObjects/*' file='Doc/_CollectionName_.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function AddObjects(_CollectionName_ $items)
    {
      foreach ($items as $item)
      {
        $this->AddObject($item);
      }
    } // AddObjects()

    // Inserts an object at the provided insert index.
    /// <include path='items/InsertObject/*' file='Doc/_CollectionName_.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function InsertObject(_ItemName_ $insertItem, int $insertIndex
      , $key = null): ?_ItemName_
    {
      $methodName = "InsertObject()";
      $process = true;
      $retItem = null;

      if (null == $key)
      {
        $key = $insertItem->_KeyPropertyName_;
      }

      // Just add object if insert index is beyond end of the array.
      if ($insertIndex > count($this->Items) - 1)
      {
        $this->AddObject($insertItem);
        $process = false;
      }
      if ($insertIndex < 0)
      {
        $insertIndex = 0;
      }

      if ($process)
      {
        // Create new items with inserted item.
        $tempItems = [];
        for (int $index = 0; $index < count($this->Items); $index++)
        {
          // RetrieveWithIndex() is in LJCCollectionBase.
          $item = $this->RetrieveWithIndex($index);

          // Insert before insert index.
          if ($index == $insertIndex)
          {
            $key = $insertItem->_KeyPropertyName_;

            if (isset($this->Items[$key]))
            {
              throw new Exception("Key: {$key} is already in use.");
            }
            $tempItems[$key] = $insertItem;
            $retItem = $insertItem;
          }

          $tempItems[$item->_KeyPropertyName_] = $item;
        }

        // Replace items with new items.
        $this->Items = $tempItems;
      }
      return $retItem;
    } // InsertObject()

    // Removes the item by Key value.
    /// <include path='items/Remove/*' file='Doc/_CollectionName_.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Remove($key, bool $throwError = true): void
    {
      // DeleteItem() is in LJCCollectionBase.
      $this->DeleteItem($key, $throwError);
    }

    // Retrieves the item by Key value.
    /// <include path='items/Retrieve/*' file='Doc/_CollectionName_.xml'/>
    /// <ParentGroup>Data</ParentGroup>
    public function Retrieve($key, bool $throwError = true): ?_ItemName_
    {
      // RetrieveItem() is in LJCCollectionBase.
      $retItem = $this->RetrieveItem($key, $throwError);
      return $retItem;
    } // Retrieve()

    // ---------------
    // Other Methods

    /// <summary>Creates a KeyNames list from the collection.</summary>
    /// <ParentGroup>Other</ParentGroup>
    public function KeyNames(): array
    {
      $retKeyNames = [];

      foreach ($this as $item)
      {
        $retKeyNames[] = $item->_KeyPropertyName_;
      }
      return $retKeyNames;
    } // KeyNames()

    // Get the items that match the key names array values.
    /// <include path='items/SelectItems/*' file='Doc/_CollectionName_.xml'/>
    /// <ParentGroup>Other</ParentGroup>
    public function SelectItems(array $keyNames = null): self
    {
      $retItems = null;

      if (null == $keyNames)
      {
        $retItems = $this->Clone();
      }
      else
      {
        $retItems = new self();
        foreach ($keyNames as $keyName)
        {
          if (array_key_exists($keyName, $this->Items))
          {
            $retItems->AddObject($this->Items[$keyName]);
          }
        }
      }
      return $retItems;
    } // SelectItems()

    /// <summary>Get an array of item objects.</summary>
    /// <ParentGroup>Other</ParentGroup>
    public function ToArray()
    {
      $retArray = [];

      foreach ($this->Items as $item)
      {
        $retArray[] = clone $item;
      }
      return $retArray;
    }

    // ---------------
    // Debug Methods

    // Output _ItemName_ information.
    /// <ParentGroup>Debug</ParentGroup>
    public static function DebugItems(_CollectionName_ $items
      , string $location = null): void
    {
      $text = "Debug_CollectionName_:";
      if ($location != null)
      {
        $text .= " {$location}";
      }
      LJC::Debug(0, $text);
      foreach ($items as $item)
      {
        LJC::Debug(0, "\$item-Name", $item->Name);
        LJC::Debug(0, "\$item-Description", $item->Description);
      }
      LJC::Debug();
    }
  }  // DebugItems()

    // ---------------
    // Properties

    /// <summary>The debug text.</summary>
    public string $DebugText;
  // #SectionEnd
?>