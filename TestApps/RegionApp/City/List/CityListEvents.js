"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// CityListEvents.js

// ***************
/// <summary>Contains CityList event handlers.</summary>
class CityListEvents
{
  // The Constructor function.
  constructor()
  {
    this.CityRowIndex = 0;
  }

  /// <summary>Adds the HTML event handlers.</summary>
  AddEvents()
  {
    // Document Event Handlers.
    document.addEventListener("click", this.DocumentClick.bind(this));
    document.addEventListener("dblclick", this.DocumentDoubleClick.bind(this));
    document.addEventListener("contextmenu", this.DocumentContextMenu.bind(this));

    // Menu Event Handlers.
    this.AddEvent("new", "click", this.NewClick);
    this.AddEvent("edit", "click", this.EditClick);
    this.AddEvent("delete", "click", this.DeleteClick);
  }

  /// <summary>Adds an event handler.</summary>
  /// <param name="elementID"></param>
  /// <param name="evantName"></param>
  /// <param name="handler"></param>
  AddEvent(elementID, eventName, handler)
  {
    let element = LJC.Element(elementID);
    if (element != null)
    {
      element.addEventListener(eventName, handler.bind(this));
    }
  }

  // ---------------
  // Document Event Handlers

  /// <summary>Document "click" handler method.</summary>
  /// <param name="event">The Target event.</param>
  /// <remarks>
  ///   Selects the current row.
  /// </remarks>
  DocumentClick(event)
  {
    this.SetVisibility("menu", "hidden");
    let srcElement = event.target;
    let table = this.GetTableByID(srcElement, "cityTable");
    if (table != null)
    {
      this.SelectRow(srcElement);
    }
  }

  /// <summary>Displays the context menu.</summary>
  /// <param name="event">The Target event.</param>
  DocumentContextMenu(event)
  {
    let srcElement = event.target;
    let table = this.GetTableByID(srcElement, "cityTable");
    if (table != null)
    {
      event.preventDefault();

      this.SelectRow(srcElement);
      let menu = LJC.Element("menu");
      if (menu != null)
      {
        menu.style.top = `${event.pageY}px`;
        menu.style.left = `${event.pageX}px`;
        this.SetVisibility("menu", "visible");
      }
    }
  }

  /// <summary>Document "dblclick" handler method.</summary>
  /// <param name="event">The Target event.</param>
  DocumentDoubleClick(event)
  {
    this.EditClick(event);
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

  // ---------------
  // Methods

  /// <summary>Submit the hidden form listAction to CityDetail.php.</summary>
  /// <param name="action">The listAction type.</param>
  DoAction(action)
  {
    let success = true;

    // No selected row so do not allow delete or update.
    if ("" == LJC.GetValue("cityID"))
    {
      if ("Delete" == action || "Update" == action)
      {
        success = false;
      }
    }

    if (success)
    {
      // Set form ProgramAction.
      let eListAction = LJC.Element("listAction");
      if (eListAction != null)
      {
        eListAction.value = action;
      }

      // Submit the form.
      let form = LJC.Element("hiddenForm");
      form.submit();
    }
  }

  /// <summary>
  ///   Get the HTML table element if the HTML element is a table data row or
  ///		column.
  /// </summary >
  /// <param name="element">The HTML element.</param>
  /// <returns>The table element if applicable, otherwise null.</returns>
  GetTable(element)
  {
    let retValue = null;

    // Process Table
    let tableRow = this.GetTableRow(element);
    if (tableRow != null)
    {
      // table/tbody/tr
      retValue = tableRow.parentElement.parentElement;
    }
    return retValue;
  }

  /// <summary>
  ///   Get the HTML table element by ID if the HTML element is a table column.
  /// </summary >
  /// <param name="element">The HTML element.</param>
  /// <param name="tableID">The table element ID.</param>
  /// <returns>The table element if applicable, otherwise null.</returns>
  GetTableByID(element, tableID)
  {
    let retValue = null;

    let table = this.GetTable(element);
    if (table != null)
    {
      if (tableID == table.id)
      {
        retValue = table;
      }
    }
    return retValue;
  }

  /// <summary>
  ///		Get the table row element if the element is a table data row or column.
  /// </summary >
  /// <param name="element">The HTML element.</param>
  /// <returns>The table row element if applicable, otherwise null.</returns>
  GetTableRow(element)
  {
    let retValue = null;

    let tableRow = element.parentElement;
    if ("TD" == element.tagName)
    {
      retValue = tableRow;
    }
    return retValue;
  }

  /// <summary>Highlight the selected row.</summary>
  /// <param name="srcElement">The HTML element.</param>
  SelectRow(srcElement)
  {
    let table = this.GetTableByID(srcElement, "cityTable");
    if (table != null)
    {
      // Clear highlight from previous row.
      let prevRow = LJC.TagElements(table, "TR")[this.CityRowIndex];
      prevRow.style.backgroundColor = "";

      // Highlight current row.
      let tableRow = this.GetTableRow(srcElement);
      if (tableRow != null)
      {
        this.CityRowIndex = tableRow.rowIndex;
        tableRow.style.backgroundColor = "lightsteelblue";
      }

      // Set values for click on a table column.
      this.SetRowFormValues(srcElement);
    }
  }

  /// <summary>Sets the form values before a submit.</summary>
  /// <param name="srcElement">The HTML element.</param>
  SetRowFormValues(srcElement)
  {
    let table = this.GetTableByID(srcElement, "cityTable");
    if (table != null)
    {
      // Set HTML table row index.
      let cityRowIndex = LJC.Element("cityRowIndex");
      if (cityRowIndex != null)
      {
        cityRowIndex.value = this.CityRowIndex;
      }

      // Set HTML Table row CityID.
      let cityID = LJC.Element("cityID");
      if (cityID != null)
      {
        let row = this.GetTableRow(srcElement);
        if (row != null)
        {
          cityID.value = row.cells[0].innerHTML;
        }
      }
    }
  }

  /// <summary>Sets the element visibility.</summary>
  /// <param name="elementID">The element ID.</param>
  /// <param name="value">"visible" to show the element, "hidden" to hide.</param>
  SetVisibility(elementID, value)
  {
    let element = LJC.Element(elementID);
    if (element != null)
    {
      element.style.visibility = value;
    }
  }
}