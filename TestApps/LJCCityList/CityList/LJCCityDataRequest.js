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

  /// <summary>The service name.</summary>
  ServiceName = "LJCCityData";

  /// <summary>The message encoding type name.</summary>
  /// <remarks>"JSON" or "XML".</summary>
  MessageEncoding = "JSON";

  /// <summary>The action type name.</summary>
  /// <remarks>"Delete", "Insert", "Retrieve" or "Update".</summary>
  Action = "";

  /// <summary>The data access configuration file name.</summary>
  ConfigFile = "";

  /// <summary>The data access configuration name.</summary>
  ConfigName = "";

  /// <summary>
  ///   The primary keys where clause LJCDataColumns collection.
  /// </summary>
  /// <remarks>Required for "Delete", "Retrieve" and "Update".</remarks>
  KeyColumns = null;

  /// <summary>The request items.</summary>
  /// <remarks>
  ///   Insert - A Cities collection of items to insert.<br />
  ///   Update - A Cities collection of items to update.
  /// </remarks>
  RequestItems = null;

  /// <summary>The array of "Order By" names.</summary>
  OrderByNames = [];

  /// <summary>The array of property names.</summary>
  PropertyNames = [];

  /// <summary>The table names.</summary>
  TableName = "City";

  /// <summary>
  ///   The unique keys where clause LJCDataColumns collection.
  /// </summary >
  UniqueColumns = null;

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(configName = "", configFile = "DataConfigs.xml")
  {
    this.ConfigName = configName;
    this.ConfigFile = configFile;

    this.KeyColumns = new LJCDataColumns();
    this.RequestItems = new Cities();
    this.UniqueColumns = new LJCDataColumns();
  }

  /// <summary>Creates a clone of this object.</summary>
  Clone()
  {
    let retRequest = null;

    let json = LJC.CreateJSON(this);
    retRequest = LJC.ParseJSON(json);
    return retRequest;
  }
}
