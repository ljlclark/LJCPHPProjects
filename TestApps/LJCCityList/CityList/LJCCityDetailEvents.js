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

  /// <summary>The text area element ID.</summary>
  TextAreaID = "";

  /// <summary>The text dialog element ID.</summary>
  TextDialogID = "";

  // ---------------
  // Private Properties

  // The associated city table helper object.
  #CityTable = null; // LJCTable

  // The debug location class name.
  #ClassName = "";

  // ---------------
  // Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(cityTable)
  {
    this.#ClassName = "LJCCityDetailEvents";
    let methodName = "constructor()";

    this.CityRequest = new LJCCityDataRequest("TestData", "../DataConfigs.xml");
    //this.#CityTable = new LJCTable(cityTableID, cityMenuID);
    this.UpdateTable(cityTable);
    this.#AddEvents();
  }

  /// <summary>Updates the table helper class after paging.</summary>
  UpdateTable(cityTable)
  {
    this.#CityTable = cityTable;
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    let methodName = "AddEvents()";

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
    let methodName = "CommitClick()";

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
    let methodName = "CityFormData()";

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
    let methodName = "PrimaryKeyColumns()";

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
    let methodName = "#DataRequest()";

    // Save a reference to this class for anonymous function.
    const self = this;

    // Update to latest. Make method in LJCTable?
    //let eTable = LJC.Element(this.#CityTable.TableID);
    //this.#CityTable.ETable = eTable;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "CityList/LJCCityDataService.php");
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function ()
    {
      // Get the AJAX response.
      if (LJC.HasText(this.responseText))
      {
        LJC.ShowText(self.#ClassName, methodName
          , "this.responseText", this.responseText, false);

        let response = LJC.ParseJSON(this.responseText);

        if ("Update" == response.Action.trim())
        {
          let objCity = response.ResultItems[0];
          self.#CityTable.UpdateUniqueRow(objCity);
        }

        LJC.ShowText(self.#ClassName, methodName
          , "response.DebugText", response.DebugText, false);
        LJC.ShowText(self.#ClassName, methodName
          , "response.SQL", response.SQL, false);
      }
    }

    let request = LJC.CreateJSON(cityRequest);
    xhr.send(request);
  }
}
