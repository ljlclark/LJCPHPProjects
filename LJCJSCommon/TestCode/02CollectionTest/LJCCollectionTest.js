"use strict";
// LJCCollectionTest.js

/// <summary>Represents a Test data object.</summary>
class TestItem
{
  // #region Properties - TestItem

  /// <summary>The unique Name value.</summary>
  /// <remarks>The unique key.</remarks>
  Name = "";

  /// <summary>The Sequence value.</summary>
  Sequence = 0;
  // #endregion

  // #region Static Methods - TestItem

  /// <summary>Creates a new data object from simple object values.</summary>
  /// <param name="objTestItem">The simple object.</param>
  /// <returns>The TestItem object.
  Copy(objTestItem)
  {
    let retItem = new TestItem();

    // Look for properties of simple object in typed object.
    for (let propertyName in objTestItem)
    {
      if (propertyName in retItem)
      {
        // Update new typed object properties from the simple object.
        retItem[propertyName] = objTestItem[propertyName];
      }
    }
    return retItem;
  }
  // #endregion

  // #region Constructor Methods

  /// <summary>Initializes a class instance.</summary>
  /// <param name="Name">The unique name value.</param>
  constructor(name)
  {
    this.Name = name;
    this.Sequence = 0;
  }
  // #endregion

  // #region Data Class Methods - TestItem

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The cloned object.</returns>
  Clone()
  {
    let retItem = new TestItem();
    retItem.Name = this.Name;
    retItem.Sequence = this.Sequence;
    return retItem;
  }
  // #endregion
}

class TestItems extends LJCCollection
{
  // #region Collection Data Methods

  /// <summary>Creates and adds the item to the list.</summary>
  /// <param name="name">The unique name value.</summary>
  Add(name)
  {
    let retItem = null;

    let item = new TestItem(name);
    retItem = this.AddObject(item);
    return retItem;
  }

  /// <summary>Adds the supplied item to the list.</summary>
  /// <param name="item">The item to be added to the list.</param>
  /// <returns>The added item.</returns>
  AddObject(item)
  {
    let retItem = null;

    // This check is part of what makes it a strongly typed collection.
    if (item instanceof TestItem)
    {
      // _AddItem is only used here.
      retItem = this._AddItem(item);
    }
    return retItem;
  }
  // #endregion
}

class LJCCollectionTest
{
  Run()
  {
    // Data Class Methods
    this.Clone();

    // Collection Data Methods
    this._AddItem();
    this.Clear();
    this.Remove();
    this.Retrieve();
    this.RetrieveAtIndex();

    // Other Methods
    this.GetIndex();
    this.IsMatch();
  }

  // Creates a clone of this object.
  Clone()
  {
    const testItems = new TestItems();
    let testItem = testItems.Add("NameOne");
    testItem.Sequence = 1;
    testItem = testItems.Add("NameTwo");
    testItem.Sequence = 2;

    // testItems is used as a template.
    const clonedItems = testItems.Clone(testItems);
    let result = 0;
    if (LJC.HasItems(clonedItems))
    {
      result = clonedItems.Count;
    }
    let compare = 2;
    LJC.CheckValues("Clone()", result, compare);

    let dataColumns = new LJCDataColumns();
    dataColumns.AddValue("Name", "NameTwo");
    testItem = testItems.Retrieve(dataColumns);
    result = testItem.Sequence;
    compare = 2;
    LJC.CheckValues("Clone()", result, compare);
  }

  // Adds the item to the array.
  _AddItem()
  {
    const testItems = new TestItems();
    let testItem = new TestItem("NameOne");
    testItem.Sequence = 1;
    testItems._AddItem(testItem);

    let result = testItems.Count;
    let compare = 1;
    LJC.CheckValues("_AddItem()", result, compare);
  }

  // Clears the collection list.
  Clear()
  {
    const testItems = new TestItems();
    let testItem = testItems.Add("NameOne");
    testItem.Sequence = 1;
    testItems.Clear();

    let result = testItems.Count;
    let compare = 0;
    LJC.CheckValues("Clear()", result, compare);
  }

  // Removes the object which matches the data values.
  Remove()
  {
    const testItems = new TestItems();
    let testItem = testItems.Add("NameOne");
    testItem.Sequence = 1;
    testItem = testItems.Add("NameTwo");
    testItem.Sequence = 2;

    const dataColumns = new LJCDataColumns();
    dataColumns.AddValue("Name", "NameOne");
    testItems.Remove(dataColumns);

    testItem = testItems.RetrieveAtIndex(0);
    const result = testItem.Name;
    const compare = "NameTwo";
    LJC.CheckValues("Remove()", result, compare);
  }

  // Retrieves the object which matches the data values.
  Retrieve()
  {
    const testItems = new TestItems();
    let testItem = testItems.Add("NameOne");
    testItem.Sequence = 1;
    testItem = testItems.Add("NameTwo");
    testItem.Sequence = 2;

    const dataColumns = new LJCDataColumns();
    dataColumns.AddValue("Name", "NameTwo");
    testItem = testItems.Retrieve(dataColumns);
    const result = testItem.Name;
    const compare = "NameTwo";
    LJC.CheckValues("Retrieve()", result, compare);
  }

  // Retrieves the object at the supplied index.
  RetrieveAtIndex()
  {
    const testItems = new TestItems();
    let testItem = testItems.Add("NameOne");
    testItem.Sequence = 1;
    testItem = testItems.Add("NameTwo");
    testItem.Sequence = 2;

    testItem = testItems.RetrieveAtIndex(1);
    const result = testItem.Name;
    const compare = "NameTwo";
    LJC.CheckValues("RetrieveAtIndex()", result, compare);
  }

  // Gets the index of the object which matches the data values.
  GetIndex()
  {
    const testItems = new TestItems();
    let testItem = testItems.Add("NameOne");
    testItem.Sequence = 1;
    testItem = testItems.Add("NameTwo");
    testItem.Sequence = 2;

    const dataColumns = new LJCDataColumns();
    dataColumns.AddValue("Name", "NameTwo");
    const result = testItems.GetIndex(dataColumns);
    const compare = 1;
    LJC.CheckValues("GetIndex()", result, compare);
  }

  // Checks if the item matches the data values.
  IsMatch()
  {
    const testItems = new TestItems();
    let testItem = testItems.Add("NameOne");
    testItem.Sequence = 1;
    testItem = testItems.Add("NameTwo");
    testItem.Sequence = 2;

    const dataColumns = new LJCDataColumns();
    dataColumns.AddValue("Name", "NameTwo");
    let result = testItems.IsMatch(testItem, dataColumns);
    result = result ? "true" : "false";
    const compare = "true";
    LJC.CheckValues("IsMatch()", result, compare);
  }
}