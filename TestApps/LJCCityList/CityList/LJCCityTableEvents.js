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
// <script src="CityList/LJCCityDAL.js"></script>
//   City
// <script src="LJCTable.js"></script>
//   LJCTable: SelectRow(), SelectColumnRow()
// <script src="LJCTableMessageLib.js"></script>
//   LJCTableResponse:
//   LJCTableRequest: Request()
// #endregion

/// <summary>The City Table Events</summary>
/// LibName: LJCCityTableEvents
//  Classes: LJCCityTableEvents

// ***************
/// <summary>Contains City HTML Table event handlers.</summary>
class LJCCityTableEvents
{
  // #region Properties
  // ---------------

  /// <summary>The associated table helper object.</summary>
  // Used in Page() and #TableClick.
  Table = null; // LJCTable;

  TableName = "";

  /// <summary> The city table data request.</summary>
  // Used in CityListEvents constructor(), #Refresh().
  TableRequest = null; // LJCTableRequest;
  // #endregion

  // #region Private Properties
  // ---------------

  #ConfigName = "";

  #ConfigFile = "";

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
  #HTMLMenuID = "";

  // The associated table ID name.
  #HTMLTableID = "";
  // #endregion

  // #region Constructor Methods.
  // ---------------

  /// <summary>Initializes the object instance.</summary>
  constructor(cityListEvents, htmlMenuID, configName = ""
    , configFile = "DataConfigs.xml")
  {
    this.#Debug = new Debug("LJCCityTableEvents");

    // Set properties from parameters.
    this.#CityListEvents = cityListEvents;
    this.#HTMLMenuID = htmlMenuID;
    this.#ConfigName = configName;
    this.#ConfigFile = configFile;

    this.#IsNextPage = false;
    this.#IsPrevPage = false;
    this.#HTMLTableID = this.#CityListEvents.CityTableID;

    this.Table = new LJCTable(this.#HTMLTableID, this.#HTMLMenuID);

    // Service request for LJCCityTableService.php
    this.TableRequest = new LJCTableRequest("LJCCityTableService", configName
      , configFile);
    let tableRequest = this.TableRequest;
    tableRequest.HTMLTableID = this.#HTMLTableID;
    tableRequest.TableName = City.TableName;

    // Set retrieve property names.
    // null includes all columns.
    tableRequest.PropertyNames = null;

    // Get table columns.
    // Can include join column names.
    tableRequest.TableColumnNames = this.#TableColumnNames();

    // Insert join table columns.
    //let addColumns = new LJCDataColumns()
    //let addColumn = addColumns.Add(City.PropertyProvinceName);
    //addColumn.InsertIndex = 0; // Default
    //tableRequest.AddTableColumns = LJC.ToArray(addColumns);

    this.#AddEvents();
  }

  /// <summary>Sets the dialog values.</summary>
  SetDialogValues(textDialogID, textAreaID)
  {
    this.#Debug.SetDialogValues(textDialogID, textAreaID);
  }

  // Call this after constructor to test generic.
  /// <summary>Generic values.</summary>
  SetTableValues(htmlTableID, tableName, tableColumnNames, propertyNames = null
    , joinPropertyNames = null)
  {
    this.#HTMLTableID = htmlTableID;
    this.TableName = tableName;

    // Service request for LJCTableService.php
    this.TableRequest = new LJCTableRequest("LJCTableService", this.#ConfigName
      , this.#ConfigFile);
    let tableRequest = this.TableRequest;
    tableRequest.HTMLTableID = this.#HTMLTableID;
    tableRequest.TableName = this.TableName;

    // Set retrieve property names.
    // null includes all columns.
    tableRequest.PropertyNames = propertyNames;

    // Get table columns.
    // Can include join column names.
    tableRequest.TableColumnNames = tableColumnNames;

    // Insert join table columns.
    if (joinPropertyNames != null)
    {
      let addColumns = new LJCDataColumns()
      for (const propertyName of joinPropertyNames)
      {
        let addColumn = addColumns.Add(propertyName);
        //addColumn.InsertIndex = 0; // Default
      }
      tableRequest.AddTableColumns = LJC.ToArray(addColumns);
    }
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("click", this.#DocumentClick.bind(this));

    // Table Event Handlers.
    LJC.AddEvent(this.#HTMLTableID, "click", this.#TableClick, this);
  }
  // #endregion

  // #region Event Handlers
  // ---------------

  // The Document "click" handler.
  #DocumentClick()
  {
    LJC.Visibility(this.#HTMLMenuID, "hidden");
  }

  // The Table "click" handler.
  #TableClick(event)
  {
    LJC.Visibility(this.#HTMLMenuID, "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      if (this.Table != null)
      {
        this.Table.SelectColumnRow(eCell);
        this.UpdateTableRequest();
        this.#CityListEvents.FocusTable = this.Table;
      }
    }
  }
  // #endregion

  // #region Page Methods
  // ---------------

  /// <summary>Gets the next page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  NextPage()
  {
    if (!this.Table.EndOfData)
    {
      this.#IsNextPage = true;
      this.TableRequest.Action = "Next";
      this.UpdateTableRequest();
      this.Page(this.TableRequest);
    }
  }

  /// <summary>Gets the previous page for City table.</summary>
  // Called from LJCCityListEvents.DocumentKeyDown().
  PrevPage()
  {
    if (!this.Table.BeginningOfData)
    {
      this.#IsPrevPage = true;
      this.TableRequest.Action = "Previous";
      this.UpdateTableRequest();
      this.Page(this.TableRequest);
    }
  }
  // #endregion

  // #region Web Service Methods
  // ---------------

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
        if (!LJCTableResponse.IsValidResponse(this.responseText))
        {
          // ToDo: Remove response check?
          self.#Debug.ShowText(methodName, "this.responseText"
            , this.responseText, false);
        }

        let response = new LJCTableResponse(this.responseText);

        self.#Debug.ShowText(methodName, "response.DebugText"
          , response.DebugText, false);
        self.#Debug.ShowText(methodName, "response.SQL"
          , response.SQL, false);

        // Handle new table HTML and associated values.
        if (self.#HasData(response.HTMLTable))
        {
          // ***** 
          //self.#Debug.ShowText(methodName, "response.HTMLTable", response.HTMLTable);
          // Create new table element and add new "click" event.
          let eTable = LJC.Element(self.#HTMLTableID);
          eTable.outerHTML = response.HTMLTable;
          LJC.AddEvent(self.#HTMLTableID, "click", self.#TableClick
            , self);

          let table = self.Table;
          table.Keys = response.Keys;
          table.TableColumns = response.TableColumns;

          // Updates the BeginningOfData and EndOfData flags.
          let rowIndex = table.CurrentRowIndex;
          if (self.#UpdateLimitFlags())
          {
            // Get row index if "NextPage" or "PrevPage";
            rowIndex = table.CurrentRowIndex;
          }

          table.SelectRow(rowIndex, rowIndex);

          // Set hidden form primary keys and TableRequest.
          self.UpdateTableRequest()

          // Can only assign public data.
          self.#CityListEvents.CityTable = table;
          self.#CityListEvents.FocusTable = table;
        }
      }
    };

    let tableRequest = this.TableRequest.Clone();
    tableRequest.ConfigFile = "../DataConfigs.xml";
    let request = tableRequest.Request();
    xhr.send(request);
  }

  TablePage()
  {
    let methodName = "TablePage()";

    // Save a reference to this class for anonymous function.
    const self = this;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "CityList/LJCTableService.php");
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function ()
    {
      // Get the AJAX response.
      if (LJC.HasText(this.responseText))
      {
        if (!LJCTableResponse.IsValidResponse(this.responseText))
        {
          // ToDo: Remove response check?
          self.#Debug.ShowText(methodName, "this.responseText"
            , this.responseText, false);
        }

        let response = new LJCTableResponse(this.responseText);

        self.#Debug.ShowText(methodName, "response.DebugText"
          , response.DebugText, false);
        self.#Debug.ShowText(methodName, "response.SQL"
          , response.SQL, false);

        // Handle new table HTML and associated values.
        if (self.#HasData(response.HTMLTable))
        {
          // ***** 
          //self.#Debug.ShowText(methodName, "response.HTMLTable", response.HTMLTable);
          // Create new table element and add new "click" event.
          let eTable = LJC.Element(self.#HTMLTableID);
          eTable.outerHTML = response.HTMLTable;
          LJC.AddEvent(self.#HTMLTableID, "click", self.#TableClick
            , self);

          // Update table.
          let table = self.Table;
          table.Keys = response.Keys;
          table.TableColumns = response.TableColumns;

          // Updates the BeginningOfData and EndOfData flags.
          rowIndex = table.RowIndex;
          if (self.#UpdateLimitFlags())
          {
            // Get row index if "NextPage" or "PrevPage";
            rowIndex = table.CurrentRowIndex;
          }

          table.SelectRow(rowIndex, rowIndex);

          // Set hidden form primary keys and TableRequest.
          self.UpdateTableRequest()

          // Can only assign public data.
          self.#CityListEvents.CityTable = table;
          self.#CityListEvents.FocusTable = table;
        }
      }
    };

    let tableRequest = this.TableRequest.Clone();
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
    let table = this.Table;

    // Set selected row primaryKeys in hidden form for detail dialog.
    let rowKeys = table.Keys[table.CurrentRowIndex - 1];
    if (rowKeys != null)
    {
      // Set HTML current row keys.
      rowCityID.value = rowKeys.CityID;
      rowProvinceID.value = rowKeys.ProvinceID;
      rowName.value = rowKeys.Name;
    }

    if (this.TableRequest != null)
    {
      // Get first row key.
      let rowKeys = table.Keys[0];
      if (rowKeys != null)
      {
        let tableRequest = this.TableRequest;
        tableRequest.BeginKeyData.ProvinceID = rowKeys.ProvinceID;
        tableRequest.BeginKeyData.Name = rowKeys.Name;
      }

      // Get last row key.
      let lastIndex = table.Keys.length - 1;
      rowKeys = table.Keys[lastIndex];
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
    let retValue = true;

    // There is no data.
    if (!LJC.HasText(tableText))
    {
      retValue = false;
      if (this.#IsNextPage)
      {
        // Keep at last row.
        this.Table.CurrentRowIndex = this.TableRequest.Limit - 1;
        this.#IsNextPage = false;
      }
      if (this.#IsPrevPage)
      {
        // Keep at first row.
        this.Table.CurrentRowIndex = 1;
        this.#IsPrevPage = false;
      }
    }
    return retValue;
  }

  // Creates the table column names.
  #TableColumnNames()
  {
    let retColumnNames = [
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

    let table = this.Table;
    if (this.#IsNextPage)
    {
      retValue = true;
      table.BeginningOfData = false;
      table.EndOfData = false;
      if (table.Keys.length < this.TableRequest.Limit)
      {
        table.EndOfData = true;
      }
      table.CurrentRowIndex = 1;
      this.#IsNextPage = false;
    }

    if (this.#IsPrevPage)
    {
      retValue = true;
      table.BeginningOfData = false;
      table.EndOfData = false;
      if (table.Keys.length < this.TableRequest.Limit)
      {
        table.BeginningOfData = true;
      }
      table.CurrentRowIndex = this.TableRequest.Limit;
      this.#IsPrevPage = false;
    }
    return retValue;
  }
  // #endregion
}
