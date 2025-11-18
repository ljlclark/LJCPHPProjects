"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityListEvents.js

// #region External
// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   LJC: AddEvent(), CreateJSON(), HasText(), MouseLocation(), ParseJSON()
//   Visibility()
//   Debug: ShowText(), ShowDialog()
// <script src="LJCTable.js"></script>
//   LJCTable: GetTable(), ShowMenu() MoveNext(), MovePrevious()
//   SelectColumnRow()
// #endregion

/// <summary>The City List Events</summary>
/// LibName: LJCCityListEvents

// ***************
/// <summary>Contains CityList event handlers.</summary>
class LJCCityListEvents
{
  // #region Properties
  // ---------------

  /// <summary>The city table helper object.</summary>
  // Used in LJCCityTableEvents Page().
  CityTable = null; // LJCTable

  /// <summary>The city HTML Table ID.</summary>
  // Used in LJCCityTableEvents constructor().
  CityTableID = "";

  /// <summary>The region table helper object.</summary>
  // Used in LJCRegionTableEvents Page().
  RegionTable = null // LJCTable

  /// <summary>The region HTML Table ID.</summary>
  RegionTableID = "";
  // #endregion

  // #region Private Properties
  // ---------------

  // The city HTML menu ID.
  #CityMenuID = "";

  // The city table events.
  #CityTableEvents = null; // LJCCityTableEvents

  // The data configuration file.
  #ConfigFile = "";

  // The data configuration name.
  #ConfigName = "";

  // The show debug text object.
  #Debug = null;

  // The detail dialog events.
  #RegionDetailEvents = null; // LJCRegionDetailEvents

  // The region HTML menu ID.
  #RegionMenuID = "";

  // The region table events.
  #RegionTableEvents = null; // LJCRegionTableEvents
  // #endregion

  // #region Constructor Methods.
  // ---------------

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/LJCCityListEvents.xml'/>
  constructor(cityTableID, configName = "", configFile = "DataConfigs.xml")
  {
    this.#Debug = new Debug("LJCCityListEvents");

    this.CityTableID = cityTableID;
    this.#ConfigName = configName;
    this.#ConfigFile = configFile;

    this.RegionTableID = "selectTable";

    this.#CityMenuID = "cityMenu";
    this.#RegionMenuID = "regionMenu";

    // Creates CityTable and #CityTableEvents.
    this.#SetupCity();
    this.#SetupRegion();

    this.#AddEvents();
    this.#CityTableEvents.Refresh();
  }

