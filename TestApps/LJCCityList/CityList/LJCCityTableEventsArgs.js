"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityTableEventsArgs.js

/// <summary>The City Table Events Args</summary>
/// LibName: LJCCityTableEventsArgs
//  Classes: LJCCityTableEventsArgs

// ***************
/// <summary>The City HTML Table event handlers constructor arguments.</summary>
class LJCCityTableEventsArgs
{
  // #region Properties
  // ---------------

  // The data configuration file.
  ConfigFile = "";

  // The data configuration name.
  ConfigName = "";

  // The database table name.
  DBTableName = "";

  // The HTML table column names.
  HTMLTableColumnNames = [];

  // The associated menu ID name.
  HTMLMenuID = "";

  // The associated table ID name.
  HTMLTableID = "";

  // The optional query property names.
  // Uses all properties if none provided.
  QueryPropertyNames = [];

  // The optional query join property names.
  QueryJoinPropertyNames = [];

  // The Unique key property names.
  UniquePropertyNames = [];
  // #endregion

  // #region Static Methods
  // ---------------

  /// <summary>
  ///   Creates a new typed object with the provided object values.
  /// </summary>
  /// <param name="sourceObject">The source object.</param>
  /// <returns>The typed object.</returns>
  static Copy(sourceObject)
  {
    let retType = new LJCCityTableEventsArgs();

    // Update properties of typed object from provided source object.
    for (let propertyName in retType)
    {
      if (propertyName in sourceObject)
      {
        retType[propertyName] = sourceobject[propertyName];
      }
    }
    return retType;
  }
  // #endregion

  // #region Constructor Methods.
  // ---------------

  /// <summary>Initializes the object instance.</summary>
  /// <param name="htmlTableID">The associated table ID name.</param>
  /// <param name="htmlMenuID">The associated menu ID name.</param>
  /// <param name="dbTableName">The database table name.</param>
  /// <param name="htmlTableColumnNames">The HTML table column names.</param>
  /// <param name="configName">The data configuration name.</param>
  /// <param name="configFile">The data configuration file.</param>
  constructor(htmlTableID, htmlMenuID, dbTableName, htmlTableColumnNames
    , configName = "", configFile = "DataConfigs.xml")
  {
    this.HTMLTableID = htmlTableID;
    this.HTMLMenuID = htmlMenuID;
    this.DBTableName = dbTableName;
    this.HTMLTableColumnNames = htmlTableColumnNames;
    this.ConfigName = configName;
    this.ConfigFile = configFile;
  }
  // #endregion

  // #region Data Class Methods
  // ---------------

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retType = new LJCCityTableEventsArgs(this.HTMLTableID, this.HTMLMenuID
      , this.DBTableName, this.HTMLTableColumnNames, this.ConfigName
      , this.ConfigFile);

    retType.QueryPropertyNames = this.QueryPropertyNames;
    retType.QueryJoinPropertyNames = this.QueryJoinPropertyNames;
    return retType;
  }
  // #endregion
}