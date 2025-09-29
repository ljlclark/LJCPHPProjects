"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCTable.js
// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   Element(), TagElements(), Visibility()
// <script src="../../LJCJSCommon/LJCDataLib.js"></script>
//   LJCDataColumn, LJCDataColumns

/// <summary>The Table Helper Class</summary>
/// LibName: LJCTable
//  Classes: LJCTable

// ***************
/// <summary>Represents an HTML Table.</summary>
/// <remarks>
///   Contains methods for utilizing an HTML Table and associated context menu
//    including data paging.
/// </remarks>
//  Static: GetTable(), GetTableRow()
//  Methods: ShowMenu()
//  Table Methods: CellText(), HeadingIndex(), GetRow()
//    MoveNext(), MovePrevious(), RowCount(), SelectRow()
//  Selected Column: IsSelectedTable(), SelectColumnRow()
class LJCTable
{
  // ---------------
  // Properties

  /// <summary>Indicates if paging is at the beginning of the data.</summary>
  BeginningOfData;

  /// <summary>The current row index.</summary>
  CurrentRowIndex;

  /// <summary>The table data column definitions.</summary>
  DataColumns = new LJCDataColumns();

  /// <summary>The associated menu element.</summary>
  EMenu;

  /// <summary>Indicates if paging is at the end of the data.</summary>
  EndOfData;

  /// <summary>The associated table element.</summary>
  ETable;

  /// <summary>The current page primary keys.</summary>
  Keys;

  /// <summary>The unselected row background color.</summary>
  RowBackColor;

  /// <summary>The selected row background color.</summary>
  RowSelectedColor;

  /// <summary>The associated table element ID name.</summary>
  TableID;

  // ---------------
  // Static Methods

  // Get the HTML table element if the HTML element is table data row or column.
  /// <include path='items/GetTable/*' file='Doc/LJCTable.xml'/>
  static GetTable(eCell)
  {
    let retValue = null;

    // Process Table
    let tableRow = LJCTable.GetTableRow(eCell);
    if (tableRow != null)
    {
      // table/tbody/tr
      retValue = tableRow.parentElement.parentElement;
    }
    return retValue;
  }

  // Get the table row element if the element is a table data row or column.
  /// <include path='items/GetTableRow/*' file='Doc/LJCTable.xml'/>
  static GetTableRow(eCell)
  {
    let retValue = null;

    let tableRow = eCell.parentElement;
    if ("TD" == eCell.tagName)
    {
      retValue = tableRow;
    }
    return retValue;
  }

  // ---------------
  // Constructor methods.

  // Initializes the object.
  /// <include path='items/constructor/*' file='Doc/LJCTable.xml'/>
  constructor(tableID, menuID)
  {
    this.RowBackColor = "";
    this.BeginningOfData = true;
    this.CurrentRowIndex = -1;
    this.EndOfData = false;

    this.EMenu = null;
    if (menuID != null)
    {
      let eMenu = LJC.Element(menuID);
      if (eMenu != null
        && "DIV" == eMenu.tagName)
      {
        this.EMenu = eMenu;
      }
    }

    this.ETable = null;
    this.TableID = tableID;
    if (this.TableID != null)
    {
      let eTable = LJC.Element(this.TableID);
      if (eTable != null
        && "TABLE" == eTable.tagName)
      {
        this.ETable = eTable;
      }
    }

    this.RowSelectedColor = "lightsteelblue";
    this.Keys = [];
  }

  // ---------------
  // Methods

  /// <summary>Make the menu visible.</summary>
  /// <param name="location">The menu loction.</param>
  ShowMenu(location)
  {
    if (this.EMenu != null)
    {
      this.EMenu.style.top = `${location.Top}px`;
      this.EMenu.style.left = `${location.Left}px`;
      LJC.Visibility(this.EMenu.id, "visible");
    }
  }

  // ---------------
  // Cell by property name or heading text Methods

  // Get cell text with heading text.
  /// <include path='items/CellText/*' file='Doc/LJCTable.xml'/>
  CellText(propertyName, rowIndex = -1)
  {
    let retText = "";

    let cellIndex = this.ColumnIndex(propertyName);
    if (cellIndex > -1)
    {
      if (-1 == rowIndex)
      {
        rowIndex = this.CurrentRowIndex;
      }
      let eRow = this.GetRow(rowIndex);
      let eCells = eRow.cells;
      retText = eCells[cellIndex].innerText;
    }
    return retText;
  }

  // Get column index with heading text.
  /// <include path='items/GetColumnText/*' file='Doc/LJCTable.xml'/>
  HeadingIndex(headingText)
  {
    let retIndex = -1;

    let eHead = this.GetRow(0);
    let eCells = eHead.cells;
    for (let index = 0; index < eCells.length; index++)
    {
      let eCell = eCells[index];
      if (eCell.innerText == headingText)
      {
        retIndex = index;
        index = eCells.length;
        break;
      }
    }
    return retIndex;
  }

  /// <summary>Get row where cell has the search text.</summary>
  /// <returns>A row element object.</returns>
  RowMatch(propertyName, searchText)
  {
    let retRow = null;

    let cellIndex = this.ColumnIndex(propertyName, 0);
    if (cellIndex > -1)
    {
      let eRows = LJC.TagElements(this.ETable, "TR");
      for (let rowIndex = 1; rowIndex < eRows.length; rowIndex++)
      {
        let eRow = eRows[rowIndex];
        let eCell = eRow.cells[cellIndex];
        let cellText = eCell.innerText;
        if (cellText == searchText)
        {
          retRow = eRow;
          break;
        }
      }
    }
    return retRow;
  }

  // ---------------
  // Cell index by property name methods.

