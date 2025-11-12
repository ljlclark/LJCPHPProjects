"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCTable.js

// #region External
// <script src="../../LJCJSCommon/LJCCommonLib.js"></script>
//   LJC: Element(), TagElements(), Visibility()
// <script src="../../LJCJSCommon/LJCDataLib.js"></script>
//   LJCDataColumns: Count(), GetIndex()
// #endregion

/// <summary>The Table Helper Class</summary>
/// LibName: LJCTable

// ***************
// Represents an HTML Table.
/// <include path='items/LJCTable/*' file='Doc/LJCTable.xml'/>
class LJCTable
{
  // #region Properties

  /// <summary>The current row index.</summary>
  /// <remarks>
  ///   The value must be updated after a new table page has been generated.
  /// </remarks>
  CurrentRowIndex = 0;

  /// <summary>The associated menu element ID.</summary>
  MenuID = "";

  /// <summary>The unselected row background color.</summary>
  RowBackColor = "";

  /// <summary>The selected row background color.</summary>
  RowSelectedColor = "";

  /// <summary>The table data column definitions.</summary>
  TableColumns = new LJCDataColumns();

  /// <summary>The associated table element ID.</summary>
  TableID = "";
  // #endregion

  // #region Paging Properties

  /// <summary>Indicates if paging is at the beginning of the data.</summary>
  /// <remarks>
  ///   This value must be updated after a new table page has been generated.
  /// </remarks>
  BeginningOfData;

  /// <summary>Indicates if paging is at the end of the data.</summary>
  /// <remarks>
  ///   This value must be updated after a new table page has been generated.
  /// </remarks>
  EndOfData;

  /// <summary>The current page keys.</summary>
  /// <remarks>
  ///   This value must be updated after a new table page has been generated.
  /// </remarks>
  Keys;

  /// <summary>The data object unique property names.</summary>
  UniqueProperties = [];
  // #endregion

  // #region Static Methods

  // Gets HTML table element if the supplied element is a table cell and
  // the table has the supplied ID.
  /// <include path='items/GetSelectedTable/*' file='Doc/LJCTable.xml'/>
  static GetSelectedTable(eCell, tableID)
  {
    let retTable = null;

    let eTable = LJCTable.GetTable(eCell);
    if (eTable != null)
    {
      if (tableID == eTable.id)
      {
        retTable = eTable;
      }
    }
    return retTable;
  }

  // Gets the HTML table element if the supplied element is a table cell.
  /// <include path='items/GetTable/*' file='Doc/LJCTable.xml'/>
  static GetTable(eCell)
  {
    let retTable = null;

    // Process Table
    const eRow = LJCTable.GetTableRow(eCell);
    if (eRow != null)
    {
      // table/tbody/tr
      retTable = eRow.parentElement.parentElement;
    }
    return retTable;
  }

  // Gets the table row element if the suppliled element is a table cell.
  /// <include path='items/GetTableRow/*' file='Doc/LJCTable.xml'/>
  static GetTableRow(eCell)
  {
    let retRow = null;

    const eRow = eCell.parentElement;
    if ("TD" == eCell.tagName)
    {
      retRow = eRow;
    }
    return retRow;
  }
  // #endregion

  // #region Constructor methods.

  // Initializes the object.
  /// <include path='items/constructor/*' file='Doc/LJCTable.xml'/>
  constructor(tableID, menuID)
  {
    this.BeginningOfData = true;
    this.CurrentRowIndex = -1;
    this.EndOfData = false;
    this.Keys = [];

    if (menuID != null)
    {
      const eMenu = LJC.Element(menuID);
      if (eMenu != null
        && "DIV" == eMenu.tagName)
      {
        this.MenuID = menuID;
      }
    }

    this.RowSelectedColor = "lightsteelblue";

    if (tableID != null)
    {
      const eTable = LJC.Element(tableID);
      if (eTable != null
        && "TABLE" == eTable.tagName)
      {
        this.TableID = tableID;
      }
    }
  }
  // #endregion

  // #region Methods

  // Makes the menu visible.
  /// <include path='items/ShowMenu/*' file='Doc/LJCTable.xml'/>
  ShowMenu(location)
  {
    const eMenu = LJC.Element(this.MenuID);
    if (eMenu != null)
    {
      eMenu.style.top = `${location.Top}px`;
      eMenu.style.left = `${location.Left}px`;
      LJC.Visibility(eMenu.id, "visible");
    }
  }
  // #endregion

