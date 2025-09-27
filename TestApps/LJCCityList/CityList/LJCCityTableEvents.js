"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityTableEvents.js
// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   AddEvent(), Element(), Visibility()
// <script src="City/LJCCityTableRequest.js"></script>
// <script src="LJCTable.js"></script>
//   MoveNext(), MovePrevious(), SelectRow(), SelectColumnRow()

/// <summary>The City Table Events</summary>
/// LibName: LJCCityTableEvents
//  Classes: LJCCityTableEvents

// ***************
/// <summary>Contains City HTML Table methods.</summary>
//  Constructor: constructor(), #AddEvents()
//  Event Handlers: #DocumentClick, #TableClick()
//  Page Event Handlers: NextPage(), PrevPage()
//  Web Service: Page(), UpdateTableRequest(), #HasData(), #UpdateLimitFlags()
//    #UpdateCityTable()
class LJCCityTableEvents
{
  // ---------------
  // Properties

  // The associated city table helper object.
  // Where is this used as public?
  CityTable;

  // The city table data request.
  // Used in CityListEvents constructor(), #Refresh().
  TableRequest;

  // ---------------
  // Private Properties

  // The debug location class name.
  #ClassName = "";

  // Flags set in NextPage() and PrevPage().
  // Used in #HasData() and #UpdateLimitFlags() which are called in the Page()
  // response.
  #IsNextPage;
  #IsPrevPage;

  // The city list events.
  #ListEvents;

  // The associated menu ID name.
  #MenuID;

  // The associated table ID name.
  #TableID;

