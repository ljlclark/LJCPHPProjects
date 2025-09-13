"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityDetailEvents.js
// <script src="../../LJCJSCommon/LJCJSCommonLib.js"></script>
//   AddEvent()

// ***************
/// <summary>Contains City HTML Table methods.</summary>
//  Constructor: constructor(), AddEvents()
class LJCCityDetailEvents
{
  // ---------------
  // Properties

  // The associated cancel button ID name.
  CancelID = "";

  // The associated commit button ID name.
  CommitID = "";

  // *** Add ***
  IsNew = false;

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor()
  {
    this.CityRequest = new LJCCityDataRequest("TestData", "../DataConfigs.xml");
    this.AddEvents();
  }

  /// <summary>Adds the HTML event listeners.</summary>
  AddEvents()
  {
    // Button Event Handlers.
    LJC.AddEvent("cancel", "click", this.CancelClick, this);
    LJC.AddEvent("commit", "click", this.CommitClick, this);
  }

  // ---------------
  // Event Handlers

  // Close the dialog without updating the data.
  CancelClick(event)
  {
    cityDialog.close();
  }

  /// <summary>Update data and close dialog.</summary>
  CommitClick(event)
  {
    let city = this.CityFormData();
    this.CityRequest.Action = "Update";
    // *** Next Statement *** Add
    if (this.IsNew)
    {
      this.CityRequest.Action = "Insert";
    }
    this.CityRequest.KeyColumns = this.PrimaryKeyColumns();

    // Create request items.
    let cities = new Cities();
    cities.AddObject(city);
    this.CityRequest.RequestItems = cities;

    this.DataRequest(this.CityRequest);

    // If successful.
    cityDialog.close();
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
  // Other Event Handler Methods

  // Creates a City object from the form data.
  CityFormData()
  {
    let provinceID = LJC.GetValue("provinceID");
    let name = LJC.GetValue("name");
    let cityFlag = LJC.GetValue("cityFlag");
    let cityID = LJC.GetValue("cityID");
    let retCity = new City(provinceID, name, cityFlag, cityID);

    retCity.Description = LJC.GetValue("description");
    retCity.District = LJC.GetValue("district");
    retCity.ZipCode = LJC.GetValue("zipCode");
    return retCity;
  }

  // Clears the City form data.
  ClearCityFormData()
  {
    LJC.SetValue("cityID", "0");
    LJC.SetValue("provinceID", "");
    LJC.SetValue("name", "");

    LJC.SetValue("cityFlag", "0");
    LJC.SetValue("description", "");
    LJC.SetValue("district", "0");
    LJC.SetValue("zipCode", "0");
  }

  // ---------------
  // Web Service Methods

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
        let text = "LJCCityDetailEvents.DataRequest() this.responseText";
        LJC.Message(text, this.responseText);

        let response = LJC.ParseJSON(this.responseText);

        text = "LJCCityDetailEvents.DataRequest() response.DebugText";
        LJC.Message(text, response.DebugText);
        text = "LJCCityDetailEvents.DataRequest() response.SQL";
        LJC.Message(text, response.SQL);
      }
    }

    let request = LJC.CreateJSON(cityRequest);

    let text = "LJCCityDetailEvents.DataRequest() request";
    LJC.Message(text, request);

    xhr.send(request);
  }
}