"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityDAL.js

/// <summary>The City Data Access Layer Library</summary>
/// LibName: LJCCityDAL

// A data object is a user defined data type that contains a group of
// related values. Each contained value has a unique name and a data type.
// It is a convenient way to represent a data entity.

// Data objects simplify the organization of related data, creating a
// structured and consistent way to manage information within an
// application.

// In object-oriented programming, a data object may also contain
// methods that operate on or access the data it contains.

// A data object can be reused across different parts of an application,
// which saves development effort and ensures data consistency. 

// ***************
/// <summary>The City data object class.</summary>
class City
{
  // #region Properties

  /// <summary>The city primary key.</summary>
  CityID = 0;

  /// <summary>The province parent key and partial unique key.</summary>
  ProvinceID = 0;

  // varchar(60)
  /// <summary>The province name.</summary>
  ProvinceName = "";

  // varchar(60)
  /// <summary>The partial unique key.</summary>
  Name = "";

  // varchar(100)
  /// <summary>The city description.</summary>
  Description = "";

  // bit(1)
  /// <summary>The city flag.</summary>
  /// <remarks>1 = city, 0 = municipality.</remarks>
  CityFlag = 0;

  // smallint
  /// <summary>The city district number.</summary>
  District = 0;

  // char(4) ?
  /// <summary>The city zip code.</summary>
  ZipCode = 0;

  static TableName = "City";
  static PropertyCityID = "CityID";
  static PropertyProvinceID = "ProvinceID";
  static PropertyProvinceName = "ProvinceName";
  static PropertyName = "Name";
  static PropertyDescription = "Description";

  static PropertyCityFlag = "CityFlag";
  static PropertyDistrict = "District";
  static PropertyZipCode = "ZipCode";

  static ProvinceNameLength = 60;
  static NameLength = 60;
  static DescriptionLength = 60;
  static ZipCodeLength = 4;
  // #endregion

  // #region Static Methods

  // Creates a new object with the supplied object values.
  /// <include path='items/Copy/*' file='Doc/City.xml'/>
  static Copy(objCity)
  {
    let retCity = new City();

    // Update properties of new object from provided object.
    for (let propertyName in this)
    {
      if (propertyName in objCity)
      {
        retCity[propertyname] = objCity[propertyName];
      }
    }
    return retCity;
  }
  // #endregion

  // #region Constructor Methods.

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/City.xml'/>
  constructor(provinceID, name, cityFlag = 0, cityID = 0)
  {
    this.CityID = cityID;
    this.ProvinceID = provinceID;
    this.Name = name;
    this.Description = "";

    this.CityFlag = cityFlag;
    this.ZipCode = 0;
    this.District = 0;
  }
  // #endregion

  // #region Data Object Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retCity = new City(this.ProvinceID, this.Name, this.CityFlag
      , this.CityID)
    retCity.Description = this.Description;

    retCity.ZipCode = this.ZipCode;
    retCity.District = this.District;
    return retCity;
  }
  // #endregion
}

// ***************
/// <summary>Represents a collection of city data objects.</summary>
class Cities
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
    let retCities = new Cities();

    for (let index = 0; index < names.length; index++)
    {
      let city = this.#Items[index];
      if (city != null)
      {
        retCities.AddObject(city.Clone());
      }
    }
    return retCities;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='items/Add/*' file='Doc/Cities.xml'/>
  Add(provinceID, name, cityFlag = 0, cityID = 0)
  {
    let retCity = new City(provinceID, name, cityFlag, cityID);
    this.AddObject(retCity);
    return retCity;
  }

  /// <summary>Adds the supplied city to the list.</summary>
  /// <param name="city">The City data object.</param>
  AddObject(city)
  {
    this.#Items.push(city);
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
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
    let retCity = this.#Items.find(item =>
      item.Name == name);
    return retCity;
  }

  // Retrieves the item at the supplied index.
  /// <include path='items/RetrieveWithIndex/*' file='Doc/Cities.xml'/>
  RetrieveWithIndex(index)
  {
    let retCity = null;

    if (index >= 0
      && this.#Items.length > index)
    {
      retCity = this.#Items[index];
    }
    return retCity;
  }
  #endregion

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
      let city = this.#Items[index];
      retNames.push(city.Name);
    }
    return retNames;
  }

  // Gets the items that match the supplied names.
  /// <include path='items/Items/*' file='Doc/Cities.xml'/>
  SelectItems(propertyNames)
  {
    let retCities = null;

    if (null == propertyNames)
    {
      retCities = this.#Items;
    }
    else
    {
      retCities = new Cities();
      for (let index = 0; index < names.length; index++)
      {
        let name = propertyNames[index];
        let city = this.Retrieve(name);
        if (city != null)
        {
          retCities.AddObject(city);
        }
      }
    }
    return retCities;
  }
  // #endregion
}