"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCollectionLib.js

/// <summary>Base class that represents a collection of data objects.</summary>
// Data Class: Clone()
// Collection Data: _AddItem(), Clear(), Remove(), Retrieve(),
//   RetrieveAtIndex()
// Other: GetIndex(), IsMatch()
class LJCCollection
{
  // #region Properties

  /// <summary>The current items count.</summary>
  Count = 0;

  /// <summary>The contains the array of _Items data.</summary>
  ReadItems = [];

  /// <summary>
  /// The protected object array is part of what makes it a strongly typed
  /// collection.
  /// </summary>
  _Items = [];
  // #endregion

  // #region Data Class Methods

  // Creates a clone of this object.
  /// <include path='items/Clone/*' file='Doc/LJCCollection.xml'/>
  Clone(items)
  {
    let retItems = null;

    if (items instanceof LJCCollection)
    {
      retItems = Object.create(items);
      retItems.Clear();
      for (let index = 0; index < this._Items.length; index++)
      {
        let item = this._Items[index];
        if (item != null)
        {
          retItems.AddObject(item.Clone());
        }
      }
    }
    return retItems;
  }
  // #endregion

  // #region Collection Data Methods

  // Adds the item to the array.
  /// <include path='items/_AddItem/*' file='Doc/LJCCollection.xml'/>
  _AddItem(item)
  {
    let retItem = item;
    this._Items.push(item);
    //this.#UpdateProperties();
    this.Count = this._Items.length;
    this.ReadItems = Array.from(this._Items);
    return retItem;
  }

  // Clears the collection list.
  /// <include path='items/Clear/*' file='Doc/LJCCollection.xml'/>
  Clear()
  {
    this._Items = [];
    //this.#UpdateProperties();
    this.Count = this._Items.length;
    this.ReadItems = Array.from(this._Items);
  }

  // Removes the the object which matches the data values.
  /// <include path='items/Remove/*' file='Doc/LJCCollection.xml'/>
  Remove(dataColumns)
  {
    let itemIndex = this.GetIndex(dataColumns);
    if (itemIndex > -1)
    {
      this._Items.splice(itemIndex, 1);
      this.#UpdateProperties();
    }
  }

  // Retrieves the object which matches the data values.
  /// <include path='items/Retrieve/*' file='Doc/LJCCollection.xml'/>
  Retrieve(dataColumns)
  {
    let retItem = null;

    for (let index = 0; index < this._Items.length; index++)
    {
      let item = this._Items[index];
      let match = this.IsMatch(item, dataColumns);
      if (match)
      {
        retItem = item;
        break;
      }
    }
    return retItem;
  }

  // Retrieves the object with the supplied index.
  /// <include path='items/RetrieveAtIndex/*' file='Doc/LJCCollection.xml'/>
  RetrieveAtIndex(index)
  {
    let retObject = null;

    if (index >= 0
      && this._Items.length > index)
    {
      retObject = this._Items[index];
    }
    return retObject;
  }

  // Updates the property values.
  #UpdateProperties()
  {
    this.Count = this._Items.length;
    this.ReadItems = Array.from(this._Items);
  }
  // #endregion

  // #region Other Methods

  // Gets the index of the object which matches the data values.
  /// <include path='items/GetIndex/*' file='Doc/LJCCollection.xml'/>
  GetIndex(dataColumns)
  {
    let retIndex = -1;

    for (let index = 0; index < this._Items.length; index++)
    {
      let item = this._Items[index];
      let match = this.IsMatch(item, dataColumns);
      if (match)
      {
        retIndex = index;
        break;
      }
    }
    return retIndex;
  }

  // Checks if the item matches the data values.
  /// <include path='items/IsMatch/*' file='Doc/LJCCollection.xml'/>
  IsMatch(item, dataColumns)
  {
    let retMatch = true;

    for (let dataIndex = 0; dataIndex < dataColumns.Count; dataIndex++)
    {
      let dataColumn = dataColumns.RetrieveAtIndex(dataIndex);
      let propertyName = dataColumn.PropertyName;
      let itemValue = item[propertyName];
      let value = dataColumn.Value;
      if (itemValue != value)
      {
        retMatch = false;
        break;
      }
    }
    return retMatch;
  }
  // #endregion
}
