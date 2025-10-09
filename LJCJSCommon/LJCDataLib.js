"use strict";
// Copyright (c) Lester J. Clark and Contributors.
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
  // #region Properties

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
  // #endregion

  // #region Static Methods

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
  // #endregion

  // #region Constructor Methods

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
  // #endregion

  // #region Data Object Methods

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
  // #endregion
}

// ***************
/// <summary>Represents a collection of LJCDataColumn data objects.</summary>
class LJCDataColumns
{
  // #region Properties

  // The current items count.
  Count = 0;

  // The current #Items clone.
  ReadItems = [];

  // *** Change ***
  #Items = [];
  // #endregion

  // #region Static Methods

  /// <summary>Adds an item value.</summary>
  /// <param name="propertyName">The item PropertyName value.</param>
  /// <param name="value">The added value.</param>
  static AddValue(propertyName, value)
  {
    let itemDataColumn = this.Retrieve(propertyName);
    if (itemDataColumn != null)
    {
      itemDataColumn.Value = value;
    }
  }

  /// <summary>
  ///   Create typed collection from deserialized JavasScript array.
  /// </summary>
  /// <param name="items">The items object.</param>
  /// <returns>The collection></returns.
  static ToCollection(items)
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
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retDataColumns = new LJCDataColumns();

    let names = this.PropertyNames();
    for (let index = 0; index < names.length; index++)
    {
      let dataColumn = this.#Items[index];
      if (dataColumn != null)
      {
        retDataColumns.AddObject(dataColumn.Clone());
      }
    }
    return retDataColumns;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='items/Add/*' file='Doc/LJCDataColumns.xml'/>
  Add(propertyName, columnName = null, renameAs = null
    , dataTypeName = "string", value = null)
  {
    let methodName = "Add()";
    let retDataColumn = null;

    let dataColumn = new LJCDataColumn(propertyName, columnName, renameAs
      , dataTypeName, value);
    retDataColumn = this.AddObject(dataColumn);
    return retDataColumn;
  }

  /// <summary>Adds the supplied column to the list.</summary>
  /// <param name="dataColumn">The column object.</param>
  AddObject(dataColumn)
  {
    let methodName = "AddObject()";

    this.#Items.push(dataColumn);
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
    return dataColumn;
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
      this.Count = this.#Items.length;
      this.ReadItems = Array.from(this.#Items);
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

    if (index >= 0
      && this.#Items.length > index)
    {
      retDataColumn = this.#Items[index];
    }
    return retDataColumn;
  }
  // #endregion

  // #region Other Methods

  /// <summary>Clears the collection list.</summary>
  Clear()
  {
    this.#Items = [];
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }

  // Gets the column object with the supplied property name.
  /// <include path='items/GetIndex/*' file='Doc/LJCDataColumns.xml'/>
  GetIndex(propertyName)
  {
    let retIndex = -1;

    for (let index = 0; index < this.#Items.length; index++)
    {
      let item = this.#Items[index];
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
    let retNames = [];

    for (let index = 0; index < this.Items.length; index++)
    {
      let dataColumn = this.Items[index];
      retNames.push(dataColumn.PropertyName);
    }
    return retNames;
  }

  // Gets the items that match the supplied names.
  /// <include path='items/Items/*' file='Doc/Cities.xml'/>
  SelectItems(propertyNames)
  {
    let retDataColumns = null;

    if (null == propertyNames)
    {
      retDataColumns = this.#Items.Clone();
    }
    else
    {
      retDataColumns = new LJCDataColumns();
      for (let index = 0; index < names.length; index++)
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
  // #endregion
}