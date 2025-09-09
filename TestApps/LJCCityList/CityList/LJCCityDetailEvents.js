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

  IsNew = false;

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(cityID = 0)
  {
    this.CityRequest = new LJCCityDataRequest();
    this.CityRequest.ConfigFile = "../DataConfigs.xml";
    this.CityRequest.ConfigName = "TestData";
    this.CityRequest.TableName = "City";
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

  CommitClick(event)
  {
    let city = this.GetCityFormData();
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
  // Event Handlers

  GetCityFormData()
  {
    let provinceID = LJC.GetValue("provinceID");
    let name = LJC.GetValue("name");
    let cityFlag = LJC.GetValue("cityFlag");
    let cityID = LJC.GetValue("cityID");
    let retCity = new City(provinceID, name, cityFlag, cityID);

    retCity.Description = LJC.GetValue("description");
    retCity.ZipCode = LJC.GetValue("zipCode");
    retCity.District = LJC.GetValue("district");
    return retCity;
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
        alert(`DetailEvents responseText: ${this.responseText}\r\n`);
        let response = JSON.parse(this.responseText);
        alert(`DetailEvents responseSQL: ${response.SQL}`);
      }
    }
    let request = JSON.stringify(cityRequest);
    //alert(`DetailEvents request: ${request}`);
    xhr.send(request);
  }
}