"use strict";
// LJCJoinOnsTest.js

class LJCJoinOnsTest
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
      { FromColumnName: "fromColumnName1", ToColumnName: "toColumnName1" },
      { FromColumnName: "fromColumnName2", ToColumnName: "toColumnName2" },
    ];

    const joinOns = LJCJoinOns.ToCollection(array);
    const result = joinOns.Count;
    const compare = 2;
    LJC.CheckValues("ToCollection()", result, compare);
  }
  // #endregion

  // #region Data Class Methods

  // Creates a clone of this object.
  Clone()
  {
    const joinOns = new LJCJoinOns();
    let joinOn = joinOns.Add("fromColumnName1", "toColumnName1");
    joinOn = joinOns.Add("fromColumnName2", "toColumnName2");

    let clone = joinOns.Clone();
    const result = clone.Count;
    const compare = 2;
    LJC.CheckValues("Clone()", result, compare);
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  Add()
  {
    const joinOns = new LJCJoinOns();
    let joinOn = joinOns.Add("fromColumnName1", "toColumnName1");
    joinOn = joinOns.Add("fromColumnName2", "toColumnName2");

    joinOn = joinOns.Retrieve("fromColumnName2");
    let result = joinOn.ToColumnName;
    let compare = "toColumnName2";
    LJC.CheckValues("Add()", result, compare);
  }

  // Adds the supplied item to the list.
  AddObject()
  {
    const joinOns = new LJCJoinOns();
    let joinOn = joinOns.Add("fromColumnName1", "toColumnName1");
    joinOns.AddObject(joinOn);
    joinOn = joinOns.Add("fromColumnName2", "toColumnName2");
    joinOns.AddObject(joinOn);

    joinOn = joinOns.Retrieve("fromColumnName2");
    let result = joinOn.ToColumnName;
    let compare = "toColumnName2";
    LJC.CheckValues("AddObject()", result, compare);
  }

  // Clears the collection list.
  Clear()
  {
    const joinOns = new LJCJoinOns();
    let joinOn = joinOns.Add("fromColumnName1", "toColumnName1");
    joinOn = joinOns.Add("fromColumnName2", "toColumnName2");

    joinOns.Clear();
    const result = joinOns.Count;
    const compare = 0;
    LJC.CheckValues("Clear()", result, compare);
  }

  // Removes the object which matches the data values.
  Remove()
  {
    const joinOns = new LJCJoinOns();
    let joinOn = joinOns.Add("fromColumnName1", "toColumnName1");
    joinOn = joinOns.Add("fromColumnName2", "toColumnName2");

    joinOns.Remove("fromColumnName1");
    joinOn = joinOns.Retrieve("fromColumnName1");
    let result = "Found";
    if (null == joinOn)
    {
      result = "";
    }
    let compare = "";
    LJC.CheckValues("Remove()", result, compare);
  }

  // Retrieves the object which matches the data values.
  Retrieve()
  {
    const joinOns = new LJCJoinOns();
    let joinOn = joinOns.Add("fromColumnName1", "toColumnName1");
    joinOn = joinOns.Add("fromColumnName2", "toColumnName2");

    joinOn = joinOns.Retrieve("fromColumnName1");
    const result = joinOn.FromColumnName;
    let compare = "fromColumnName1";
    LJC.CheckValues("Retrieve()", result, compare);
  }

  // Retrieves the object at the supplied index.
  RetrieveAtIndex()
  {
    const joinOns = new LJCJoinOns();
    let joinOn = joinOns.Add("fromColumnName1", "toColumnName1");
    joinOn = joinOns.Add("fromColumnName2", "toColumnName2");

    joinOn = joinOns.RetrieveAtIndex(1);
    const result = joinOn.FromColumnName;
    let compare = "fromColumnName2";
    LJC.CheckValues("RetrieveAtIndex()", result, compare);
  }
  // #endregion

  // #region Other Methods

  // Gets the index of the object which matches the data values.
  GetIndex()
  {
    const joinOns = new LJCJoinOns();
    let joinOn = joinOns.Add("fromColumnName1", "toColumnName1");
    joinOn = joinOns.Add("fromColumnName2", "toColumnName2");

    const result = joinOns.GetIndex("fromColumnName2");
    const compare = 1;
    LJC.CheckValues("GetIndex()", result, compare);
  }
  // #endregion
}