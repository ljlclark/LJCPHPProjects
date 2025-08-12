"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityListEvents.js
// <script src="../../Common/Common.js"></script>
//   Element(), GetValue()
//   MouseLocation(), Visibility()
// <script src="LJCTable.js"></script>
//   GetTable(), GetTableRow(), ShowMenu()
//   MoveNext(), MovePrevious(), RowCount(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains CityList event handlers.</summary>
//  Add Events: AddEvents(), AddEvent()
//  Document Handlers: DocumentClick(), DocumentContextMenu()
//    DocumentDoubleClick(), DocumentKeyDown()
//  Page Handlers: NextPage(), PrevPage(), CityPage(), UpdateCityTable()
//  Menu Handlers: Delete(), DoAction(), Edit(), New(), Refresh()
//  Table Column: SelectedTable()
//  Page Data: UpdatePageData(), UpdateCityPageData()
class LJCCityListEvents
{
  // ---------------
  // The Constructor functions.

  /// <summary>Initializes the object instance.</summary>
  constructor()
  {
    this.CityTable = null;
    // *** Begin ***
    this.CityDivID = "cityTableDiv";
    this.CityTableID = "cityTableItem";
    // *** End   ***

    // Data for LJCCityList.php
    this.CityPageData = {
      Action: "None", // Next, Previous, Top, Bottom, First?, Last?
      BeginKeyData: { ProvinceID: 0, Name: "" },
      ConfigName: "TestData",
      EndKeyData: { ProvinceID: 0, Name: "" },
      Limit: 10,
    };

    this.FocusTable = null;
    this.IsNextPage = false;
    this.IsPrevPage = false;
  }

  // ---------------
  // Add Event Methods

  /// <summary>Adds the HTML event handlers.</summary>
  AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("click", this.DocumentClick.bind(this));
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
    //Common.Visibility("menu", "hidden");

