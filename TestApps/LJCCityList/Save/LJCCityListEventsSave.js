"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCDataTableEvents.js
// <script src="../../Common/Common.js"></script>
//   Element(), TagElements(), GetValue()
// <script src="LJCTable.js"></script>
//   GetTable(), GetTableRow(), ShowMenu(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains CityList event handlers.</summary>
class LJCCityListEvents
{
  // ---------------
  // The Constructor function.

  /// <summary>Initializes the object instance.</summary>
  constructor()
  {
    this.CityTable = null;
    this.CityPageData = {
      Action: "None", // Next, Previous, Top, Bottom, First?, Last?
      BeginKeyData: { ProvinceID: 0, Name: "" },
      ConfigName: "TestData",
      EndKeyData: { ProvinceID: 0, Name: "" },
      Limit: 0,
    };
    this.FocusTable = null;
    this.Limit = 10;
  }

  // ---------------
  // Add Event Methods

  /// <summary>Adds the HTML event handlers.</summary>
  AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("click", this.DocumentClick.bind(this));
    document.addEventListener("dblclick", this.DocumentDoubleClick.bind(this));
    document.addEventListener("contextmenu", this.DocumentContextMenu.bind(this));
    document.addEventListener("keydown", this.DocumentKeyDown.bind(this));

