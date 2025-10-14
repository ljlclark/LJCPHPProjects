"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityTableEvents.js

// #region External

// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   LJC: AddEvent(), Element(), HasElements(), Visibility()
//   Debug: ShowText(), ShowDialog()
// <script src="../../LJCJSCommon/LJCDataLib.js"></script>
//   LJCDataColumn: 
//   LJCDataColumns: Add(), Count()
// <script src="CityList/LJCCityTableRequest.js"></script>
//   LJCCityTableRequest: Clone()
// <script src="LJCTable.js"></script>
//   LJCTable: SelectRow(), SelectColumnRow()
// #endregion

/// <summary>The City Table Events</summary>
/// LibName: LJCCityTableEvents

// ***************
/// <summary>Contains City HTML Table event handlers.</summary>
class LJCCityTableEvents
{
  // #region Properties

  /// <summary>The associated city table helper object.</summary>
  // Used in Page() and #TableClick.
  CityTable = null; // LJCTable;

  /// <summary> The city table data request.</summary>
  // Used in CityListEvents constructor(), #Refresh().
  CityTableRequest = null; // LJCCityTableRequest;
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
  #MenuID = "";

  // The associated table ID name.
  #TableID = "";
  // #endregion

  // #region Constructor Methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(cityListEvents, menuID, configName = ""
    , configFile = "DataConfigs.xml")
  {
    this.#Debug = new Debug("LJCCityTableEvents");

    // Set properties from parameters.
    this.#CityListEvents = cityListEvents;
    this.#MenuID = menuID;

    this.#IsNextPage = false;
    this.#IsPrevPage = false;
    this.#TableID = this.#CityListEvents.CityTableID;

    this.CityTable = new LJCTable(this.#TableID, this.#MenuID);

    // Service request for LJCCityTableService.php
    this.CityTableRequest = new LJCCityTableRequest(configName, configFile);
    let tableRequest = this.CityTableRequest;
    tableRequest.CityTableID = this.#TableID;
    tableRequest.TableName = City.TableName;

    // Set retrieve property names.
    // null includes all columns.
    tableRequest.PropertyNames = null;

    // Get table columns.
    // Can include join column names.
    tableRequest.TableColumnNames = this.#TableColumnNames();

    // Add join table columns.
    //let addColumns = new LJCDataColumns()
    //let addColumn = addColumns.Add(City.PropertyProvinceName);
    //addColumn.InsertIndex = 0; // Default
    //tableRequest.AddColumns = LJC.ToArray(addColumns);

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
    LJC.AddEvent(this.#TableID, "click", this.#TableClick, this);
  }
  // #endregion

  // #region Event Handlers

  // The Document "click" handler.
  #DocumentClick()
  {
    LJC.Visibility(this.#MenuID, "hidden");
  }

  // The Table "click" handler.
  #TableClick(event)
  {
    LJC.Visibility(this.#MenuID, "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      if (this.CityTable != null)
      {
        this.CityTable.SelectColumnRow(eCell);
        this.UpdateTableRequest();
        this.#CityListEvents.FocusTable = this.CityTable;
      }
    }
  }
  // #endregion

  // #region Page Methods

  /// <summary>Get next page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  NextPage()
  {
    if (!this.CityTable.EndOfData)
    {
      this.#IsNextPage = true;
      this.CityTableRequest.Action = "Next";
      this.UpdateTableRequest();
      this.Page(this.CityTableRequest);
    }
  }

  /// <summary>Get previous page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  PrevPage()
  {
    if (!this.CityTable.BeginningOfData)
    {
      this.#IsPrevPage = true;
      this.CityTableRequest.Action = "Previous";
      this.UpdateTableRequest();
      this.Page(this.CityTableRequest);
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
    xhr.open("POST", "CityList/LJCCityTableService.php");
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function ()
    {
      // Get the AJAX response.
      if (LJC.HasText(this.responseText))
      {
        self.#Debug.ShowText(methodName, "this.responseText"
          , this.responseText, false);

        let response = JSON.parse(this.responseText);

        self.#Debug.ShowDialog(methodName, "response.DebugText"
          , response.DebugText, false);
        self.#Debug.ShowText(methodName, "response.SQL"
          , response.SQL, false);

        // Handle new table HTML and associated values.
        if (self.#HasData(response.HTMLTable))
        {
          // Create new table element and add new "click" event.
          let eTable = LJC.Element(self.#TableID);
          eTable.outerHTML = response.HTMLTable;
          LJC.AddEvent(self.#TableID, "click", self.#TableClick
            , self);

          // Updates CityTable with new table element, keys and data columns.
          let rowIndex = self.#UpdateCityTable(self, response.Keys);
          let cityTable = self.CityTable;

          let tableColumnsArray = response.TableColumnsArray;
          cityTable.TableColumns = LJCDataColumns.ToCollection(tableColumnsArray);

          // Updates the BeginningOfData and EndOfData flags.
          if (self.#UpdateLimitFlags())
          {
            // Get row index if "NextPage" or "PrevPage";
            rowIndex = cityTable.CurrentRowIndex;
          }

          cityTable.SelectRow(rowIndex, rowIndex);

          // Set hidden form primary keys and CityTableRequest.
          self.UpdateTableRequest()

          // Can only assign public data.
          self.#CityListEvents.CityTable = cityTable;
          self.#CityListEvents.FocusTable = cityTable;
        }
      }
    };

    let tableRequest = this.CityTableRequest.Clone();
    tableRequest.ConfigFile = "../DataConfigs.xml";
    let request = LJC.CreateJSON(tableRequest);
    xhr.send(request);
  }

  // Sets the form values before a detail submit and the page values before
  // a page submit.
  /// <include path='items/UpdateTableRequest/*' file='Doc/LJCCityTableEvents.xml'/>
  // Called from LJCCityListEvents #DocumentContextMenu()
  // #TableClick(), NextPage(), PrevPage(), Page().
  UpdateTableRequest()
  {
    let cityTable = this.CityTable;

    // Set selected row primaryKeys in hidden form for detail dialog.
    let rowKeys = cityTable.Keys[cityTable.CurrentRowIndex - 1];
    if (rowKeys != null)
    {
      // Set HTML current row keys.
      rowCityID.value = rowKeys.CityID;
      rowProvinceID.value = rowKeys.ProvinceID;
      rowName.value = rowKeys.Name;
    }

    if (this.CityTableRequest != null)
    {
      // Get first row key.
      let rowKeys = cityTable.Keys[0];
      if (rowKeys != null)
      {
        let tableRequest = this.CityTableRequest;
        tableRequest.BeginKeyData.ProvinceID = rowKeys.ProvinceID;
        tableRequest.BeginKeyData.Name = rowKeys.Name;
      }

      // Get last row key.
      let lastIndex = cityTable.Keys.length - 1;
      rowKeys = cityTable.Keys[lastIndex];
      if (rowKeys != null)
      {
        let tableRequest = this.CityTableRequest;
        tableRequest.EndKeyData.ProvinceID = rowKeys.ProvinceID;
        tableRequest.EndKeyData.Name = rowKeys.Name;
      }
    }
  }

  // Creates the table property names.
  #DefaultPropertyNames()
  {
    let retPropertyNames = [
      City.PropertyCityID,
      City.PropertyProvinceID,
      City.PropertyProvinceName,
      City.PropertyName,
      City.PropertyDescription,
      City.PropertyCityFlag,
      City.PropertyZipCode,
      City.PropertyDistrict,
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
        this.CityTable.CurrentRowIndex = this.CityTableRequest.Limit - 1;
        this.#IsNextPage = false;
      }
      if (this.#IsPrevPage)
      {
        // Keep at first row.
        this.CityTable.CurrentRowIndex = 1;
        this.#IsPrevPage = false;
      }
    }
    return retValue;
  }

  // Creates the table column names.
  #TableColumnNames()
  {
    let retColumnNames = [
      //City.PropertyProvinceName,
      City.PropertyName,
      City.PropertyDescription,
      City.PropertyCityFlag,
      City.PropertyZipCode,
      City.PropertyDistrict,
    ];
    return retColumnNames;
  }

  // Updates the BeginningOfData and EndOfData flags.
  // Called from Page().
  #UpdateLimitFlags()
  {
    let retValue = false;

    let cityTable = this.CityTable;
    if (this.#IsNextPage)
    {
      retValue = true;
      cityTable.BeginningOfData = false;
      cityTable.EndOfData = false;
      if (cityTable.Keys.length < this.CityTableRequest.Limit)
      {
        cityTable.EndOfData = true;
      }
      cityTable.CurrentRowIndex = 1;
      this.#IsNextPage = false;
    }

    if (this.#IsPrevPage)
    {
      retValue = true;
      cityTable.BeginningOfData = false;
      cityTable.EndOfData = false;
      if (cityTable.Keys.length < this.CityTableRequest.Limit)
      {
        cityTable.BeginningOfData = true;
      }
      cityTable.CurrentRowIndex = this.CityTableRequest.Limit;
      this.#IsPrevPage = false;
    }
    return retValue;
  }

  // Updates the CityTable ETable and Keys values.
  // Called from Page().
  #UpdateCityTable(self, keys)
  {
    let retRowIndex = -1;

    let cityTable = self.CityTable;

    // Return existing row index.
    retRowIndex = cityTable.CurrentRowIndex;

    // Reset table to new table element.
    cityTable.ETable = LJC.Element(this.#TableID);

    cityTable.Keys = keys;
    return retRowIndex;
  }
  // #endregion
}