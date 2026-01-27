"use strict";
// LJCDataColumnTest.js

class LJCDataColumnTest
{
  Run()
  {
    // Static Methods
    this.Copy();
    this.GetDataType();

    // Data Class Methods
    this.Clone();
  }

  // Creates a new object with existing standard object values.
  Copy()
  {
    const simpleDataColumn = {
      "PropertyName": "PropertyName",
      "Value": "PropertyValue",
    };

    const column = LJCDataColumn.Copy(simpleDataColumn);
    const result = column.PropertyName;
    const compare = "PropertyName";
    LJC.CheckValues("Copy()", result, compare);
  }

  // Converts MySQL type names to JavaScript type names.
  GetDataType()
  {
    let result = LJCDataColumn.GetDataType("bit");
    let compare = "int";
    LJC.CheckValues("GetDataType()", result, compare);
  }

  // Creates an object clone.
  Clone()
  {
    const propertyName = "PropertyName";
    const value = "PropertyValue";
    const column = new LJCDataColumn(propertyName);
    column.Value = value;

    let clone = column.Clone();
    const result = clone.PropertyName;
    const compare = "PropertyName";
    LJC.CheckValues("Clone()", result, compare);
  }
}