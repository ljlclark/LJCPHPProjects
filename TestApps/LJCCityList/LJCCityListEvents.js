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
    this.CityTableID = "cityTableItem";

    // Data for LJCCityList.php
    this.CityPageData = {
      Action: "None", // Next, Previous, Top, Bottom, First?, Last?
      BeginKeyData: { ProvinceID: 0, Name: "" },
      ConfigName: "TestData",
      EndKeyData: { ProvinceID: 0, Name: "" },
      Limit: 10,
    };

    // CityTable events.
    this.LJCCityTable = new LJCCityTable(this, "menu");
    this.LJCCityTable.PageData.Limit = 15;

    // CityTable data.
    this.CityTableData = new LJCTableData(this.CityTableID, "menu");
    this.FocusTableData = null;
    this.IsNextPage = false;
    this.IsPrevPage = false;
    this.AddEvents();
  }

  // ---------------
  // Add Event Methods

  /// <summary>Adds the HTML event handlers.</summary>
  AddEvents()
  {
    // Document Event Handlers.
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
        // *** Begin ***
        switch (tableData.TableID)
        {
          case this.CityTableID:
            this.LJCCityTable.UpdatePageData();
            break;
        }
        // *** End   ***
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
      // *** Begin ***
      let ljcTable = null;
      switch (tableData.TableID)
      {
        case this.CityTableID:
          ljcTable = this.LJCCityTable;
          break;
      }
      // *** End   ***

      switch (event.keyCode)
      {
        case DOWN_ARROW:
          // False if at end of page.
          if (tableData.MoveNext())
          {
            ljcTable.NextPage();
          }
          break;

        case ESCAPE_KEY:
          Common.Visibility("menu", "hidden");
          break;

        case UP_ARROW:
          // False if at beginning of page.
          if (tableData.MovePrevious())
          {
            ljcTable.PrevPage();
          }
          break;
      }
    }
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
    let cityTable = this.LJCCityTable;
    cityTable.PageData.Action = "Refresh";
    cityTable.Page();
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

  /// <summary>Gets the selected table data object.</summary>
  /// <param name="eColumn">The table column element.</param>
  SelectedTableData(eColumn)
  {
    let retTableData = null;

    let eTable = LJCTableData.GetTable(eColumn);
    if (eTable != null)
    {
      switch (eTable.id)
      {
        case this.CityTableID:
          retTableData = this.CityTableData;
          break;
      }
    }
    return retTableData;
  }
}