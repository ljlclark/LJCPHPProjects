"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCCommonLib.js

// ***************
/// <summary>Common Static Class Library</summary>
//  Element: AddEvent(), AverageCharWidth(), Element(), ElementStyle(),
//    Round(), SelectorStyle(), TagElements(), TextWidth()
//  Check: HasElementValue(), HasElements(), HasText(), IsBackTab(),
//    IsNumber(), IsShiftOnly(), IsSimpleType(), IsString(), IsTab()
//  Value: GetText(), GetValue(), SetText(), SetValue()
//  TextArea: EventTextRows(), GetTextCols(), SetTextRows()
//  Search: BinarySearch(), MiddlePosition()
//  Other: CreateJSON(), Location(), Message(), MouseLocation(), ParseJSON()
//         ShowText(), ToArray(), Visibility()
//  Show Property: AddPropertyOutput(), GetPropertyNames()
//    GetStartText(), ShowProperties(), ShowSelectProperties()
class LJC
{
  // #region Element Methods

  // Adds an event handler.
  /// <include path='members/AddEvent/*' file='Doc/LJC.xml'/>
  static AddEvent(elementID, eventName, handler, handlerOwner = null)
  {
    let element = this.Element(elementID);
    if (element != null)
    {
      if (handlerOwner != null)
      {
        element.addEventListener(eventName, handler.bind(handlerOwner));
      }
      else
      {
        element.addEventListener(eventName, handler);
      }
    }
  }

  // Gets the average character width using the first selector element font.
  /// <include path='members/AverageCharWidth/*' file='Doc/LJC.xml'/>
  static AverageCharWidth(selector, text)
  {
    let font = LJC.SelectorStyle(selector, "font");
    let textWidth = LJC.TextWidth(font, text);
    let averageWidth = textWidth / text.length;
    let retValue = LJC.Round(averageWidth, 2);
    return retValue;
  }

  // Gets the HTMLElement.
  /// <include path='members/Element/*' file='Doc/LJC.xml'/>
  static Element(elementID)
  {
    let retElement = null;

    retElement = document.getElementById(elementID);
    return retElement;
  }

  // Gets the element ComputedStyle property.
  /// <include path='members/ElementStyle/*' file='Doc/LJC.xml'/>
  static ElementStyle(element, propertyName)
  {
    let css = window.getComputedStyle(element, null);
    let retValue = css.getPropertyValue(propertyName);
    return retValue;
  }

  // Rounds and truncates to the provided place value.
  /// <include path='members/Round/*' file='Doc/LJC.xml'/>
  static Round(value, placeValue = 0)
  {
    if (placeValue < 0)
    {
      placeValue = 0;
    }
    let apply = "1" + "0".repeat(placeValue);
    apply = Number(apply);

    let retValue = value * apply;
    retValue = Math.round(retValue);
    retValue = retValue / apply;
    return retValue;
  }

  // Gets the first matching selector ComputedStyle property.
  /// <include path='members/SelectorStyle/*' file='Doc/LJC.xml'/>
  static SelectorStyle(selector, propertyName)
  {
    let eItem = document.querySelector(selector);
    let retValue = LJC.ElementStyle(eItem, propertyName);
    return retValue;
  }

  // Gets HTMLElements by Tag.
  /// <include path='members/TagElements/*' file='Doc/LJC.xml'/>
  static TagElements(parentElement, tagName)
  {
    return parentElement.getElementsByTagName(tagName);
  }

  // Gets the text width.
  /// <include path='members/TextWidth/*' file='Doc/LJC.xml'/>
  static TextWidth(font, text)
  {
    let canvas = document.createElement("canvas");
    let context = canvas.getContext("2d");
    context.font = font;
    let metric = context.measureText(text);
    let retValue = metric.width;
    return retValue;
  }
  // #endregion

  // #region Check Value Methods

