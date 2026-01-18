"use strict";
// Copyright (c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCDBAccessLib.js

/// <summary>The Data Access Class Library</summary>
/// LibName: LJCDBAccessLib
//  Classes: LJCDataColumn, LJCDataColumns,
//    LJCJoin, LJCJoins,
//    LJCJoinOn, LJCJoinOns

// ***************
/// <summary>Represents a data column definition.</summary>
//  Static: Copy(), GetDataType()
//  Data Class: Clone()
class LJCDataColumn
{
  // #region Properties - LJCDataColumn

  /// <summary>Indicates if the Column allows nulls.</summary>
  AllowDbNull;

  /// <summary>The AutoIncrement flag.</summary>
  AutoIncrement;

  /// <summary>The Caption value.</summary>
  Caption;

  /// <summary>The Column name.</summary>
  ColumnName;

  /// <summary>The DataType name.</summary>
  DataTypeName;

  /// <summary>The Default value.</summary>
  DefaultValue;

  /// <summary>The insert index used in LJCDataColumns.InsertObject()</summary>
  InsertIndex;

  /// <summary>The MaxLength value.</summary>
  MaxLength;

  /// <summary>The MySQL Type name.</summary>
  MySQLTypeName;

  /// <summary>The fixed length field position value.</summary>
  Position;

  /// <summary>The Property name.</summary>
  PropertyName;

  /// <summary>The RenameAs value.</summary>
  RenameAs;

  /// <summary>The Column value.</summary>
  Value;

  /// <summary>The Where clause boolean operator.</summary>
  WhereBoolOperator;

  /// <summary>The Where clause comparison operator.</summary>
  WhereCompareOperator;
  // #endregion

  // #region Static Methods

  // Creates a new object from standard object values.
  /// <include path='items/Copy/*' file='Doc/LJCDataColumn.xml'/>
  static Copy(objColumn)
  {
    let retColumn = new LJCDataColumn();

    // Look for properties of standard object in typed object.
    for (let propertyName in objColumn)
    {
      if (propertyName in retColumn)
      {
        // Update new typed object properties from the standard object.
        retColumn[propertyName] = objColumn[propertyName];
      }
    }
    return retColumn;
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
    if (null == columnName)
    {
      columnName = propertyName;
    }
    this.ColumnName = columnName;
    this.RenameAs = renameAs;
    this.DataTypeName = dataTypeName;
    this.Value = value;

    this.AllowDbNull = false;
    this.AutoIncrement = false;
    this.Caption = propertyName;
    this.DefaultValue = null;
    this.InsertIndex = 0;
    this.MaxLength = -1;
    this.MySQLTypeName = null;
    this.Position = 0;
    this.WhereBoolOperator = "and";
    this.WhereCompareOperator = "=";
  }
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates an object clone.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retColumn = new LJCDataColumn(this.PropertyName, this.ColumnName
      , this.RenameAs, this.DataTypeName, this.Value);
    retColumn.AllowDbNull = this.AllowDbNull;
    retColumn.AutoIncrement = this.AutoIncrement;
    retColumn.Caption = this.Caption;
    retColumn.DefaultValue = this.DefauleValue;
    retColumn.InsertIndex = this.InsertIndex;
    retColumn.MaxLength = this.MaxLength;
    retColumn.MySQLTypeName = this.MySQLTypeName;
    retColumn.WhereBoolOperator = this.WhereBoolOperator;
    retColumn.WhereCompareOperator = this.WhereCompareOperator;
    retColumn.Value = this.Value;
    return retColumn;
  }
  // #endregion
} // LJCDataColumn

// ***************
/// <summary>Represents a collection of LJCDataColumn data objects.</summary>
// Static: AddValue(), ToCollection()
// Data Class: Clone()
// Collection Data: Add(), AddObject(), Remove(), Retrieve(),
//   RetrieveAtIndex()
// Other: Clear(), Count(), GetIndex(), PropertyNames, SelectItems()
class LJCDataColumns
{
  // #region Properties

  /// <summary>The current items count.</summary>
  Count = 0;

  /// <summary>The current #Items clone.</summary>
  ReadItems = [];

  // *** Change ***
  #Items = [];
  // #endregion

  // #region Static Methods

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
    let retDataColumn = null;

    if (dataColumn instanceof LJCDataColumn)
    {
      retDataColumn = dataColumn;
      this.#Items.push(dataColumn);
      this.Count = this.#Items.length;
      this.ReadItems = Array.from(this.#Items);
    }
    return retDataColumn;
  }

