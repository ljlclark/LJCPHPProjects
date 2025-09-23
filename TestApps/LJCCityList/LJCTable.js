"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCTable.js
// <script src="../../LJCJSCommon/LJCJSCommonLib.js"></script>
//   Element(), TagElements(), Visibility()

// ***************
/// <summary>Represents an HTML Table.</summary>
/// <remarks>
///   Contains methods for utilizing an HTML Table and associated context menu
//    including data paging.
/// </remarks>
//   Static: GetTable(), GetTableRow()
//   Methods: ShowMenu()
//   Table Methods: GetCellText(), GetColumnIndex(), GetRow()
//     MoveNext(), MovePrevious(), RowCount(), SelectRow()
//   Selected Column: IsSelectedTable(), SelectColumnRow()
class LJCTable
{
  // ---------------
  // Properties

  /// <summary>Indicates if paging is at the beginning of the data.</summary>
  BeginningOfData;

  /// <summary>The current row index.</summary>
  CurrentRowIndex;

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
  static GetTable(eColumn)
  {
    let retValue = null;

    // Process Table
    let tableRow = LJCTable.GetTableRow(eColumn);
    if (tableRow != null)
    {
      // table/tbody/tr
      retValue = tableRow.parentElement.parentElement;
    }
    return retValue;
  }

  // Get the table row element if the element is a table data row or column.
  /// <include path='items/GetTableRow/*' file='Doc/LJCTable.xml'/>
  static GetTableRow(eColumn)
  {
    let retValue = null;

    let tableRow = eColumn.parentElement;
    if ("TD" == eColumn.tagName)
    {
      retValue = tableRow;
    }
    return retValue;
  }

  // ---------------
  // The Constructor methods.

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
  // Table Methods

  // Get cell text with heading text.
  /// <include path='items/GetCellText/*' file='Doc/LJCTable.xml'/>
  GetCellText(headingText, rowIndex = -1)
  {
    let retText = "";

    let cellIndex = this.GetColumnIndex(headingText);
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

  // Get cell index with heading text.
  /// <include path='items/GetColumnText/*' file='Doc/LJCTable.xml'/>
  GetColumnIndex(headingText)
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
      }
    }
    return retIndex;
  }

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
      retRow = LJC.TagElements(this.ETable, "TR")[rowIndex];
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

  // Gets the selected table if the supplied element is a table column/cell and
  // the table has the supplied ID.
  /// <include path='items/GetTableByID/*' file='Doc/LJCTable.xml'/>
  GetSelectedTable(eColumn, tableID)
  {
    let retValue = null;

    let table = LJCTable.GetTable(eColumn);
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
  SelectColumnRow(eColumn)
  {
    if (this.ETable != null)
    {
      let eTableRow = LJCTable.GetTableRow(eColumn);
      if (eTableRow != null)
      {
        let prevIndex = this.CurrentRowIndex;
        this.CurrentRowIndex = eTableRow.rowIndex;
        this.SelectRow(prevIndex, this.CurrentRowIndex);
      }
    }
  }
}