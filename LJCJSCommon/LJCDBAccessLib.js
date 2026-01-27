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

  // Creates a new object from simple object values.
  /// <include path='members/Copy/*' file='Doc/LJCDataColumn.xml'/>
  static Copy(objColumn)
  {
    let retColumn = new LJCDataColumn();

    // Look for properties of simple object in typed object.
    for (let propertyName in objColumn)
    {
      if (propertyName in retColumn)
      {
        // Update new typed object properties from the simple object.
        retColumn[propertyName] = objColumn[propertyName];
      }
    }
    return retColumn;
  }

  // Converts MySQL type names to JavaScript type names.
  /// <include path='members/GetDataType/*' file='Doc/LJCDataColumn.xml'/>
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

  // #region Data Object Methods

  // Creates an object clone.
  /// <include path='members/Clone/*' file='Doc/LJCDataColumn.xml'/>
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
    //retColumn.Value = this.Value;
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

  // Create typed collection from deserialized JavasScript array.
  /// <include path='members/ToCollection/*' file='Doc/LJCDataColumns.xml'/>
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

  // Creates a clone of this object.
  /// <include path='members/Clone/*' file='Doc/LJCDataColumns.xml'/>
  Clone()
  {
    let retDataColumns = new LJCDataColumns();

    let count = this.#Items.length;
    for (let index = 0; index < count; index++)
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
  /// <include path='members/Add/*' file='Doc/LJCDataColumns.xml'/>
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

  // Adds the supplied item to the list.
  /// <include path='members/AddObject/*' file='Doc/LJCDataColumns.xml'/>
  AddObject(dataColumn)
  {
    let methodName = "AddObject()";
    let retDataColumn = null;

    if (dataColumn instanceof LJCDataColumn)
    {
      retDataColumn = dataColumn;
      this.#Items.push(dataColumn);
      this.#UpdateProperties();
    }
    return retDataColumn;
  }

  // Adds an item and value.
  /// <include path='members/AddValue/*' file='Doc/LJCDataColumns.xml'/>
  AddValue(propertyName, value)
  {
    let retDataColumn = new LJCDataColumn(propertyName);
    retDataColumn.Value = value;
    this.AddObject(retDataColumn);
    return retDataColumn;
  }

  // Clears the collection list.
  /// <include path='members/Clear/*' file='Doc/LJCDataColumns.xml'/>
  Clear()
  {
    this.#Items = [];
    this.#UpdateProperties();
  }

  // Removes the column object which matches the data values.
  /// <include path='members/Remove/*' file='Doc/LJCDataColumns.xml'/>
  Remove(propertyName)
  {
    let itemIndex = this.GetIndex(propertyName);
    if (itemIndex > -1)
    {
      this.#Items.splice(itemIndex, 1);
      this.#UpdateProperties();
    }
  }

  // Retrieves the object which matches the data values.
  /// <include path='members/Retrieve/*' file='Doc/LJCDataColumns.xml'/>
  Retrieve(propertyName)
  {
    let retDataColumn = this.#Items.find(item =>
      item.PropertyName == propertyName);
    return retDataColumn;
  }

  // Retrieves the column object with the supplied index.
  /// <include path='members/RetrieveAtIndex/*' file='Doc/LJCDataColumns.xml'/>
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

  // Sets an item value.
  /// <include path='members/SetValue/*' file='Doc/LJCDataColumns.xml'/>
  SetValue(propertyName, value)
  {
    let itemDataColumn = this.Retrieve(propertyName);
    if (itemDataColumn != null)
    {
      itemDataColumn.Value = value;
    }
  }

  // Updates the property values.
  #UpdateProperties()
  {
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }
  // #endregion

  // #region Other Methods

  // Gets the index of the object which matches the data values.
  /// <include path='members/GetIndex/*' file='Doc/LJCDataColumns.xml'/>
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
  /// <include path='members/PropertyNames/*' file='Doc/LJCDataColumns.xml'/>
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
  /// <include path='members/SelectItems/*' file='Doc/LJCDataColumn.xml'/>
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

  // Creates a new object from simple object values.
  /// <include path='members/Copy/*' file='Doc/LJCJoin.xml'/>
  static Copy(objJoin)
  {
    let retJoin = new LJCJoin();

    // Look for properties of simple object in typed object.
    for (let propertyName in objJoin)
    {
      if (propertyName in retJoin)
      {
        // Update new typed object properties from the simple object.
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

  // Creates an object clone.
  /// <include path='members/Clone/*' file='Doc/LJCJoin.xml'/>
  Clone()
  {
    let retJoin = new LJCJoin(this.TableName, this.TableAlias);
    retJoin.Columns = this.Columns;
    retJoin.JoinOns = this.JoinOns;
    retJoin.JoinType = this.JoinType;
    retJoin.SchemaName = this.SchemaName;
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

  // Create typed collection from deserialized JavasScript array.
  /// <include path='members/ToCollection/*' file='Doc/LJCJoins.xml'/>
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

  // Creates a clone of this object.
  /// <include path='members/Clone/*' file='Doc/LJCJoins.xml'/>
  Clone()
  {
    let retJoins = new LJCJoins();

    let count = this.#Items.length;
    for (let index = 0; index < count; index++)
    {
      let join = this.#Items[index];
      if (join != null)
      {
        retJoins.AddObject(join.Clone());
      }
    }
    return retJoins;
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

  // Adds the supplied column to the list.
  /// <include path='items/AddObject/*' file='Doc/LJCJoins.xml'/>
  AddObject(join)
  {
    let methodName = "AddObject()";

    this.#Items.push(join);
    this.#UpdateProperties();
    return join;
  }

  // Clears the collection list.
  /// <include path='items/Clear/*' file='Doc/LJCJoins.xml'/>
  Clear()
  {
    this.#Items = [];
    this.#UpdateProperties();
  }

  // Removes the the object which matches the data values.
  /// <include path='items/Remove/*' file='Doc/LJCJoins.xml'/>
  Remove(tableName, tableAlias = null)
  {
    let itemIndex = this.GetIndex(tableName, tableAlias);
    if (itemIndex > -1)
    {
      this.#Items.splice(itemIndex, 1);
      this.#UpdateProperties();
    }
  }

  // Retrieves the object which matches the data values.
  /// <include path='items/Retrieve/*' file='Doc/LJCJoins.xml'/>
  Retrieve(tableName, tableAlias = null)
  {
    let retJoin = this.#Items.find(item =>
      item.TableName == tableName
      && item.TableAlias == tableAlias);
    return retJoin;
  }

  // Retrieves the object at the supplied index.
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

  // Updates the property values.
  #UpdateProperties()
  {
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }
  // #endregion

  // #region Other Methods

  // Gets the index of the object which matches the data values.
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

  // Creates a new object from simple object values.
  /// <include path='members/Copy/*' file='Doc/LJCJoinOn.xml'/>
  static Copy(objJoinOn)
  {
    let retJoinOn = new LJCJoinOn();

    // Look for properties of simple object in typed object.
    for (let propertyName in objJoinOn)
    {
      if (propertyName in retJoinOn)
      {
        // Update new typed object properties from the simple object.
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

  // Creates an object clone.
  /// <include path='members/Clone/*' file='Doc/LJCJoinOn.xml'/>
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

  // Create typed collection from deserialized JavasScript array.
  /// <include path='members/ToCollection/*' file='Doc/LJCJoinOns.xml'/>
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

  // Creates a clone of this object.
  /// <include path='members/Clone/*' file='Doc/LJCJoinOns.xml'/>
  Clone()
  {
    let retJoinOns = new LJCJoinOns();

    const count = this.#Items.length;
    for (let index = 0; index < count; index++)
    {
      let joinOn = this.#Items[index];
      if (joinOn != null)
      {
        retJoinOns.AddObject(joinOn.Clone());
      }
    }
    return retJoinOns;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='items/Add/*' file='Doc/LJCJoinOns.xml'/>
  Add(fromColumnName, toColumnName = null)
  {
    let methodName = "Add()";
    let retJoinOn = null;

    let JoinOn = new LJCJoinOn(fromColumnName, toColumnName);
    retJoinOn = this.AddObject(JoinOn);
    return retJoinOn;
  }

  // Adds the supplied item to the list.
  /// <include path='items/AddObject/*' file='Doc/LJCJoinOns.xml'/>
  AddObject(joinOn)
  {
    let methodName = "AddObject()";

    this.#Items.push(joinOn);
    this.#UpdateProperties();
    return joinOn;
  }

  // Clears the collection list.
  /// <include path='items/Clear/*' file='Doc/LJCJoinOns.xml'/>
  Clear()
  {
    this.#Items = [];
    this.#UpdateProperties();
  }

  // Removes the the object which matches the data values.
  /// <include path='items/Remove/*' file='Doc/LJCJoinOns.xml'/>
  Remove(fromColumnName)
  {
    let itemIndex = this.GetIndex(fromColumnName);
    if (itemIndex > -1)
    {
      this.#Items.splice(itemIndex, 1);
      this.#UpdateProperties();
    }
  }

  // Retrieves the object which matches the data values.
  /// <include path='items/Retrieve/*' file='Doc/LJCJoinOns.xml'/>
  Retrieve(fromColumnName)
  {
    let retJoinOn = this.#Items.find(item =>
      item.FromColumnName == fromColumnName);
    return retJoinOn;
  }

  // Retrieves the object at the supplied index.
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

  // Updates the property values.
  #UpdateProperties()
  {
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }
  // #endregion

  // #region Other Methods

  // Gets the index of the object which matches the data values.
  /// <include path='items/GetIndex/*' file='Doc/LJCJoinOns.xml'/>
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

