"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityListEvents.js

// #region External
// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   LJC: MouseLocation(), Visibility()
// <script src="CityList/LJCCityDataRequest.js"></script>
// <script src="LJCTable.js"></script>
//   LJCTable: GetTable(), ShowMenu() MoveNext(), MovePrevious()
//   SelectColumnRow()
// #endregion

/// <summary>The City List Events</summary>
/// LibName: LJCCityListEvents

// ***************
/// <summary>Contains CityList event handlers.</summary>
class LJCCityListEvents
{
  // #region Properties

  // The city table helper object.
  // Used in LJCCityTableEvents Page().
  CityTable = null; // LJCTable

  // The city table ID name.
  // Used in LJCCityTableEvents constructor().
  CityTableID = "";

  // The active table.
  // Used in LJCCityTableEvents Page() and #TableClick().
  FocusTable = null; // LJCTable
  // #endregion

  // #region Private Properties

  // The detail dialog events.
  #CityDetailEvents = null; // LJCCityDetailEvents

  // The city table events.
  #CityTableEvents = null; // LJCCityTableEvents

  // The show debug text object.
  #Debug = null;
  // #endregion

  // #region Constructor Methods.

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/LJCCityListEvents.xml'/>
  constructor(cityTableID, configName = "", configFile = "DataConfigs.xml")
  {
    this.#Debug = new Debug("LJCCityListEvents");

    // Set properties from parameters.
    this.CityTableID = cityTableID;

    // City Table helper object.
    this.CityTable = new LJCTable(this.CityTableID, "menu");
    this.FocusTable = null;

    // City Table events.
    this.#CityTableEvents = new LJCCityTableEvents(this, "menu", configName
      , configFile);
    let tableEvents = this.#CityTableEvents;
    tableEvents.SetDialogValues("textDialog", "text");
    let tableRequest = this.#CityTableEvents.TableRequest;
    tableRequest.Limit = 20;
    tableRequest.PropertyNames = this.#PropertyNames();

    // City Detail events.
    this.#CityDetailEvents = new LJCCityDetailEvents(this.CityTable);
    // ToDo
    //this.#CityDetailEvents.SetDialogValues("textDialog", "text");

