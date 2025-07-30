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
    this.CityRowIndex = -1;
    this.Data = {
      ConfigName: "TestData",
      BeginID: 1,
      EndID: 10,
      Limit: 0,
    };
    this.FocusTable = null;
    this.RowCount = 10;
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
      let ljcTable = this.SelectedTable(event.target);
      if (ljcTable != null)
      {
        ljcTable.SelectColumnRow(event.target);

        // Move to LJCTable?
        this.SaveRowData(event.target);
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
      let ljcTable = this.SelectedTable(event.target);
      if (ljcTable != null)
      {
        event.preventDefault();
        ljcTable.SelectColumnRow(event.target);

        // Move to LJCTable?
        this.SaveRowData(event.target);

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

    let eTable = null;
    switch (event.keyCode)
    {
      case ESCAPE_KEY:
        Common.Visibility("menu", "hidden");
        break;

      case UP_ARROW:
        if (this.FocusTable != null)
        {
          this.FocusTable.MovePrevious();
        }
        break;

      case DOWN_ARROW:
        if (this.FocusTable != null)
        {
          this.FocusTable.MoveNext();
        }
        break;
    }
  }

  // ---------------
  // Page Event Handlers

  /// <summary>Send request for HTML Table page with url data.</summary>
  /// <param name="data">The data object.</param>
  CityPageGet(data)
  {
    // Create data.
    let url = "LJCCityListGet.php";
    url += `?configName=${data.ConfigName}`;
    url += `&beginID=${data.BeginID}`;
    url += `&endID=${data.EndID}`;
    url += `&rowCount=${this.RowCount}`;

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
  CityPageJSON(data)
  {
    const self = this;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "LJCCityList.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onload = function ()
    {
      // Creates a new table element.
      let response = JSON.parse(this.responseText);
      let keys = response.Keys;
      dataDiv.innerHTML = response.HTMLTable;

      if (null == self.CityTable)
      {
        self.CityTable = new LJCTable("dataTable", "menu");
      }

      // Reset table to new table element.
      self.CityTable.ETable = Common.Element("dataTable");

      // Saved previous row index.
      let rowIndex = self.CityRowIndex;
      self.CityTable.SelectRow(rowIndex, rowIndex);
      self.FocusTable = self.CityTable;
    };
    this.Data.Limit = 10;
    xhr.send(JSON.stringify(data));
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
    params.append("rowCount", data.RowCount);

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
    //this.CityPageGet(this.Data);
    this.CityPageJSON(this.Data);
    //this.CityPagePost(this.Data);
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

  /// <summary>Highlight the selected row.</summary>
  /// <param name="eColumn">The table column element.</param>
  SaveRowData(eColumn)
  {
    let ljcTable = this.SelectedTable(eColumn);
    if (ljcTable != null)
    {
      let eTableRow = LJCTable.GetTableRow(eColumn);
      if (eTableRow != null)
      {
        // Save current row index.
        switch (ljcTable.TableID)
        {
          case "dataTable":
            this.CityRowIndex = eTableRow.rowIndex;
            break;
        }
      }

      // Set values for click on a table column.
      this.SetRowFormValues(eColumn);
    }
  }

  // ---------------
  // Other Methods

  /// <summary>Sets the form values before a submit.</summary>
  /// <param name="eTarget">The HTML element.</param>
  SetRowFormValues(eColumn)
  {
    let ljcTable = this.SelectedTable(eColumn);
    if (ljcTable != null)
    {
      // Set HTML table row index in hidden form.
      let eRowIndex = Common.Element("rowIndex");
      if (eRowIndex != null)
      {
        // Set hidden form value with current value.
        eRowIndex.value = ljcTable.RowIndex;
      }

      // Set HTML Table row ID from hidden form.
      let eRowID = Common.Element("rowID");
      if (eRowID != null)
      {
        let eRow = LJCTable.GetTableRow(eColumn);
        if (eRow != null)
        {
          // Set hidden form value.
          rowID.value = eRow.cells[0].innerHTML;
        }
      }
    }
  }
}