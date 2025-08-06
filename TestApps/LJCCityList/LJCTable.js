"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCTable.js
// <script src="../../Common/Common.js"></script>
//   TagElements()

// ***************
/// <summary>Contains HTML Table methods.</summary>
//   Static: GetTable(), GetTableRow()
//   Methods: ShowMenu()
//   Table Methods: GetCellText(), GetColumnIndex(), GetRow()
//     MoveNext(), MovePrevious(), RowCount(), SelectRow()
//   Selected Column: GetTableByID(), SelectColumnRow()
class LJCTable
{
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
  constructor(tableID, menuID)
  {
    this.BackColor = "";
    this.HighlightColor = "lightsteelblue";

    this.EMenu = null;
    if (menuID != null)
    {
      let eMenu = Common.Element(menuID);
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
      let eTable = Common.Element(this.TableID);
      if (eTable != null
        && "TABLE" == eTable.tagName)
      {
        this.ETable = eTable;
      }
    }

    this.EndOfData = true;
    this.BeginningOfData = true;
    this.Keys = [];
    this.RowIndex = -1;
  }

  // ---------------
  // Methods

  /// <summary>Highlight the selected row.</summary>
  /// <param name="eColumn">The table column element.</param>
  ShowMenu(location)
  {
    if (this.EMenu != null)
    {
      this.EMenu.style.top = `${location.Top}px`;
      this.EMenu.style.left = `${location.Left}px`;
      Common.Visibility(this.EMenu.id, "visible");
    }
  }

  // ---------------
  // Table Methods

  // Get cell index with heading text.
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

  // Get cell text with heading text
  GetCellText(headingText, rowIndex = -1)
  {
    let retText = "";

    let cellIndex = this.GetColumnIndex(headingText);
    if (cellIndex > -1)
    {
      if (-1 == rowIndex)
      {
        rowIndex = this.RowIndex;
      }
      let eRow = this.GetRow(rowIndex);
      let eCells = eRow.cells;
      retText = eCells[cellIndex].innerText;
    }
    return retText;
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
        rowIndex = this.RowIndex;
      }
      retRow = Common.TagElements(this.ETable, "TR")[rowIndex];
    }
    return retRow;
  }

  // Move selection to next row.
  MoveNext()
  {
    let retNextPage = false;

    if (this.ETable != null)
    {
      if (-1 == this.RowIndex)
      {
        // Default to top row.
        this.RowIndex = 0;
      }

      let rowCount = this.RowCount();

      // Index already at bottom so load page.
      if (this.RowIndex == rowCount - 1)
      {
        retNextPage = true;
      }

      if (!retNextPage)
      {
        // Not at bottom so increment row.
        if (this.RowIndex < rowCount - 1)
        {
          let prevRowIndex = this.RowIndex;
          this.RowIndex++;
          let rowIndex = this.RowIndex;
          this.SelectRow(prevRowIndex, rowIndex);
        }
      }
    }
    return retNextPage;
  }

  // Move selection to previous row.
  MovePrevious()
  {
    let retPrevPage = false;

    if (this.ETable != null)
    {
      if (-1 == this.RowIndex)
      {
        // Default to first data row.
        this.RowIndex = 1;
      }

      // Index already at first data row so load page.
      if (this.RowIndex == 1)
      {
        retPrevPage = true;
      }


      if (!retPrevPage)
      {
        // Not at first data row so increment row.
        if (this.RowIndex > 1)
        {
          let prevRowIndex = this.RowIndex;
          this.RowIndex--;
          let rowIndex = this.RowIndex;
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
      let eRows = Common.TagElements(this.ETable, "TR");
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
        ePrevRow.style.backgroundColor = this.BackColor;
      }

      let eTableRow = this.GetRow(rowIndex);
      if (eTableRow != null)
      {
        this.RowIndex = rowIndex;
        eTableRow.style.backgroundColor = this.HighlightColor;
      }
    }
  }

  // ---------------
  // Selected Column Methods

  // Get the HTML table element by ID if the HTML element is a table column.
  /// <include path='items/GetTableByID/*' file='Doc/LJCTable.xml'/>
  GetTableByID(eColumn, tableID)
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
  /// <include path='items/SelectColumnRow/*' file='Doc/LJCTable.xml'/>
  SelectColumnRow(eColumn)
  {
    if (this.ETable != null)
    {
      let eTableRow = LJCTable.GetTableRow(eColumn);
      if (eTableRow != null)
      {
        let prevIndex = this.RowIndex;
        this.RowIndex = eTableRow.rowIndex;
        this.SelectRow(prevIndex, this.RowIndex);
      }
    }
  }
}