    this.#AddEvents();
    this.#Refresh();
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("contextmenu", this.#DocumentContextMenu.bind(this));
    document.addEventListener("dblclick", this.#DocumentDoubleClick.bind(this));
    document.addEventListener("keydown", this.#DocumentKeyDown.bind(this));

    // Menu Event Handlers.
    LJC.AddEvent("delete", "click", this.#Delete, this);
    LJC.AddEvent("edit", "click", this.#Edit, this);
    LJC.AddEvent("new", "click", this.#New, this);
    LJC.AddEvent("next", "click", this.#Next, this);
    LJC.AddEvent("previous", "click", this.#Previous, this);
    LJC.AddEvent("refresh", "click", this.#Refresh, this);
  }

  // Creates the table property names.
  #PropertyNames()
  {
    let retPropertyNames = [
      City.PropertyProvinceID,
      City.PropertyName,
      City.PropertyDescription,
      City.PropertyCityFlag,
      City.PropertyZipCode,
      City.PropertyDistrict,
    ];
    return retPropertyNames;
  }
  // #endregion

  // #region Document Event Handlers

  // The Document "contextmenu" event handler.
  #DocumentContextMenu(event)
  {
    // Handle table row right button click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      let ljcTable = this.#SelectedTable(eCell);
      if (ljcTable != null)
      {
        event.preventDefault();

        ljcTable.SelectColumnRow(eCell);
        this.FocusTable = ljcTable;

        let tableEvents = this.#SelectedTableEvents(eCell);
        tableEvents.UpdateTableRequest();

        let location = LJC.MouseLocation(event);
        ljcTable.ShowMenu(location);
      }
    }
  }

  // The Document "dblclick" event handler.
  #DocumentDoubleClick()
  {
    this.#Edit();
  }

  // The Document "keydown" event handler.
  #DocumentKeyDown(event)
  {
    let ESCAPE_KEY = 27;
    let UP_ARROW = 38;
    let DOWN_ARROW = 40;

    // Table cannot receive focus so set FocusTable in
    // DocumentContextMenu(), LJCCityTableEvents #DocumentClick() and Page().
    let tableEvents = this.#FocusTableEvents();
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
  // #endregion

  // #region Menu Event Handlers

  // Deletes the selected item.
  #Delete()
  {
    this.#CityDetailEvents.Action = "Delete";
    let cityRequest = this.#CityRequest();
    cityRequest.Action = "Delete";
    cityRequest.KeyColumns = this.#PrimaryKeyColumns();
    this.#CityDataRequest(cityRequest);
  }

  // Displays the CityDetail form for editing the selected item.
  #Edit()
  {
    this.#CityDetailEvents.Action = "Retrieve";
    let cityRequest = this.#CityRequest();
    cityRequest.Action = "Retrieve";
    cityRequest.KeyColumns = this.#PrimaryKeyColumns();
    this.#CityDataRequest(cityRequest);
  }

  // Displays the CityDetail form for adding a new item.
  #New()
  {
    this.#CityDetailEvents.Action = "Insert";
    let cityRequest = this.#CityRequest();
    cityRequest.Action = "Insert";
    this.#CityDataRequest(cityRequest);
  }

  // Displays the next page.
  #Next()
  {
    let tableEvents = this.#FocusTableEvents();
    if (tableEvents)
    {
      tableEvents.NextPage();

      // Update the table with new values ETable and Keys.
      this.#CityDetailEvents.UpdateTable(tableEvents.CityTable);
    }
  }

  // Displays the previous page.
  #Previous()
  {
    let tableEvents = this.#FocusTableEvents();
    if (tableEvents)
    {
      tableEvents.PrevPage();

      // Update the table with new values ETable and Keys.
      this.#CityDetailEvents.UpdateTable(tableEvents.CityTable);
    }
  }

  // Refreshes the current page.
  #Refresh()
  {
    let tableEvents = this.#CityTableEvents;
    tableEvents.TableRequest.Action = "Refresh";
    tableEvents.Page();

    // Update the table with new values ETable and Keys.
    this.#CityDetailEvents.UpdateTable(tableEvents.CityTable);
  }
  // #endregion

  // #region Other Menu Methods

  // Creates the city request.
  #CityRequest()
  {
    let retCityRequest = new LJCCityDataRequest("TestData"
      , "../DataConfigs.xml");
    return retCityRequest;
  }

  // Retrieves the focus table events object.
  #FocusTableEvents()
  {
    let retTableEvents = null;

    if (this.FocusTable != null)
    {
      let ljcTable = this.FocusTable;
      switch (ljcTable.TableID)
      {
        case this.CityTableID:
          retTableEvents = this.#CityTableEvents;
          break;
      }
    }
    return retTableEvents;
  }

  /// <summary>Get the primary key DataColumns.</summary>
  /// <returns>The primary key DataColumns.</returns>
  #PrimaryKeyColumns()
  {
    let retKeyColumns = new LJCDataColumns();

    // Get key value from hidden form.
    let dataColumn = new LJCDataColumn("CityID");
    dataColumn.Value = rowCityID.value;
    retKeyColumns.AddObject(dataColumn);
    return retKeyColumns;
  }
  // #endregion

  // #region Web Service Methods

  // Clears the City form data.
  #ClearCityFormData()
  {
    LJC.SetValue("cityID", "0");
    LJC.SetValue("provinceID", "");
    LJC.SetValue("name", "");

    LJC.SetValue("cityFlag", "0");
    LJC.SetValue("description", "");
    LJC.SetValue("district", "0");
    LJC.SetValue("zipCode", "0");
    LJC.SetValue("commit", "Create");
  }

  // Sends data request to CityData web service.
  // Called from Delete(), Edit(), New()
  #CityDataRequest(cityRequest)
  {
    let methodName = "#CityDataRequest()";

    // Save a reference to this class for anonymous function.
    const self = this;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "CityList/LJCCityDataService.php");
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function ()
    {
      // Get the AJAX response.
      if (LJC.HasText(this.responseText))
      {
        self.#Debug.ShowText(methodName, "this.responseText"
          , this.responseText, false);

        let response = LJC.ParseJSON(this.responseText);

        self.#Debug.ShowText(methodName, "response.DebugText"
          , response.DebugText, false);
        self.#Debug.ShowText(methodName, "response.SQL"
          , response.SQL, false);

        self.#ShowCityDetail(response);
      }
    }

    let request = LJC.CreateJSON(cityRequest);
    xhr.send(request);
  }

  // Set the City Form values.
  #SetCityForm(city)
  {
    LJC.SetValue("cityID", city.CityID);
    LJC.SetValue("provinceID", city.ProvinceID);

    LJC.SetValue("province", city.ProvinceName);
    LJC.SetValue("name", city.Name);
    LJC.SetValue("description", city.Description);
    LJC.SetValue("cityFlag", city.CityFlag);
    LJC.SetValue("zipCode", city.ZipCode);
    LJC.SetValue("district", city.District);
  }

  // Process the web service response.
  // Called from CityDataRequest().
  #ShowCityDetail(cityResponse)
  {
    switch (cityResponse.Action.toLowerCase())
    {
      case "insert":
        // Sets commit button text to "Create".
        this.#ClearCityFormData();
        cityDialog.showModal();
        break;

      case "retrieve":
        let cities = cityResponse.ResultItems;
        if (cities != null)
        {
          let city = cities[0];
          this.#SetCityForm(city);
          cityDialog.showModal();
        }
        break;
    }
  }
  // #endregion

  // #region Table Column Methods

  // Gets the LJCTable object based on the selected table cell.
  #SelectedTable(eCell)
  {
    let retLJCTable = null;

    let eTable = LJCTable.GetTable(eCell);
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

  // Retrieves the table events object based on the selected table cell.
  #SelectedTableEvents(eCell)
  {
    let retTable = null;

    let eTable = LJCTable.GetTable(eCell);
    if (eTable != null)
    {
      switch (eTable.id)
      {
        case this.CityTableID:
          retTable = this.#CityTableEvents;
          break;
      }
    }
    return retTable;
  }
  // #endregion
}