"use strict";
// LJCJoinTest.js

class LJCJoinTest
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
    const simpleJoin = { "TableName": "TableName", "TableAlias": "t" };
    const join = LJCJoin.Copy(simpleJoin);

    const result = join.TableName;
    const compare = "TableName";
    LJC.CheckValues("Copy()", result, compare);
  }
  // #endregion

  // #region Data Class Methods

  // Creates a clone of this object.
  Clone()
  {
    const tableName = "TableName";
    const tableAlias = "t";
    const join = new LJCJoin(tableName, tableAlias);

    let clone = join.Clone();
    const result = clone.TableName;
    const compare = "TableName";
    LJC.CheckValues("Clone()", result, compare);
  }
  // #endregion
}