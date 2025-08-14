"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityListEvents.js
// <script src="../../Common/Common.js"></script>
//   Element(), GetValue()
//   MouseLocation(), Visibility()
// <script src="LJCTableData.js"></script>
//   GetTable(), GetTableRow(), ShowMenu()
//   MoveNext(), MovePrevious(), RowCount(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains CityList event handlers.</summary>
//  Add Events: AddEvents(), AddEvent()
//  Document Handlers: DocumentClick(), DocumentContextMenu()
//    DocumentDoubleClick(), DocumentKeyDown()
//  Page Handlers: NextPage(), PrevPage(), CityPage(), UpdateCityTable()
//  Menu Handlers: Delete(), DoAction(), Edit(), New(), Refresh()
//  Table Column: SelectedTableData()
//  Page Data: UpdatePageData(), UpdateCityPageData()
class LJCCityListEvents
{
  // ---------------
  // Properties

  CityPageData;
  CityTableData;
  CityTableID;
  FocusTableData;
  IsNextPage;
  IsPrevPage;
  LJCCityTable;
  UseNew;

  // ---------------
  // The Constructor functions.

  /// <summary>Initializes the object instance.</summary>
  constructor()
  {
    //this.CityDivID = "cityTableDiv";
    this.CityTableID = "cityTableItem";

    // Data for LJCCityList.php
    this.CityPageData = {
      Action: "None", // Next, Previous, Top, Bottom, First?, Last?
      BeginKeyData: { ProvinceID: 0, Name: "" },
      ConfigName: "TestData",
      EndKeyData: { ProvinceID: 0, Name: "" },
      Limit: 10,
    };

    this.UseNew = true;

    if (this.UseNew)
    {
      this.LJCCityTable = new LJCCityTable(this.CityTableID, "menu");
    }
    this.CityTableData = new LJCTableData(this.CityTableID, "menu");
    this.FocusTableData = null;
    this.IsNextPage = false;
    this.IsPrevPage = false;
  }

  // ---------------
  // Add Event Methods

  /// <summary>Adds the HTML event handlers.</summary>
  AddEvents()
  {
    // Document Event Handlers.
    if (!this.UseNew)
    {
      document.addEventListener("click", this.DocumentClick.bind(this));
    }
    document.addEventListener("dblclick", this.DocumentDoubleClick.bind(this));
    document.addEventListener("contextmenu", this.DocumentContextMenu.bind(this));
    document.addEventListener("keydown", this.DocumentKeyDown.bind(this));

    // Menu Event Handlers.
    this.AddEvent("delete", "click", this.Delete);
    this.AddEvent("edit", "click", this.Edit);
    this.AddEvent("new", "click", this.New);
    this.AddEvent("refresh", "click", this.Refresh);
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
  // Document Event Handlers

  // Document "click" handler method.
  /// <include path='items/DocumentClick/*' file='Doc/LJCCityListEvents.xml'/>
  DocumentClick(event)
  {
    Common.Visibility("menu", "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      let tableData = this.SelectedTableData(eCell);
      if (tableData != null)
      {
        tableData.SelectColumnRow(eCell);
        this.UpdatePageData(tableData);
        // *** Add ***
        this.FocusTableData = tableData;
      }
    }
  }

  /// <summary>Displays the context menu.</summary>
  /// <param name="event">The Target event.</param>
  DocumentContextMenu(event)
  {
    // Handle table row right button click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      let tableData = this.SelectedTableData(eCell);
      if (tableData != null)
      {
        event.preventDefault();
        tableData.SelectColumnRow(eCell);
        this.UpdatePageData(tableData);
        // *** Add ***
        this.FocusTableData = tableData;

        let location = Common.MouseLocation(event);
        tableData.ShowMenu(location);
      }
    }
  }

  /// <summary>Document "dblclick" handler method.</summary>
  DocumentDoubleClick()
  {
    this.EditClick();
  }

