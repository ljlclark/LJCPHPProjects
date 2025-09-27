"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityDetailEvents.js
// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
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
    let prev = this.Debug.SetMethodName("constructor()");

    this.Debug.TextDialogID = "textDialog";
    this.Debug.TextValueID = "text";
    this.Debug.SetInactive();

    this.CityRequest = new LJCCityDataRequest("TestData", "../DataConfigs.xml");
    this.#AddEvents();

    // End of root method.
    this.Debug.ResetMethodName("");
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    let prev = this.Debug.SetMethodName("AddEvents()");

    // Button Event Handlers.
    LJC.AddEvent("cancel", "click", this.#CancelClick, this);
    LJC.AddEvent("commit", "click", this.#CommitClick, this);

    this.Debug.ResetMethodName(prev);
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
    let prev = this.Debug.SetMethodName("CommitClick()");

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

    // End of root method.
    this.Debug.ResetMethodName("");
  }

  // ---------------
  // Other Methods

  // Creates a City object from the form data.
  #CityFormData()
  {
    let prev = this.Debug.SetMethodName("CityFormData()");

    let cityID = LJC.GetValue("cityID");
    let provinceID = LJC.GetValue("provinceID");
    let name = LJC.GetValue("name");

    let cityFlag = LJC.GetValue("cityFlag");
    let retCity = new City(provinceID, name, cityFlag, cityID);

    retCity.Description = LJC.GetValue("description");
    retCity.District = LJC.GetValue("district");
    retCity.ZipCode = LJC.GetValue("zipCode");

    this.Debug.ResetMethodName(prev);
    return retCity;
  }

  // Get the primary key columns.
  #PrimaryKeyColumns()
  {
    let prev = this.Debug.SetMethodName("PrimaryKeyColumns()");

    let retKeyColumns = new LJCDataColumns();

    // Get key value from hidden form.
    let dataColumn = new LJCDataColumn("CityID");
    dataColumn.Value = rowCityID.value;
    retKeyColumns.AddObject(dataColumn);

    this.Debug.ResetMethodName(prev);
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
    let prev = this.Debug.SetMethodName("#DataRequest");

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
        let prev = self.Debug.SetMethodName("#DataRequest");
        self.Debug.ShowText("this.responseText", this.responseText
          , true);

        let response = LJC.ParseJSON(this.responseText);
        // ***** Begin
        let jsonResultItems = LJC.CreateJSON(response.ResultItems);
        self.Debug.ShowText("response.ResultItems", jsonResultItems
          , true);
        // ***** end
        self.#UpdateRow(response);

        self.Debug.ShowText("response.DebugText", response.DebugText);
        self.Debug.ShowText("response.SQL", response.SQL);
        self.Debug.ResetMethodName(prev);
      }
    }

    let request = LJC.CreateJSON(cityRequest);
    xhr.send(request);
  }

  #UpdateRow(response)
  {
    let prev = this.Debug.SetMethodName("#UpdateRow");

    this.Debug.ShowText("#UpdateRow", "#UpdateRow");
    if ("Update" == response.Action.trim())
    {
      let objCity = response.ResultItems[0];
      let jsonCity = LJC.CreateJSON(objCity);
      this.Debug.ShowText("objCity", jsonCity, true);
    }

    this.Debug.ResetMethodName(prev);
  }
}
