"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityDetailEvents.js
// <script src="../../LJCJSCommon/LJCJSCommonLib.js"></script>
//   AddEvent()

/// <summary>The City Detail Events</summary>
/// LibName: LJCCityDetailEvents
//  Classes: LJCCityDetailEvents

// ***************
/// <summary>Contains City detail dialog events handlers.</summary>
//  Constructor: constructor(), #AddEvents()
//  Event Handlers: #CancelClick(), #CommitClick()
//  Other: #CityFormData(), #PrimaryKeyColumns(), #ValidFormValues()
//  Web Service: #DataRequest()
class LJCCityDetailEvents
{
  // ---------------
  // Properties

  /// <summary>The detail action.</summary>
  // Used in LJCCityListEvents #Delete(), #Edit() and #New().
  Action = "";

  // ---------------
  // Private Properties

  // The debug location class name.
  #ClassName = "";

  // ---------------
  // Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor()
  {
    this.Debug = new Debug("LJCCityDetailEvents");
    this.Debug.MethodName = "constructor()";
    this.Debug.TextDialogID = "textDialog";
    this.Debug.TextValueID = "text";
    this.Debug.SetInactive();

    this.CityRequest = new LJCCityDataRequest("TestData", "../DataConfigs.xml");
    this.#AddEvents();
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    this.Debug.MethodName = "AddEvents()";

    // Button Event Handlers.
    LJC.AddEvent("cancel", "click", this.#CancelClick, this);
    LJC.AddEvent("commit", "click", this.#CommitClick, this);
  }

  // ---------------
  // Event Handlers

  // Close the dialog without updating the data.
  #CancelClick(event)
  {
    cityDialog.close();
  }

  // Update data and close dialog.
  #CommitClick(event)
  {
    this.Debug.MethodName = "CommitClick()";

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

  // ---------------
  // Other Methods

  // Creates a City object from the form data.
  #CityFormData()
  {
    this.Debug.MethodName = "CityFormData()";

    let cityID = LJC.GetValue("cityID");
    let provinceID = LJC.GetValue("provinceID");
    let name = LJC.GetValue("name");

    let cityFlag = LJC.GetValue("cityFlag");
    let retCity = new City(provinceID, name, cityFlag, cityID);

    retCity.Description = LJC.GetValue("description");
    retCity.District = LJC.GetValue("district");
    retCity.ZipCode = LJC.GetValue("zipCode");
    return retCity;
  }

  // Get the primary key columns.
  #PrimaryKeyColumns()
  {
    this.Debug.MethodName = "PrimaryKeyColumns()";

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

  // ---------------
  // Web Service Methods

  // Sends data request to CityData web service.
  // Called from CommitClick().
  #DataRequest(cityRequest)
  {
    this.Debug.MethodName = "DataRequest()";

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
        self.Debug.ShowText("this.responseText", this.responseText);

        let response = LJC.ParseJSON(this.responseText);
        self.Debug.ShowText("response.ResultItems", response.ResultItems
          , true);
        self.#UpdateRow(response);

        self.Debug.ShowText("response.DebugText", response.DebugText);
        self.Debug.ShowText("response.SQL", response.SQL);
      }
    }

    let request = LJC.CreateJSON(cityRequest);
    xhr.send(request);
  }

  #UpdateRow(response)
  {
    this.Debug.MethodName = "UpdateRow";

    // ***** 
    this.Debug.ShowText("response.ResultItems", response.ResultItems, true);
    if ("Update" == response.Action)
    {
      let objCity = response.ResultItems[0];
      // ***** 
      this.Debug.ShowText("objCity", objCity, true);
    }
  }
}