  // Checks if an array has elements.
  /// <include path='members/HasElements/*' file='Doc/LJC.xml'/>
  static HasElements(arrValue)
  {
    let retValue = false;

    if (Array.isArray(arrValue))
    {
      if (arrValue.length > 0)
      {
        retValue = true;
      }
    }
    return retValue;
  }

  // Checks if an element has a value.
  /// <include path='members/HasElementValue/*' file='Doc/LJC.xml'/>
  static HasElementValue(element)
  {
    let retValue = false;

    if (element
      && LJC.HasText(element.value))
    {
      retValue = true;
    }
    return retValue;
  }

  // Checks if the text has a value.
  /// <include path='members/HasText/*' file='Doc/LJC.xml'/>
  static HasText(text)
  {
    let retValue = false;

    if (text != null
      && this.IsString(text)
      && text.trim().length > 0)
    {
      retValue = true;
    }
    return retValue;
  }

  // Checks keydown for a Backtab key.
  /// <include path='members/IsBackTab/*' file='Doc/LJC.xml'/>
  static IsBackTab(keyDownEvent)
  {
    let retValue = false;

    if (keyDownEvent.shiftKey
      && "Tab" == keyDownEvent.key)
    {
      retValue = true;
    }
    return retValue;
  }

  // Checks if the text is a number.
  /// <include path='members/IsNumber/*' file='Doc/LJC.xml'/>
  static IsNumber(text)
  {
    let retValue = true;

    for (let index = 0; index < text.length; index++)
    {
      let ch = text.charAt(index);
      //let result = /^\d+$/.test(ch);
      let result = /^\d+/.test(ch);
      if (!result)
      {
        if (!"+-.".includes(ch))
        {
          retValue = false;
          break;
        }
      }
    }
    return retValue;
  }

  // Checks keydown for only a Shift key.
  /// <include path='members/IsShiftOnly/*' file='Doc/LJC.xml'/>
  static IsShiftOnly(keyDownEvent)
  {
    let retValue = false;

    if ("Shift" == keyDownEvent.key)
    {
      retValue = true;
    }
    return retValue;
  }

  // Checks if the value is a primitive type.
  /// <include path='members/IsSimpleType/*' file='Doc/LJC.xml'/>
  static IsSimpleType(value)
  {
    let retValue = false;

    if (value === null)
    {
      retValue = true;
    }

    if (!retValue)
    {
      const typeName = typeof value;
      if (
        typeName === 'string' ||
        typeName === 'number' ||
        typeName === 'boolean' ||
        typeName === 'undefined' ||
        typeName === 'bigint' ||
        typeName === 'symbol')
      {
        retValue = true;
      }
    }
    return retValue;
  }

  // Checks if the value is a string.
  /// <include path='members/IsString/*' file='Doc/LJC.xml'/>
  static IsString(value)
  {
    return typeof value === 'string';
  }

  // Checks keydown for a Tab key.
  /// <include path='members/IsTab/*' file='Doc/LJC.xml'/>
  static IsTab(keyDownEvent)
  {
    let retValue = false;

    if (keyDownEvent.key != "Shift")
    {
      if (!keyDownEvent.shiftKey
        && "Tab" == keyDownEvent.key)
      {
        retValue = true;
      }
    }
    return retValue;
  }
  // #endregion

  // #region Text and Value Methods

  // Gets the element text.
  /// <include path='members/GetText/*' file='Doc/LJC.xml'/>
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

  // Gets the input element value.
  /// <include path='members/GetValue/*' file='Doc/LJC.xml'/>
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
  /// <include path='members/SetText/*' file='Doc/LJC.xml'/>
  static SetText(elementID, text)
  {
    let element = this.Element(elementID);
    if (element != null)
    {
      element.innerText = text;
    }
  }

  // Sets the input element value.
  /// <include path='members/SetValue/*' file='Doc/LJC.xml'/>
  static SetValue(elementID, value)
  {
    let element = this.Element(elementID);
    if (element != null)
    {
      element.value = value;
    }
  }
  // #endregion

  // #region TextArea Functions

