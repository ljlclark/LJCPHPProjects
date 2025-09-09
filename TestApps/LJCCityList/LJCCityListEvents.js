"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityListEvents.js
// <script src="../../LJCJSCommon/LJCJSCommonLib.js"></script>
//   Element(), GetValue()
//   MouseLocation(), Visibility()
// <script src="LJCTable.js"></script>
//   GetTable(), GetTableRow(), ShowMenu()
//   MoveNext(), MovePrevious(), RowCount(), SelectRow(), SelectColumnRow()
// <script src="CityList/LJCCityDataRequest.js"></script>

// ***************
/// <summary>Contains CityList event handlers.</summary>
//  Add Events: AddEvents(), AddEvent()
//  Document Handlers: DocumentContextMenu(), DocumentDoubleClick()
//    , DocumentKeyDown()
//  Menu Handlers: Delete(), DoAction(), Edit(), New(), Next(), Previous()
//    , Refresh()
//  Table Column: SelectedTable()
class LJCCityListEvents
{
  // ---------------
  // Properties

  CityTable;

  // The LJCCityTableEvents JS object.
  CityTableEvents;

  CityTableID;

  FocusTable;

  // ---------------
  // The Constructor functions.

  /// <summary>Initializes the object instance.</summary>
  constructor()
  {
    this.CityTableID = "cityTableItem";

    // CityTable data.
    this.CityTable = new LJCTable(this.CityTableID, "menu");
    this.FocusTable = null;

    // CityTable events.
    this.CityTableEvents = new LJCCityTableEvents(this, "menu");
    this.CityTableEvents.TableRequest.Limit = 20;

    //this.IsNextPage = false;
    //this.IsPrevPage = false;
    this.AddEvents();
  }

  /// <summary>Adds the HTML event listeners.</summary>
  AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("dblclick", this.DocumentDoubleClick.bind(this));
    document.addEventListener("contextmenu", this.DocumentContextMenu.bind(this));
    document.addEventListener("keydown", this.DocumentKeyDown.bind(this));