  // #region Cell Methods

  // Gets cell text by property name or heading text.
  /// <include path='items/CellText/*' file='Doc/LJCTable.xml'/>
  CellText(propertyName, rowIndex = -1)
  {
    let retText = "";

    const cellIndex = this.ColumnIndex(propertyName);
    if (cellIndex > -1)
    {
      if (-1 == rowIndex)
      {
        rowIndex = this.CurrentRowIndex;
      }
      const eRow = this.GetRow(rowIndex);
      const eCells = eRow.cells;
      retText = eCells[cellIndex].innerText;
    }
    return retText;
  }

  // Gets the column index by property name or heading text.
  /// <include path='items/ColumnIndex/*' file='Doc/LJCTable.xml'/>
  ColumnIndex(propertyName)
  {
    let retIndex = -1;

    if (this.TableColumns != null
      && this.TableColumns.Count > 0)
    {
      retIndex = this.TableColumns.GetIndex(propertyName);
    }
    else
    {
      retIndex = this.HeadingIndex(propertyName);
    }
    return retIndex;
  }

  // Gets the column width.
  /// <include path='items/ColumnWidth/*' file='Doc/LJCTable.xml'/>
  ColumnWidth(columnIndex)
  {
    let retWidth = 0;

    if (this.HasColumnIndex(columnIndex))
    {
      const eRow = this.GetRow(0);
      const eCell = eRow.cells[columnIndex];
      retWidth = LJC.ElementStyle(eCell, "width");
    }
    return retWidth;
  }

  // Gets column index by the heading text.
  /// <include path='items/HeadingIndex/*' file='Doc/LJCTable.xml'/>
  HeadingIndex(headingText)
  {
    let retIndex = -1;

    const eHead = this.GetRow(0);
    if (eHead != null)
    {
      const eCells = eHead.cells;
      for (let index = 0; index < eCells.length; index++)
      {
        const eCell = eCells[index];
        if (eCell.innerText == headingText)
        {
          retIndex = index;
          index = eCells.length;
          break;
        }
      }
    }
    return retIndex;
  }

  // Sets the row[0] cell width by the column index value.
  /// <include path='items/SetColumnWidth/*' file='Doc/LJCTable.xml'/>
  SetColumnWidth(columnIndex, width)
  {
    if (this.HasColumnIndex(columnIndex))
    {
      const row = this.GetRow(0);
      let cell = row.cells[columnIndex];
      cell.style.width = width;
    }
  }

  // Sets the row[0] cell width by the column property name.
  /// <include path='items/SetColumnWidthByName/*' file='Doc/LJCTable.xml'/>
  SetColumnWidthByName(propertyName, width)
  {
    const columnIndex = this.ColumnIndex(propertyName);
    if (this.HasColumnIndex(columnIndex))
    {
      this.SetColumnWidth(columnIndex, width);
    }
  }

  // Checks if the column index exists.
  /// <include path='items/HasColumnIndex/*' file='Doc/LJCTable.xml'/>
  HasColumnIndex(columnIndex)
  {
    let retValue = false;

    if (this.RowCount() > 0)
    {
      const eRow = this.GetRow(0);
      const cellCount = eRow.cells.length;
      if (cellCount > 0
        && columnIndex >= 0
        && columnIndex < cellCount)
      {
        retValue = true;
      }
    }
    return retValue;
  }
  // #endregion

  // #region Row Methods

  // Gets the table row element by index.
  /// <include path='items/GetRow/*' file='Doc/LJCTable.xml'/>
  GetRow(rowIndex = -1)
  {
    let retRow = null;

    const eTable = LJC.Element(this.TableID);
    if (eTable != null)
    {
      if (-1 == rowIndex)
      {
        rowIndex = this.CurrentRowIndex;
      }
      const eRows = LJC.TagElements(eTable, "TR");
      if (eRows != null)
      {
        retRow = eRows[rowIndex];
      }
    }
    return retRow;
  }

  // Gets the table row count.
  /// <include path='items/GetRowCount/*' file='Doc/LJCTable.xml'/>
  RowCount()
  {
    let retCount = 0;

    const eTable = LJC.Element(this.TableID);
    if (eTable != null)
    {
      const eRows = LJC.TagElements(eTable, "TR");
      if (eRows != null)
      {
        retCount = eRows.length;
      }
    }
    return retCount;
  }

