"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCTableMessageLib.js

/// <summary>The Table Service Message Library.</summary>
/// LibName: LJCTableMessageLib
//  Classes: LJCTableRequest

// ***************
/// <summary>The HTML Table web service request.</summary>
class LJCTableRequest
{
  // #region Properties

  // The action type name.
  /// <include path='items/Action/*' file='Doc/LJCTableRequest.xml'/>
  Action = "";

  /// <summary>The array of LJCDataColumn objects to add to the table.</summary>
  AddTableColumns = [];

  /// <summary>The unique key of the first page item.</summary>
  BeginKeyData = null;

  /// <summary>The HTML city table element ID.</summary>
  HTMLTableID = "";

  /// <summary>The data access configuration file name.</summary>
  ConfigFile = "";

  /// <summary>The data access configuration name.</summary>
  ConfigName = "";

  /// <summary>The unique key of the last page item.</summary>
  EndKeyData = null;

  /// <summary>The page item count limit.<summary>
  Limit = 18;

  /// <summary>The data column property names.</summary>
  PropertyNames = [];

  /// <summary>The service name.</summary>
  ServiceName = "";

  /// <summary>The table column property names.</summary>
  TableColumnNames = [];

  /// <summary>The data source table name.</summary>
  TableName = "";
  // #endregion

  // #region Constructor Methods.

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/LJCTableRequest.xml'/>
  constructor(serviceName, configName = "", configFile = "DataConfigs.xml")
  {
    this.ServiceName = serviceName;
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
    //let retRequest = new LJCTableRequest(this.ConfigName, this.ConfigFile);
    let retRequest = new this.constructor(this.ConfigName, this.ConfigFile);

    retRequest.Action = this.Action;

    // Array of LJCDataColumn objects.
    retRequest.AddTableColumns = [];
    for (let index = 0; index < this.AddTableColumns.length; index++)
    {
      retRequest.AddTableColumns.push(this.AddTableColumns[index].Clone());
    }
    // ***** Test
    //retRequest.AddTableColumns = structuredClone(this.AddTableColumns);

    retRequest.BeginKeyData = structuredClone(this.BeginKeyData);
    retRequest.HTMLTableID = this.HTMLTableID;
    retRequest.ConfigFile = this.ConfigFile;
    retRequest.ConfigName = this.ConfigName;
    retRequest.EndKeyData = structuredClone(this.EndKeyData);
    retRequest.Limit = this.Limit;
    retRequest.PropertyNames = structuredClone(this.PropertyNames);
    retRequest.ServiceName = this.ServiceName;
    retRequest.TableColumnNames = structuredClone(this.TableColumnNames);
    retRequest.TableName = this.TableName;
    return retRequest;
  }
  // #endregion

  // #region Methods

  /// <summary>Creates the JSON request.</summary>
  /// <returns>The request as JSON.</returns>
  Request()
  {
    let retRequest = "";

    retRequest = LJC.CreateJSON(this);
    return retRequest;
  }
  // #endregion
}

// ***************
/// <summary>The HTML Table web service response.</summary>
class LJCTableResponse
{
  // #region Properties

  /// <summary>The service debug text.</summary>
  DebugText = "";

  /// <summary>The created HTML table text.</summary>
  HTMLTable = "";

  /// <summary>The keys that correspond to the HTML table text.</summary>
  Keys = [];

  /// <summary>The service name.</summary>
  ServiceName = "";

  /// <summary>The executed SQL statement.</summary>
  SQL = "";

  /// <summary>The table columns collection.</summary>
  TableColumns = null; // LJCDataColumns
  // #endregion

  // #region Static Methods

  /// <summary>Checks if the response is valid.</summary>
  /// <param name="responseText">The response text.</param>
  /// <returns>true if valid; otherwise false.</returns>
  static IsValidResponse(responseText)
  {
    let retValid = false;

    if (LJC.HasText(responseText))
    {
      let text = responseText.toLowerCase().trim();
      if (text.startsWith("{\"servicename\":"))
      {
        retValid = true;
      }
    }
    return retValid;
  }
  // #endregion

  // #region Constructor Methods.

  /// <summary>Initializes the object instance.</summary>
  /// <param name="responseText">The response text.</param>
  constructor(responseText)
  {
    if (LJCTableResponse.IsValidResponse(responseText))
    {
      let response = JSON.parse(responseText);

      this.DebugText = response.DebugText;
      this.HTMLTable = response.HTMLTable;
      this.Keys = response.Keys;
      this.ServiceName = response.ServiceName;
      this.SQL = response.SQL;
      let tableColumnsArray = response.TableColumnsArray;
      this.TableColumns = LJCDataColumns.ToCollection(tableColumnsArray);
    }
  }
  // #endregion
}
