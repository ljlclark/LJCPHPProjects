"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCRegionDetailEvents.js

// #region External

// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   LJC: AddEvent(), CreateJSON(), GetValue(), HasText(), ParseJSON()
//   Debug: ShowText(), ShowDialog()
// #endregion

/// <summary>The City Detail Events</summary>
/// LibName: LJCCityDetailEvents

// ***************
/// <summary>Contains Region detail dialog event handlers.</summary>
class LJCRegionDetailEvents
{
  // #region Properties

  /// <summary>The detail action.</summary>
  // Used in LJCCityListEvents #DeleteRegion(), #EditRegion() and #NewRegion().
  Action = "";
  // #endregion

  RegionRequest = null; // LJCRegionDataRequest

  // #region Private Properties

  // The associated region table helper object.
  #RegionTable = null; // LJCTable

  // The show debug text object.
  #Debug = null;
  // #endregion

  // #region Constructor Methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(regionTable)
  {
    this.#Debug = new Debug("LJCRegionDetailEvents");

    this.RegionRequest = new LJCRegionDataRequest("TestData", "../DataConfigs.xml");
    this.UpdateTable(regionTable);

    this.#AddEvents();
  }

  /// <summary>Sets the dialog values.</summary>
  SetDialogValues(textDialogID, textAreaID)
  {
    this.#Debug.SetDialogValues(textDialogID, textAreaID);
  }

  /// <summary>Updates the table helper class after paging.</summary>
  // Called from LJCCityListEvents #NextRegion(), #PreviousRegion() and #RefreshRegion().
  UpdateTable(regionTable)
  {
    this.#RegionTable = regionTable;
  }

  // Adds the HTML event listeners.
  #AddEvents()
  {
    // Button Event Handlers.
    LJC.AddEvent("regionCancel", "click", this.#CancelClick, this);
    LJC.AddEvent("regionCommit", "click", this.#CommitClick, this);
  }
  // #endregion

  // #region Event Handlers

  // Close the dialog without updating the data.
  #CancelClick(event)
  {
    regionDialog.close();
  }

  // Update data and close dialog.
  #CommitClick(event)
  {
    this.RegionRequest.Action = this.Action;
    let region = this.#RegionFormData();
    if (this.#ValidFormValues(region))
    {
      if ("Retrieve" == this.RegionRequest.Action)
      {
        this.RegionRequest.Action = "Update";
      }
      this.RegionRequest.KeyColumns = this.#PrimaryKeyColumns();

      // Create request items.
      let regions = new Regions();
      regions.AddObject(city);
      this.RegionRequest.RequestItems = regions;

      this.#DataRequest(this.RegionRequest);

      // If successful.
      regionDialog.close();
    }
  }
  // #endregion

  // #region Other Methods

  // Creates a Region object from the form data.
  #RegionFormData()
  {
    let regionID = LJC.GetValue("regionID");
    let number = LJC.GetValue("regionNumber");
    let name = LJC.GetValue("regionName");

    let retRegion = new Region(name, number);
    retRegion.Description = LJC.GetValue("regionDescription");
    return retRegion;
  }

  // Get the primary key columns.
  #PrimaryKeyColumns()
  {
    let retKeyColumns = new LJCDataColumns();

    // Get key value from hidden form.
    let dataColumn = new LJCDataColumn("RegionID");
    dataColumn.Value = rowRegionID.value;
    retKeyColumns.AddObject(dataColumn);
    return retKeyColumns;
  }

  // Checks the form values.
  #ValidFormValues(region)
  {
    let retSuccess = true;

    let message = "";
    if (!LJC.HasText(region.Name))
    {
      message += "\r\nThe region must have a name.";
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
  #DataRequest(regionRequest)
  {
    let methodName = "#DataRequest()";

    // Save a reference to this class for anonymous function.
    const self = this;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "RegionList/LJCRegionDataService.php");
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
          let objRegion = response.ResultItems[0];
          self.#RegionTable.UpdateUniqueRow(objRegion);
        }

        self.#Debug.ShowText(methodName, "response.DebugText"
          , response.DebugText, false);
        self.#Debug.ShowText(methodName, "response.SQL"
          , response.SQL, false);
      }
    }

    let request = LJC.CreateJSON(regionRequest);
    xhr.send(request);
  }
  // #endregion
}

// ***************
/// <summary>Contains RegionData web service request data.</summary>
class LJCRegionDataRequest
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
  ServiceName = "LJCRegionDataService";

  /// <summary>The table names.</summary>
  TableName = "Region";

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
    this.RequestItems = new LJCRegions();
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