  // ---------------
  // Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(listEvents, menuID, configName = ""
    , configFile = "DataConfigs.xml")
  {
    this.#ClassName = "LJCCityTableEvents";
    let methodName = "constructor()";

    this.#ListEvents = listEvents;
    this.#MenuID = menuID;

    this.#IsNextPage = false;
    this.#IsPrevPage = false;

    // Data for LJCCityTableService.php
    this.TableRequest = new LJCCityTableRequest(configName, configFile);

    this.#TableID = listEvents.CityTableID;
    this.CityTable = new LJCTable(this.#TableID, this.#MenuID);
    this.#AddEvents();
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("click", this.#DocumentClick.bind(this));

    // Table Event Handlers.
    LJC.AddEvent(this.#TableID, "click", this.#TableClick, this);
  }

  // ---------------
  // Event Handlers

  // The Document "click" handler.
  #DocumentClick()
  {
    LJC.Visibility(this.#MenuID, "hidden");
  }

  // The Table "click" handler.
  #TableClick(event)
  {
    let methodName = "TableClick()";

    LJC.Visibility(this.#MenuID, "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      if (this.CityTable != null)
      {
        this.CityTable.SelectColumnRow(eCell);
        this.UpdateTableRequest();
        this.#ListEvents.FocusTable = this.CityTable;
      }
    }
  }

  // ---------------
  // Page Methods

  /// <summary>Get next page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  NextPage()
  {
    let methodName = "NextPage()";

    if (!this.CityTable.EndOfData)
    {
      this.#IsNextPage = true;
      this.TableRequest.Action = "Next";
      this.UpdateTableRequest();
      this.Page(this.TableRequest);
    }
  }

  /// <summary>Get previous page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  PrevPage()
  {
    let methodName = "PrevPage()";

    if (!this.CityTable.BeginningOfData)
    {
      this.#IsPrevPage = true;
      this.TableRequest.Action = "Previous";
      this.UpdateTableRequest();
      this.Page(this.TableRequest);
    }
  }

  // ---------------
  // Web Service Methods

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
      LJC.ShowText(self.#ClassName, methodName, "this.responseText"
        , this.responseText);

      // Get the AJAX response.
      let response = JSON.parse(this.responseText);

      LJC.ShowText(self.#ClassName, methodName, "response.DebugText"
        , response.DebugText);
      LJC.ShowText(self.#ClassName, methodName, "response.SQL"
        , response.SQL);

      // Check if there is more data.
      if (self.#HasData(response.HTMLTable))
      {
        // Create new table element and add new "click" event.
        let eTable = LJC.Element(self.#TableID);
        eTable.outerHTML = response.HTMLTable;
        LJC.AddEvent(self.#TableID, "click", self.#TableClick
          , self);

        // Updates CityTable with new table element and keys.
        let rowIndex = self.#UpdateCityTable(self, response.Keys);

        let cityTable = self.CityTable;

        // Updates the BeginningOfData and EndOfData flags.
        if (self.#UpdateLimitFlags())
        {
          // Get row index if "NextPage" or "PrevPage";
          rowIndex = cityTable.CurrentRowIndex;
        }

        cityTable.SelectRow(rowIndex, rowIndex);

        // Set hidden form primary keys and CityTableRequest.
        self.UpdateTableRequest()

        self.#ListEvents.CityTable = cityTable;
        self.#ListEvents.FocusTable = cityTable;
      }
    };

    let tableRequest = this.TableRequest.Clone();
    tableRequest.ConfigFile = "../DataConfigs.xml";
    let request = LJC.CreateJSON(tableRequest);

    let text = "LJCCityTableEvents.Page() request";
    LJC.Message(text, request);

    xhr.send(request);
  }

  // Sets the form values before a detail submit and the page values before
  // a page submit.
  /// <include path='items/UpdateTableRequest/*' file='Doc/LJCCityTableEvents.xml'/>
  // Called from LJCCityListEvents #DocumentContextMenu().
  UpdateTableRequest()
  {
    let methodName = "UpdateTableRequest()";

    let cityTable = this.CityTable;

    // Set selected row primaryKeys in hidden form for detail dialog.
    let rowKeys = cityTable.Keys[cityTable.CurrentRowIndex - 1];
    if (rowKeys != null)
    {
      // Set current row keys.
      rowCityID.value = rowKeys.CityID;
      rowProvinceID.value = rowKeys.ProvinceID;
      rowName.value = rowKeys.Name;
    }

    if (this.TableRequest != null)
    {
      // Get first row key.
      let rowKeys = cityTable.Keys[0];
      if (rowKeys != null)
      {
        let tableRequest = this.TableRequest;
        tableRequest.BeginKeyData.ProvinceID = rowKeys.ProvinceID;
        tableRequest.BeginKeyData.Name = rowKeys.Name;
      }

      // Get last row key.
      let lastIndex = cityTable.Keys.length - 1;
      rowKeys = cityTable.Keys[lastIndex];
      if (rowKeys != null)
      {
        let tableRequest = this.TableRequest;
        tableRequest.EndKeyData.ProvinceID = rowKeys.ProvinceID;
        tableRequest.EndKeyData.Name = rowKeys.Name;
      }
    }
  }

  // Checks if the provided table text exists.
  // Called from Page().
  #HasData(tableText)
  {
    let methodName = "HasData()";
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
        this.CityTable.CurrentRowIndex = 1;
        this.#IsPrevPage = false;
      }
    }
    return retValue;
  }

  // Updates the BeginningOfData and EndOfData flags.
  // Called from Page().
  #UpdateLimitFlags()
  {
    let methodName = "UpdateLimitFlags()";
    let retValue = false;

    let cityTable = this.CityTable;
    if (this.#IsNextPage)
    {
      retValue = true;
      cityTable.BeginningOfData = false;
      cityTable.EndOfData = false;
      if (cityTable.Keys.length < this.TableRequest.Limit)
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
      if (cityTable.Keys.length < this.TableRequest.Limit)
      {
        cityTable.BeginningOfData = true;
      }
      cityTable.CurrentRowIndex = this.TableRequest.Limit;
      this.#IsPrevPage = false;
    }
    return retValue;
  }

  // Updates the CityTable ETable and Keys values.
  // Called from Page().
  #UpdateCityTable(self, keys)
  {
    let methodName = "UpdateCityTable()";
    let retRowIndex = -1;

    let cityTable = self.CityTable;

    // Return existing row index.
    retRowIndex = cityTable.CurrentRowIndex;

    // Reset table to new table element.
    cityTable.ETable = LJC.Element(this.#TableID);

    cityTable.Keys = keys;
    return retRowIndex;
  }
}