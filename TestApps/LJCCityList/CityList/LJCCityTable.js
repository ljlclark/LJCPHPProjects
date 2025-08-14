"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityTable.js
// <script src="../../Common/Common.js"></script>
//   Element(), Visibility()
// <script src="LJCTableData.js"></script>
//   MoveNext(), MovePrevious(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains City HTML Table methods.</summary>
//  Constructor: constructor(), AddEvent()
//  Event Handlers: TableClick(), TableKeyDown()
//  Page Event Handlers: NextPage(), PrevPage(), Page()
//    UpdateLimitFlags(), UpdatePageData(), UpdateTableData()
class LJCCityTable
{
  // ---------------
  // Properties

  FocusTableData;
  IsNextPage;
  IsPrevPage;
  MenuID;
  PageData;
  TableID;
  TableData;

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(tableID, menuID)
  {
    this.TableID = tableID;
    this.MenuID = menuID;

    this.IsNextPage = false;
    this.IsPrevPage = false;
    this.TableData = new LJCTableData(this.TableID, this.MenuID);

    // Data for LJCCityList.php
    this.PageData = {
      Action: "None", // Next, Previous, Top, Bottom, First?, Last?
      BeginKeyData: { ProvinceID: 0, Name: "" },
      ConfigName: "TestData",
      EndKeyData: { ProvinceID: 0, Name: "" },
      Limit: 10,
    };

    this.FocusTableData = null;
    this.AddEvents();
  }

  AddEvents()
  {
    this.AddEvent(this.TableID, "click", this.TableClick);
    //this.AddEvent(this.TableID, "keydown", this.TableKeyDown);
  }

  // Adds an event handler.
  /// <include path='items/AddEvent/*' file='Doc/LJCCityListEvents.xml'/>
  AddEvent(elementID, eventName, handler)
  {
    let element = Common.Element(elementID);
    if (element != null)
    {
      element.addEventListener(eventName, handler.bind(this));
    }
  }

  // ---------------
  // Event Handlers

  // Table "click" handler method.
  TableClick(event)
  {
    Common.Visibility("menu", "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      if (this.TableData != null)
      {
        this.TableData.SelectColumnRow(eCell);
        this.UpdatePageData();
        // *** Add ***
        this.FocusTableData = this.TableData;
      }
    }
  }

  /// <summary>Table "keydown" handler method.</summary>
  /// <param name="event">The Target event.</param>
  TableKeyDown(event)
  {
    let ESCAPE_KEY = 27;
    let UP_ARROW = 38;
    let DOWN_ARROW = 40;

    switch (event.keyCode)
    {
      case DOWN_ARROW:
        if (this.TableData != null)
        {
          // False if at end of page.
          if (this.TableData.MoveNext())
          {
            this.NextPage();
          }
        }
        break;

      case ESCAPE_KEY:
        Common.Visibility("menu", "hidden");
        break;

      case UP_ARROW:
        if (this.TableData != null)
        {
          // False if at beginning of page.
          if (this.TableData.MovePrevious())
          {
            this.PrevPage();
          }
        }
        break;
    }
  }

  // ---------------
  // Page Event Handlers

  // If no data returned: Clears the Next and Previous settings and keeps the
  // CurrentRowIndex from moving.
  HasData(htmlTable)
  {
    let retValue = true;

    // There is no data.
    if (!Common.HasText(htmlTable))
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

  /// <summary>Get next page for supplied table.
  /// <param name="tableHelper">The target table helper.</param>
  // Called from TableKeyDown().
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

  /// <summary>Get previous page for supplied table.
  // Called from DocumentKeyDown().
  PrevPage()
  {
    if (!this.LJCHelper.BeginningOfData)
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
    xhr.open("POST", "LJCCityList.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onload = function ()
    {
      // Get the AJAX response.
      let response = JSON.parse(this.responseText);
      //alert(`responseSQL: ${response.SQL}`);

      // Check if there is more data.
      if (saveThis.HasData(response.HTMLTable))
      {
        // Create new table element.
        let eTable = Common.Element(saveThis.TableID);
        eTable.outerHTML = response.HTMLTable;
        saveThis.AddEvent(saveThis.TableID, "click", saveThis.TableClick);

        // Updates TableData with new table element and keys.
        let rowIndex = saveThis.UpdateTableData(saveThis, response.Keys);

        let TableData = saveThis.TableData;
        if (saveThis.UpdateLimitFlags())
        {
          // Get row index if "NextPage" or "PrevPage";
          rowIndex = TableData.CurrentRowIndex;
        }
        TableData.SelectRow(rowIndex, rowIndex);

        // Set hidden form primary keys and CityPageData.
        saveThis.UpdatePageData()
        //saveThis.FocusTableData = TableData;
      }
    };
    xhr.send(JSON.stringify(this.PageData));
  }

  // Updates the BeginningOfData and EndOfData flags.
  /// <include path='items/UpdateLimitsFlags/*' file='Doc/LJCCityListEvents.xml'/>
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
  ///   Sets the form values before a detail submit and the page values before
  ///   a page submit.
  /// </summary>
  /// <param name="eTarget">The HTML element.</param>
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
  /// <include path='items/UpdateCityTable/*' file='Doc/LJCCityListEvents.xml'/>
  UpdateTableData(saveThis, keys)
  {
    let retRowIndex = -1;

    let TableData = saveThis.TableData;

    // Return existing row index.
    retRowIndex = TableData.CurrentRowIndex;

    // Reset table to new table element.
    TableData.ETable = Common.Element(this.TableID);

    TableData.Keys = keys;
    return retRowIndex;
  }
}