  /// <summary>Document "keydown" handler method.</summary>
  /// <param name="event">The Target event.</param>
  DocumentKeyDown(event)
  {
    let ESCAPE_KEY = 27;
    let UP_ARROW = 38;
    let DOWN_ARROW = 40;

    // Table cannot receive focus so set FocusTableData in
    // DocumentClick(), DocumentContextMenu() and CityPage(). 
    if (this.FocusTableData != null)
    {
      let tableData = this.FocusTableData;
      switch (event.keyCode)
      {
        case DOWN_ARROW:
          // False if at end of page.
          if (tableData.MoveNext())
          {
            if (this.UseNew)
            {
              this.LJCCityTable.NextPage();
            }
            else
            {
              this.NextPage(tableData);
            }
          }
          break;

        case ESCAPE_KEY:
          Common.Visibility("menu", "hidden");
          break;

        case UP_ARROW:
          // False if at beginning of page.
          if (tableData.MovePrevious())
          {
            if (this.UseNew)
            {
              this.LJCCityTable.PrevPage();
            }
            else
            {
              this.PrevPage(tableData);
            }
          }
          break;
      }
    }
  }

  // ---------------
  // Page Event Handlers

  /// <summary>Get next page for supplied table.
  /// <param name="LJCTableData">The target table.</param>
  // Called from DocumentKeyDown().
  NextPage(tableData)
  {
    if (tableData != null)
    {
      switch (tableData.ETable.id)
      {
        case this.CityTableID:
          if (!tableData.EndOfData)
          {
            this.IsNextPage = true;
            this.CityPageData.Action = "Next";
            this.UpdateCityPageData();
            this.CityPage(this.CityPageData);
          }
          break;
      }
    }
  }

  /// <summary>Get previous page for supplied table.
  /// <param name="ljcTable">The target table.</param>
  // Called from DocumentKeyDown().
  PrevPage(tableData)
  {
    if (tableData != null)
    {
      switch (tableData.ETable.id)
      {
        case this.CityTableID:
          if (!tableData.BeginningOfData)
          {
            this.IsPrevPage = true;
            this.CityPageData.Action = "Previous";
            this.UpdateCityPageData();
            this.CityPage(this.CityPageData);
          }
          break;
      }
    }
  }

  /// <summary>Send request for City Table page.</summary>
  /// <param name="data">The data object.</param>
  // Called from NextPage(), PrevPage() and Refesh().
  CityPage(cityPageData)
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
        let eTable = Common.Element(saveThis.CityTableID);
        eTable.outerHTML = response.HTMLTable;

        // Updates CityTable with new table element and keys.
        let rowIndex = saveThis.UpdateCityTable(saveThis, response.Keys);

        let tableData = saveThis.CityTableData;
        if (saveThis.UpdateLimitsFlags(tableData))
        {
          // Get row index if "NextPage" or "PrevPage";
          rowIndex = tableData.CurrentRowIndex;
        }
        tableData.SelectRow(rowIndex, rowIndex);