    // Menu Event Handlers.
    this.AddEvent("refresh", "click", this.Refresh);
    this.AddEvent("new", "click", this.NewClick);
    this.AddEvent("edit", "click", this.EditClick);
    this.AddEvent("delete", "click", this.DeleteClick);
  }

  // Adds an event handler.
  /// <include path='items/AddEvent/*' file='Doc/LJCCityListEvents.xml'/>
  AddEvent(elementID, eventName, handler)
  {
    let element = Common.Element(elementID);
    if (element != null)
    {
      element.addEventListener(eventName, handler.bind(this));
    }
  }

  // ---------------
  // Document Event Handlers

  // Document "click" handler method.
  /// <include path='items/DocumentClick/*' file='Doc/LJCCityListEvents.xml'/>
  DocumentClick(event)
  {
    Common.Visibility("menu", "hidden");

    // Handle table row click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      let ljcTable = this.SelectedTable(eCell);
      if (ljcTable != null)
      {
        ljcTable.SelectColumnRow(eCell);
        this.SetValues(ljcTable);
      }
    }
  }

  /// <summary>Displays the context menu.</summary>
  /// <param name="event">The Target event.</param>
  DocumentContextMenu(event)
  {
    // Handle table row right button click.
    if ("TD" == event.target.tagName)
    {
      let eCell = event.target;
      let ljcTable = this.SelectedTable(eCell);
      if (ljcTable != null)
      {
        event.preventDefault();
        ljcTable.SelectColumnRow(eCell);
        this.SetValues(ljcTable);

        let location = Common.MouseLocation(event);
        ljcTable.ShowMenu(location);
      }
    }
  }

  /// <summary>Document "dblclick" handler method.</summary>
  /// <param name="event">The Target event.</param>
  DocumentDoubleClick(event)
  {
    this.EditClick();
  }

  /// <summary>Document "keydown" handler method.</summary>
  /// <param name="event">The Target event.</param>
  DocumentKeyDown(event)
  {
    let ESCAPE_KEY = 27;
    let UP_ARROW = 38;
    let DOWN_ARROW = 40;

    switch (event.keyCode)
    {
      case DOWN_ARROW:
        if (this.FocusTable != null)
        {
          if (this.FocusTable.MoveNext())
          {
            this.NextPage(this.FocusTable);
          }
        }
        break;

      case ESCAPE_KEY:
        Common.Visibility("menu", "hidden");
        break;

      case UP_ARROW:
        if (this.FocusTable != null)
        {
          if (this.FocusTable.MovePrevious())
          {
            this.PrevPage(this.FocusTable);
          }
        }
        break;
    }
  }

  // ---------------
  // Page Event Handlers

  NextPage(ljcTable)
  {
    if (ljcTable != null)
    {
      switch (ljcTable.ETable.id)
      {
        case "dataTable":
          this.CityPageData.Action = "Next";
          this.SetCityValues();
          this.CityPageJSON(this.CityPageData);
          let rowCount = this.CityTable.RowCount();
          this.CityTable.SelectRow(rowCount, 1);
          break;
      }
    }
  }

  PrevPage(ljcTable)
  {
    if (ljcTable != null)
    {
      switch (ljcTable.ETable.id)
      {
        case "dataTable":
          this.CityPageData.Action = "Previous";
          this.SetCityValues();
          this.CityPageJSON(this.CityPageData);
          break;
      }
    }
  }

  /// <summary>Send request for HTML Table page with url data.</summary>
  /// <param name="data">The data object.</param>
  CityPageGet(data)
  {
    // Create data.
    let url = "LJCCityListGet.php";
    url += `?configName=${data.ConfigName}`;
    url += `&beginID=${data.BeginID}`;
    url += `&endID=${data.EndID}`;
    url += `&limit=${this.Limit}`;

    let xhr = new XMLHttpRequest();
    xhr.open("GET", url);
    xhr.onload = function ()
    {
      dataDiv.innerHTML = this.response;
    };
    xhr.send();
  }

  /// <summary>Send request for HTML Table page with JSON data.</summary>
  /// <param name="data">The data object.</param>
  CityPageJSON(cityPageData)
  {
    // Save a reference to this class for anonymous function.
    const self = this;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "LJCCityList.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onload = function ()
    {
      // Get the AJAX response.
      alert(this.responseText);
      let response = JSON.parse(this.responseText);
      let keys = response.Keys;

      // Create new table element.
      dataDiv.innerHTML = response.HTMLTable;

      let rowIndex = self.SetCityTable(self, keys);
      let cityTable = self.CityTable;

      cityTable.SelectRow(rowIndex, rowIndex);
      self.SetCityValues()
      self.FocusTable = cityTable;
    };
    this.CityPageData.Limit = 10;
    xhr.send(JSON.stringify(cityPageData));
  }

  /// <summary>Sets the CityTable values.</summary>
  /// <param name="self">A reference to this class.</parm>
  /// <param name="keys">The key values.</parm>
  SetCityTable(self, keys)
  {
    let retRowIndex = -1;

    let cityTable = self.CityTable;
    if (cityTable != null)
    {
      retRowIndex = cityTable.RowIndex;
    }
    if (null == cityTable)
    {
      // Create initial CityTable object.
      self.CityTable = new LJCTable("dataTable", "menu");
      cityTable = self.CityTable;
    }

    // Reset table to new table element.
    cityTable.ETable = Common.Element("dataTable");

    cityTable.Keys = keys;
    return retRowIndex;
  }

  /// <summary>Send request for HTML Table page with POST data.</summary>
  /// <param name="data">The data object.</param>
  CityPagePost(data)
  {
    // Create data.
    let params = new URLSearchParams();
    params.append("configName", data.ConfigName);
    params.append("beginID", data.BeginID);
    params.append("endID", data.EndID);
    params.append("limit", data.Limit);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "LJCCityListPost.php");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function ()
    {
      dataDiv.innerHTML = this.response;
    };
    xhr.send(params.toString());
  }

  // ---------------
  // Menu Event Handlers

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Delete".
  /// </summary>
  DeleteClick()
  {
    this.DoAction("Delete");
  }

  /// <summary>Submit the hidden form listAction to CityDetail.php.</summary>
  /// <param name="action">The listAction type.</param>
  DoAction(action)
  {
    let success = true;

    // Get hidden form row ID.
    if ("" == Common.GetValue("rowID"))
    {
      // No selected row so do not allow delete or update.
      if ("Delete" == action || "Update" == action)
      {
        success = false;
      }
    }

    if (success)
    {
      // Set hidden form ProgramAction.
      let eListAction = Common.Element("listAction");
      if (eListAction != null)
      {
        eListAction.value = action;
      }

      // Submit the form.
      rowID.value++;
      let form = Common.Element("hiddenForm");
      form.submit();
    }
  }

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Update".
  /// </summary>
  EditClick()
  {
    this.DoAction("Update");
  }

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Add".
  /// </summary>
  NewClick()
  {
    this.DoAction("Add");
  }

  /// <summary>Refreshes the current page.</summary>
  Refresh()
  {
    //let rowIndex = this.RowIndex;
    //this.CityPageGet(this.CityPageData);
    this.CityPageJSON(this.CityPageData);
    //this.CityPagePost(this.CityPageData);
  }

  // ---------------
  // Table Column Methods

  /// <summary>Gets the selected table object.</summary>
  /// <param name="eColumn">The table column element.</param>
  SelectedTable(eColumn)
  {
    let retTable = null;

    let eTable = LJCTable.GetTable(eColumn);
    if (eTable != null)
    {
      switch (eTable.id)
      {
        case "dataTable":
          retTable = this.CityTable;
          break;
      }
    }
    return retTable;
  }

  // ---------------
  // Set Values Methods

  SetValues(ljcTable)
  {
    switch (ljcTable.ETable.id)
    {
      case "dataTable":
        this.SetCityValues();
        break;
    }
  }

  /// <summary>Sets the form values before a submit.</summary>
  /// <param name="eTarget">The HTML element.</param>
  SetCityValues()
  {
    let ljcTable = this.CityTable;
    if (ljcTable != null)
    {
      // Set selected row primaryKeys in hidden form.
      let ePrimaryKeys = Common.Element("primaryKeys");
      if (ePrimaryKeys != null)
      {
        let rowKeys = ljcTable.Keys[ljcTable.RowIndex - 1];
        if (rowKeys != null)
        {
          let keys = [];
          keys.push({ Name: "CityID", Value: rowKeys.CityID });
          let keysJSON = JSON.stringify(keys);
          ePrimaryKeys.value = keysJSON;
        }
      }

      if (this.CityPageData != null)
      {
        // Get first row.
        let rowKeys = ljcTable.Keys[0];
        if (rowKeys != null)
        {
          let CityPageData = this.CityPageData;
          CityPageData.BeginKeyData.ProvinceID = rowKeys.ProvinceID;
          CityPageData.BeginKeyData.Name = rowKeys.Name;
        }

        // Get last row.
        let keys = ljcTable.Keys;
        let lastIndex = keys.length - 1;
        rowKeys = ljcTable.Keys[lastIndex];
        if (rowKeys != null)
        {
          let CityPageData = this.CityPageData;
          CityPageData.EndKeyData.ProvinceID = rowKeys.ProvinceID;
          CityPageData.EndKeyData.Name = rowKeys.Name;
        }
      }
    }
  }
}