  /// <summary>Adds an item value.</summary>
  /// <param name="propertyName">The item PropertyName value.</param>
  /// <param name="value">The added value.</param>
  AddValue(propertyName, value)
  {
    let itemDataColumn = this.Retrieve(propertyName);
    if (itemDataColumn != null)
    {
      itemDataColumn.Value = value;
    }
  }

  /// <summary>Clears the collection list.</summary>
  Clear()
  {
    this.#Items = [];
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }

  // Removes the column object with the supplied property name.
  /// <include path='items/Remove/*' file='Doc/LJCDataColumns.xml'/>
  Remove(propertyName)
  {
    let beginIndex = this.GetIndex(propertyName);
    if (beginIndex > -1)
    {
      //let beginIndex = 0;
      //this.#Items.splice(beginIndex, itemIndex);
      this.#Items.splice(beginIndex, 1);
      this.Count = this.#Items.length;
      this.ReadItems = Array.from(this.#Items);
    }
  }

  // Retrieves the column object with the supplied property name.
  /// <include path='items/Retrieve/*' file='Doc/LJCDataColumns.xml'/>
  Retrieve(propertyName)
  {
    let retDataColumn = this.#Items.find(item =>
      item.PropertyName == propertyName);
    return retDataColumn;
  }

  // Retrieves the column object with the supplied index.
  /// <include path='items/RetrieveAtIndex/*' file='Doc/LJCDataColumns.xml'/>
  RetrieveAtIndex(index)
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

  // <summary>Returns the collection element count.</summary>
  //Count()
  //{
  //  let retCount = 0;
  //
  //  retCount = this.#Items.length;
  //  return retCount;
  //}

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

    for (let index = 0; index < this.#Items.length; index++)
    {
      let dataColumn = this.#Items[index];
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
  // #endregion
} // LJCDataColumns

// ***************
/// <summary>Represents a SQL Join.</summary>
//  Static: Copy()
//  Data Class: Clone()
class LJCJoin
{
  // #region Properties - LJCJoin

  /// <summary>The included join table columns.</summary>
  Columns = null;

  /// <summary>The JoinOn definintions.</summary>
  JoinOns = null;

  /// <summary>The Join type.</summary>
  JoinType = "";

  /// <summary>The Schema name.</summary>
  SchemaName = "";

  /// <summary>The table alias.</summary>
  TableAlias = "";

  /// <summary>The table name.</summary>
  TableName = "";
  // #endregion

  // #region Static Methods

  // Creates a new object from standard object values.
  /// <include path='items/Copy/*' file='Doc/LJCDataColumn.xml'/>
  static Copy(objJoin)
  {
    let retJoin = new LJCJoin();

    // Look for properties of standard object in typed object.
    for (let propertyName in objJoin)
    {
      if (propertyName in retJoin)
      {
        // Update new typed object properties from the standard object.
        retJoin[propertyName] = objJoin[propertyName];
      }
    }
    return retJoin;
  }
  // #endregion

  // #region Constructor Methods - LJCJoin

  /// <summary>Initializes a class instance.</summary>
  /// <include path='items/constructor/*' file='Doc/LJCJoin.xml'/>
  constructor(tableName, tableAlias = "")
  {
    this.TableName = tableName;
    this.TableAlias = tableAlias;

    this.Columns = new LJCDataColumns();
    this.JoinOns = new LJCJoinOns();
    this.JoinType = "left";
    this.SchemaName = "";
  } // constructor()
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates an object clone.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retJoin = new LJCJoin(this.TableName, this.AliasName);
    retJoin.Columns = this.Columns;
    retJoin.JoinOns = this.JoinOns;
    retJoin.JoinType = this.JoinType;
    retJoin.SchemaName = this.SchemaName;
    retJoin.TableAlias = this.TableAlias;
    retJoin.TableName = this.TableName;
    return retJoin;
  } // Clone()
  // #endregion
} // Join

// ***************
/// <summary>Represents a collection of LJCJoin objects.</summary>
// Static: ToCollection()
// Data Class: Clone()
// Collection Data: Add(), AddObject(), Remove(), Retrieve(),
//   RetrieveAtIndex()
// Other: Clear(), Count(), GetIndex()
class LJCJoins
{
  // #region Properties

  /// <summary>The current items count.</summary>
  Count = 0;

  /// <summary>The current #Items clone.</summary>
  ReadItems = [];

  // *** Change ***
  #Items = [];
  // #endregion

  // #region Static Methods

