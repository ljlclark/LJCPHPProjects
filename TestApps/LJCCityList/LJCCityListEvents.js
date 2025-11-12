"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityListEvents.js

// #region External
// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   LJC: AddEvent(), CreateJSON(), HasText(), MouseLocation(), ParseJSON()
//   Visibility()
//   Debug: ShowText(), ShowDialog()
// <script src="CityList/LJCCityDetailEvents.js"></script>
//   LJCCityDataRequest:
//   LJCCityDetailEvents: UpdateTable()
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
  // ---------------

  /// <summary>The city table helper object.</summary>
  // Used in LJCCityTableEvents Page().
  CityTable = null; // LJCTable

  /// <summary>The city HTML Table ID.</summary>
  // Used in LJCCityTableEvents constructor().
  CityTableID = "";

  /// <summary>The active table.</summary>
  // Used in LJCCityTableEvents Page() and #TableClick().
  FocusTable = null; // LJCTable

  /// <summary>The region table helper object.</summary>
  // Used in LJCRegionTableEvents Page().
  RegionTable = null // LJCTable

  /// <summary>The region HTML Table ID.</summary>
  RegionTableID = "";
  // #endregion

  // #region Private Properties
  // ---------------

  // The detail dialog events.
  #CityDetailEvents = null; // LJCCityDetailEvents

  // The city HTML menu ID.
  #CityMenuID = "";

  // The city table events.
  #CityTableEvents = null; // LJCCityTableEvents

  // The data configuration file.
  #ConfigFile = "";

  // The data configuration name.
  #ConfigName = "";

  // The show debug text object.
  #Debug = null;

  // The detail dialog events.
  #RegionDetailEvents = null; // LJCRegionDetailEvents

  // The region HTML menu ID.
  #RegionMenuID = "";

  // The region table events.
  #RegionTableEvents = null; // LJCRegionTableEvents
  // #endregion

  // #region Constructor Methods.
  // ---------------

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/LJCCityListEvents.xml'/>
  constructor(cityTableID, configName = "", configFile = "DataConfigs.xml")
  {
    this.#Debug = new Debug("LJCCityListEvents");

    this.CityTableID = cityTableID;
    this.#ConfigName = configName;
    this.#ConfigFile = configFile;

    this.RegionTableID = "selectTable";

    this.#CityMenuID = "cityMenu";
    this.#RegionMenuID = "regionMenu";

    this.#SetupCity();
    this.#SetupRegion();

    this.#AddEvents();
    this.#Refresh();
  }

  // Sets the dialog values.
  /// <include path='items/SetDialogValues/*' file='Doc/LJCCityListEvents.xml'/>
  SetDialogValues(textDialogID, textAreaID)
  {
    this.#Debug.SetDialogValues(textDialogID, textAreaID);
    this.#CityTableEvents.SetDialogValues(textDialogID, textAreaID);
    this.#CityDetailEvents.SetDialogValues(textDialogID, textAreaID);
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("contextmenu", this.#DocumentContextMenu.bind(this));
    document.addEventListener("dblclick", this.#DocumentDoubleClick.bind(this));
    document.addEventListener("keydown", this.#DocumentKeyDown.bind(this));

    LJC.AddEvent("regionButton", "click", this.#RegionButton, this);
    LJC.AddEvent("provinceButton", "click", this.#ProvinceButton, this);

    // City Menu Event Handlers.
    LJC.AddEvent("delete", "click", this.#Delete, this);
    LJC.AddEvent("edit", "click", this.#Edit, this);
    LJC.AddEvent("new", "click", this.#New, this);
    LJC.AddEvent("next", "click", this.#Next, this);
    LJC.AddEvent("previous", "click", this.#Previous, this);
    LJC.AddEvent("refresh", "click", this.#Refresh, this);

    // Region Menu Event Handlers.
    LJC.AddEvent("regionRefresh", "click", this.#RegionRefresh, this);
  }

  // Creates the region table property names.
  #RegionPropertyNames()
  {
    let retPropertyNames = [
      Region.PropertyRegionID,
      Region.PropertyNumber,
      Region.PropertyName,
      Region.PropertyDescription,
    ];
    return retPropertyNames;
  }
  // #endregion

  // #region Setup City Table and Detail.

  // Creates the primary key DataColumns.
  #CityPrimaryKeys()
  {
    let retKeyColumns = new LJCDataColumns();

    // Get key value from hidden form.
    let dataColumn = new LJCDataColumn("CityID");
    dataColumn.Value = rowCityID.value;
    retKeyColumns.AddObject(dataColumn);
    return retKeyColumns;
  }

  // Creates the table column property names.
  #CityTableColumnNames()
  {
    let retTableColumnNames = [
      City.PropertyName,
      City.PropertyDescription,
      City.PropertyCityFlag,
      City.PropertyZipCode,
      City.PropertyDistrict,
    ];
    return retTableColumnNames;
  }

  // Creates the city table property names.
  #CityQueryProperties()
  {
    let retQueryProperties = [
      City.PropertyCityID,
      City.PropertyProvinceID,
      City.PropertyProvinceName,
      City.PropertyName,
      City.PropertyDescription,
      City.PropertyCityFlag,
      City.PropertyZipCode,
      City.PropertyDistrict,
    ];
    return retQueryProperties;
  }

  // Creates the city unique property nanes.
  #CityUniqueProperties()
  {
    let retUniqueProperties = [
      "ProvinceID",
      "Name",
    ];
    return retUniqueProperties;
  }

  #SetupCity()
  {
    // City Table helper object.
    this.CityTable = new LJCTable(this.CityTableID, this.#CityMenuID);
    const uniqueProperties = this.#CityUniqueProperties();
    this.CityTable.UniqueProperties = uniqueProperties;
    this.FocusTable = null;

    // City Table events.
    this.#CityTableEvents = new LJCCityTableEvents(this, this.#CityMenuID
      , this.#ConfigName, this.#ConfigFile);
    let htmlTableID = this.CityTableID;
    let tableName = City.TableName;
    let tableColumnNames = this.#CityTableColumnNames();
    this.#CityTableEvents.SetTableValues(htmlTableID, tableName
      , tableColumnNames);
    // *** Add ***
    this.#CityTableEvents.Table = this.CityTable;

    let tableRequest = this.#CityTableEvents.TableRequest;
    tableRequest.Limit = 18;
    tableRequest.PropertyNames = this.#CityQueryProperties();

    // City Detail events.
    this.#CityDetailEvents = new LJCCityDetailEvents(this.CityTable);
  }
  #endregion

  // #region Setup Region Table and Detail.

  #SetupRegion()
  {
    // Region Table helper object.
    this.RegionTable = new LJCTable(this.RegionTableID, this.#RegionMenuID);

    // Region Table Events
    this.#RegionTableEvents = new LJCRegionTableEvents(this, this.#RegionMenuID
      , this.#ConfigName, this.#ConfigFile);
    let tableRequest = this.#RegionTableEvents.TableRequest;
    tableRequest.Limit = 18;
    // No join columns so leave null to use all columns.
    //tableRequest.PropertyNames = this.#RegionPropertyNames();

    // Region Detail events.
    this.#RegionDetailEvents = new LJCRegionDetailEvents(this.RegionTable);
  }
  // #endregion

  // #region Document Event Handlers
  // ---------------

  // The Document "contextmenu" event handler.
  // Move this method to LJCCityTableEvents?
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
  // Move this method to LJCCityTableEvents?
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
          // True if at end of page.
          if (ljcTable.MoveNext())
          {
            tableEvents.NextPage();
          }
          break;

        case ESCAPE_KEY:
          LJC.Visibility("cityMenu", "hidden");
          break;

        case UP_ARROW:
          // True if at beginning of page.
          if (ljcTable.MovePrevious())
          {
            tableEvents.PrevPage();
          }
          break;
      }
    }
  }
  // #endregion

  // #region City List Event Handlers
  // ---------------

  // Displays the Region Selection table.
  #RegionButton()
  {
    this.#RegionRefresh();
    //selectDialog.showModal();
  }

  // Displays the Province Selection table.
  #ProvinceButton()
  {
    alert("Province Button");
  }
  // #endregion

  // #region City Menu Event Handlers
  // Move to LJCCityTableEvents?
  // ---------------

  // Deletes the selected item.
  #Delete()
  {
    this.#CityDetailEvents.Action = "Delete";
    let cityRequest = this.#CityRequest();
    cityRequest.Action = "Delete";
    cityRequest.KeyColumns = this.#CityPrimaryKeys();
    this.#CityDataRequest(cityRequest);
  }

  // Displays the CityDetail form for editing the selected item.
  #Edit()
  {
    this.#CityDetailEvents.Action = "Retrieve";
    let cityRequest = this.#CityRequest();
    cityRequest.Action = "Retrieve";
    cityRequest.KeyColumns = this.#CityPrimaryKeys();
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

      // Update the table with new Keys.
      this.#CityDetailEvents.UpdateTable(tableEvents.Table);
    }
  }

  // Displays the previous page.
  #Previous()
  {
    let tableEvents = this.#FocusTableEvents();
    if (tableEvents)
    {
      tableEvents.PrevPage();

      // Update the table with new Keys.
      this.#CityDetailEvents.UpdateTable(tableEvents.Table);
    }
  }

  // Refreshes the current page.
  #Refresh()
  {
    let tableEvents = this.#CityTableEvents;
    tableEvents.TableRequest.Action = "Refresh";
    tableEvents.Page();

    // Update the table with new Keys.
    this.#CityDetailEvents.UpdateTable(tableEvents.Table);
  }
  // #endregion

  // #region Region Menu Event Handlers
  // ---------------

  static #DialogResize()
  {
    const tableWidth = selectTable.offsetWidth;
    const tableHeight = selectTable.offsetHeight;
    selectDialog.style.width = tableWidth + 'px';
    selectDialog.style.height = tableHeight + 'px';
  }

  // Gets the float or int value of an html percentage or pixels.
  /// <include path='items/GetValue/*' file='Doc/LJCCityListEvents.xml'/>
  GetValue(htmlValue)
  {
    let retValue = htmlValue.trim();

    // Strip prefix and suffix.
    let length = retValue.length;
    if (retValue.includes("%"))
    {
      retValue = value.substring(0, length - 1);
    }
    if (retValue.includes("px"))
    {
      retValue = retValue.substring(0, length - 2);
    }

    // Convert to value.
    if (retValue.includes("."))
    {
      retValue = retValue.parseFloat(value);
    }
    else
    {
      retValue = retValue.parseInt(value);
    }

    return retValue;
  }

  // Sets the column widths and displays the dialog.
  /// <include path='items/PageDone/*' file='Doc/LJCCityListEvents.xml'/>
  PageDone(regionTableEvents)
  {
    // Set attributes including column widths.
    let regionTable = regionTableEvents.RegionTable;

    regionTable.SetColumnWidth(0, "103px");
    regionTable.SetColumnWidth(1, "200px");
    regionTable.SetColumnWidth(2, "200px");

    // Column widths are set.
    let width1 = regionTable.ColumnWidth(0);
    let width2 = regionTable.ColumnWidth(1);
    let width3 = regionTable.ColumnWidth(2);

    // Show the parent dialog.
    selectDialog.showModal();
    //LJCCityListEvents.#DialogResize();
    //LJC.AddEvent("selectTable", "resize", LJCCityListEvents.#DialogResize
    //  , this);
  }

  // Refreshes the current page.
  #RegionRefresh()
  {
    let tableEvents = this.#RegionTableEvents;
    tableEvents.TableRequest.Action = "Refresh";
    this.#TableDataRequest(tableEvents);
  }

  // Sends page request to RegionTable web service.
  #TableDataRequest(tableEvents)
  {
    tableEvents.Page(this.PageDone);
  }
  // #endregion

  // #region Other Menu Methods
  // ---------------

  // Creates the city request.
  #CityRequest()
  {
    let configFile = "../DataConfigs.xml";
    let configName = "TestData";
    let retCityRequest = new LJCCityDataRequest(configName, configFile);
    retCityRequest.TableName = City.TableName;
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
  // #endregion

  // #region Web Service Methods
  // ---------------

  // Clears the City form data.
  #ClearCityFormData()
  {
    LJC.SetValue("cityID", "0");
    LJC.SetValue("cityProvinceID", "");
    LJC.SetValue("cityName", "");

    LJC.SetValue("cityFlag", "0");
    LJC.SetValue("cityDescription", "");
    LJC.SetValue("district", "0");
    LJC.SetValue("zipCode", "0");

    LJC.SetValue("cityCommit", "Create");
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
    LJC.SetValue("cityProvinceID", city.ProvinceID);
    LJC.SetValue("cityName", city.Name);

    LJC.SetValue("cityFlag", city.CityFlag);
    LJC.SetValue("cityDescription", city.Description);
    LJC.SetValue("cityProvinceName", city.ProvinceName);
    LJC.SetValue("district", city.District);
    LJC.SetValue("zipCode", city.ZipCode);
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