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
//  Menu Handlers: Delete(), DoAction(), Edit(), New(), Next(), Previous()
//    , Refresh()
//  Table Column: SelectedTableData()
class LJCCityListEvents
{
  // ---------------
  // Properties

  CityTableData;
  CityTableEvents;
  CityTableID;
  FocusTableData;
  IsNextPage;
  IsPrevPage;
  UseNew;

  // ---------------
  // The Constructor functions.

  /// <summary>Initializes the object instance.</summary>
  constructor()
  {
    this.CityTableID = "cityTableItem";

    // CityTable data.
    this.CityTableData = new LJCTableData(this.CityTableID, "menu");
    this.FocusTableData = null;

    // CityTable events.
    this.CityTableEvents = new LJCCityTableEvents(this, "menu");
    this.CityTableEvents.PageData.Limit = 20;

    this.IsNextPage = false;
    this.IsPrevPage = false;
    this.AddEvents();
  }

  // ---------------
  // Add Event Methods

  /// <summary>Adds the HTML event listeners.</summary>
  AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("dblclick", this.DocumentDoubleClick.bind(this));
    document.addEventListener("contextmenu", this.DocumentContextMenu.bind(this));
    document.addEventListener("keydown", this.DocumentKeyDown.bind(this));

    // Menu Event Handlers.
    Common.AddEvent("delete", "click", this.Delete, this);
    Common.AddEvent("edit", "click", this.Edit, this);
    Common.AddEvent("new", "click", this.New, this);
    Common.AddEvent("next", "click", this.Next, this);
    Common.AddEvent("previous", "click", this.Previous, this);
    Common.AddEvent("refresh", "click", this.Refresh, this);
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

        let tableEvents = this.SelectedTableEvents(eCell);
        tableEvents.UpdatePageData();

        tableData.SelectColumnRow(eCell);
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
    let ljcTable = this.FocusTable();
    if (ljcTable != null)
    {
      let tableData = this.FocusTableData;
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
    //cityDialog.showModal();
  }

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Add".
  /// </summary>
  New()
  {
    this.SubmitDetail("Add");
  }

  /// <summary>Display the next page.</summary>
  Next()
  {
    let ljcTable = this.FocusTable();
    if (ljcTable)
    {
      ljcTable.NextPage();
    }
  }

  /// <summary>Display the previous page.</summary>
  Previous()
  {
    let ljcTable = this.FocusTable();
    if (ljcTable)
    {
      ljcTable.PrevPage();
    }
  }

  /// <summary>Refreshes the current page.</summary>
  Refresh()
  {
    let cityEvents = this.CityTableEvents;
    cityEvents.PageData.Action = "Refresh";
    cityEvents.Page();
  }

  /// <summary>Retrieves the focus table events object.</summary>
  FocusTable()
  {
    let retTable = null;

    if (this.FocusTableData != null)
    {
      let tableData = this.FocusTableData;
      switch (tableData.TableID)
      {
        case this.CityTableID:
          retTable = this.CityTableEvents;
          break;
      }
    }
    return retTable;
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
      // Set hidden form listAction.
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

  /// <summary>Retrieves the selected table events object.</summary>
  SelectedTableEvents(eColumn)
  {
    let retTable = null;

    let eTable = LJCTableData.GetTable(eColumn);
    if (eTable != null)
    {
      switch (eTable.id)
      {
        case this.CityTableID:
          retTable = this.CityTableEvents;
          break;
      }
    }
    return retTable;
  }

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