"use strict";
// #SectionBegin Collection
// #Value _CollectionName_ LJCDataColumns
// #Value _CollectionVar_ DataColumns
// #Value _CollectionLocal_ dataColumns
// #Value _FileName_ LJCDataLib.js
// #Value _ItemName_ LJCDataColumn
// #Value _ItemVar_ DataColumn
// #Value _ItemLocal_ dataColumn
// #Value _KeyPropertyName_ PropertyName
// #Value _KeyPropertyLocal_ propertyName
// Copyright (c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// _FileName_

// ***************
/// <summary>Represents a collection of _ItemName_ data objects.</summary>
class _CollectionName_
{
  // #region Properties

  // The current items count.
  Count = 0;

  // The current #Items clone.
  ReadItems = [];

  // The internal collection item array.
  #Items = [];
  // #endregion

  // #region Static Methods

  /// <summary>
  ///   Create typed collection from deserialized JavasScript array.
  /// </summary>
  /// <param name="items">The items object.</param>
  /// <returns>The collection></returns.
  static ToCollection(items)
  {
    let ret_CollectionVar_ = new LJCDataColumns();

    if (items != null
      && items.length > 0)
    {
      for (let index = 0; index < items.length; index++)
      {
        let objItem = items[index];

        // Create typed object from stdClass.
        let _ItemLocal_ = LJCDataColumn.Copy(objItem);
        ret_CollectionVar_.AddObject(_ItemLocal_);
      }
    }
    return ret_CollectionVar_;
  }
  // #endregion

  // #region Data Class Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let ret_CollectionVar_ = new _CollectionName_();

    let names = this.PropertyNames();
    for (let index = 0; index < names.length; index++)
    {
      let _ItemLocal_ = this.#Items[index];
      if (_ItemLocal_ != null)
      {
        ret_CollectionVar_.AddObject(_ItemLocal_.Clone());
      }
    }
    return ret_CollectionVar_;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='items/Add/*' file='Doc/_CollectionName_.xml'/>
  Add(name, description)
  {
    let methodName = "Add()";
    let ret_ItemVar_ = null;

    let _ItemLocal_ = new _ItemName_(name, description);
    ret_ItemVar_ = this.AddObject(_ItemLocal_);
    return ret_ItemVar_;
  }

  /// <summary>Adds the supplied item to the list.</summary>
  /// <param name="item">The data object.</param>
  AddObject(_ItemLocal_)
  {
    let methodName = "AddObject()";

    this.#Items.push(_ItemLocal_);
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
    return _ItemLocal_;
  }

  /// <summary>Removes the item with the supplied name.</summary>
  /// <param name="name">The data object name.</param>
  Remove(_KeyPropertyLocal_)
  {
    let itemIndex = this.GetIndex(_KeyPropertyLocal_);
    if (itemIndex > -1)
    {
      let beginIndex = 0;
      this.#Items.splice(beginIndex, itemIndex);
      this.Count = this.#Items.length;
      this.ReadItems = Array.from(this.#Items);
    }
  }

  // Retrieves the item with the supplied name.
  /// <include path='items/Retrieve/*' file='Doc/_CollectionName_.xml'/>
  Retrieve(_KeyPropertyLocal_)
  {
    let ret_ItemVarName_ = this.#Items.find(item =>
      item.Name == _KeyPropertyLocal_);
    return ret_ItemVarName_;
  }

  // Retrieves the item at the supplied index.
  /// <include path='items/RetrieveWithIndex/*' file='Doc/_CollectionName_.xml'/>
  RetrieveWithIndex(index)
  {
    let ret_ItemVarName_ = null;

    if (index >= 0
      && this.#Items.length > index)
    {
      ret_ItemVarName_ = this.#Items[index];
    }
    return ret_ItemVarName_;
  }
  // #endregion

  // #region Other Methods

  /// <summary>Clears the collection list.</summary>
  Clear()
  {
    this.#Items = [];
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
  }

  /// <summary>Returns the collection element count.</summary>
  Count()
  {
    let retCount = 0;

    retCount = this.#Items.length;
    return retCount;
  }

  // Gets the index of the item with the supplied name.
  /// <include path='items/GetIndex/*' file='Doc/_CollectionName_.xml'/>
  GetIndex(_KeyPropertyLocal_)
  {
    let retIndex = -1;

    for (let index = 0; index < this.#Items.length; index++)
    {
      let item = this.#Items[index];
      if (item._KeyPropertyName == _KeyPropertyLocal_)
      {
        retIndex = index;
        break;
      }
    }
    return retIndex;
  }

  /// <summary>Gets an array of property names.</summary>
  /// <returns>The property name array.</returns>
  PropertyNames()
  {
    let retNames = [];

    for (let index = 0; index < this.#Items.length; index++)
    {
      let _KeyPropertyLocal_ = this.#Items[index];
      retNames.push(_KeyPropertyLocal_._KeyPropertyName_);
    }
    return retNames;
  }

  // Gets the items that match the supplied names.
  /// <include path='items/Items/*' file='Doc/Cities.xml'/>
  SelectItems(propertyNames)
  {
    let ret_CollectionVarName_ = null;

    if (null == propertyNames)
    {
      ret_CollectionVarName_ = this.#Items.Clone();
    }
    else
    {
      ret_CollectionVarName_ = new _CollectionName_();
      for (let index = 0; index < names.length; index++)
      {
        let _KeyPropertyLocal_ = propertyNames[index];
        let _ItemLocal_ = this.Retrieve(_KeyPropertyLocal_);
        if (_ItemLocal_ != null)
        {
          ret_CollectionVarName_.AddObject(_ItemLocal_);
        }
      }
    }
    return ret_CollectionVarName_;
  }
  // #endregion
}
// #SectionEnd Collection