  /// <summary>
  ///   Create typed collection from deserialized JavasScript array.
  /// </summary>
  /// <param name="items">The items object.</param>
  /// <returns>The collection></returns.
  static ToCollection(items)
  {
    let retJoins = new LJCJoins();

    if (items != null
      && items.length > 0)
    {
      for (let index = 0; index < items.length; index++)
      {
        let objItem = items[index];

        // Create typed object from stdClass.
        let joinOn = LJCJoin.Copy(objItem);
        retJoins.AddObject(joinOn);
      }
    }
    return retJoins;
  }
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retJoins = new LJCJoins();

    let names = this.PropertyNames();
    for (let index = 0; index < names.length; index++)
    {
      let join = this.#Items[index];
      if (join != null)
      {
        retjoins.AddObject(join.Clone());
      }
    }
    return retjoins;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='items/Add/*' file='Doc/LJCJoins.xml'/>
  Add(tableName, tableAlias = null)
  {
    let methodName = "Add()";
    let retJoin = null;

    let join = new LJCJoin(tableName, tableAlias);
    retJoin = this.AddObject(join);
    return retJoin;
  }

  /// <summary>Adds the supplied column to the list.</summary>
  /// <param name="dataColumn">The column object.</param>
  AddObject(join)
  {
    let methodName = "AddObject()";

    this.#Items.push(join);
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
    return join;
  }

  /// <summary>Clears the collection list.</summary>
  Clear()
  {
    this.#Items = [];
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }

  // Removes the join object with the supplied values.
  /// <include path='items/Remove/*' file='Doc/LJCJoins.xml'/>
  Remove(tableName, tableAlias = null)
  {
    let itemIndex = this.GetIndex(tableName, tableAlias);
    if (itemIndex > -1)
    {
      let beginIndex = 0;
      this.#Items.splice(beginIndex, itemIndex);
      this.Count = this.#Items.length;
      this.ReadItems = Array.from(this.#Items);
    }
  }

  // Retrieves the join on object with the supplied from column name.
  /// <include path='items/Retrieve/*' file='Doc/LJCJoins.xml'/>
  Retrieve(tableName, tableAlias = null)
  {
    let retJoinOn = this.#Items.find(item =>
      item.tableName == tableName
      && item.TableAlias == tableAlias);
    return retJoinOn;
  }

  // Retrieves the join on object with the supplied index.
  /// <include path='items/RetrieveAtIndex/*' file='Doc/LJCJoins.xml'/>
  RetrieveAtIndex(index)
  {
    let retJoin = null;

    if (index >= 0
      && this.#Items.length > index)
    {
      retJoin = this.#Items[index];
    }
    return retJoin;
  }
  // #endregion

  // #region Other Methods

  // <summary>Returns the collection element count.</summary>
  //Count()
  //{
  //  let retCount = 0;
  //
  //  retCount = this.#Items.length;
  //  return retCount;
  //}

  // Gets the column object with the supplied values.
  /// <include path='items/GetIndex/*' file='Doc/LJCJoins.xml'/>
  GetIndex(tableName, tableAlias = null)
  {
    let retIndex = -1;

    for (let index = 0; index < this.#Items.length; index++)
    {
      let item = this.#Items[index];
      if (item.TableName == tableName
        && item.TableAlias == tableAlias)
      {
        retIndex = index;
        break;
      }
    }
    return retIndex;
  }
  // #endregion
} // LJCJoins

// ***************
// Represents a SQL JoinOn
//  Static: Copy()
//  Data Class: Clone()
class LJCJoinOn
{
  // #region Properties - LJCJoinOn

  /// <summary>The Boolean Operator value.</summary>
  BooleanOperator = "";

  /// <summary>The 'From' column name.</summary>
  FromColumnName = "";

  /// <summary>The Join On Operator.</summary>
  JoinOnOperator = "";

    /// <summary>The contained JoinOns.</summary>
  JoinOns = [];

  /// <summary>The 'To' column name.</summary>
  ToColumnName = "";
  // #endregion

  // #region Static Methods

  // Creates a new object with existing standard object values.
  /// <include path='items/Copy/*' file='Doc/LJCJoinOn.xml'/>
  static Copy(objJoinOn)
  {
    let retJoinOn = new LJCJoinOn();

    // Look for properties of standard object in typed object.
    for (let propertyName in objJoinOn)
    {
      if (propertyName in retJoinOn)
      {
        // Update new typed object properties from the standard object.
        retJoinOn[propertyName] = objJoinOn[propertyName];
      }
    }
    return retJoinOn;
  }
  // #endregion

  // #region Constructor Methods - LJCJoin

  /// <summary>Initializes a class instance.</summary>
  /// <include path='items/constructor/*' file='Doc/LJCJoinOn.xml'/>
  constructor(fromColumnName, toColumnName)
  {
    this.FromColumnName = fromColumnName;
    this.ToColumnName = toColumnName;

    this.BooleanOperator = "and";
    this.JoinOnOperator = "=";
    this.JoinOns = new LJCJoinOns();
  }
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates an object clone.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retJoinOn = new LJCJoinOn(this.FromColumnName, this.ToColumnName);
    retJoinOn.BooleanOperator = this.BooleanOperator;
    retJoinOn.JoinOnOperator = this.JoinOnOperator;
    retJoinOn.JoinOns = this.JoinOns;
    return retJoinOn;
  } // Clone()
  // #endregion
} // LJCJoinOn

// ***************
/// <summary>Represents a collection of LJCJoinOn objects.</summary>
// Static: ToCollection()
// Data Class: Clone()
// Collection Data: Add(), AddObject(), Remove(), Retrieve(),
//   RetrieveAtIndex()
// Other: Clear(), Count(), GetIndex()
class LJCJoinOns
{
  // #region Properties

