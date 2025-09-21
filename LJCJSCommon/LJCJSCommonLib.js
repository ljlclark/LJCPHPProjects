"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCJSCommonLib.js

// ***************
/// <summary>Common Static Functions</summary>
//  Element: AddEvent(), Element(), TagElements()
//  Check: HasText(), HasValue()
//  Value: GetText(), GetValue(), SetText(), SetValue()
//  Search: BinarySearch(), MiddlePosition()
//  Other: MouseLocation(), Visibility()
//  Show Property: AddPropertyOutput(), GetPropertyNames()
//    GetStartText(), ShowProperties(), ShowSelectProperties()
class LJC
{
  // ---------------
  // Element Methods

  // Adds an event handler.
  /// <include path='items/AddEvent/*' file='Doc/LJCCityListEvents.xml'/>
  static AddEvent(elementID, eventName, handler, parent)
  {
    let element = this.Element(elementID);
    if (element != null)
    {
      element.addEventListener(eventName, handler.bind(parent));
    }
  }

  // Gets the HTMLElement.
  static Element(elementID)
  {
    let retElement = null;

    retElement = document.getElementById(elementID);
    return retElement;
  }

  // Gets HTMLElements by Tag.
  static TagElements(parentElement, tag)
  {
    return parentElement.getElementsByTagName(tag);
  }

  // ---------------
  // Check Value Methods

  // Checks if the text has a value.
  static HasText(text)
  {
    let retValue = false;

    // *** Change ***
    if (text != null
      && this.IsString(text)
      && text.trim().length > 0)
    {
      retValue = true;
    }
    return retValue;
  }

  // Checks if an element has a value.
  static HasValue(element)
  {
    let retValue = false;

    if (element
      && element != null)
    {
      retValue = true;
    }
    return retValue;
  }

  // Checks if the values is a string.
  static IsString(value)
  {
    return typeof value === 'string';
  }

  // ---------------
  // Text and Value Methods

  // Gets the element text.
  static GetText(elementID)
  {
    let retValue = null;

    let element = this.Element(elementID);
    if (element != null)
    {
      retValue = element.innerText;
    }
    return retValue;
  }

  // Gets the element value.
  static GetValue(elementID)
  {
    let retValue = null;

    let element = this.Element(elementID);
    if (element != null)
    {
      retValue = element.value;
    }
    return retValue;
  }

  // Sets the element text.
  static SetText(elementID, text)
  {
    let element = this.Element(elementID);
    if (element != null)
    {
      element.innerText = text;
    }
  }

  // Sets the element value.
  static SetValue(elementID, value)
  {
    let element = this.Element(elementID);
    if (element != null)
    {
      element.value = value;
    }
  }

  // ---------------
  // Binary Search Methods

  // Returns the index of a search item in the array.
  static BinarySearch(array, sortFunction, compareFunction, showAlerts = false)
  {
    var retValue = -1;

    if (array
      && Array.isArray(array))
    {
      array.sort(sortFunction);

      // Set initial bounds
      let lowerIndex = 0;
      let upperIndex = array.length - 1;
      let nextCount = upperIndex - lowerIndex + 1;
      let index = this.MiddlePosition(nextCount) - 1;

      retValue = -2;
      while (-2 == retValue)
      {
        if (showAlerts)
        {
          let text = `${lowerBound} to ${upperBound}, (${nextCount}), ${index}`;
          alert(text);
        }

        let result = compareFunction(array[index]);
        switch (result)
        {
          // Item was found.
          case 0:
            retValue = index;
            if (showAlerts)
            {
              alert(`Found: index: ${index}`);
            }
            break;

          // Set previous index.
          case 1:
            if (1 == nextCount)
            {
              // There are no items left.
              retValue = -1;
              break;
            }

            // Get middle index of previous items.
            upperIndex = index;
            nextCount = upperIndex - lowerIndex;
            if (0 == nextCount)
            {
              retValue = NotFound;
            }
            index = upperIndex - this.MiddlePosition(nextCount);
            break;

          // Set next index.
          case -1:
            if (1 == nextCount)
            {
              // There are no items left.
              retValue = -1;
              break;
            }

            // Get middle index of next items.
            lowerIndex = index;
            nextCount = upperIndex - lowerIndex;
            index = lowerIndex + this.MiddlePosition(nextCount);
            break;
        }
      }
    }
    return retValue;
  }

  // Returns the middle position of the count value.
  static MiddlePosition(count)
  {
    var retValue = 0;
    if (0 == count % 2)
    {
      // Even length.
      retValue = count / 2;
    }
    else
    {
      // Odd length.
      let remainder = count % 2;
      retValue = (count - remainder) / 2 + 1;
    }
    return retValue;
  }

  // ---------------
  // Other Methods

  /// <summary>Creates JSON from the provided value.</summary>
  /// <param name="value">The object value.</param>
  /// <returns>The JSON text.</returns>
  static CreateJSON(value)
  {
    let retJSON = "";

    retJSON = JSON.stringify(value);
    return retJSON;
  }

