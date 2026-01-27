"use strict";
// LJCAttributesTest.js

class LJCAttributesTest
{
  Run()
  {
    // Static Methods
    this.ToCollection();

    // Collection Data Methods
    this.Add();
    this.AddObject();
  }

  // #region Static Methods

  // Create typed collection from deserialized JavasScript array.
  ToCollection()
  {
    const array = [
      { Name: "id", Value: "idName" },
      { Name: "class", Value: "className" },
    ];

    const attributes = LJCAttributes.ToCollection(array);
    let result = attributes.Count;
    let compare = 2;
    LJC.CheckValues("ToCollection()", result, compare);
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  Add()
  {
    const attributes = new LJCAttributes();
    attributes.Add("id");

    const attribute = attributes.Retrieve("id");
    const result = attribute.Name;
    const compare = "id";
    LJC.CheckValues("Add()", result, compare);
  }

  // Adds the supplied item to the list.
  AddObject()
  {
    const attributes = new LJCAttributes();
    let attribute = new LJCAttribute("id", "idName");

    const addedAttrib = attributes.AddObject(attribute);
    let result = addedAttrib.Name;
    let compare = "id";
    LJC.CheckValues("AddObject()", result, compare);

    attribute = attributes.Retrieve("id");
    result = attribute.Name;
    compare = "id";
    LJC.CheckValues("AddObject()", result, compare);
  }
  // #endregion
} // LJCAttributesTest