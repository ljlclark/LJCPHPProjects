"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityTableRequest.js

/// <summary>The City Table Request</summary>
/// LibName: LJCCityTableRequest

// ***************
/// <summary>Contains City HTML Table web service request data.</summary>
class LJCCityTableRequest
{
  // #region Properties

  /// <summary>The service name.</summary>
  ServiceName = "LJCCityTable";

  // The action type name.
  /// <include path='items/Action/*' file='Doc/LJCCityTableRequest.xml'/>
  Action = "";

  /// <summary>The unique key of the first page item.</summary>
  BeginKeyData = null;

  /// <summary>The data access configuration file name.</summary>
  ConfigFile = "";

  /// <summary>The data access configuration name.</summary>
  ConfigName = "";

  /// <summary>The unique key of the last page item.</summary>
  EndKeyData = null;

  /// <summary>The page item count limit.<summary>
  Limit = 20;

  // *** Add ***
  /// <summary>The table column property names.</summary>
  PropertyNames = [];
  // #endregion

  // #region Constructor Methods.

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/LJCCityTableRequest.xml'/>
  constructor(configName = "", configFile = "DataConfigs.xml")
  {
    this.ConfigName = configName;
    this.ConfigFile = configFile;

    this.BeginKeyData = { ProvinceID: 0, Name: "" };
    this.EndKeyData = { ProvinceID: 0, Name: "" };
  }
  // #endregion

  // #region Data Object Methods

  /// <summary>Creates a clone of this object.</summary>
  Clone()
  {
    let retRequest = null;

    let json = LJC.CreateJSON(this);
    retRequest = LJC.ParseJSON(json);
    return retRequest;
  }
  // #endregion
}

