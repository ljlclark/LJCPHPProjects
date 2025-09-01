// Copyright(c) Lester J. Clark 2022 - All Rights Reserved
// Params.js

// ***************
// Represents a collection of Param objects.
class Params
{
  // The Constructor function.
  constructor()
  {
    this.Items = [];
  }

  // Creates and adds an item to the collection.
  // Returns the added item.
  Add(sName, sValue)
  {
    let item = new Param(sName, sValue);
    this.Items[this.Items.length] = item;
    return item;
  }

  // Clones this object.
  Clone()
  {
    let retValue = new Params();
    let len = this.Items.length;
    for (let index = 0; index < len; index++)
    {
      let item = this.Items[index];
      let newItem = item.Clone();
      retValue.Items[retValue.Items.length] = newItem;
    }
    return retValue;
  }

  // Returns the item from the collection.
  // Uses iterative search.
  Find(name)
  {
    let retValue = null;

    if (Common.HasValue(name))
    {
      let len = this.Items.length;
      for (let index = 0; index < len; index++)
      {
        let item = this.Items[index];
        if (0 == item.Name.localeCompare(name))
        {
          retValue = item;
          break;
        }
      }
    }
    return retValue;
  }

  // Clears the Value property for the specified Param.
  ClearValue(name)
  {
    let param = this.Find(name);
    if (Common.HasValue(param))
    {
      param.Value = null;
    }
  }

  // Creates and returns a Params object from the Query string.
  static GetQueryParams(query)
  {
    let retValue = new Params();
    let parms = query.substr(1).split("&");
    if (parms.length > 0)
    {
      for (let index = 0; index < parms.length; index++)
      {
        let parm = parms[index].split("=");
        if (parm && 2 == parm.length)
        {
          retValue.Add(parm[0], parm[1]);
        }
      }
    }
    return retValue;
  }

  // Creates and returns the Query string from the Param items.
  CreateQuery(pageName)
  {
    let retValue = null;

    if (Common.HasValue(pageName))
    {
      retValue = `${pageName}?`;
      for (let index = 0; index < this.Items.length; index++)
      {
        let item = this.Items[index];
        if (Common.HasValue(item)
          && Common.HasValue(item.Value))
        {
          if (index > 0)
          {
            retValue += "&";
          }
          retValue += `${item.Name}=${item.Value}`;
        }
      }
    }
    return retValue;
  }
}
