"use strict";
// #SectionBegin Collection
// #Value _CollectionName_
// #Value _FileName_
// #Value _ItemName_
// #Value _KeyPropertyName_
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

  // #region Data Class Methods

  /// <summary>Creates a clone of this object.</summary>
  /// <returns>The new cloned object.</returns>
  Clone()
  {
    let retCollection = new _CollectionName_();

    for (let index = 0; index < names.length; index++)
    {
      let item = this.#Items[index];
      if (item != null)
      {
        retCollection.AddObject(item.Clone());
      }
    }
    return retCollection;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='items/Add/*' file='Doc/_CollectionName_.xml'/>
  Add(name, description)
  {
    let methodName = "Add()";
    let retItem = null;

    let item = new _ItemName_(name, description);
    retItem = this.AddObject(item);
    return retItem;
  }

  /// <summary>Adds the supplied item to the list.</summary>
  /// <param name="item">The data object.</param>
  AddObject(item)
  {
    let methodName = "AddObject()";

    this.#Items.push(item);
    this.Count = this.#Items.length;
    this.ReadItems = Array.from(this.#Items);
    return item;
  }

  /// <summary>Removes the item with the supplied name.</summary>
  /// <param name="name">The data object name.</param>
  Remove(name)
  {
    let itemIndex = this.GetIndex(name);
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
  Retrieve(name)
  {
    let retItem = this.#Items.find(item =>
      item.Name == name);
    return retItem;
  }

  // Retrieves the item at the supplied index.
  /// <include path='items/RetrieveWithIndex/*' file='Doc/_CollectionName_.xml'/>
  RetrieveWithIndex(index)
  {
    let retItem = null;

    if (index >= 0
      && this.#Items.length > index)
    {
      retItem = this.#Items[index];
    }
    return retItem;
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
  GetIndex(name)
  {
    let retIndex = -1;

    for (let index = 0; index < this.#Items.length; index++)
    {
      if (this.#Items.Name == name)
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
      let item = this.#Items[index];
      retNames.push(item._KeyPropertyName_);
    }
    return retNames;
  }

  // Gets the items that match the supplied names.
  /// <include path='items/Items/*' file='Doc/Cities.xml'/>
  SelectItems(propertyNames)
  {
    let retItems = null;

    if (null == propertyNames)
    {
      retItems = this.#Items.Clone();
    }
    else
    {
      retItems = new _CollectionName_();
      for (let index = 0; index < names.length; index++)
      {
        let name = propertyNames[index];
        let item = this.Retrieve(name);
        if (item != null)
        {
          retItems.AddObject(item);
        }
      }
    }
    return retItems;
  }
  // #endregion
}