  /// <summary>The current items count.</summary>
  Count = 0;

  /// <summary>The current #Items clone.</summary>
  ReadItems = [];

  // *** Change ***
  #Items = [];
  // #endregion

  // #region Static Methods

  /// <summary>
  ///   Create typed collection from deserialized JavasScript array.
  /// </summary>
  /// <param name="items">The items object.</param>
  /// <returns>The collection></returns.
  static ToCollection(items)
  {
    let retJoinOns = new LJCJoinOns();

    if (items != null
      && items.length > 0)
    {
      for (let index = 0; index < items.length; index++)
      {
        let objItem = items[index];

        // Create typed object from stdClass.
        let joinOn = LJCJoinOn.Copy(objItem);
        retJoinOns.AddObject(joinOn);
      }
    }
    return retJoinOns;
  }
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retJoinOns = new LJCJoinOns();

    let names = this.PropertyNames();
    for (let index = 0; index < names.length; index++)
    {
      let joinOn = this.#Items[index];
      if (joinOn != null)
      {
        retjoinOns.AddObject(joinOn.Clone());
      }
    }
    return retjoinOns;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='items/Add/*' file='Doc/LJCDataColumns.xml'/>
  Add(fromColumnName, toColumnName = null)
  {
    let methodName = "Add()";
    let retJoinOn = null;

    let JoinOn = new LJCJoinOn(fromColumnName, toColumnName);
    retJoinOn = this.AddObject(JoinOn);
    return retJoinOn;
  }

  /// <summary>Adds the supplied item to the list.</summary>
  /// <param name="joinOn">The JoinOn object.</param>
  AddObject(joinOn)
  {
    let methodName = "AddObject()";

    this.#Items.push(joinOn);
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
    return joinOn;
  }

  /// <summary>Clears the collection list.</summary>
  Clear()
  {
    this.#Items = [];
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }

  // Removes the join on object with the supplied from Column name.
  /// <include path='items/Remove/*' file='Doc/LJCJoinOns.xml'/>
  Remove(fromColumnName)
  {
    let itemIndex = this.GetIndex(fromColumnName);
    if (itemIndex > -1)
    {
      let beginIndex = 0;
      this.#Items.splice(beginIndex, itemIndex);
      this.Count = this.#Items.length;
      this.ReadItems = Array.from(this.#Items);
    }
  }

  // Retrieves the join on object with the supplied from column name.
  /// <include path='items/Retrieve/*' file='Doc/LJCJoinOns.xml'/>
  Retrieve(fromColumnName)
  {
    let retJoinOn = this.#Items.find(item =>
      item.FromColumnName == fromColumnName);
    return retJoinOn;
  }

  // Retrieves the join on object with the supplied index.
  /// <include path='items/RetrieveAtIndex/*' file='Doc/LJCJoinOns.xml'/>
  RetrieveAtIndex(index)
  {
    let retJoinOn = null;

    if (index >= 0
      && this.#Items.length > index)
    {
      retJoinOn = this.#Items[index];
    }
    return retJoinOn;
  }
  // #endregion

  // #region Other Methods

  // Gets the column object with the supplied property name.
  /// <include path='items/GetIndex/*' file='Doc/LJCDataColumns.xml'/>
  GetIndex(fromColumnName)
  {
    let retIndex = -1;

    for (let index = 0; index < this.#Items.length; index++)
    {
      let item = this.#Items[index];
      if (item.FromColumnName == fromColumnName)
      {
        retIndex = index;
        break;
      }
    }
    return retIndex;
  }
  // #endregion
} // LJCJoinOns

