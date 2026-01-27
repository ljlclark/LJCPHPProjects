"use strict";
// LJCJoinOnTest.js

class LJCJoinOnTest
{
  Run()
  {
    // Static Methods
    this.Copy();

    // Data Class Methods
    this.Clone();
  }

  // #region Static Methods

  // Creates a new object with existing simple object values.
  Copy()
  {
    const simpleJoinOn = {
      "FromColumnName": "FromColumn",
      "ToColumn": "ToColumn",
    };
    const joinOn = LJCJoinOn.Copy(simpleJoinOn);

    const result = joinOn.FromColumnName;
    const compare = "FromColumn";
    LJC.CheckValues("Copy()", result, compare);
  }
  // #endregion

  // #region Data Class Methods

  // Creates a clone of this object.
  Clone()
  {
    const fromColumn = "FromColumn";
    const toColumn = "ToColumn";
    const joinOn = new LJCJoinOn(fromColumn, toColumn);

    let clone = joinOn.Clone();
    const result = clone.FromColumnName;
    const compare = "FromColumn";
    LJC.CheckValues("Clone()", result, compare);
  }
  // #endregion
}