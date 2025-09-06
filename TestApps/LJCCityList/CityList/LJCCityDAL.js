"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityDAL.js

/// <summary>The Retion Data Access Layer Library</summary>
/// LibNmae: LJCCityDAL
//  Classes:
//    City, Cities
//    CitySection, CitySections
//    Region, Regions

// ***************
/// <summary>The City data object class.</summary>
//  Constructor: constructor(), Clone()
class City
{
  // ---------------
  // Properties

  CityID;
  ProvinceID;
  Name;
  Description;
  CityFlag;
  ZipCode;
  District;

  // Creates a new object with existing standard object values.
  /// <include path='items/Copy/*' file='Doc/LJCDataColumn.xml'/>
  static Copy(cityObject)
  {
    let retCity = new City();

    for (let propertyName in this)
    {
      if (propertyName in cityObject)
      {
        retCity[propertyname] = cityObject[propertyName];
      }
    }
    return retCity;
  }

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(provinceID, name, cityFlag = 0, cityID = 0)
  {
    this.ProvinceID = provinceID;
    this.Name = name;
    this.CityFlag = cityFlag;
    this.CityID = cityID;
    this.Description = "";
    this.ZipCode = "";
    this.District = "";
  }

  /// <summary>Creates a clone of this object.</summary>
  Clone()
  {
    let retCity = new City(this.ProvinceID, this.Name, this.CityFlag
      , this.CityID)
    retCity.Description = this.Description;
    retCity.ZipCode = this.ZipCode;
    retCity.District = this.District;
    return retCity;
  }
}

// ***************
/// <summary>Represents a collection of city data objects.</summary>
//  Add(), AddObject(), Clear(), Columns(), GetIndex(), PropertytNames()
//  Remove(), Retrieve(), RetrieveWithIndex()
class Cities
{
  // ---------------
  // Properties

  Items = [];

  // ---------------
  // Methods

  // Creates and adds the data object to the list.
  /// <include path='items/Add/*' file='Doc/Cities.xml'/>
  Add(provinceID, name, cityFlag = 0, cityID = 0)
  {
    let retCity = new City(provinceID, name, cityFlag, cityID);
    this.AddObject(retCity);
    return retCity;
  }

  /// <summary>Adds the supplied column to the list.</summary>
  /// <param name="city">The City data object.</param>
  AddObject(city)
  {
    this.Items.push(city);
  }

  /// <summary>Clears the collection list.</summary>
  Clear()
  {
    this.Items = [];
  }

  // Gets the data objects that match the property names.
  /// <include path='items/Columns/*' file='Doc/Cities.xml'/>
  Columns(names)
  {
    let retCities = null;

    if (null == names)
    {
      retCities = this.Items;
    }
    else
    {
      retCities = new Cities();
      for (let index = 0; index < names.length; index++)
      {
        let name = names[index];
        let city = this.Retrieve(name);
        if (city != null)
        {
          retCities.AddObject(city);
        }
      }
    }
    return retCities;
  }

  // Gets the data object with the supplied property name.
  /// <include path='items/GetIndex/*' file='Doc/Cities.xml'/>
  GetIndex(name)
  {
    let retIndex = -1;

    for (let index = 0; index < this.Items.length; index++)
    {
      if (this.Items.Name == name)
      {
        retIndex = index;
        break;
      }
    }
    return retIndex;
  }

  /// <summary>Gets an array of names.</summary>
  /// <returns>The name array.</returns>
  Names()
  {
    let retNames = [];

    for (let index = 0; index < this.Items.length; index++)
    {
      let city = this.Items[index];
      retPropertyNames.push(city.Name);
    }
    return retNames;
  }

  // Removes the column object with the supplied property name.
  /// <include path='items/Remove/*' file='Doc/Cities.xml'/>
  Remove(name)
  {
    let itemIndex = this.GetIndex(name);
    if (itemIndex > -1)
    {
      let beginIndex = 0;
      this.Items.splice(beginIndex, itemIndex);
    }
  }

  // Retrieves the column object with the supplied property name.
  /// <include path='items/Retrieve/*' file='Doc/LJCDataColumns.xml'/>
  Retrieve(name)
  {
    let retCity = this.Items.find(item =>
      item.Name == name);
    return retCity;
  }

  // Retrieves the data object with the supplied index.
  /// <include path='items/RetrieveWithIndex/*' file='Doc/Cities.xml'/>
  RetrieveWithIndex(index)
  {
    let retCity = null;

    if (index >= 0 && this.Items.length > index)
    {
      retCity = this.Items[index];
    }
    return retCity;
  }
}