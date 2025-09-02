"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityDataRequest.js

// ***************
/// <summary>Contains CityData web service request data.</summary>
//  Constructor: constructor(), Clone()
class LJCCityDataRequest
{
  // ---------------
  // Properties

  // Values: "Delete", "Insert", "Retrieve", "Update"
  Action;
  ConfigFile;
  ConfigName;
  KeyColumns;
  RequestItems;
  OrderByNames;
  PropertyNames;
  TableName;
  UniqueColumns;

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(listEvents, menuID)
  {
    this.Action = "";
    this.ConfigFile = "DataConfigs.xml";
    this.ConfigName = "TestData";
    this.KeyColumns = new LJCDataColumns();
    this.RequestItems = new LJCDataColumns();
    this.OrderByNames = [];
    this.PropertyNames = [];
    this.TableName = "";
    this.UniqueColumns = new LJCDataColumns();
  }

  /// <summary>Creates a clone of this object.</summary>
  Clone()
  {
    return JSON.parse(JSON.stringify(this));
  }
}