  // Sets the dialog values.
  /// <include path='items/SetDialogValues/*' file='Doc/LJCCityListEvents.xml'/>
  SetDialogValues(textDialogID, textAreaID)
  {
    this.#Debug.SetDialogValues(textDialogID, textAreaID);
    this.#CityTableEvents.SetDialogValues(textDialogID, textAreaID);
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    LJC.AddEvent("regionButton", "click", this.#RegionButton, this);
    LJC.AddEvent("provinceButton", "click", this.#ProvinceButton, this);

    // Region Menu Event Handlers.
    LJC.AddEvent("regionRefresh", "click", this.#RegionRefresh, this);
  }

  // Creates the region table property names.
  #RegionPropertyNames()
  {
    let retPropertyNames = [
      Region.PropertyRegionID,
      Region.PropertyNumber,
      Region.PropertyName,
      Region.PropertyDescription,
    ];
    return retPropertyNames;
  }
  // #endregion

  // #region Setup City Table.

  // Creates the table column property names.
  // Can include join column property names.
  #CityTableColumnNames()
  {
    let retTableColumnNames = [
      City.PropertyName,
      City.PropertyDescription,
      City.PropertyCityFlag,
      City.PropertyZipCode,
      City.PropertyDistrict,
    ];
    return retTableColumnNames;
  }

  // Creates the city table result property names.
  #CityQueryProperties()
  {
    let retQueryProperties = [
      City.PropertyCityID,
      City.PropertyProvinceID,
      City.PropertyProvinceName,
      City.PropertyName,
      City.PropertyDescription,
      City.PropertyCityFlag,
      City.PropertyZipCode,
      City.PropertyDistrict,
    ];
    // *** Default ***
    retQueryProperties = null;
    return retQueryProperties;
  }

  // Creates the city unique property nanes.
  #CityUniqueProperties()
  {
    let retUniqueProperties = [
      "ProvinceID",
      "Name",
    ];
    return retUniqueProperties;
  }

  #SetupCity()
  {
    // City Table helper object.
    this.CityTable = new LJCTable(this.CityTableID, this.#CityMenuID);
    const uniqueProperties = this.#CityUniqueProperties();
    this.CityTable.UniqueProperties = uniqueProperties;

    // City Table events.
    this.#CityTableEvents = new LJCCityTableEvents(this, this.#CityMenuID
      , this.#ConfigName, this.#ConfigFile);
    let htmlTableID = this.CityTableID;
    let tableName = City.TableName;
    let tableColumnNames = this.#CityTableColumnNames();
    this.#CityTableEvents.SetTableValues(htmlTableID, tableName
      , tableColumnNames);
    this.#CityTableEvents.Table = this.CityTable;

    let tableRequest = this.#CityTableEvents.TableRequest;
    tableRequest.Limit = 18;
    tableRequest.PropertyNames = this.#CityQueryProperties();
  }
  // #endregion

  // #region Setup Region Table and Detail.

  #SetupRegion()
  {
    // Region Table helper object.
    this.RegionTable = new LJCTable(this.RegionTableID, this.#RegionMenuID);

    // Region Table Events
    this.#RegionTableEvents = new LJCRegionTableEvents(this, this.#RegionMenuID
      , this.#ConfigName, this.#ConfigFile);

    let tableRequest = this.#RegionTableEvents.TableRequest;
    tableRequest.Limit = 18;
    // No join columns so leave null to use all columns.
    //tableRequest.PropertyNames = this.#RegionPropertyNames();

    // Region Detail events.
    this.#RegionDetailEvents = new LJCRegionDetailEvents(this.RegionTable);
  }
  // #endregion

  // #region City List Event Handlers
  // ---------------

  // Displays the Region Selection table.
  #RegionButton()
  {
    this.#RegionRefresh();
    //selectDialog.showModal();
  }

  // Displays the Province Selection table.
  #ProvinceButton()
  {
    alert("Province Button");
  }
  // #endregion

  // #region Region Menu Event Handlers
  // ---------------

  static #DialogResize()
  {
    const tableWidth = selectTable.offsetWidth;
    const tableHeight = selectTable.offsetHeight;
    selectDialog.style.width = tableWidth + 'px';
    selectDialog.style.height = tableHeight + 'px';
  }

  // Gets the float or int value of an html percentage or pixels.
  /// <include path='items/GetValue/*' file='Doc/LJCCityListEvents.xml'/>
  GetValue(htmlValue)
  {
    let retValue = htmlValue.trim();

    // Strip prefix and suffix.
    let length = retValue.length;
    if (retValue.includes("%"))
    {
      retValue = value.substring(0, length - 1);
    }
    if (retValue.includes("px"))
    {
      retValue = retValue.substring(0, length - 2);
    }

    // Convert to value.
    if (retValue.includes("."))
    {
      retValue = retValue.parseFloat(value);
    }
    else
    {
      retValue = retValue.parseInt(value);
    }

    return retValue;
  }

  // Sets the column widths and displays the dialog.
  /// <include path='items/PageDone/*' file='Doc/LJCCityListEvents.xml'/>
  PageDone(regionTableEvents)
  {
    // Set attributes including column widths.
    let regionTable = regionTableEvents.RegionTable;

    regionTable.SetColumnWidth(0, "103px");
    regionTable.SetColumnWidth(1, "200px");
    regionTable.SetColumnWidth(2, "200px");

    // Show the parent dialog.
    selectDialog.showModal();
  }

  // Refreshes the current page.
  #RegionRefresh()
  {
    let tableEvents = this.#RegionTableEvents;
    tableEvents.TableRequest.Action = "Refresh";
    this.#TableDataRequest(tableEvents);
  }

  // Sends page request to RegionTable web service.
  #TableDataRequest(tableEvents)
  {
    tableEvents.Page(this.PageDone);
  }
  // #endregion
}