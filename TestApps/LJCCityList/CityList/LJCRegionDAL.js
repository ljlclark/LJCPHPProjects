"use strict";
// Copyright (c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCRegionDAL.js

/// <summary>The City Data Access Layer Library</summary>
/// LibName: LJCRegionDAL

// ***************
/// <summary>The Region data object class.</summary>
class LJCRegion
{
  // #region Properties

  /// <summary>The region primary key.</summary>
  RegionID = 0;

  // varchar(60)
  /// <summary>The Number value.</summary>
  Number = "";

  // varchar(60)
  /// <summary>The unique key.</summary>
  Name = "";

  // varchar(100)
  /// <summary>The Description value.</summary>
  Description = "";

  static TableName = "Region";
  static PropertyRegionID = "RegionID";
  static PropertyNumber = "Number";
  static PropertyName = "Name";
  static PropertyDescription = "Description";

  static DescriptionLength = 60;
  static NameLength = 60;
  static NumberLength = 5;
  // #endregion

  // #region Static Methods

  // Creates a new object with the supplied object values.
  /// <include path='items/Copy/*' file='Doc/LJCRegion.xml'/>
  static Copy(objRegion)
  {
    let retRegion = new LJCRegion();

    // Update properties of new object from provided object.
    for (let propertyName in retRegion)
    {
      if (propertyName in objRegion)
      {
        retRegion[propertyName] = objRegion[propertyName];
      }
    }
    return retRegion;
  }
  // #endregion

  // #region Constructor Methods.

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/LJCRegion.xml'/>
  constructor(name, number = "")
  {
    this.Name = name;
    this.Number = number;
    this.Description = "";
  }
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retRegion = new LJCRegion(this.Name, this.Number);
    retRegion.Description = this.Description;
    return retRegion;
  }
  // #endregion
}

// ***************
/// <summary>Represents a collection of region data objects.</summary>
class LJCRegions
{
  // #region Properties

  // The current items count.
  Count = 0;

  // The current #Items clone.
  ReadItems = [];

  // The internal collection item array.
  #Items = [];
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retRegions = new LJCRegions();

    for (let index = 0; index < names.length; index++)
    {
      let item = this.#Items[index];
      if (item != null)
      {
        retRegions.AddObject(item.Clone());
      }
    }
    return retRegions;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='items/Add/*' file='Doc/Cities.xml'/>
  Add(name, number = "")
  {
    let methodName = "Add()";
    let retRegion = null;

    let item = new LJCRegion(name, number);
    retRegion = this.AddObject(item);
    return retRegion;
  }

  /// <summary>Adds the supplied item to the list.</summary>
  /// <param name="item">The data object.</param>
  AddObject(item)
  {
    let methodName = "AddObject()";
    let retItem = null;

    if (!item instanceof LJCRegion)
    {
      throw new Exception("item is not of type LJCRegion.");
    }

    //if (item instanceof LJCRegion)
    //{
    this.#Items.push(item);
    retItem = item;
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
    //}
    return retItem;
  }

  /// <summary>Removes the item with the supplied name.</summary>
  /// <param name="name">The data object name.</param>
  Remove(name)
  {
    let itemIndex = this.GetIndex(name);
    if (itemIndex > -1)
    {
      let beginIndex = 0;
      this.#Items.splice(beginIndex, itemIndex);
      this.Count = this.#Items.length;
      this.ReadItems = Array.from(this.#Items);
    }
  }

  // Retrieves the item with the supplied name.
  /// <include path='items/Retrieve/*' file='Doc/Cities.xml'/>
  Retrieve(name)
  {
    let retRegion = this.#Items.find(item =>
      item.Name == name);
    return retRegion;
  }

  // Retrieves the item at the supplied index.
  /// <include path='items/RetrieveWithIndex/*' file='Doc/Cities.xml'/>
  RetrieveWithIndex(index)
  {
    let retRegion = null;

    if (index >= 0
      && this.#Items.length > index)
    {
      retRegion = this.#Items[index];
    }
    return retRegion;
  }
  // #endregion

  // #region Other Methods

  /// <summary>Clears the collection list.</summary>
  Clear()
  {
    this.#Items = [];
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }

  /// <summary>Returns the collection element count.</summary>
  Count()
  {
    let retCount = 0;

    retCount = this.#Items.length;
    return retCount;
  }

  // Gets the index of the item with the supplied name.
  /// <include path='items/GetIndex/*' file='Doc/Cities.xml'/>
  GetIndex(name)
  {
    let retIndex = -1;

    for (let index = 0; index < this.#Items.length; index++)
    {
      if (this.#Items.Name == name)
      {
        retIndex = index;
        break;
      }
    }
    return retIndex;
  }

  /// <summary>Gets an array of property names.</summary>
  /// <returns>The property name array.</returns>
  PropertyNames()
  {
    let retNames = [];

    for (let index = 0; index < this.#Items.length; index++)
    {
      let item = this.#Items[index];
      retNames.push(item.Name);
    }
    return retNames;
  }

  // Gets the items that match the supplied names.
  /// <include path='items/Items/*' file='Doc/Cities.xml'/>
  SelectItems(propertyNames)
  {
    let retRegions = null;

    if (null == propertyNames)
    {
      retRegions = this.#Items.Clone();
    }
    else
    {
      retRegions = new Cities();
      for (let index = 0; index < names.length; index++)
      {
        let name = propertyNames[index];
        let item = this.Retrieve(name);
        if (item != null)
        {
          retRegions.AddObject(item);
        }
      }
    }
    return retRegions;
  }
  // #endregion
}
