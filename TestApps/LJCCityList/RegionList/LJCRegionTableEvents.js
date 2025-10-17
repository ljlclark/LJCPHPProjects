"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCRegionTableEvents.js

// #region External

// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   LJC: AddEvent(), Element(), HasElements(), Visibility()
//   Debug: ShowText(), ShowDialog()
// <script src="../../LJCJSCommon/LJCDataLib.js"></script>
//   LJCDataColumn:
//   LJCDataColumns: Add(), Count()
// <script src="CityList/LJCRegionDAL.js"></script>
//   Region
// <script src="LJCTable.js"></script>
//   LJCTable: SelectRow(), SelectColumnRow()
// #endregion

/// <summary>The Region Table Events</summary>
/// LibName: LJCRegionTableEvents
//  Classes: LJCRegionTableEvents, LJCRegionTableRequest, LJCRegionTableResponse

// ***************
/// <summary>Contains Region HTML Table event handlers.</summary>
class LJCRegionTableEvents
{
  // #region Properties

  /// <summary>The associated city table helper object.</summary>
  // Used in Page() and #TableClick.
  RegionTable = null; // LJCTable;

  /// <summary> The city table data request.</summary>
  // Used in CityListEvents constructor(), #Refresh().
  RegionTableRequest = null; // LJCRegionTableRequest;
  // #endregion

  // #region Private Properties

  // The show debug text object.
  #Debug = null;

  // Flags set in NextPage() and PrevPage().
  // Used in #HasData() and #UpdateLimitFlags() which are called in the Page()
  // response.
  #IsNextPage = false;
  #IsPrevPage = false;

  // The city list events.
  #CityListEvents = null; // LJCCityListEvents;

  // The associated menu ID name.
  #RegionMenuID = "";

  // The associated table ID name.
  #RegionTableID = "";
  // #endregion

