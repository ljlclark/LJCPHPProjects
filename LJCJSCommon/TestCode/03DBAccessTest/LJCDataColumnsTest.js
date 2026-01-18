"use strict";
// LJCDataColumnsTest.js

class LJCDataColumnsTest
{
  Run()
  {
    // Static Methods
    this.ToCollection();

    // Data Class Methods
    this.Clone();

    // Collection Data Methods
    this.Add();
    this.AddObject();
    this.AddValue()
    this.Clear();
    this.Remove();
    this.Retrieve();
    this.RetrieveAtIndex();

    // Other Methods
    this.GetIndex();
    this.PropertyNames();
    this.SelectItems();
  }

  // #region Static Methods

  // Create typed collection from deserialized JavasScript array.
  ToCollection()
  {
    const array = [
      { PropertyName: "Name", Value: "First" },
      { PropertyName: "Sequence", Value: 1 },
    ];

    const columns = LJCDataColumns.ToCollection(array);
    const result = columns.Count;
    const compare = 2;
    LJC.CheckValues("ToCollection()", result, compare);
  }
  // #endregion

  // #region Data Class Methods

  // Creates a clone of this object.
  Clone()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");

    let clone = columns.Clone();
    const result = clone.Count;
    const compare = 2;
    LJC.CheckValues("Clone()", result, compare);
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  Add()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");
    column.Value = 1;

    column = columns.Retrieve("Sequence");
    let result = column.Value;
    let compare = 1;
    LJC.CheckValues("Add()", result, compare);
  }

  // Adds the supplied column to the list.
  AddObject()
  {
    const columns = new LJCDataColumns();
    let column = new LJCDataColumn("Name");
    columns.AddObject(column);
    column = new LJCDataColumn("Sequence");
    column.Value = 1;
    columns.AddObject(column);

    column = columns.Retrieve("Sequence");
    let result = column.Value;
    let compare = 1;
    LJC.CheckValues("AddObject()", result, compare);
  }

  // Adds an item value.
  AddValue()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");
    column.Value = 1;

    column = columns.Retrieve("Sequence");
    let result = column.Value;
    let compare = 1;
    LJC.CheckValues("AddValue()", result, compare);

    columns.AddValue("Sequence", 2);
    column = columns.Retrieve("Sequence");
    result = column.Value;
    compare = 2;
    LJC.CheckValues("AddValue()", result, compare);
  }

  // Clears the collection list.
  Clear()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");

    columns.Clear();
    const result = columns.Count;
    const compare = 0;
    LJC.CheckValues("Clear()", result, compare);
  }

  // Removes the object which matches the data values.
  Remove()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");

    columns.Remove("Name");
    column = columns.Retrieve("Name");
    let result = "Found";
    if (null == column)
    {
      result = "";
    }
    let compare = "";
    LJC.CheckValues("Remove()", result, compare);
  }

  // Retrieves the object which matches the data values.
  Retrieve()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");

    column = columns.Retrieve("Name");
    const result = column.PropertyName;
    let compare = "Name";
    LJC.CheckValues("Retrieve()", result, compare);
  }

  // Retrieves the object at the supplied index.
  RetrieveAtIndex()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");

    column = columns.RetrieveAtIndex(1);
    const result = column.PropertyName;
    let compare = "Sequence";
    LJC.CheckValues("RetrieveAtIndex()", result, compare);
  }
  // #endregion

  // #region Other Methods

  // Gets the index of the object which matches the data values.
  GetIndex()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");

    const result = columns.GetIndex("Name");
    const compare = 0;
    LJC.CheckValues("GetIndex()", result, compare);
  }

  // Gets an array of property names.
  PropertyNames()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");

    const names = columns.PropertyNames();
    let result = "";
    for (let index = 0; index < names.length; index++)
    {
      let column = columns.RetrieveAtIndex(index);
      if (LJC.HasText(result))
      {
        result += ", ";
      }
      result += column.PropertyName;
    }
    const compare = "Name, Sequence";
    LJC.CheckValues("PropertyNames()", result, compare);
  }

  // Gets the items that match the supplied names.
  SelectItems()
  {
    const columns = new LJCDataColumns();
    let column = columns.Add("Name");
    column = columns.Add("Sequence");

    const propertyNames = [
      "Sequence",
    ]
    const selected = columns.SelectItems(propertyNames);
    let result = selected.Count;
    let compare = 1;
    LJC.CheckValues("SelectItems()", result, compare);

    column = selected.RetrieveAtIndex(0);
    result = column.PropertyName;
    compare = "Sequence";
    LJC.CheckValues("SelectItems()", result, compare);
  }
  // #endregion
}