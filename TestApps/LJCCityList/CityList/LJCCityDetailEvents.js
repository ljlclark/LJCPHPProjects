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

  // The detail action.
  Action = "";

  // The associated cancel button ID name.
  CancelID = "";

  // The class name for debug text.
  ClassName = "";

  // The associated commit button ID name.
  CommitID = "";

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor()
  {
    this.ClassName = "LJCCityDetailEvents";
    let methodName = "constructor()";

    this.CityRequest = new LJCCityDataRequest("TestData", "../DataConfigs.xml");
    this.AddEvents();
  }

  /// <summary>Adds the HTML event listeners.</summary>
  AddEvents()
  {
    let methodName = "AddEvents()";

    // Button Event Handlers.
    LJC.AddEvent("cancel", "click", this.CancelClick, this);
    LJC.AddEvent("commit", "click", this.CommitClick, this);
  }

  // Standard debug method for each class.
  Debug(methodName, valueName, value, force = false)
  {
    let text = LJC.Location(this.ClassName, methodName, valueName);
    // Does not show alert if no value unless force = true.
    LJC.Message(text, value, force);
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
    let methodName = "CommitClick()";

    this.CityRequest.Action = this.Action;
    let city = this.CityFormData();
    if (this.ValidFormValues(city))
    {
      if ("Retrieve" == this.CityRequest.Action)
      {
        this.CityRequest.Action = "Update";
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
  }

  // ---------------
  // Other Methods

  // Creates a City object from the form data.
  CityFormData()
  {
    let methodName = "CityFormData()";

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

  /// <summary>Get the primary key columns.</summary>
  PrimaryKeyColumns()
  {
    let methodName = "PrimaryKeyColumns()";

    let retKeyColumns = new LJCDataColumns();

    // Get key value from hidden form.
    let dataColumn = new LJCDataColumn("CityID");
    dataColumn.Value = rowCityID.value;
    retKeyColumns.AddObject(dataColumn);
    return retKeyColumns;
  }

  /// <summary>Checks the form values.</summary>
  /// <returns>The City data object.</returns>
  ValidFormValues(city)
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

  // ---------------
  // Web Service Methods

  /// <summary>Call the web service.</summary>
  // Called from CommitClick().
  DataRequest(cityRequest)
  {
    let methodName = "DataRequest()";

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
        saveThis.Debug(methodName, "responseText", this.responseText);

        let response = LJC.ParseJSON(this.responseText);

        saveThis.Debug(methodName, "response.DebugText", response.DebugText);
        saveThis.Debug(methodName, "response.SQL", response.SQL);
      }
    }

    let request = LJC.CreateJSON(cityRequest);
    xhr.send(request);
  }
}