  // Gets the row where the matching cell has the search text.
  /// <include path='items/RowMatch/*' file='Doc/LJCTable.xml'/>
  RowMatch(propertyName, searchText)
  {
    let retRow = null;

    const cellIndex = this.ColumnIndex(propertyName, 0);
    if (cellIndex > -1)
    {
      const eTable = LJC.Element(this.TableID);
      const eRows = LJC.TagElements(eTable, "TR");
      for (let rowIndex = 1; rowIndex < eRows.length; rowIndex++)
      {
        const eRow = eRows[rowIndex];
        const eCell = eRow.cells[cellIndex];
        const cellText = eCell.innerText;
        if (cellText == searchText)
        {
          retRow = eRow;
          break;
        }
      }
    }
    return retRow;
  }

  // Clears background for previous row and Highlights the current row
  // if the supplied element is a table cell.
  /// <include path='items/SelectColumnRow/*' file='Doc/LJCTable.xml'/>
  SelectColumnRow(eCell)
  {
    const eTableRow = LJCTable.GetTableRow(eCell);
    if (eTableRow != null)
    {
      const prevIndex = this.CurrentRowIndex;
      this.CurrentRowIndex = eTableRow.rowIndex;
      this.SelectRow(prevIndex, this.CurrentRowIndex);
    }
  }

  // Clears background for previous row and Highlights the current row.
  /// <include path='items/SelectRow/*' file='Doc/LJCTable.xml'/>
  SelectRow(prevRowIndex, rowIndex)
  {
    const ePrevRow = this.GetRow(prevRowIndex);
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

  /// <summary>
  ///   Gets the row index by unique key values.
  /// <include path='items/UniqueRowIndex/*' file='Doc/LJCTable.xml'/>
  UniqueRowIndex(dataObject)
  {
    let retIndex = -1;

    if (Array.isArray(this.UniqueProperties)
      && this.UniqueProperties.length > 0)
    {
      for (let keyIndex = 0; keyIndex < this.Keys.length; keyIndex++)
      {
        let keyMatch = true;
        const key = this.Keys[keyIndex];
        for (const propertyName of this.UniqueProperties)
        {
          if (key[propertyName] != dataObject[propertyName])
          {
            keyMatch = false;
            break;
          }
        }

        if (keyMatch)
        {
          // Skip heading row.
          retIndex = keyIndex + 1;
          break;
        }
      }
    }
    return retIndex;
  }

  /// <summary>
  ///   Updates the row for data object unique keys.
  /// </summary>
  /// <param name="dataObject">The data object.</param>
  UpdateUniqueRow(dataObject)
  {
    const rowIndex = this.UniqueRowIndex(dataObject);
    if (rowIndex > -1)
    {
      const eRow = this.GetRow(rowIndex);
      const cells = eRow.cells;
      for (const propertyName in dataObject)
      {
        // Headings same as property name.
        const cellIndex = this.ColumnIndex(propertyName);
        if (cellIndex > -1)
        {
          cells[cellIndex].innerText = dataObject[propertyName];
        }
      }
    }
  }
  // #endregion

  // #region Paging Methods

  /// <summary>
  ///   Moves selection to next row.
  /// </summary>
  /// <returns>
  ///   false if selection was moved to the next item; otherwise true for next
  ///   page.
  /// </returns>
  /// <remarks>
  ///   Moves the selection to the next row if available and increments
  ///   CurrentRowIndex.
  /// </remarks>
  MoveNext()
  {
    let retNextPage = false;

    if (-1 == this.CurrentRowIndex)
    {
      // Default to top row.
      this.CurrentRowIndex = 0;
    }

    const rowCount = this.RowCount();

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
        const prevRowIndex = this.CurrentRowIndex;
        this.CurrentRowIndex++;
        const rowIndex = this.CurrentRowIndex;
        this.SelectRow(prevRowIndex, rowIndex);
      }
    }
    return retNextPage;
  }

  /// <summary>
  ///   Moves selection to previous row.
  /// </summary>
  /// <returns>
  ///   false if selection was moved to the previous item; otherwise true for
  ///   previous page.
  /// </returns>
  /// <remarks>
  ///   Moves the selection to the previous row if available and decrements
  ///   CurrentRowIndex.
  /// </remarks>
  MovePrevious()
  {
    let retPrevPage = false;

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
        const prevRowIndex = this.CurrentRowIndex;
        this.CurrentRowIndex--;
        const rowIndex = this.CurrentRowIndex;
        this.SelectRow(prevRowIndex, rowIndex);
      }
    }
    return retPrevPage;
  }
  // #endregion
}