        // Set hidden form primary keys and CityPageData.
        saveThis.UpdateCityPageData()
        saveThis.FocusTableData = tableData;
      }
    };
    xhr.send(JSON.stringify(cityPageData));
  }

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
        this.CityTableData.CurrentRowIndex = this.CityPageData.Limit - 1;
        this.IsNextPage = false;
      }
      if (this.IsPrevPage)
      {
        // Keep at first row.
        this.CityTableData.CurrentRowIndex = 1;
        this.IsPrevPage = false;
      }
    }
    return retValue;
  }

  // Updates the CityTable ETable and Keys values.
  /// <include path='items/UpdateCityTable/*' file='Doc/LJCCityListEvents.xml'/>
  UpdateCityTable(saveThis, keys)
  {
    let retRowIndex = -1;

    let tableData = saveThis.CityTableData;
    if (tableData != null)
    {
      // Return existing row index.
      retRowIndex = tableData.CurrentRowIndex;
    }
    if (null == tableData)
    {
      // Create initial CityTable object.
      saveThis.CityTableData = new LJCTableData(this.CityTableID, "menu");
      tableData = saveThis.CityTableData;
    }

    // Reset table to new table element.
    tableData.ETable = Common.Element(this.CityTableID);

    tableData.Keys = keys;
    return retRowIndex;
  }

  // Updates the BeginningOfData and EndOfData flags.
  /// <include path='items/UpdateLimitsFlags/*' file='Doc/LJCCityListEvents.xml'/>
  UpdateLimitsFlags(tableData)
  {
    let retValue = false;

    if (this.IsNextPage)
    {
      retValue = true;
      tableData.BeginningOfData = false;
      tableData.EndOfData = false;
      if (tableData.Keys.length < this.CityPageData.Limit)
      {
        tableData.EndOfData = true;
      }
      tableData.CurrentRowIndex = 1;
      this.IsNextPage = false;
    }

    if (this.IsPrevPage)
    {
      retValue = true;
      tableData.BeginningOfData = false;
      tableData.EndOfData = false;

      if (tableData.Keys.length < this.CityPageData.Limit)
      {
        tableData.BeginningOfData = true;
      }
      tableData.CurrentRowIndex = this.CityPageData.Limit;
      this.IsPrevPage = false;
    }
    return retValue;
  }

  // ---------------
  // Menu Event Handlers

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Delete".
  /// </summary>
  Delete()
  {
    this.SubmitDetail("Delete");
  }

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Update".
  /// </summary>
  Edit()
  {
    this.SubmitDetail("Update");
  }

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Add".
  /// </summary>
  New()
  {
    this.SubmitDetail("Add");
  }

  /// <summary>Refreshes the current page.</summary>
  Refresh()
  {
    if (this.UseNew)
    {
      let cityTable = this.LJCCityTable;
      cityTable.PageData.Action = "Refresh";
      cityTable.Page();
      // *** Add *** ?
      this.FocusTableData = cityTable.tableData;
    }
    else
    {
      this.CityPageData.Action = "Refresh";
      this.CityPage(this.CityPageData);
    }
  }

  /// <summary>Submit the hidden form listAction to CityDetail.php.</summary>
  /// <param name="action">The listAction type.</param>
  SubmitDetail(action)
  {
    let success = true;

    // Get hidden form row ID.
    if ("" == Common.GetValue("rowID"))
    {
      // No selected row so do not allow delete or update.
      if ("Delete" == action || "Update" == action)
      {
        success = false;
      }
    }

    if (success)
    {
      // Set hidden form ProgramAction.
      let eListAction = Common.Element("listAction");
      if (eListAction != null)
      {
        eListAction.value = action;
      }

      // Submit the form.
      rowID.value++;
      let form = Common.Element("hiddenForm");
      form.submit();
    }
  }

  // ---------------
  // Table Column Methods

  /// <summary>Gets the selected table common object.</summary>
  /// <param name="eColumn">The table column element.</param>
  SelectedTableData(eColumn)
  {
    let rettableData = null;

    let eTable = LJCTableData.GetTable(eColumn);
    if (eTable != null)
    {
      switch (eTable.id)
      {
        case this.CityTableID:
          rettableData = this.CityTableData;
          break;
      }
    }
    return rettableData;
  }

  // ---------------
  // Set Page Values Methods

  /// <summary>Updates page data for LJCTable current table.</summary>
  UpdatePageData(tableData)
  {
    switch (tableData.ETable.id)
    {
      case this.CityTableID:
        this.UpdateCityPageData();
        break;
    }
  }

  /// <summary>
  ///   Sets the form values before a detail submit and the page values before
  ///   a page submit.
  /// </summary>
  /// <param name="eTarget">The HTML element.</param>
  UpdateCityPageData()
  {
    let tableData = this.CityTableData;
    if (tableData != null)
    {
      // Set selected row primaryKeys in hidden form for detail dialog.
      let ePrimaryKeys = Common.Element("primaryKeys");
      if (ePrimaryKeys != null)
      {
        let rowKeys = tableData.Keys[tableData.CurrentRowIndex - 1];
        if (rowKeys != null)
        {
          let keys = [];
          keys.push({ Name: "CityID", Value: rowKeys.CityID });
          let keysJSON = JSON.stringify(keys);
          ePrimaryKeys.value = keysJSON;
        }
      }

      if (this.CityPageData != null)
      {
        // Get first row.
        let rowKeys = tableData.Keys[0];
        if (rowKeys != null)
        {
          let cityPageData = this.CityPageData;
          cityPageData.BeginKeyData.ProvinceID = rowKeys.ProvinceID;
          cityPageData.BeginKeyData.Name = rowKeys.Name;
        }

        // Get last row.
        let keys = tableData.Keys;
        let lastIndex = keys.length - 1;
        rowKeys = tableData.Keys[lastIndex];
        if (rowKeys != null)
        {
          let cityPageData = this.CityPageData;
          cityPageData.EndKeyData.ProvinceID = rowKeys.ProvinceID;
          cityPageData.EndKeyData.Name = rowKeys.Name;
        }
      }
    }
  }
}