  /// <summary>Get column index with property name.
  ColumnIndex(propertyName)
  {
    let retIndex = -1;

    if (this.DataColumns != null
      && this.DataColumns.Count() > 0)
    {
      retIndex = this.DataColumns.GetIndex(propertyName);
    }
    else
    {
      retIndex = this.HeadingIndex(propertyName);
    }
    return retIndex;
  }

  // ---------------
  // Row by Unique Key Methods

  /// <summary>Get the row index by unique key values.</summary>
  UniqueRowIndex(objCity)
  {
    let retIndex = -1;

    for (let index = 0; index < this.Keys.length; index++)
    {
      let key = this.Keys[index];
      if (key.ProvinceID == objCity.ProvinceID
        && key.Name == objCity.Name)
      {
        // Skip heading row.
        retIndex = index + 1;
        break;
      }
    }
    return retIndex;
  }

  /// <summary>Update the row for data object unique keys.</summary>
  UpdateUniqueRow(objCity)
  {
    let rowIndex = this.UniqueRowIndex(objCity);
    if (rowIndex > -1)
    {
      let eRow = this.GetRow(rowIndex);
      let cells = eRow.cells;
      for (let propertyName in objCity)
      {
        // Headings same as property name.
        let cellIndex = this.ColumnIndex(propertyName);
        if (cellIndex > -1)
        {
          cells[cellIndex].innerText = objCity[propertyName];
        }
      }
    }
  }

  // ---------------
  // Other Methods

  // Gets table row by index.
  /// <include path='items/GetRow/*' file='Doc/LJCTable.xml'/>
  GetRow(rowIndex = -1)
  {
    let retRow = null;

    if (this.ETable != null)
    {
      if (-1 == rowIndex)
      {
        rowIndex = this.CurrentRowIndex;
      }
      let eRows = LJC.TagElements(this.ETable, "TR");
      if (eRows != null)
      {
        retRow = eRows[rowIndex];
      }
    }
    return retRow;
  }

  /// <summary>Move selection to next row.</summary>
  MoveNext()
  {
    let retNextPage = false;

    if (this.ETable != null)
    {
      if (-1 == this.CurrentRowIndex)
      {
        // Default to top row.
        this.CurrentRowIndex = 0;
      }

      let rowCount = this.RowCount();

      // Index already at bottom so load page.
      if (this.CurrentRowIndex == rowCount - 1)
      {
        retNextPage = true;
      }

      if (!retNextPage)
      {
        // Not at bottom so increment row.
        if (this.CurrentRowIndex < rowCount - 1)
        {
          let prevRowIndex = this.CurrentRowIndex;
          this.CurrentRowIndex++;
          let rowIndex = this.CurrentRowIndex;
          this.SelectRow(prevRowIndex, rowIndex);
        }
      }
    }
    return retNextPage;
  }

  /// <summary>Move selection to previous row.</summary>
  MovePrevious()
  {
    let retPrevPage = false;

    if (this.ETable != null)
    {
      if (-1 == this.CurrentRowIndex)
      {
        // Default to first data row.
        this.CurrentRowIndex = 1;
      }

      // Index already at first data row so load page.
      if (this.CurrentRowIndex == 1)
      {
        retPrevPage = true;
      }


      if (!retPrevPage)
      {
        // Not at first data row so decrement row.
        if (this.CurrentRowIndex > 1)
        {
          let prevRowIndex = this.CurrentRowIndex;
          this.CurrentRowIndex--;
          let rowIndex = this.CurrentRowIndex;
          this.SelectRow(prevRowIndex, rowIndex);
        }
      }
    }
    return retPrevPage;
  }

  // Gets the table row count.
  /// <include path='items/GetRowCount/*' file='Doc/LJCTable.xml'/>
  RowCount()
  {
    let retCount = 0;

    if (this.ETable != null)
    {
      let eRows = LJC.TagElements(this.ETable, "TR");
      if (eRows != null)
      {
        retCount = eRows.length;
      }
    }
    return retCount;
  }

  // Clears background for previous row and Highlights the current row.
  /// <include path='items/SelectRow/*' file='Doc/LJCTable.xml'/>
  SelectRow(prevRowIndex, rowIndex)
  {
    if (this.ETable != null)
    {
      let ePrevRow = this.GetRow(prevRowIndex);
      if (ePrevRow != null)
      {
        ePrevRow.style.backgroundColor = this.RowBackColor;
      }

      let eTableRow = this.GetRow(rowIndex);
      if (eTableRow != null)
      {
        this.CurrentRowIndex = rowIndex;
        eTableRow.style.backgroundColor = this.RowSelectedColor;
      }
    }
  }

  // ---------------
  // Selected Column Methods

  // Gets the selected table if the supplied element is a table cell and
  // the table has the supplied ID.
  /// <include path='items/GetTableByID/*' file='Doc/LJCTable.xml'/>
  GetSelectedTable(eCell, tableID)
  {
    let retValue = null;

    let table = LJCTable.GetTable(eCell);
    if (table != null)
    {
      if (tableID == table.id)
      {
        retValue = table;
      }
    }
    return retValue;
  }

  // Clears background for previous row and Highlights the current row.
  // if the supplied element is a table cell.
  /// <include path='items/SelectColumnRow/*' file='Doc/LJCTable.xml'/>
  SelectColumnRow(eCell)
  {
    if (this.ETable != null)
    {
      let eTableRow = LJCTable.GetTableRow(eCell);
      if (eTableRow != null)
      {
        let prevIndex = this.CurrentRowIndex;
        this.CurrentRowIndex = eTableRow.rowIndex;
        this.SelectRow(prevIndex, this.CurrentRowIndex);
      }
    }
  }
}