  // Add to common.
  static Location(className, methodName, valueName = null)
  {
    let retLocation = "";

    if (LJC.HasText(className))
    {
      retLocation += className;
    }
    if (LJC.HasText(retLocation)
      && LJC.HasText(methodName))
    {
      retLocation += `.${methodName}`;
    }
    if (LJC.HasText(retLocation)
      && LJC.HasText(valueName))
    {
      retLocation += ` ${valueName}`;
    }
    if (LJC.HasText(retLocation))
    {
      retLocation += ":";
    }
    return retLocation;
  }

  /// <summary>Show the service message.</summary>
  /// <param name="location">The code location.</param>
  /// <param name="textValue">The text value.</param>
  /// <param name="force">Force the text to show.</param>
  static Message(location, textValue, force = false)
  {
    let show = force;

    if (LJC.HasText(textValue))
    {
      let text = textValue.toLowerCase();
      if (!text.startsWith("{\"servicename\":")
        && !text.startsWith("delete")
        && !text.startsWith("insert")
        && !text.startsWith("select")
        && !text.startsWith("update"))
      {
        show = true;
      }
    }

    if (show)
    {
      alert(`${location} ${textValue}`);
    }
  }

  /// <summary>Create a mouse location object.</summary.
  /// <param name="event">The event object.</param>
  /// <returns>The mouse location object.</returns>
  static MouseLocation(event)
  {
    let retLocation =
    {
      Top: event.pageY,
      Left: event.pageX,
    };
    return retLocation;
  }

  /// <summary>Parses JSON into an object.</summary>
  /// <param name="json">The json text.</param>
  /// <returns>The parsed object.</returns>
  static ParseJSON(json)
  {
    let retObject = "";

    retObject = JSON.parse(json);
    return retObject;
  }

  /// <summary>Sets the element visibility.</summary>
  /// <param name="elementID">The element ID.</param>
  /// <param name="value">
  ///   "visible" to show the element, "hidden" to hide.
  /// </param>
  static Visibility(elementID, value)
  {
    let element = LJC.Element(elementID);
    if (element != null)
    {
      element.style.visibility = value;
    }
  }

  // ---------------
  // Show Property Methods

  // 
  static AddPropertyOutput(item, propertyName)
  {
    let retValue = "";

    if (propertyName in item)
    {
      retValue = `${propertyName}=${item[propertyName]}\r\n`;
    }
    return retValue;
  }

  // Gets the default property names.
  static GetPropertyNames(typeName)
  {
    let retValue = null;

    switch (typeName.toLowerCase().trim())
    {
      case "window":
        retValue = [
          "parent", "document", "location", "frames", "length",
          "addEventListener", "removeEventListener"
        ];
        break;
      case "document":
        retValue = [
          "documentElement", "location", "baseURI", "body", "head",
          "nodeType", "hasChildNodes", "childNodes", "firstChild",
          "getElementByID", "getElementsByName",
          "getElementsByClassName", "getElementsByTagName",
          "addEventListener", "removeEventListener"
        ];
        break;
      case "element":
        retValue = [
          "localName", "tagName",
          "innerHTML", "outerHTML",
          "nodeType", "parentNode", "hasChildNodes", "childNodes", "firstChild",
          "getElementsByClassName", "getElementsByTagName",
        ];
        break;
      default:
        retValue = [
          "addEventListener", "removeEventListener"
        ];
        break;
    }
    return retValue;
  }

  // Get the property list start text.
  static GetStartText(typeName, startText)
  {
    let retValue = null;

    if (null == startText)
    {
      retValue = `${typeName.toLowerCase().trim()}: `;
    }
    else
    {
      retValue = `${starText.trim()}: `;
    }
    return retValue;
  }

  // Show the properties of an object that are not null or "" and
  // do not start with "on".
  static ShowProperties(location, item)
  {
    if (item)
    {
      let startText = `${location}: `;

      let results = startText;
      let page = 1;
      let count = 1;
      for (let propertyName in item)
      {
        if (false == propertyName.startsWith("on")
          && item[propertyName] != null
          && item[propertyName] != "")
        {
          if (count % 12 == 0)
          {
            alert(`${page} ${results}`);
            results = startText;
            page++;
          }
          count++;
          results += this.AddPropertyOutput(item, propertyName);
        }
      }
      if (results != startText)
      {
        alert(`${page} ${results}`);
      }
    }
  }

  // Show selected properties of an object.
  static ShowSelectProperties(item, typeName, startText, propertyNames)
  {
    if (item)
    {
      if (null == propertyNames)
      {
        propertyNames = this.GetPropertyNames(typeName);
      }
      startText = this.GetStartText(typeName, startText);

      let results = startText;
      let page = 1;
      let count = 1;
      let length = propertyNames.length;
      for (let index = 0; index < length; index++)
      {
        let propertyName = propertyNames[index];
        if (count % 12 == 0)
        {
          alert(`${page} ${results}`);
          results = startText;
          page++;
        }
        count++;
        results += this.AddPropertyOutput(item, propertyName);
      }
      if (results != startText)
      {
        alert(`${page} ${results}`);
      }
    }
  }
}
