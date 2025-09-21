"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityTableRequest.js
// <script src="LJCCityTableRequest.js"></script>
//   MoveNext(), MovePrevious(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains City HTML Table web service request data.</summary>
//  Constructor: constructor(), AddEvent()
//  Event Handlers: TableClick(), TableKeyDown()
//  Page Event Handlers: NextPage(), PrevPage(), Page()
//    UpdateLimitFlags(), UpdatePageData(), UpdateTableData()
class LJCCityTableRequest
{
  // ---------------
  // Properties

  /// <summary>The service name.</summary>
  ServiceName = "LJCCityTable";

  /// <summary>The action type name.</summary>
  /// <remarks>
  ///   "Next", "Previous", "Top"?, "Bottom"?, "First"?, "Last"?
  /// </remarks>
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

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(configName = "", configFile = "DataConfigs.xml")
  {
    this.ConfigName = configName;
    this.ConfigFile = configFile;

    this.BeginKeyData = { ProvinceID: 0, Name: "" };
    this.EndKeyData = { ProvinceID: 0, Name: "" };
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