  // Sets the textarea rows for newlines.
  /// <include path='members/EventTextRows/*' file='Doc/LJC.xml'/>
  static EventTextRows(event)
  {
    let element = event.target;
    if ("textarea" == element.localName)
    {
      LJC.SetTextRows(element);
    }
  }

  // Gets the textarea columns.
  /// <include path='members/GetTextCols/*' file='Doc/LJC.xml'/>
  static GetTextCols(width, widthDivisor, fontDivisor)
  {
    let retValue = Math.ceil((width / widthDivisor) / fontDivisor);
    return retValue;
  }

  // Sets the textarea rows for newlines.
  /// <include path='members/SetTextRows/*' file='Doc/LJC.xml'/>
  static SetTextRows(element)
  {
    let count = element.rows;
    let matches = element.value.match(/\n/g);
    if (Array.isArray(matches))
    {
      count = matches.length + 1;
    }
    else
    {
      count = 1;
    }
    element.rows = count;
  }
  // #endregion

  // #region Binary Search Methods

  // Returns the index of a search item in the array.
  /// <include path='members/BinarySearch/*' file='Doc/LJC.xml'/>
  static BinarySearch(array, sortMethod, compareMethod, showAlerts = false)
  {
    var retValue = -1;

    if (array
      && Array.isArray(array))
    {
      array.sort(sortMethod);

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

        let result = compareMethod(array[index]);
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

  // Returns the middle index of the count value.
  /// <include path='members/MiddlePosition/*' file='Doc/LJC.xml'/>
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
  // #endregion

  // #region Other Methods

  // Creates JSON from the provided value.
  /// <include path='members/CreateJSON/*' file='Doc/LJC.xml'/>
  static CreateJSON(value)
  {
    let retJSON = "";

    retJSON = JSON.stringify(value);
    return retJSON;
  }

  // Creates the debug location text.
  /// <include path='members/Location/*' file='Doc/LJC.xml'/>
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

  // Shows the service message.
  /// <include path='members/Message/*' file='Doc/LJC.xml'/>
  static Message(location, textValue, force = false, textDialogID = ""
    , textAreaID = "")
  {
    let show = force;

    if (LJC.HasText(textValue))
    {
      let text = textValue.toLowerCase().trim();
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
      if (LJC.HasText(textDialogID)
        && LJC.HasText(textAreaID))
      {
        let eText = LJC.Element(textAreaID);
        eText.value = `${location}\r\n ${textValue}`;
        let eTextDialog = LJC.Element(textDialogID);
        eTextDialog.showModal();
      }
      else
      {
        alert(`${location} ${textValue}`);
      }
    }
  }

  // Creates a mouse location object.
  /// <include path='members/MouseLocation/*' file='Doc/LJC.xml'/>
  static MouseLocation(event)
  {
    let retLocation =
    {
      Top: event.pageY,
      Left: event.pageX,
    };
    return retLocation;
  }

  // Parses JSON into an object.
  /// <include path='members/ParseJSON/*' file='Doc/LJC.xml'/>
  static ParseJSON(json)
  {
    let retObject = "";

    retObject = JSON.parse(json);
    return retObject;
  }

  // Show text in textArea element.
  /// <include path='members/ShowText/*' file='Doc/LJC.xml'/>
  static ShowText(className, methodName, valueName, objValue, force = false
    , textDialogID = "", textAreaID = "")
  {
    let locationText = LJC.Location(className, methodName, valueName);

    let value = objValue;
    if (!LJC.IsSimpleType(objValue))
    {
      value = LJC.CreateJSON(objValue);
    }
    if (LJC.HasText(textDialogID)
      && LJC.HasText(textAreaID))
    {
      let eText = LJC.Element(textAreaID);
      eText.value = `${locationText} \r\n${value}`;
      let eTextDialog = LJC.Element(textDialogID);
      eTextDialog.showModal();
    }
    else
    {
      // Value that has no text or starts with ""ServiceName:"", "delete",
      // "insert", "select" or "update" will not display unless force = true.
      LJC.Message(locationText, value, force);
    }
  }

  // Copy collection items to an indexed array.
  /// <include path='members/ToArray/*' file='Doc/LJC.xml'/>
  static ToArray(items)
  {
    let retArray = [];

    // items must support Count and RetrieveAtIndex().
    //if (Object.hasOwn(items, "Count")
    //  && typeof items.RetrieveAtIndex === "function")
    if (items instanceof LJCCollection)
    {
      for (let index = 0; index < items.Count; index++)
      {
        let item = items.RetrieveAtIndex(index);
        retArray.push(item);
      }
    }
    return retArray;
  }

  // Sets the element visibility.
  /// <include path='members/Visibility/*' file='Doc/LJC.xml'/>
  static Visibility(elementID, value)
  {
    let element = LJC.Element(elementID);
    if (element != null)
    {
      element.style.visibility = value;
    }
  }
  // #endregion

  // #region Show Property Methods

  // Gets the default property names.
  /// <include path='members/GetPropertyNames/*' file='Doc/LJC.xml'/>
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

  // Gets the property output.
  /// <include path='members/AddPropertyOutput/*' file='Doc/LJC.xml'/>
  static GetPropertyOutput(item, propertyName)
  {
    let retValue = "";

    if (propertyName in item)
    {
      retValue = `${propertyName}=${item[propertyName]}\r\n`;
    }
    return retValue;
  }

  // Get the property list start text.
  /// <include path='members/GetStartText/*' file='Doc/LJC.xml'/>
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
  /// <include path='members/ShowProperties/*' file='Doc/LJC.xml'/>
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
          results += this.GetPropertyOutput(item, propertyName);
        }
      }
      if (results != startText)
      {
        alert(`${page} ${results}`);
      }
    }
  }

  // Show selected properties of an object.
  /// <include path='members/ShowSelectProperties/*' file='Doc/LJC.xml'/>
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
        results += this.GetPropertyOutput(item, propertyName);
      }
      if (results != startText)
      {
        alert(`${page} ${results}`);
      }
    }
  }
  // #endregion
}

