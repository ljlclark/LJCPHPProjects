"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityTableEvents.js
// <script src="../../Common/Common.js"></script>
//   Element(), Visibility()
// <script src="City/LJCCityPageData.js"></script>
// <script src="LJCTableData.js"></script>
//   MoveNext(), MovePrevious(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains City HTML Table methods.</summary>
//  Constructor: constructor(), AddEvent()
//  Event Handlers: TableClick(), TableKeyDown()
//  Page Event Handlers: NextPage(), PrevPage(), Page()
//    UpdateLimitFlags(), UpdatePageData(), UpdateTableData()
class LJCCityTableEvents
{
  // ---------------
  // Properties

  IsNextPage;
  IsPrevPage;
  ListEvents;
  MenuID;
  PageData;
  TableID;
  TableData;

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(listEvents, menuID)
  {
    this.ListEvents = listEvents;
    this.MenuID = menuID;

    this.IsNextPage = false;
    this.IsPrevPage = false;

    // Data for LJCCityTable.php
    this.PageData = new LJCCityPageData();

    this.TableID = listEvents.CityTableID;
    this.TableData = new LJCTableData(this.TableID, this.MenuID);
    this.AddEvents();
  }

  /// <summary>Adds the HTML event listeners.</summary>
  AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("click", this.DocumentClick.bind(this));

