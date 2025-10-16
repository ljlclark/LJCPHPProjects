"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityDetailEvents.js

// #region External

// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   LJC: AddEvent(), CreateJSON(), GetValue(), HasText(), ParseJSON()
//   Debug: ShowText(), ShowDialog()
// #endregion

/// <summary>The City Detail Events</summary>
/// LibName: LJCCityDetailEvents

// ***************
/// <summary>Contains City detail dialog event handlers.</summary>
class LJCCityDetailEvents
{
  // #region Properties

  /// <summary>The detail action.</summary>
  // Used in LJCCityListEvents #Delete(), #Edit() and #New().
  Action = "";
  // #endregion

  // #region Private Properties

  // The associated city table helper object.
  #CityTable = null; // LJCTable

  // The show debug text object.
  #Debug = null;
  // #endregion

  // #region Constructor Methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(cityTable)
  {
    this.#Debug = new Debug("LJCCityDetailEvents");

    this.CityRequest = new LJCCityDataRequest("TestData", "../DataConfigs.xml");
    this.UpdateTable(cityTable);

    this.#AddEvents();
  }

  /// <summary>Sets the dialog values.</summary>
  SetDialogValues(textDialogID, textAreaID)
  {
    this.#Debug.SetDialogValues(textDialogID, textAreaID);
  }

  /// <summary>Updates the table helper class after paging.</summary>
  // Called from LJCCityListEvents #Next(), #Previous() and #Refresh().
  UpdateTable(cityTable)
  {
    this.#CityTable = cityTable;
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    // Button Event Handlers.
    LJC.AddEvent("cityCancel", "click", this.#CancelClick, this);
    LJC.AddEvent("cityCommit", "click", this.#CommitClick, this);
  }
  // #endregion

  // #region Event Handlers

  // Close the dialog without updating the data.
  #CancelClick(event)
  {
    cityDialog.close();
  }

  // Update data and close dialog.
  #CommitClick(event)
  {
    this.CityRequest.Action = this.Action;
    let city = this.#CityFormData();
    if (this.#ValidFormValues(city))
    {
      if ("Retrieve" == this.CityRequest.Action)
      {
        this.CityRequest.Action = "Update";
      }
      this.CityRequest.KeyColumns = this.#PrimaryKeyColumns();

      // Create request items.
      let cities = new Cities();
      cities.AddObject(city);
      this.CityRequest.RequestItems = cities;

      this.#DataRequest(this.CityRequest);

      // If successful.
      cityDialog.close();
    }
  }
  // #endregion

  // #region Other Methods

  // Creates a City object from the form data.
  #CityFormData()
  {
    let cityID = LJC.GetValue("cityID");
    let provinceID = LJC.GetValue("cityProvinceID");
    let name = LJC.GetValue("cityName");

    let cityFlag = LJC.GetValue("cityFlag");
    let retCity = new City(provinceID, name, cityFlag, cityID);
    retCity.Description = LJC.GetValue("cityDescription");
    retCity.District = LJC.GetValue("district");
    retCity.ProvinceName = LJC.GetValue("cityProvinceName");
    retCity.ZipCode = LJC.GetValue("zipCode");
    return retCity;
  }

  // Get the primary key columns.
  #PrimaryKeyColumns()
  {
    let retKeyColumns = new LJCDataColumns();

    // Get key value from hidden form.
    let dataColumn = new LJCDataColumn("CityID");
    dataColumn.Value = rowCityID.value;
    retKeyColumns.AddObject(dataColumn);
    return retKeyColumns;
  }

  // Checks the form values.
  #ValidFormValues(city)
  {
    let retSuccess = true;

    let message = "";
    if (city.ProvinceID <= 0)
    {
      message += "A parent province must be selected.";
    }
    if (!LJC.HasText(city.Name))
    {
      message += "\r\nThe city must have a name.";
    }
    if (LJC.HasText(message))
    {
      retSuccess = false;
      alert(message);
    }
    return retSuccess;
  }
  // #endregion

  // #region Web Service Methods

  // Sends data request to CityData web service.
  // Called from CommitClick().
  #DataRequest(cityRequest)
  {
    let methodName = "#DataRequest()";

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

        if ("Update" == response.Action.trim())
        {
          let objCity = response.ResultItems[0];
          self.#CityTable.UpdateUniqueRow(objCity);
        }

        self.#Debug.ShowText(methodName, "response.DebugText"
          , response.DebugText, false);
        self.#Debug.ShowText(methodName, "response.SQL"
          , response.SQL, false);
      }
    }

    let request = LJC.CreateJSON(cityRequest);
    xhr.send(request);
  }
  // #endregion
}

// ***************
/// <summary>Contains CityData web service request data.</summary>
class LJCCityDataRequest
{
  // #region Properties

  /// <summary>The action type name.</summary>
  /// <remarks>"Delete", "Insert", "Retrieve" or "Update".</summary>
  Action = "";

  /// <summary>The data access configuration file name.</summary>
  ConfigFile = "";

  /// <summary>The data access configuration name.</summary>
  ConfigName = "";

  // The primary keys where clause LJCDataColumns collection.
  /// <include path='items/KeyColumns/*' file='Doc/LJCCityDataRequest.xml'/>
  KeyColumns = null;

  /// <summary>The array of "Order By" names.</summary>
  OrderByNames = [];

  /// <summary>The array of property names.</summary>
  PropertyNames = [];

  // The request Cities collection.
  /// <include path='items/RequestItems/*' file='Doc/LJCCityDataRequest.xml'/>
  RequestItems = [];

  /// <summary>The service name.</summary>
  ServiceName = "LJCCityData";

  /// <summary>The table names.</summary>
  TableName = "City";

  // The unique keys where clause LJCDataColumns collection.
  /// <include path='items/UniqueColumns/*' file='Doc/LJCCityDataRequest.xml'/>
  UniqueColumns = null;
  // #endregion

  // #region Constructor Methods.

  /// <summary>Initializes the object instance.</summary>
  /// <include path='items/constructor/*' file='Doc/LJCCityTableRequest.xml'/>
  constructor(configName = "", configFile = "DataConfigs.xml")
  {
    this.ConfigName = configName;
    this.ConfigFile = configFile;

    this.KeyColumns = new LJCDataColumns();
    this.RequestItems = new Cities();
    this.UniqueColumns = new LJCDataColumns();
  }
  // #endregion

  // #region Data Object Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retRequest = null;

    let json = LJC.CreateJSON(this);
    retRequest = LJC.ParseJSON(json);
    return retRequest;
  }
  // #endregion
}
