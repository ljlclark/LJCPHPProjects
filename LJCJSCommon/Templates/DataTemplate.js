"use strict";
// #SectionBegin Collection
// #Value _FileName_
// #Value _ItemName_
// Copyright (c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// _FileName_

// ***************
/// <summary>The _ItemName_ data object class.</summary>
class _ItemName_
{
  // #region Properties

  /// <summary>The primary key.</summary>
  ID = 0;

  /// <summary>The parent key and partial unique key.</summary>
  ParentID = 0;

  // varchar(60)
  /// <summary>The parent name.</summary>
  ParentName = "";

  // varchar(60)
  /// <summary>The partial unique key.</summary>
  Name = "";

  // varchar(100)
  /// <summary>The description.</summary>
  Description = "";
  // #endregion

  // #region Static Methods

  // Creates a new object with the supplied object values.
  /// <include path='items/Copy/*' file='Doc/_ItemName_.xml'/>
  static Copy(obj_ItemName_)
  {
    let ret_ItemName_ = new _ItemName_();

    // Update properties of new object from provided object.
    for (let propertyName in this)
    {
      if (propertyName in obj_ItemName_)
      {
        ret_ItemName_[propertyname] = obj_ItemName_[propertyName];
      }
    }
    return ret_ItemName_;
  }
  // #endregion

  // #region Constructor Methods.

  // Initializes the object instance.
  /// <include path='items/constructor/*' file='Doc/_ItemName_.xml'/>
  constructor(parentID, name, description, id = 0)
  {
    this.ID = id;
    this.ParentID = parentID;
    this.Name = name;

    this.Description = description;
  }
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let ret_ItemName_ = new _ItemName_(this.ParentID, this.Name
      , this.Description, this.ID);
    return ret_ItemName_;
  }
  // #endregion
}