    // Table Event Handlers.
    Common.AddEvent(this.TableID, "click", this.TableClick, this);
  }

  // ---------------
  // Event Handlers

  /// <summary>The Document "click" handler method.</summary>
  DocumentClick()
  {
    Common.Visibility(this.MenuID, "hidden");
  }

  /// <summary>The Table "click" handler method.</summary>
  /// <param name="event">The Target event.</param>
  TableClick(event)
  {
    Common.Visibility(this.MenuID, "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      if (this.TableData != null)
      {
        this.TableData.SelectColumnRow(eCell);
        this.UpdatePageData();
        this.ListEvents.FocusTableData = this.TableData;
      }
    }
  }

  // ---------------
  // Page Methods

  // Checks if the provided table text exists.
  /// <include path='items/HasData/*' file='Doc/LJCCityTableEvents.xml'/>
  // Called from Page().
  HasData(tableText)
  {
    let retValue = true;

    // There is no data.
    if (!Common.HasText(tableText))
    {
      retValue = false;
      if (this.IsNextPage)
      {
        // Keep at last row.
        this.TableData.CurrentRowIndex = this.PageData.Limit - 1;
        this.IsNextPage = false;
      }
      if (this.IsPrevPage)
      {
        // Keep at first row.
        this.TableData.CurrentRowIndex = 1;
        this.IsPrevPage = false;
      }
    }
    return retValue;
  }

  /// <summary>Get next page for City table.
  // Called from LJCCityListEvents.DocumentKeyDown().
  NextPage()
  {
    if (!this.TableData.EndOfData)
    {
      this.IsNextPage = true;
      this.PageData.Action = "Next";
      this.UpdatePageData();
      this.Page(this.PageData);
    }
  }

  /// <summary>Get previous page for City table.
  // Called from LJCCityListEvents.DocumentKeyDown().
  PrevPage()
  {
    if (!this.TableData.BeginningOfData)
    {
      this.IsPrevPage = true;
      this.PageData.Action = "Previous";
      this.UpdatePageData();
      this.Page(this.PageData);
    }
  }

  /// <summary>Send request for a City Table page.</summary>
  // Called from NextPage(), PrevPage() and Refesh().
  Page()
  {
    // Save a reference to this class for anonymous function.
    const saveThis = this;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "CityList/LJCCityTable.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onload = function ()
    {
      // *** Get Program Errors ***
      //alert(`responseText: ${this.responseText}`);

      // Get the AJAX response.
      let response = JSON.parse(this.responseText);
      // *** Get Response Errors ***
      //alert(`responseSQL: ${response.SQL}`);

      // Check if there is more data.
      if (saveThis.HasData(response.HTMLTable))
      {
        // Create new table element and add new "click" event.
        let eTable = Common.Element(saveThis.TableID);
        eTable.outerHTML = response.HTMLTable;
        Common.AddEvent(saveThis.TableID, "click", saveThis.TableClick
          , saveThis);

        // Updates TableData with new table element and keys.
        let rowIndex = saveThis.UpdateTableData(saveThis, response.Keys);

        let tableData = saveThis.TableData;
        if (saveThis.UpdateLimitFlags())
        {
          // Get row index if "NextPage" or "PrevPage";
          rowIndex = tableData.CurrentRowIndex;
        }
        tableData.SelectRow(rowIndex, rowIndex);

        // Set hidden form primary keys and CityPageData.
        saveThis.UpdatePageData()

        // *** Next Statement *** Add
        saveThis.ListEvents.CityTableData = tableData;
        saveThis.ListEvents.FocusTableData = tableData;
      }
    };
    let pageData = this.PageData.Clone();
    pageData.ConfigFile = "../DataConfigs.xml";
    xhr.send(JSON.stringify(pageData));
  }

  // Updates the BeginningOfData and EndOfData flags.
  /// <include path='items/UpdateLimitsFlags/*' file='Doc/LJCCityTableEvents.xml'/>
  UpdateLimitFlags()
  {
    let retValue = false;

    let TableData = this.TableData;
    if (this.IsNextPage)
    {
      retValue = true;
      TableData.BeginningOfData = false;
      TableData.EndOfData = false;
      if (TableData.Keys.length < this.PageData.Limit)
      {
        TableData.EndOfData = true;
      }
      TableData.CurrentRowIndex = 1;
      this.IsNextPage = false;
    }

    if (this.IsPrevPage)
    {
      retValue = true;
      TableData.BeginningOfData = false;
      TableData.EndOfData = false;
      if (TableData.Keys.length < this.PageData.Limit)
      {
        TableData.BeginningOfData = true;
      }
      TableData.CurrentRowIndex = this.PageData.Limit;
      this.IsPrevPage = false;
    }
    return retValue;
  }

  /// <summary>
  /// <include path='items/UpdatePageData/*' file='Doc/LJCCityTableEvents.xml'/>
  UpdatePageData()
  {
    let TableData = this.TableData;

    // Set selected row primaryKeys in hidden form for detail dialog.
    let ePrimaryKeys = Common.Element("primaryKeys");
    if (ePrimaryKeys != null)
    {
      let rowKeys = TableData.Keys[TableData.CurrentRowIndex - 1];
      if (rowKeys != null)
      {
        let keys = [];
        keys.push({ Name: "CityID", Value: rowKeys.CityID });
        let keysJSON = JSON.stringify(keys);
        ePrimaryKeys.value = keysJSON;
      }
    }

    if (this.PageData != null)
    {
      // Get first row key.
      let rowKeys = TableData.Keys[0];
      if (rowKeys != null)
      {
        let pageData = this.PageData;
        pageData.BeginKeyData.ProvinceID = rowKeys.ProvinceID;
        pageData.BeginKeyData.Name = rowKeys.Name;
      }

      // Get last row key.
      let lastIndex = TableData.Keys.length - 1;
      rowKeys = TableData.Keys[lastIndex];
      if (rowKeys != null)
      {
        let pageData = this.PageData;
        pageData.EndKeyData.ProvinceID = rowKeys.ProvinceID;
        pageData.EndKeyData.Name = rowKeys.Name;
      }
    }
  }

  // Updates the CityTable ETable and Keys values.
  /// <include path='items/UpdateCityTable/*' file='Doc/LJCCityTableEvents.xml'/>
  UpdateTableData(saveThis, keys)
  {
    let retRowIndex = -1;

    let tableData = saveThis.TableData;

    // Return existing row index.
    retRowIndex = tableData.CurrentRowIndex;

    // Reset table to new table element.
    tableData.ETable = Common.Element(this.TableID);

    tableData.Keys = keys;
    return retRowIndex;
  }
}