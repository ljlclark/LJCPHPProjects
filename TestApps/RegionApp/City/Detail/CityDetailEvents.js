"use strict";
// Copyright(c) Lester J.Clark and Contributors.
// Licensed under the MIT License.
// CityDetailEvents.js

// ***************
/// <summary>Contains CityDetail event handlers.</summary>
class CityDetailEvents
{
  // The Constructor function.
  constructor()
  {
  }

  /// <summary>Adds the HTML event handlers.</summary>
  AddEvents()
  {
    document.addEventListener("keypress", this.DocumentKeyPress.bind(this));
    this.AddEvent("cancel", "click", this.CancelClick);
    this.AddEvent("commit", "click", this.CommitClick);
  }

  /// <summary>Adds an event handler.</summary>
  /// <param name="elementID"></param>
  /// <param name="evantName"></param>
  /// <param name="handler"></param>
  AddEvent(elementID, eventName, handler)
  {
    let element = Common.Element(elementID);
    if (element != null)
    {
      element.addEventListener(eventName, handler.bind(this));
    }
  }

  // ---------------
  // Event Handlers

  /// <summary>Document "click" handler method.</summary>
  /// <param name="event">The Target event.</param>
  DocumentKeyPress(event)
  {
    let code = event.which ? event.which : event.keycode;

    // Enter key clicks the "commit" button.
    if (13 == code)
    {
      let button = Common.Element("commit");
      if (button != null)
      {
        button.click();
      }
    }
  }

  /// <summary>Cancel button "click" handler method.</summary>
  CancelClick()
  {
    // Return to the main page.
    window.location = "../List/CityList.php";
  }

  /// <summary>Commit button "click" handler method.</summary>
  /// <remarks>
  ///   Submits "cityForm" to CityData.php.
  /// </remarks>
  CommitClick()
  {
    let success = true;

    success = this.MissingValue("name", "nameError");

    if (success)
    {
      // Submit the form.
      let form = Common.Element("cityForm");
      if (form != null)
      {
        form.action = "CityData.php";
        form.submit();
      }
    }
  }

  // ---------------
  // Methods

  // Sets the missing value error text.
  MissingValue(elementID, errorElementID)
  {
    let retValue = true;

    Common.SetText(errorElementID, "*");
    let value = Common.GetValue(elementID);
    if ("" == value.trim())
    {
      retValue = false;
      Common.SetText(errorElementID, "* Missing Value");
    }
    return retValue;
  }
}