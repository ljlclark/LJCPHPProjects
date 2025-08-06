"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityListEvents.js
// <script src="../../Common/Common.js"></script>
//   Element(), GetValue()
//   MouseLocation(), Visibility()
// <script src="LJCTable.js"></script>
//   GetTable(), GetTableRow(), ShowMenu()
//   MoveNext(), MovePrevious(), RowCount(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains CityList event handlers.</summary>
//  Add Events: AddEvents(), AddEvent()
//  Document Handlers: DocumentClick(), DocumentContextMenu()
//    DocumentDoubleClick(), DocumentKeyDown()
//  Page Handlers: NextPage(), PrevPage(), CityPage(), UpdateCityTable()
//  Menu Handlers: Delete(), DoAction(), Edit(), New(), Refresh()
//  Table Column: SelectedTable()
//  Page Data: UpdatePageData(), UpdateCityPageData()
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
      BeginningOfData: false,
      ConfigName: "TestData",
      EndKeyData: { ProvinceID: 0, Name: "" },
      EndOfData: false,
      Limit: 10,
    };
    this.FocusTable = null;
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
    this.AddEvent("delete", "click", this.Delete);
    this.AddEvent("edit", "click", this.Edit);
    this.AddEvent("new", "click", this.New);
    this.AddEvent("refresh", "click", this.Refresh);
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
        this.UpdatePageData(ljcTable);
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
        this.UpdatePageData(ljcTable);

        let location = Common.MouseLocation(event);
        ljcTable.ShowMenu(location);
      }
    }
  }

  /// <summary>Document "dblclick" handler method.</summary>
  DocumentDoubleClick()
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

  /// <summary>Get next page for supplied table.
  /// <param name="ljcTable">The target table.</param>
  NextPage(ljcTable)
  {
    if (ljcTable != null)
    {
      switch (ljcTable.ETable.id)
      {
        case "dataTable":
          if (!this.CityPageData.EndOfData)
          {
            this.CityPageData.Action = "Next";
            this.UpdateCityPageData();

            this.CityPage(this.CityPageData);
            // *** Begin ***
            this.CityTable.EndOfData = false;
            if (this.CityTable.Keys.length < this.CityPageData.Length)
            {
              this.CityTable.EndOfData = true;
            }
            // *** End   ***


            // Assume previous was table row count.
            let rowCount = this.CityTable.RowCount();

            // Select first data row.
            this.CityTable.SelectRow(rowCount, 1);
          }
          break;
      }
    }
  }

  /// <summary>Get previous page for supplied table.
  /// <param name="ljcTable">The target table.</param>
  PrevPage(ljcTable)
  {
    if (ljcTable != null)
    {
      switch (ljcTable.ETable.id)
      {
        case "dataTable":
          this.CityPageData.Action = "Previous";
          this.UpdateCityPageData();

          this.CityPage(this.CityPageData);
          // *** Begin ***
          this.CityTable.BeginningOfData = false;
          this.CityTable.EndOfData = false;
          if (this.CityTable.Keys.length < this.CityPageData.Length)
          {
            this.CityTable.BeginningOfData = true;
            this.CityTable.EndOfData = true;
          }
          // *** End   ***
          break;
      }
    }
  }

  /// <summary>Send request for City Table page.</summary>
  /// <param name="data">The data object.</param>
  // Called from NextPage(), PrevPage() and Refesh().
  CityPage(cityPageData)
  {
    // Save a reference to this class for anonymous function.
    const saveThis = this;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "LJCCityList.php");
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.onload = function ()
    {
      // Get the AJAX response.
      let response = JSON.parse(this.responseText);
      let keys = response.Keys;

      // Create new table element.
      dataDiv.innerHTML = response.HTMLTable;

      // Creates initial CityTable
      // or Updates with new table element and keys.
      let rowIndex = saveThis.UpdateCityTable(saveThis, keys);
      let cityTable = saveThis.CityTable;

      // Select former row.
      cityTable.SelectRow(rowIndex, rowIndex);

      // Set hidden form primary keys and CityPageData.
      saveThis.UpdateCityPageData()
      saveThis.FocusTable = cityTable;
    };
    this.CityPageData.Limit = 10;
    xhr.send(JSON.stringify(cityPageData));
  }

  /// <summary>Sets the CityTable values.</summary>
  /// <param name="saveThis">A reference to this class.</parm>
  /// <param name="keys">The key values.</parm>
  UpdateCityTable(saveThis, keys)
  {
    let retRowIndex = -1;

    let cityTable = saveThis.CityTable;
    if (cityTable != null)
    {
      // Return existing row index.
      retRowIndex = cityTable.RowIndex;
    }
    if (null == cityTable)
    {
      // Create initial CityTable object.
      saveThis.CityTable = new LJCTable("dataTable", "menu");
      cityTable = saveThis.CityTable;
    }

    // Reset table to new table element.
    cityTable.ETable = Common.Element("dataTable");

    cityTable.Keys = keys;
    return retRowIndex;
  }

  // ---------------
  // Menu Event Handlers

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Delete".
  /// </summary>
  Delete()
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
  Edit()
  {
    this.DoAction("Update");
  }

  /// <summary>
  ///   Submit "hiddenForm" to CityDetail.php with listAction "Add".
  /// </summary>
  New()
  {
    this.DoAction("Add");
  }

  /// <summary>Refreshes the current page.</summary>
  Refresh()
  {
    this.CityPageData.Action = "Refresh";
    this.CityPage(this.CityPageData);
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

  UpdatePageData(ljcTable)
  {
    switch (ljcTable.ETable.id)
    {
      case "dataTable":
        this.UpdateCityPageData();
        break;
    }
  }

  /// <summary>Sets the form values before a submit.</summary>
  /// <param name="eTarget">The HTML element.</param>
  UpdateCityPageData()
  {
    let ljcTable = this.CityTable;
    if (ljcTable != null)
    {
      // Set selected row primaryKeys in hidden form for detail dialog.
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