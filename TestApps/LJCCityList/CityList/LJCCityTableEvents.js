"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityTableEvents.js
// <script src="../../LJCJSCommon/LJCJSCommonLib.js"></script>
//   AddEvent(), Element(), Visibility()
// <script src="City/LJCCityTableRequest.js"></script>
// <script src="LJCTable.js"></script>
//   MoveNext(), MovePrevious(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains City HTML Table methods.</summary>
//  Constructor: constructor(), AddEvents()
//  Event Handlers: TableClick(), TableKeyDown()
//  Page Event Handlers: NextPage(), PrevPage(), Page()
//    UpdateLimitFlags(), UpdateTableRequest(), UpdateCityTable()
class LJCCityTableEvents
{
  // ---------------
  // Properties

  // The associated LJCTable object.
  CityTable;

  ClassName = "";

  /// <summary>The data access configuration file name.</summary>
  ConfigFile = "";

  /// <summary>The data access configuration name.</summary>
  ConfigName = "";

  // Flags set in NextPage() and PrevPage().
  // Used in HasData() and UpdateLimitFlags() which are called in the Page()
  // response.
  // Called from CityListEvents.DocumentKeyDown().
  IsNextPage;
  IsPrevPage;

  // The LJCCityListEvents JS object.
  ListEvents;

  // The associated menu ID name.
  MenuID;

  // The associated table ID name.
  TableID;

  // The LJCCityTableRequest JS object.
  TableRequest;

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(listEvents, menuID, configName = ""
    , configFile = "DataConfigs.xml")
  {
    this.ClassName = "LJCCityTableEvents";
    let methodName = "constructor()";

    this.ListEvents = listEvents;
    this.MenuID = menuID;

    this.IsNextPage = false;
    this.IsPrevPage = false;

    // Data for LJCCityTableService.php
    this.TableRequest = new LJCCityTableRequest(configName, configFile);

    this.TableID = listEvents.CityTableID;
    this.CityTable = new LJCTable(this.TableID, this.MenuID);
    this.AddEvents();
  }

  /// <summary>Adds the HTML event listeners.</summary>
  AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("click", this.DocumentClick.bind(this));

