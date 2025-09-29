"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCDataLib.js

/// <summary>The Data Definition Library</summary>
/// LibName: LJCDataLib
//  Classes: LJCDataColumn, LJCDataColumns

// ***************
/// <summary>Represents a data column definition.</summary>
//  Static: GetDataType()
//  Data Object: Clone(), Copy()
class LJCDataColumn
{
  // ---------------
  // Properties

  AllowDbNull;
  AutoIncrement;
  Caption;
  ColumnName;
  DataTypeName;
  DefaultValue;
  MaxLength;
  MySQLTypeName;
  PropertyName;
  RenameAs;
  WhereBoolOperator;
  WhereCompareOperator;
  Value;

  // ---------------
  // Static Methods

  // Creates a new object with existing standard object values.
  /// <include path='items/Copy/*' file='Doc/LJCDataColumn.xml'/>
  static Copy(dataColumn)
  {
    let retDataColumn = new LJCDataColumn();

    for (let propertyName in dataColumn)
    {
      if (propertyName in retDataColumn)
      {
        retDataColumn[propertyName] = dataColumn[propertyName];
      }
    }
    return retDataColumn;
  }

  // Converts MySQL type names to JavaScript type names.
  /// <include path='items/GetDataType/*' file='Doc/LJCDataColumn.xml'/>
  static GetDataType(mySQLTypeName)
  {
    let retValue = "string";

    switch (mySQLTypeName)
    {
      case "bit":
        retValue = "int";
        break;

      case "int":
      case "smallint":
        retValue = "int";
        break;
    }
    return retValue;
  }

  // ---------------
  // Constructor Methods

  /// <summary>Initializes a class instance.</summary>
  /// <include path='items/constructor/*' file='Doc/LJCDataColumn.xml'/>
  constructor(propertyName, columnName = null, renameAs = null
    , dataTypeName = "string", value = null)
  {
    this.PropertyName = propertyName;
    this.ColumnName = columnName;
    if (null == columnName)
    {
      this.ColumnName = propertyName;
    }
    this.RenameAs = renameAs;
    this.DataTypeName = dataTypeName;
    this.Value = value;

    this.AllowDbNull = false;
    this.AutoIncrement = false;
    this.Caption = propertyName;
    this.DefaultValue = null;
    this.MaxLength = -1;
    this.MySQLTypeName = null;
    this.WhereBoolOperator = "and";
    this.WhereCompareOperator = "=";
  }

  // ---------------
  // Data Object Methods

  /// <summary>Creates an object clone.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retDataColumn = new LJCDataColumn(this.PropertyName, this.ColumnName
      , this.RenameAs, this.DataTypeName, this.Value);
    retDataColumn.AllowDbNull = this.AllowDbNull;
    retDataColumn.AutoIncrement = this.AutoIncrement;
    retDataColumn.Caption = this.Caption;
    retDataColumn.DefaultValue = this.DefauleValue;
    retDataColumn.MaxLength = this.MaxLength;
    retDataColumn.MySQLTypeName = this.MySQLTypeName;
    retDataColumn.WhereBoolOperator = this.WhereBoolOperator;
    retDataColumn.WhereCompareOperator = this.WhereCompareOperator;
    retDataColumn.Value = this.Value;
    return retDataColumn;
  }
}

// ***************
/// <summary>Represents a collection of data column definitions.</summary>
//  Add(), AddObject(), Clear(), Columns(), GetIndex(), PropertytNames()
//  Remove(), Retrieve(), RetrieveWithIndex()
class LJCDataColumns
{
  // ---------------
  // Properties

  Items = [];

  // ---------------
  // Static Methods
  static AddValue(propertyName, value)
  {
    let dataColumn = this.Retrieve(propertyName);
    if (dataColumn != null)
    {
      dataColumn.Value = value;
    }
  }
  // ---------------
  // Static Methods

