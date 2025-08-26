"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCityPageData.js
// <script src="../../Common/Common.js"></script>
//   Element(), Visibility()
// <script src="LJCTableData.js"></script>
//   MoveNext(), MovePrevious(), SelectRow(), SelectColumnRow()

// ***************
/// <summary>Contains City HTML Table methods.</summary>
//  Constructor: constructor(), AddEvent()
//  Event Handlers: TableClick(), TableKeyDown()
//  Page Event Handlers: NextPage(), PrevPage(), Page()
//    UpdateLimitFlags(), UpdatePageData(), UpdateTableData()
class LJCCityPageData
{
  // ---------------
  // Properties

  Action;
  BeginKeyData;
  ConfigFile;
  ConfigName;
  EndKeyData
  Limit;

  // ---------------
  // The Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  constructor(listEvents, menuID)
  {
    this.Action = "None"; // Next, Previous, Top, Bottom, First?, Last?
    this.BeginKeyData = { ProvinceID: 0, Name: "" };
    this.ConfigFile = "DataConfigs.xml";
    this.ConfigName = "TestData";
    this.EndKeyData = { ProvinceID: 0, Name: "" };
    this.Limit = 10;
  }

  /// <summary>Creates a clone of this object.</summary>
  Clone()
  {
    return JSON.parse(JSON.stringify(this));
  }
}