  // #region Constructor Methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(cityListEvents, regionMenuID, configName = ""
    , configFile = "DataConfigs.xml")
  {
    this.#Debug = new Debug("LJCRegionTableEvents");

    // Set properties from parameters.
    this.#CityListEvents = cityListEvents;
    this.#RegionMenuID = regionMenuID;

    this.#IsNextPage = false;
    this.#IsPrevPage = false;
    this.#RegionTableID = this.#CityListEvents.RegionTableID;

    this.RegionTable = new LJCTable(this.#RegionTableID, this.#RegionMenuID);

    // Service request for LJCCityTableService.php
    this.RegionTableRequest = new LJCRegionTableRequest(configName, configFile);
    let tableRequest = this.RegionTableRequest;
    tableRequest.RegionTableID = this.#RegionTableID;
    tableRequest.RegionTableName = LJCRegion.TableName;

    // Set retrieve property names.
    // null includes all columns.
    tableRequest.PropertyNames = null;

    // Get table columns.
    tableRequest.TableColumnNames = this.#TableColumnNames();

    this.#AddEvents();
  }

  /// <summary>Sets the dialog values.</summary>
  SetDialogValues(textDialogID, textAreaID)
  {
    this.#Debug.SetDialogValues(textDialogID, textAreaID);
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("click", this.#DocumentClick.bind(this));

    // Table Event Handlers.
    LJC.AddEvent(this.#RegionTableID, "click", this.#TableClick, this);
  }
  // #endregion

  // #region Event Handlers

  // The Document "click" handler.
  #DocumentClick()
  {
    LJC.Visibility(this.#RegionMenuID, "hidden");
  }

  // The Table "click" handler.
  #TableClick(event)
  {
    LJC.Visibility(this.#RegionMenuID, "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      if (this.RegionTable != null)
      {
        this.RegionTable.SelectColumnRow(eCell);
        this.UpdateTableRequest();
        //this.#CityListEvents.FocusTable = this.CityTable;
      }
    }
  }
  // #endregion

  // #region Page Methods

  /// <summary>Get next page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  NextPage()
  {
    if (!this.RegionTable.EndOfData)
    {
      this.#IsNextPage = true;
      this.RegionTableRequest.Action = "Next";
      this.UpdateTableRequest();
      this.Page(this.RegionTableRequest);
    }
  }

  /// <summary>Get previous page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  PrevPage()
  {
    if (!this.RegionTable.BeginningOfData)
    {
      this.#IsPrevPage = true;
      this.RegionTableRequest.Action = "Previous";
      this.UpdateTableRequest();
      this.Page(this.TableRequest);
    }
  }
  // #endregion

  // #region Web Service Methods

  /// <summary>Sends page request to CityData web service.</summary>
  // Called from NextPage(), PrevPage() and CityListEvents #Refesh().
  Page()
  {
    let methodName = "Page()";

    // Save a reference to this class for anonymous function.
    const self = this;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "RegionList/LJCRegionTableService.php");
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function ()
    {
      // Get the AJAX response.
      if (LJC.HasText(this.responseText))
      {
        if (!LJCRegionTableResponse.IsValidResponse(this.responseText))
        {
          // ToDo: Remove response check?
          self.#Debug.ShowText(methodName, "this.responseText"
            , this.responseText, false);
        }

        //let response = JSON.parse(this.responseText);
        let response = new LJCRegionTableResponse(this.responseText);

        self.#Debug.ShowDialog(methodName, "response.DebugText"
          , response.DebugText, false);
        self.#Debug.ShowText(methodName, "response.SQL"
          , response.SQL, false);

        // Handle new table HTML and associated values.
        if (self.#HasData(response.HTMLTable))
        {
          // Create new table element and add new "click" event.
          let eTable = LJC.Element(self.#RegionTableID);
          eTable.outerHTML = response.HTMLTable;
          LJC.AddEvent(self.#RegionTableID, "click", self.#TableClick
            , self);

          // Updates CityTable with new table element, keys and data columns.
          let rowIndex = self.#UpdateRegionTable(self, response.Keys);
          let regionTable = self.RegionTable;

          regionTable.TableColumns = response.TableColumns;

          // Updates the BeginningOfData and EndOfData flags.
          if (self.#UpdateLimitFlags())
          {
            // Get row index if "NextPage" or "PrevPage";
            rowIndex = regionTable.CurrentRowIndex;
          }

          regionTable.SelectRow(rowIndex, rowIndex);

          // Set hidden form primary keys and RegionTableRequest.
          self.UpdateTableRequest()

          // Can only assign public data.
          self.#CityListEvents.RegionTable = regionTable;
          //self.#CityListEvents.FocusTable = regionTable;
        }
      }
    };

    let tableRequest = this.RegionTableRequest.Clone();
    tableRequest.ConfigFile = "../DataConfigs.xml";
    let request = tableRequest.Request();
    xhr.send(request);
  }

  // Sets the form values before a detail submit and the page values before
  // a page submit.
  /// <include path='items/UpdateTableRequest/*' file='Doc/LJCCityTableEvents.xml'/>
  // Called from LJCCityListEvents #DocumentContextMenu()
  // #TableClick(), NextPage(), PrevPage(), Page().
  UpdateTableRequest()
  {
    let regionTable = this.RegionTable;

    // Set selected row primaryKeys in hidden form for detail dialog.
    let rowKeys = regionTable.Keys[regionTable.CurrentRowIndex - 1];
    if (rowKeys != null)
    {
      // Set HTML current row keys.
      rowRegionID.value = rowKeys.RegionID;
      rowName.value = rowKeys.RegionName;
    }

    if (this.RegionTableRequest != null)
    {
      // Get first row key.
      let rowKeys = regionTable.Keys[0];
      if (rowKeys != null)
      {
        let tableRequest = this.RegionTableRequest;
        tableRequest.BeginKeyData.Name = rowKeys.RegionName;
      }

      // Get last row key.
      let lastIndex = regionTable.Keys.length - 1;
      rowKeys = regionTable.Keys[lastIndex];
      if (rowKeys != null)
      {
        let tableRequest = this.RegionTableRequest;
        tableRequest.EndKeyData.Name = rowKeys.RegionName;
      }
    }
  }

  // Creates the table property names.
  #DefaultPropertyNames()
  {
    let retPropertyNames = [
      LJCRegion.PropertyRegionID,
      LJCRegion.PropertyNumber,
      LJCRegion.PropertyName,
      LJCRegion.PropertyDescription,
    ];
    return retPropertyNames;
  }

  // Checks if the provided table text exists.
  // Called from Page().
  #HasData(tableText)
  {
    let retValue = true;

    // There is no data.
    if (!LJC.HasText(tableText))
    {
      retValue = false;
      if (this.#IsNextPage)
      {
        // Keep at last row.
        this.CityTable.CurrentRowIndex = this.TableRequest.Limit - 1;
        this.#IsNextPage = false;
      }
      if (this.#IsPrevPage)
      {
        // Keep at first row.
        this.RegionTable.CurrentRowIndex = 1;
        this.#IsPrevPage = false;
      }
    }
    return retValue;
  }

  // Creates the table column names.
  #TableColumnNames()
  {
    let retColumnNames = [
      LJCRegion.PropertyNumber,
      LJCRegion.PropertyName,
      LJCRegion.PropertyDescription,
    ];
    return retColumnNames;
  }

  // Updates the BeginningOfData and EndOfData flags.
  // Called from Page().
  #UpdateLimitFlags()
  {
    let retValue = false;

    let regionTable = this.RegionTable;
    if (this.#IsNextPage)
    {
      retValue = true;
      regionTable.BeginningOfData = false;
      regionTable.EndOfData = false;
      if (regionTable.Keys.length < this.RegionTableRequest.Limit)
      {
        regionTable.EndOfData = true;
      }
      regionTable.CurrentRowIndex = 1;
      this.#IsNextPage = false;
    }

    if (this.#IsPrevPage)
    {
      retValue = true;
      regionTable.BeginningOfData = false;
      regionTable.EndOfData = false;
      if (regionTable.Keys.length < this.RegionTableRequest.Limit)
      {
        regionTable.BeginningOfData = true;
      }
      regionTable.CurrentRowIndex = this.RegionTableRequest.Limit;
      this.#IsPrevPage = false;
    }
    return retValue;
  }

  // Updates the CityTable ETable and Keys values.
  // Called from Page().
  #UpdateRegionTable(self, keys)
  {
    let retRowIndex = -1;

    let regionTable = self.RegionTable;

    // Return existing row index.
    retRowIndex = regionTable.CurrentRowIndex;

    // Reset table to new table element.
    regionTable.ETable = LJC.Element(this.#RegionTableID);

    regionTable.Keys = keys;
    return retRowIndex;
  }
  // #endregion
}

// ***************
/// <summary>Contains Region HTML Table web service request data.</summary>
class LJCRegionTableRequest
{
  // #region Properties

  // The action type name.
  /// <include path='items/Action/*' file='Doc/LJCRegionTableRequest.xml'/>
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
  Limit = 18;

  /// <summary>The data column property names.</summary>
  PropertyNames = [];

  RegionTableID = "";

  /// <summary>The service name.</summary>
  ServiceName = "LJCRegionTable";

  /// <summary>The table column property names.</summary>
  TableColumnNames = [];

  TableName = "";
  // #endregion

  // #region Constructor Methods.

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/LJCCityTableRequest.xml'/>
  constructor(configName = "", configFile = "DataConfigs.xml")
  {
    this.ConfigName = configName;
    this.ConfigFile = configFile;

    this.BeginKeyData = { Name: "" };
    this.EndKeyData = { Name: "" };
  }
  // #endregion

  // #region Data Object Methods

  /// <summary>Creates a clone of this object.</summary>
  Clone()
  {
    let retRequest = new LJCRegionTableRequest(this.ConfigName, this.ConfigFile);

    retRequest.Action = this.Action;
    retRequest.BeginKeyData = structuredClone(this.BeginKeyData);
    retRequest.EndKeyData = structuredClone(this.EndKeyData);
    retRequest.Limit = this.Limit;
    retRequest.PropertyNames = structuredClone(this.PropertyNames);
    retRequest.RegionTableID = this.RegionTableID;
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
/// <summary>The Region HTML Table web service response.</summary>
class LJCRegionTableResponse
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
    if (LJCCityTableResponse.IsValidResponse(responseText))
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
