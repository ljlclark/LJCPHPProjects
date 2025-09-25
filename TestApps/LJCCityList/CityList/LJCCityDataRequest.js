"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityDataRequest.js

/// <summary>The City Data Request</summary>
/// LibName: LJCCityDataRequest
//  Classes: LJCCityDataRequest

// ***************
/// <summary>Contains CityData web service request data.</summary>
//  Constructor: constructor(), Clone()
class LJCCityDataRequest
{
  // ---------------
  // Properties

  /// <summary>The service name.</summary>
  ServiceName = "LJCCityData";

  /// <summary>The action type name.</summary>
  /// <remarks>"Delete", "Insert", "Retrieve" or "Update".</summary>
  Action = "";

  /// <summary>The data access configuration file name.</summary>
  ConfigFile = "";

  /// <summary>The data access configuration name.</summary>
  ConfigName = "";

  // The primary keys where clause LJCDataColumns collection.
  /// <include path='items/KeyColumns/*' file='Doc/LJCCityDataRequest.xml'/>
  KeyColumns = null;

  // The request Cities collection.
  /// <include path='items/RequestItems/*' file='Doc/LJCCityDataRequest.xml'/>
  RequestItems = null;

  /// <summary>The array of "Order By" names.</summary>
  OrderByNames = [];

  /// <summary>The array of property names.</summary>
  PropertyNames = [];

  /// <summary>The table names.</summary>
  TableName = "City";

  // The unique keys where clause LJCDataColumns collection.
  /// <include path='items/UniqueColumns/*' file='Doc/LJCCityDataRequest.xml'/>
  UniqueColumns = null;

  // ---------------
  // Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  /// <include path='items/constructor/*' file='Doc/LJCCityTableRequest.xml'/>
  constructor(configName = "", configFile = "DataConfigs.xml")
  {
    this.ConfigName = configName;
    this.ConfigFile = configFile;

    this.KeyColumns = new LJCDataColumns();
    this.RequestItems = new Cities();
    this.UniqueColumns = new LJCDataColumns();
  }

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retRequest = null;

    let json = LJC.CreateJSON(this);
    retRequest = LJC.ParseJSON(json);
    return retRequest;
  }
}