    // Table Event Handlers.
    LJC.AddEvent(this.TableID, "click", this.TableClick, this);
  }

  // Standard debug method for each class.
  Debug(methodName, valueName, value, force = false)
  {
    let text = LJC.Location(this.ClassName, methodName, valueName);
    // Does not show alert if no value unless force = true.
    LJC.Message(text, value, force);
  }

  // ---------------
  // Event Handlers

  /// <summary>The Document "click" handler.</summary>
  DocumentClick()
  {
    LJC.Visibility(this.MenuID, "hidden");
  }

  /// <summary>The Table "click" handler.</summary>
  /// <param name="event">The Target event.</param>
  TableClick(event)
  {
    let methodName = "TableClick()";

    LJC.Visibility(this.MenuID, "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      if (this.CityTable != null)
      {
        this.CityTable.SelectColumnRow(eCell);
        this.UpdateTableRequest();
        this.ListEvents.FocusTable = this.CityTable;
      }
    }
  }

  // ---------------
  // Page Methods

  // Checks if the provided table text exists.</summary>
  /// <include path='items/HasData/*' file='Doc/LJCCityTableEvents.xml'/>
  // Called from Page().
  HasData(tableText)
  {
    let methodName = "HasData()";
    let retValue = true;

    // There is no data.
    if (!LJC.HasText(tableText))
    {
      retValue = false;
      if (this.IsNextPage)
      {
        // Keep at last row.
        this.CityTable.CurrentRowIndex = this.TableRequest.Limit - 1;
        this.IsNextPage = false;
      }
      if (this.IsPrevPage)
      {
        // Keep at first row.
        this.CityTable.CurrentRowIndex = 1;
        this.IsPrevPage = false;
      }
    }
    return retValue;
  }

  /// <summary>Get next page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  NextPage()
  {
    let methodName = "NextPage()";

    if (!this.CityTable.EndOfData)
    {
      this.IsNextPage = true;
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
      this.IsPrevPage = true;
      this.TableRequest.Action = "Previous";
      this.UpdateTableRequest();
      this.Page(this.TableRequest);
    }
  }

  /// <summary>Send request for a City Table page.</summary>
  // Called from NextPage(), PrevPage() and Refesh().
  Page()
  {
    let methodName = "Page()";

    // Save a reference to this class for anonymous function.
    const saveThis = this;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "CityList/LJCCityTableService.php");
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function ()
    {
      saveThis.Debug(methodName, "responseText", this.responseText);

      // Get the AJAX response.
      let response = JSON.parse(this.responseText);

      saveThis.Debug(methodName, "response.DebugText", response.DebugText);
      saveThis.Debug(methodName, "response.SQL", response.SQL);

      // Check if there is more data.
      if (saveThis.HasData(response.HTMLTable))
      {
        // Create new table element and add new "click" event.
        let eTable = LJC.Element(saveThis.TableID);
        eTable.outerHTML = response.HTMLTable;
        LJC.AddEvent(saveThis.TableID, "click", saveThis.TableClick
          , saveThis);

        // Updates CityTable with new table element and keys.
        let rowIndex = saveThis.UpdateCityTable(saveThis, response.Keys);

        let cityTable = saveThis.CityTable;
        if (saveThis.UpdateLimitFlags())
        {
          // Get row index if "NextPage" or "PrevPage";
          rowIndex = cityTable.CurrentRowIndex;
        }
        cityTable.SelectRow(rowIndex, rowIndex);

        // Set hidden form primary keys and CityTableRequest.
        saveThis.UpdateTableRequest()

        saveThis.ListEvents.CityTable = cityTable;
        saveThis.ListEvents.FocusTable = cityTable;
      }
    };

    let tableRequest = this.TableRequest.Clone();
    tableRequest.ConfigFile = "../DataConfigs.xml";
    let request = LJC.CreateJSON(tableRequest);

    let text = "LJCCityTableEvents.Page() request";
    LJC.Message(text, request);

    xhr.send(request);
  }

  /// <summary>Show the text dialog.</summary>
  /// <param name="text">The text value.</param>
  ShowText(textValue, useAlert = true)
  {
    let methodName = "ShowText()";

    if (LJC.HasValue(textValue))
    {
      if (useAlert)
      {
        alert(textValue);
      }
      else
      {
        text.value = textValue;
        textDialog.showModal();
      }
    }
  }

  // Updates the BeginningOfData and EndOfData flags.</summary>
  /// <include path='items/UpdateLimitFlags/*' file='Doc/LJCCityTableEvents.xml'/>
  UpdateLimitFlags()
  {
    let methodName = "UpdateLimitFlags()";
    let retValue = false;

    let cityTable = this.CityTable;
    if (this.IsNextPage)
    {
      retValue = true;
      cityTable.BeginningOfData = false;
      cityTable.EndOfData = false;
      if (cityTable.Keys.length < this.TableRequest.Limit)
      {
        cityTable.EndOfData = true;
      }
      cityTable.CurrentRowIndex = 1;
      this.IsNextPage = false;
    }

    if (this.IsPrevPage)
    {
      retValue = true;
      cityTable.BeginningOfData = false;
      cityTable.EndOfData = false;
      if (cityTable.Keys.length < this.TableRequest.Limit)
      {
        cityTable.BeginningOfData = true;
      }
      cityTable.CurrentRowIndex = this.TableRequest.Limit;
      this.IsPrevPage = false;
    }
    return retValue;
  }

  /// <summary></summary>
  /// <include path='items/UpdateTableRequest/*' file='Doc/LJCCityTableEvents.xml'/>
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

  // Updates the CityTable ETable and Keys values.</summary>
  /// <include path='items/UpdateCityTable/*' file='Doc/LJCCityTableEvents.xml'/>
  UpdateCityTable(saveThis, keys)
  {
    let methodName = "UpdateCityTable()";
    let retRowIndex = -1;

    let cityTable = saveThis.CityTable;

    // Return existing row index.
    retRowIndex = cityTable.CurrentRowIndex;

    // Reset table to new table element.
    cityTable.ETable = LJC.Element(this.TableID);

    cityTable.Keys = keys;
    return retRowIndex;
  }
}