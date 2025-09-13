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

  CityDetailEvents;

  CityTable = "";

  // The LJCCityTableEvents JS object.
  CityTableEvents;

  CityTableID = "";

  ConfigFile = "";

  ConfigName = "";

  FocusTable;

  // *** Add ***
  IsNew = false;

  // ---------------
  // The Constructor functions.

  /// <summary>Initializes the object instance.</summary>
  constructor(cityTableID, configName = "", configFile = "DataConfigs.xml")
  {
    this.CityTableID = cityTableID;
    this.ConfigName = configName;
    this.ConfigFile = configFile;

    // CityTable data.
    this.CityTable = new LJCTable(this.CityTableID, "menu");
    this.FocusTable = null;

    // CityTable events.
    this.CityTableEvents = new LJCCityTableEvents(this, "menu", configName
      , configFile);
    this.CityTableEvents.TableRequest.Limit = 20;

    this.CityDetailEvents = new LJCCityDetailEvents();

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
    this.IsNew = false;
    let cityRequest = this.CityRequest();
    cityRequest.Action = "Delete";
    cityRequest.KeyColumns = this.PrimaryKeyColumns();
    this.DataRequest(cityRequest);
  }

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Update".
  /// </summary>
  Edit()
  {
    this.IsNew = false;
    let cityRequest = this.CityRequest();
    cityRequest.Action = "Retrieve";
    cityRequest.KeyColumns = this.PrimaryKeyColumns();
    this.DataRequest(cityRequest);
  }

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Add".
  /// </summary>
  New()
  {
    this.IsNew = true;
    let cityRequest = this.CityRequest();
    cityRequest.Action = "Insert";
    this.DataRequest(cityRequest);
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

  /// <summary>Creates the city request.</summary>
  CityRequest()
  {
    let retCityRequest = new LJCCityDataRequest("TestData"
      , "../DataConfigs.xml");
    return retCityRequest;
  }

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

  /// <summary>Get the primary key columns.</summary>
  PrimaryKeyColumns()
  {
    let retKeyColumns = new LJCDataColumns();

    // Get key value from hidden form.
    let dataColumn = new LJCDataColumn("CityID");
    dataColumn.Value = rowCityID.value;
    retKeyColumns.AddObject(dataColumn);
    return retKeyColumns;
  }

  // ---------------
  // Web Service Methods

  /// <summary>Call the web service.</summary>
  // Called from Delete(), Edit(), New()
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
        let text = "LJCCityListEvents.DataRequest() this.responseText";
        LJC.Message(text, this.responseText);

        let response = LJC.ParseJSON(this.responseText);

        text = "LJCCityListEvents.DataRequest() response.DebugText";
        LJC.Message(text, response.DebugText);
        text = "LJCCityListEvents.DataRequest() response.SQL";
        LJC.Message(text, response.SQL);

        saveThis.ShowCityDetail(response);
      }
    }

    let request = LJC.CreateJSON(cityRequest);

    let text = "LJCCityListEvents.DataRequest() request";
    LJC.Message(text, request);

    xhr.send(request);
  }

  /// <summary>Set the City Form values.</summary>
  SetCityForm(city)
  {
    LJC.SetValue("cityID", city.CityID);
    LJC.SetValue("provinceID", city.ProvinceID);
    LJC.SetValue("name", city.Name);
    LJC.SetValue("description", city.Description);
    LJC.SetValue("cityFlag", city.CityFlag);
    LJC.SetValue("zipCode", city.ZipCode);
    LJC.SetValue("district", city.District);
  }

  /// <summary>Process the web service response.</summary>
  /// <remarks>
  ///   Called from DataRequest() with LJCCityDataService response.
  /// </remarks>
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
          this.CityDetailEvents.IsNew = this.IsNew;
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