// ***************
/// <summary>Common Debug Class</summary>
class Debug
{
  // #region Properties

  // The debug location class name.
  #ClassName = "";

  // The text value ID.
  #TextAreaID = "";

  // The text dialog ID.
  #TextDialogID = "";
  // #endregion

  // #region Constructor methods.

  /// <summary>Initializes the object instance.</summary>
  /// <include path='members/constructor/*' file='Doc/Debug.xml'/>
  constructor(className)
  {
    this.#ClassName = className;
  }

  /// <summary>Sets the dialog values.</summary>
  /// <include path='members/SetDialogValues/*' file='Doc/Debug.xml'/>
  SetDialogValues(textDialogID, textAreaID)
  {
    this.#TextDialogID = textDialogID;
    this.#TextAreaID = textAreaID;
  }
  // #endregion

  // #region Methods.

  /// <summary>Show text with alert().</summary>
  /// <include path='members/ShowText/*' file='Doc/Debug.xml'/>
  ShowText(methodName, valueName, objValue, force = false)
  {
    let text = LJC.Location(this.#ClassName, methodName, valueName);
    let value = this.#GetValue(objValue);
    LJC.Message(text, value, force);
  }

  /// <summary>Show text in textArea element.</summary>
  /// <include path='members/ShowDialog/*' file='Doc/Debug.xml'/>
  ShowDialog(methodName, valueName, objValue, force = false)
  {
    let text = LJC.Location(this.#ClassName, methodName, valueName);
    let value = this.#GetValue(objValue);
    LJC.Message(text, value, force, this.#TextDialogID, this.#TextAreaID);
  }

  // Get value or JSON.
  #GetValue(objValue)
  {
    let retValue = objValue;

    if (!LJC.IsSimpleType(objValue))
    {
      retValue = LJC.CreateJSON(objValue);
    }
    return retValue;
  }
  // #endregion
}