    // Handle table row click.
    //if ("TD" == event.target.tagName)
    //{
    //  let eCell = event.target;
    //  let ljcTable = this.SelectedTable(eCell);
    //  if (ljcTable != null)
    //  {
    //    ljcTable.SelectColumnRow(eCell);
    //    this.UpdatePageData(ljcTable);
    //  }
    //}
  }

  // Table "click" handler method.
  TableClick(event)
  {
    Common.Visibility("menu", "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let cityTable = this.CityTable;
      let eCell = event.target;
      if (cityTable != null)
      {
        cityTable.SelectColumnRow(eCell);
        this.UpdatePageData(cityTable);
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
      let ljcTable = this.SelectedTable(eCell);
      if (ljcTable != null)
      {
        event.preventDefault();
        ljcTable.SelectColumnRow(eCell);
        this.UpdatePageData(ljcTable);

        let location = Common.MouseLocation(event);
        ljcTable.ShowMenu(location);
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

    switch (event.keyCode)
    {
      case DOWN_ARROW:
        if (this.FocusTable != null)
        {
          // False if at end of page.
          if (this.FocusTable.MoveNext())
          {
            this.NextPage(this.FocusTable);
          }
        }
        break;

      case ESCAPE_KEY:
        Common.Visibility("menu", "hidden");
        break;

      case UP_ARROW:
        if (this.FocusTable != null)
        {
          // False if at beginning of page.
          if (this.FocusTable.MovePrevious())
          {
            this.PrevPage(this.FocusTable);
          }
        }
        break;
    }
  }

  // ---------------
  // Page Event Handlers

  /// <summary>Get next page for supplied table.
  /// <param name="ljcTable">The target table.</param>
  // Called from DocumentKeyDown().
  NextPage(ljcTable)
  {
    if (ljcTable != null)
    {
      switch (ljcTable.ETable.id)
      {
        case this.CityTableID:
          if (!this.CityTable.EndOfData)
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
  PrevPage(ljcTable)
  {
    if (ljcTable != null)
    {
      switch (ljcTable.ETable.id)
      {
        case this.CityTableID:
          if (!this.CityTable.BeginningOfData)
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
        // *** Begin *** Change
        //let eTableDiv = Common.Element(saveThis.CityDivID);
        //eTableDiv.innerHTML = response.HTMLTable;
        let eTable = Common.Element(saveThis.CityTableID);
        eTable.outerHTML = response.HTMLTable;
        saveThis.AddEvent("cityTableItem", "click", saveThis.TableClick);
        // *** End ***

        // Updates CityTable with new table element and keys.
        let rowIndex = saveThis.UpdateCityTable(saveThis, response.Keys);

        let cityTable = saveThis.CityTable;
        if (saveThis.UpdateLimitsFlags(cityTable))
        {
          // Get row index if "NextPage" or "PrevPage";
          rowIndex = cityTable.CurrentRowIndex;
        }
        cityTable.SelectRow(rowIndex, rowIndex);

        // Set hidden form primary keys and CityPageData.
        saveThis.UpdateCityPageData()
        saveThis.FocusTable = cityTable;
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
        this.CityTable.CurrentRowIndex = this.CityPageData.Limit - 1;
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

  // Updates the CityTable ETable and Keys values.
  /// <include path='items/UpdateCityTable/*' file='Doc/LJCCityListEvents.xml'/>
  UpdateCityTable(saveThis, keys)
  {
    let retRowIndex = -1;

    let cityTable = saveThis.CityTable;
    if (cityTable != null)
    {
      // Return existing row index.
      retRowIndex = cityTable.CurrentRowIndex;
    }
    if (null == cityTable)
    {
      // Create initial CityTable object.
      saveThis.CityTable = new LJCTable(this.CityTableID, "menu");
      cityTable = saveThis.CityTable;
    }

    // Reset table to new table element.
    // *** Change ***
    cityTable.ETable = Common.Element(this.CityTableID);

    cityTable.Keys = keys;
    return retRowIndex;
  }

  // Updates the BeginningOfData and EndOfData flags.
  /// <include path='items/UpdateLimitsFlags/*' file='Doc/LJCCityListEvents.xml'/>
  UpdateLimitsFlags(cityTable)
  {
    let retValue = false;

    if (this.IsNextPage)
    {
      retValue = true;
      cityTable.BeginningOfData = false;
      cityTable.EndOfData = false;
      if (cityTable.Keys.length < this.CityPageData.Limit)
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
      if (cityTable.Keys.length < this.CityPageData.Limit)
      {
        cityTable.BeginningOfData = true;
      }
      cityTable.CurrentRowIndex = this.CityPageData.Limit;
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
    this.CityPageData.Action = "Refresh";
    this.CityPage(this.CityPageData);
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

  /// <summary>Gets the selected table object.</summary>
  /// <param name="eColumn">The table column element.</param>
  SelectedTable(eColumn)
  {
    let retTable = null;

    let eTable = LJCTable.GetTable(eColumn);
    if (eTable != null)
    {
      switch (eTable.id)
      {
        case this.CityTableID:
          retTable = this.CityTable;
          break;
      }
    }
    return retTable;
  }

  // ---------------
  // Set Page Values Methods

  /// <summary>Updates page data for LJCTable current table.</summary>
  UpdatePageData(ljcTable)
  {
    switch (ljcTable.ETable.id)
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
    let ljcTable = this.CityTable;
    if (ljcTable != null)
    {
      // Set selected row primaryKeys in hidden form for detail dialog.
      let ePrimaryKeys = Common.Element("primaryKeys");
      if (ePrimaryKeys != null)
      {
        let rowKeys = ljcTable.Keys[ljcTable.CurrentRowIndex - 1];
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
        let rowKeys = ljcTable.Keys[0];
        if (rowKeys != null)
        {
          let cityPageData = this.CityPageData;
          cityPageData.BeginKeyData.ProvinceID = rowKeys.ProvinceID;
          cityPageData.BeginKeyData.Name = rowKeys.Name;
        }

        // Get last row.
        let keys = ljcTable.Keys;
        let lastIndex = keys.length - 1;
        rowKeys = ljcTable.Keys[lastIndex];
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