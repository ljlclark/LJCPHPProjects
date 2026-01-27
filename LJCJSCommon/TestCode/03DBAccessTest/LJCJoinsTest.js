"use strict";
// LJCJoinsTest.js

class LJCJoinsTest
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
    this.Clear();
    this.Remove();
    this.Retrieve();
    this.RetrieveAtIndex();

    // Other Methods
    this.GetIndex();
  }

  // #region Static Methods

  // Create typed collection from deserialized JavasScript array.
  ToCollection()
  {
    const array = [
      { TableName: "TableOne", TableAlias: "t" },
      { TableName: "TableTwo", TableAlias: "t2" },
    ];

    const columns = LJCJoins.ToCollection(array);
    const result = columns.Count;
    const compare = 2;
    LJC.CheckValues("ToCollection()", result, compare);
  }
  // #endregion

  // #region Data Class Methods

  // Creates a clone of this object.
  Clone()
  {
    const joins = new LJCJoins();
    let join = joins.Add("TableOne", "t");
    join = joins.Add("TableTwo", "t2");

    let clone = joins.Clone();
    const result = clone.Count;
    const compare = 2;
    LJC.CheckValues("Clone()", result, compare);
  }
  // #endregion

  // #region Collection Data Methods

  // Adds the item to the array.
  Add()
  {
    const joins = new LJCJoins();
    let join = joins.Add("TableOne", "t");
    join = joins.Add("TableTwo", "t2");
    join.SchemaName = "Schema";

    join = joins.Retrieve("TableTwo", "t2");
    let result = join.SchemaName;
    let compare = "Schema";
    LJC.CheckValues("Add()", result, compare);
  }

  // Adds the supplied item to the list.
  AddObject()
  {
    const joins = new LJCJoins();
    let join = new LJCJoin("TableOne", "t");
    joins.AddObject(join);
    join = new LJCJoin("TableTwo", "t2");
    join.SchemaName = "Schema";
    joins.AddObject(join);

    join = joins.Retrieve("TableTwo", "t2");
    let result = join.SchemaName;
    let compare = "Schema";
    LJC.CheckValues("AddObject()", result, compare);
  }

  // Clears the collection list.
  Clear()
  {
    const joins = new LJCJoins();
    let join = joins.Add("TableOne", "t");
    join = joins.Add("TableTwo", "t2");
    join.SchemaName = "Schema";

    joins.Clear();
    const result = joins.Count;
    const compare = 0;
    LJC.CheckValues("Clear()", result, compare);
  }

  // Removes the object which matches the data values.
  Remove()
  {
    const joins = new LJCJoins();
    let join = joins.Add("TableOne", "t");
    join = joins.Add("TableTwo", "t2");
    join.SchemaName = "Schema";

    joins.Remove("TableOne", "t");
    join = joins.Retrieve("TableOne", "t");
    let result = "Found";
    if (null == join)
    {
      result = "";
    }
    let compare = "";
    LJC.CheckValues("Remove()", result, compare);
  }

  // Retrieves the object which matches the data values.
  Retrieve()
  {
    const joins = new LJCJoins();
    let join = joins.Add("TableOne", "t");
    join = joins.Add("TableTwo", "t2");
    join.SchemaName = "Schema";

    join = joins.Retrieve("TableOne", "t");
    const result = join.TableName;
    let compare = "TableOne";
    LJC.CheckValues("Retrieve()", result, compare);
  }

  // Retrieves the object at the supplied index.
  RetrieveAtIndex()
  {
    const joins = new LJCJoins();
    let join = joins.Add("TableOne", "t");
    join = joins.Add("TableTwo", "t2");
    join.SchemaName = "Schema";

    join = joins.RetrieveAtIndex(1);
    const result = join.TableName;
    let compare = "TableTwo";
    LJC.CheckValues("RetrieveAtIndex()", result, compare);
  }
  // #endregion

  // #region Other Methods

  // Gets the index of the object which matches the data values.
  GetIndex()
  {
    const joins = new LJCJoins();
    let join = joins.Add("TableOne", "t");
    join = joins.Add("TableTwo", "t2");
    join.SchemaName = "Schema";

    const result = joins.GetIndex("TableTwo", "t2");
    const compare = 1;
    LJC.CheckValues("GetIndex()", result, compare);
  }
  // #endregion
}