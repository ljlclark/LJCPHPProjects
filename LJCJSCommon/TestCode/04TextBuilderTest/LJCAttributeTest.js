"use strict";
// LJCAttributeTest.js

class LJCAttributeTest
{
  Run()
  {
    // Static Methods
    this.Copy();

    // Constructor Methods
    this.Constructor();

    // Data Class Methods
    this.Clone();
  }

  // #region Static Methods

  // Create typed collection from deserialized JavasScript array.
  Copy()
  {
    const simpleAttribute = {
      "Name": "id",
      "Value": "name",
    };

    const attribute = LJCAttribute.Copy(simpleAttribute);
    const result = attribute.Name;
    const compare = "id";
    LJC.CheckValues("Copy()", result, compare);
  }
  // #endregion

  // #region Constructor Methods

  Constructor()
  {
    const attribute = new LJCAttribute("id", "name");
    const result = attribute.Name;
    const compare = "id";
    LJC.CheckValues("Constructor()", result, compare);
  }
  // #endregion

  // #region Data Class Methods

  Clone()
  {
    const attribute = new LJCAttribute("id", "name");
    const clone = attribute.Clone();
    const result = clone.Name;
    const compare = "id";
    LJC.CheckValues("Clone()", result, compare);
  }
  // #endregion
}