  /// <summary>
  ///   Create typed collection from deserialized JavasScript array.
  /// </summary>
  /// <param name="items">The items object.</param>
  /// <returns>The collection></returns.
  static Collection(items)
  {
    let retDataColumns = new LJCDataColumns();

    if (items != null
      && items.length > 0)
    {
      for (let index = 0; index < items.length; index++)
      {
        let objItem = items[index];

        // Create typed object from stdClass.
        let dataColumn = LJCDataColumn.Copy(objItem);
        retDataColumns.AddObject(dataColumn);
      }
    }
    return retDataColumns;
  }

  // ---------------
  // Methods

  // Creates and adds the column object to the list.
  /// <include path='items/Add/*' file='Doc/LJCDataColumns.xml'/>
  Add(propertyName, columnName = null, renameAs = null
    , dataTypeName = "string", value = null)
  {
    let retDataColumn = new LJCDbColumn(propertyName, columnName, renameAs
      , dataTypeName, value);
    this.AddObject(retDataColumn);
    return retDataColumn;
  }

  /// <summary>Adds the supplied column to the list.</summary>
  /// <param name="dataColumn">The column object.</param>
  AddObject(dataColumn)
  {
    this.Items.push(dataColumn);
  }

  /// <summary>Clears the collection list.</summary>
  Clear()
  {
    this.Items = [];
  }

  // Gets the column objects that match the property names.
  /// <include path='items/Columns/*' file='Doc/LJCDataColumns.xml'/>
  Columns(propertyNames)
  {
    let retDataColumns = null;

    if (null == propertyNames)
    {
      retDataColumns = this.Items;
    }
    else
    {
      retDataColumns = new LJCDataColumns();
      for (let index = 0; index < propertyNames.length; index++)
      {
        let propertyName = propertyNames[index];
        let dataColumn = this.Retrieve(propertyName);
        if (dataColumn != null)
        {
          retDataColumns.AddObject(dataColumn);
        }
      }
    }
    return retDataColumns;
  }

  /// <summary>Get the item count.</summary>
  Count()
  {
    return this.Items.length;
  }

  // Gets the column object with the supplied property name.
  /// <include path='items/GetIndex/*' file='Doc/LJCDataColumns.xml'/>
  GetIndex(propertyName)
  {
    let retIndex = -1;

    for (let index = 0; index < this.Items.length; index++)
    {
      let item = this.Items[index];
      if (item.PropertyName == propertyName)
      {
        retIndex = index;
        break;
      }
    }
    return retIndex;
  }

  /// <summary>Gets an array of property names.</summary>
  /// <returns>The property name array.</returns>
  PropertyNames()
  {
    let retPropertyNames = [];

    for (let index = 0; index < this.Items.length; index++)
    {
      let dataColumn = this.Items[index];
      retPropertyNames.push(dataColumn.PropertyName);
    }
    return retPropertyNames;
  }

  // Removes the column object with the supplied property name.
  /// <include path='items/Remove/*' file='Doc/LJCDataColumns.xml'/>
  Remove(propertyName)
  {
    let itemIndex = this.GetIndex(propertyName);
    if (itemIndex > -1)
    {
      let beginIndex = 0;
      this.Items.splice(beginIndex, itemIndex);
    }
  }

  // Retrieves the column object with the supplied property name.
  /// <include path='items/Retrieve/*' file='Doc/LJCDataColumns.xml'/>
  Retrieve(propertyName)
  {
    let retDataColumn = this.Items.find(item =>
      item.PropertyName == propertyName);
    return retDataColumn;
  }

  // Retrieves the column object with the supplied index.
  /// <include path='items/RetrieveWithIndex/*' file='Doc/LJCDataColumns.xml'/>
  RetrieveWithIndex(index)
  {
    let retDataColumn = null;

    if (index >= 0 && this.Items.length > index)
    {
      retDataColumn = this.Items[index];
    }
    return retDataColumn;
  }
}