    // Menu Event Handlers.
    LJC.AddEvent("delete", "click", this.Delete, this);
    LJC.AddEvent("edit", "click", this.Edit, this);
    LJC.AddEvent("new", "click", this.New, this);
    LJC.AddEvent("next", "click", this.Next, this);
    LJC.AddEvent("previous", "click", this.Previous, this);
    LJC.AddEvent("refresh", "click", this.Refresh, this);
  }

  // ---------------
  // Document Event Handlers

  // The Document "contextmenu" event handler.
  /// <include path='items/DocumentContextMenu/*' file='Doc/LJCCityListEvents.xml'/>
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
        this.FocusTable = ljcTable;

        let tableEvents = this.SelectedTableEvents(eCell);
        tableEvents.UpdateTableRequest();

        let location = LJC.MouseLocation(event);
        ljcTable.ShowMenu(location);
      }
    }
  }

  // The Document "dblclick" event handler.
  /// <include path='items/DocumentDoubleClick/*' file='Doc/LJCCityListEvents.xml'/>
  DocumentDoubleClick()
  {
    this.Edit();
  }

  // The Document "keydown" event handler.
  /// <param name="event">The Target event.</param>
  DocumentKeyDown(event)
  {
    let ESCAPE_KEY = 27;
    let UP_ARROW = 38;
    let DOWN_ARROW = 40;

    // Table cannot receive focus so set FocusTable in
    // DocumentClick(), DocumentContextMenu() and LJCCityTableEvents.Page().
    let tableEvents = this.FocusTableEvents();
    if (tableEvents != null)
    {
      let ljcTable = this.FocusTable;
      switch (event.keyCode)
      {
        case DOWN_ARROW:
          // False if at end of page.
          if (ljcTable.MoveNext())
          {
            tableEvents.NextPage();
          }
          break;

        case ESCAPE_KEY:
          LJC.Visibility("menu", "hidden");
          break;

        case UP_ARROW:
          // False if at beginning of page.
          if (ljcTable.MovePrevious())
          {
            tableEvents.PrevPage();
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
    let cityRequest = new LJCCityDataRequest();
    cityRequest.Action = "Retrieve";
    cityRequest.ConfigFile = "DataConfigs.xml";
    cityRequest.ConfigName = "TestData";

    // Create key columns.
    // Get key value from hidden form.
    let keyColumns = new LJCDataColumns();
    let dataColumn = new LJCDataColumn("CityID");
    dataColumn.Value = rowCityID.value;
    keyColumns.AddObject(dataColumn);
    cityRequest.KeyColumns = keyColumns;

    cityRequest.TableName = "City";
    this.DataRequest(cityRequest);
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
    let tableEvents = this.FocusTableEvents();
    if (tableEvents)
    {
      tableEvents.NextPage();
    }
  }

  /// <summary>Display the previous page.</summary>
  Previous()
  {
    let tableEvents = this.FocusTableEvents();
    if (tableEvents)
    {
      tableEvents.PrevPage();
    }
  }

  /// <summary>Refreshes the current page.</summary>
  Refresh()
  {
    let cityEvents = this.CityTableEvents;
    cityEvents.TableRequest.Action = "Refresh";
    cityEvents.Page();
  }

  // ---------------
  // Other Menu Methods

  /// <summary>Retrieves the focus table events object.</summary>
  FocusTableEvents()
  {
    let retTableEvents = null;

    if (this.FocusTable != null)
    {
      let ljcTable = this.FocusTable;
      switch (ljcTable.TableID)
      {
        case this.CityTableID:
          retTableEvents = this.CityTableEvents;
          break;
      }
    }
    return retTableEvents;
  }

  /// <summary>Submit the hidden form listAction to CityDetail.php.</summary>
  /// <param name="action">The listAction type.</param>
  SubmitDetail(action)
  {
    let success = true;

    // Get hidden form row ID.
    if ("" == LJC.GetValue("rowID"))
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
      let eListAction = LJC.Element("listAction");
      if (eListAction != null)
      {
        eListAction.value = action;
      }

      // Submit the form.
      rowID.value++;
      let form = LJC.Element("hiddenForm");
      form.submit();
    }
  }

  // ---------------
  // Web Service Methods

  CreateJSON(value)
  {
    let retJSON = "";

    retJSON = JSON.stringify(value);
    return retJSON;
  }

  /// <summary>Call the web service.</summary>
  DataRequest(cityRequest)
  {
    // Save a reference to this class for anonymous function.
    const saveThis = this;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "CityList/LJCCityDataService.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onload = function ()
    {
      // Get the AJAX response.
      if (LJC.HasText(this.responseText))
      {
        alert(`ListEvents responseText: ${this.responseText}\r\n`);
        let response = JSON.parse(this.responseText);
        //alert(`ListEvents responseSQL: ${response.SQL}`);
        saveThis.ShowCityDetail(response);
      }
    }
    let request = JSON.stringify(cityRequest);
    //alert(`ListEvents request: ${request}`);
    xhr.send(request);
  }

  /// <summary>Set the City Form values.</summary>
  SetCityForm(city)
  {
    LJC.SetValue("cityID", city.CityID);
    provinceID.value = city.ProvinceID;
    LJC.SetValue("name", city.Name);
    LJC.SetValue("description", city.Description);
    LJC.SetValue("cityFlag", city.CityFlag);
    LJC.SetValue("zipCode", city.ZipCode);
    LJC.SetValue("district", city.District);
  }

  /// <summary>Process the web service response.</summary>
  ShowCityDetail(response)
  {
    switch (response.Action.toLowerCase())
    {
      case "retrieve":
        let cities = response.ResultItems;
        if (cities != null)
        {
          let city = cities[0];
          this.SetCityForm(city);
          // *** Next Statement *** Add
          let cityDetailEvents = new LJCCityDetailEvents();
          cityDialog.showModal();
        }
        break;
    }
  }

  // ---------------
  // Table Column Methods

  /// <summary>Gets the selected LJCTable object.</summary>
  /// <param name="eColumn">The table column element.</param>
  SelectedTable(eColumn)
  {
    let retLJCTable = null;

    let eTable = LJCTable.GetTable(eColumn);
    if (eTable != null)
    {
      switch (eTable.id)
      {
        case this.CityTableID:
          retLJCTable = this.CityTable;
          break;
      }
    }
    return retLJCTable;
  }

  /// <summary>Retrieves the selected table events object.</summary>
  SelectedTableEvents(eColumn)
  {
    let retTable = null;

    let eTable = LJCTable.GetTable(eColumn);
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
}