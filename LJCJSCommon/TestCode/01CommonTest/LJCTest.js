"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCTest.js

class LJCTest
{
  compareValue = "";

  Run()
  {
    // Element Methods
    this.AddEvent();
    this.AverageCharWidth();
    this.Element();
    this.ElementStyle();
    this.Round();
    this.SelectorStyle();
    this.TagElements();
    this.TextWidth();

    // Check Value Methods
    this.HasElements();
    this.HasElementValue();
    this.HasText();
    this.IsKey();
    this.IsNumber();
    this.IsShiftedKey();
    this.IsSimpleType();
    this.IsString();

    // Text and Value Methods
    this.GetText();
    this.GetValue();
    this.SetText();
    this.SetValue();

    // TestArea Methods
    this.EventTextRows();
    this.GetTextCols();
    this.SetTextRows();

    // Binary Search Methods
    this.BinarySearch();
    this.MiddlePosition();

    // Other Methods
    this.CreateJSON();
    this.Location();
    this.Message();
    this.MouseLocation();
    this.ParseJSON();
    this.ShowText();
    this.ToArray();
    this.Visibility();
  }

  // #region Element Methods

  // Adds an event handler.
  AddEvent()
  {
    const elementID = "targetInput";
    const eventName = "keydown";
    const handler = this.AddEventHandler.bind(this);
    LJC.AddEvent(elementID, eventName, handler);

    // Tab
    let eventOptions = {
      key: "Tab",
      code: "Tab",
      ctrlKey: false,
      shiftKey: false,
      altKey: false,
      metaKey: false,
      repeat: false,
      bubbles: true
    };
    let keyDownEvent = new KeyboardEvent('keydown', eventOptions);
    this.CompareValue = true;
    targetInput.dispatchEvent(keyDownEvent);

    targetInput.removeEventListener("keydown", handler);
  }

  AddEventHandler(keyDownEvent)
  {
    const result = LJC.IsKey("Tab", keyDownEvent);

    const compare = this.CompareValue;
    LJC.CheckValues("AddEventHandler()", result, compare);
  }

  // Gets the average character width using the first selector element font.
  AverageCharWidth()
  {
    // <body>
    //   <!-- The dialog for debug or other display text. -->
    //   <dialog id="textDialog"
    //     style="border: none; padding: 0px">
    //     <textarea id="textArea" rows="25" cols="80" autocorrect="off"
    //       autocapitalize="off" spellcheck="false" readonly
    //       style="border: none; padding: 10px">
    //     </textarea>
    //   </dialog>
    // </body>

    const selector = "div";
    let font = LJC.SelectorStyle(selector, "font");
    const text = "This is sample text.";
    let result = LJC.AverageCharWidth(font, text);

    // With font = "16px Times New Roman"
    let compare = 6.22;
    LJC.CheckValues("AverageCharWidth()", result, compare);

    const textAreaID = "textArea";
    const eTextArea = LJC.Element(textAreaID);
    font = LJC.ElementStyle(eTextArea, "font");
    result = LJC.AverageCharWidth(font, text);

    // With font = "13.3333px monospace"
    compare = 7.33;
    LJC.CheckValues("AverageCharWidth()", result, compare);
  }

  // Gets the HTMLElement by ID.
  Element()
  {
    const elementID = "testDiv";
    const element = LJC.Element(elementID);

    let result = element.tagName;
    let compare = "DIV";
    LJC.CheckValues("Element()", result, compare);

    result = element.id;
    compare = "testDiv";
    LJC.CheckValues("Element()", result, compare);
  }

  // Gets the element ComputedStyle property.
  ElementStyle()
  {
    const element = testDiv;
    const propertyName = "color";
    const result = LJC.ElementStyle(element, propertyName);

    const compare = "rgb(0, 0, 0)";
    LJC.CheckValues("ElementStyle()", result, compare);
  }

  // Rounds and truncates to the provided place value.
  Round()
  {
    const value = 3.14159;
    const placeValue = 2;
    const result = LJC.Round(value, placeValue);

    const compare = 3.14;
    LJC.CheckValues("Round()", result, compare);
  }

  // Gets the first matching selector ComputedStyle property.
  SelectorStyle()
  {
    const selector = "div";
    const propertyName = "color";
    const result = LJC.SelectorStyle(selector, propertyName);

    const compare = "rgb(0, 0, 0)";
    LJC.CheckValues("SelectorStyle()", result, compare);
  }

  // Gets HTMLElements by Tag.
  TagElements()
  {
    const parentElement = document.body;
    const tagName = "div";

    const elements = LJC.TagElements(parentElement, tagName);
    const element = elements[0];
    const result = element.id;

    const compare = "testDiv";
    LJC.CheckValues("TagElements()", result, compare);
  }

  // Gets the text width.
  TextWidth()
  {
    const element = document.body;
    const propertyName = "font";
    const font = LJC.ElementStyle(element, propertyName);
    const text = "This is sample text.";
    const value = LJC.TextWidth(font, text);
    const result = LJC.Round(value);

    // With font = "16px "Times New Roman"
    const compare = "124";
    LJC.CheckValues("TagElements()", result, compare);
  }
  // #endregion

  // #region Check Value Methods

  // Checks if an array has elements.
  HasElements()
  {
    let arrValue = ["Value"];
    let result = LJC.HasElements(arrValue);
    let compare = true;
    LJC.CheckValues("HasElements()", result, compare);

    arrValue = [];
    result = LJC.HasElements(arrValue);
    compare = false;
    LJC.CheckValues("HasElements()", result, compare);
  }

  // Checks if an element has a value.
  HasElementValue()
  {
    const element = targetInput;
    LJC.SetValue("targetInput", "X");
    let result = LJC.HasElementValue(element);
    let compare = true;
    LJC.CheckValues("HasElementValue()1", result, compare);

    LJC.SetValue("targetInput", "  ");
    result = LJC.HasElementValue(element);
    compare = false;
    LJC.CheckValues("HasElementValue()", result, compare);
  }

  // Checks if the text has a value.
  HasText()
  {
    let text = "X";
    let result = LJC.HasText(text);
    let compare = true;
    LJC.CheckValues("HasText()", result, compare);

    text = "  ";
    result = LJC.HasText(text);
    compare = false;
    LJC.CheckValues("HasText()", result, compare);
  }

  // Checks keydown for a supplied key.
  IsKey()
  {
    const elementID = "targetInput";
    const eventName = "keydown";
    const handler = this.IsKeyHandler.bind(this);
    LJC.AddEvent(elementID, eventName, handler);

    // Tab
    let eventOptions = {
      key: "Tab",
      code: "Tab",
      ctrlKey: false,
      shiftKey: false,
      altKey: false,
      metaKey: false,
      repeat: false,
      bubbles: true,
    };
    let keyDownEvent = new KeyboardEvent(eventName, eventOptions);
    this.CompareValue = true;
    targetInput.dispatchEvent(keyDownEvent);

    let element = LJC.Element("targetInput");
    element.removeEventListener("keydown", handler);
  }

  IsKeyHandler(keyDownEvent)
  {
    const result = LJC.IsKey("Tab", keyDownEvent);

    const compare = this.CompareValue;
    LJC.CheckValues("IsShiftedKey()", result, compare);
  }

  // Checks if the text is a number.
  IsNumber()
  {
    let number = 1;
    let result = LJC.IsNumber(number);
    let compare = true;
    LJC.CheckValues("IsNumber()", result, compare);

    number = "X";
    result = LJC.IsNumber(number);
    compare = false;
    LJC.CheckValues("IsNumber()", result, compare);
  }

  // Checks keydown for shift and the supplied key.
  IsShiftedKey()
  {
    const elementID = "targetInput";
    const eventName = "keydown";
    const handler = this.IsShiftedKeyHandler.bind(this);
    LJC.AddEvent(elementID, eventName, handler);

    // Back Tab
    let eventOptions = {
      key: "Tab",
      code: "Tab",
      ctrlKey: false,
      shiftKey: true,
      altKey: false,
      metaKey: false,
      repeat: false,
      bubbles: true,
    };
    let keyDownEvent = new KeyboardEvent(eventName, eventOptions);
    this.CompareValue = true;
    targetInput.dispatchEvent(keyDownEvent);

    let element = LJC.Element("targetInput");
    element.removeEventListener(keyDownEvent, handler);
  }

  IsShiftedKeyHandler(keyDownEvent)
  {
    const result = LJC.IsShiftedKey("Tab", keyDownEvent);

    const compare = this.CompareValue;
    LJC.CheckValues("IsShiftedKey()", result, compare);
  }

  //Checks if the value is a primitive type.
  IsSimpleType()
  {
    let number = 1;
    let result = LJC.IsSimpleType(number);
    let compare = true;
    LJC.CheckValues("IsSimpleType()", result, compare);

    number = [];
    result = LJC.IsSimpleType(number);
    compare = false;
    LJC.CheckValues("IsSimpleType()", result, compare);
  }

  // Checks if the value is a string.
  IsString()
  {
    let number = "1";
    let result = LJC.IsString(number);
    let compare = true;
    LJC.CheckValues("IsSimpleType()", result, compare);

    number = 1;
    result = LJC.IsString(number);
    compare = false;
    LJC.CheckValues("IsString()", result, compare);
  }
  // #endregion

  // #region Text and Value Methods

  // Gets the element text.
  GetText()
  {
    // <body>
    //   <div id="testDiv"></div>
    // </body>

    testDiv.innerText = "X";
    let result = LJC.GetText("testDiv");
    let compare = "X";
    LJC.CheckValues("GetText()", result, compare);

    testDiv.innerText = "";
    result = LJC.GetText("testDiv");
    compare = "";
    LJC.CheckValues("GetText()", result, compare);
  }

  // Gets the input element value.
  GetValue()
  {
    // <body>
    //   <input type="text" id="targetInput">
    // </body>

    targetInput.value = "X";
    let result = LJC.GetValue("targetInput");
    let compare = "X";
    LJC.CheckValues("GetValue()", result, compare);

    targetInput.value = "";
    result = LJC.GetValue("targetInput");
    compare = "";
    LJC.CheckValues("GetValue()", result, compare);
  }

  // Sets the element text.
  SetText()
  {
    // <body>
    //   <div id="testDiv"></div>
    // </body>

    LJC.SetText("testDiv", "X");
    let result = LJC.GetText("testDiv");
    let compare = "X";
    LJC.CheckValues("SetText()", result, compare);

    LJC.SetText("testDiv", "");
    result = LJC.GetText("testDiv");
    compare = "";
    LJC.CheckValues("SetText()", result, compare);
  }

  // Sets the input element value.
  SetValue()
  {
    // <body>
    //   <input type="text" id="targetInput">
    // </body>

    const elementID = "targetInput";
    LJC.SetValue(elementID, "X");
    let result = LJC.GetValue(elementID);
    let compare = "X";
    LJC.CheckValues("SetValue()", result, compare);

    LJC.SetValue(elementID, "");
    result = LJC.GetValue(elementID);
    compare = "";
    LJC.CheckValues("GetValue()", result, compare);
  }
  // #endregion

  // #region TextArea Methods

  // Sets the textarea rows for newlines.
  EventTextRows()
  {
    const elementID = "textArea";
    const eventName = "keydown";
    const handler = this.IsEnterHandler.bind(this);
    LJC.AddEvent(elementID, eventName, handler);

    // Enter
    let eventOptions = {
      key: "Enter",
      code: "Enter",
      ctrlKey: false,
      shiftKey: false,
      altKey: false,
      metaKey: false,
      repeat: false,
      bubbles: true,
    };
    let keyDownEvent = new KeyboardEvent(eventName, eventOptions);
    this.CompareValue = true;
    textArea.dispatchEvent(keyDownEvent);

    textArea.removeEventListener(eventName, handler);
  }

  IsEnterHandler(keyDownEvent)
  {
    const result = LJC.IsKey("Enter", keyDownEvent);

    const compare = this.CompareValue;
    LJC.CheckValues("EventTextRows()", result, compare);
  }

  // Gets the textarea columns.
  GetTextCols()
  {
    // <body>
    //   <!-- The dialog for debug or other display text. -->
    //   <dialog id="textDialog"
    //     style="border: none; padding: 0px">
    //     <textarea id="textArea" rows="25" cols="80" autocorrect="off"
    //       autocapitalize="off" spellcheck="false" readonly
    //       style="border: none; padding: 10px">
    //     </textarea>
    //   </dialog>
    // </body>

    const className = "ClassName";
    const methodName = "MethodName()";
    const valueName = "text";
    const value = "This is the\r\nthree line\r\ntext value.";
    const force = false;
    const textDialogID = "textDialog";
    const textAreaID = "textArea";
    LJC.ShowText(className, methodName, valueName, value, force, textDialogID
      , textAreaID);

    const eTextArea = LJC.Element(textAreaID);
    const font = LJC.ElementStyle(eTextArea, "font");
    const averageCharWidth = LJC.AverageCharWidth(font, value);

    const characterCount = 40;
    const clientWidth = characterCount * averageCharWidth;
    const columns = 1;

    eTextArea.cols = LJC.GetTextCols(clientWidth, columns, averageCharWidth);
    LJC.SetTextRows(eTextArea);
  }

  // Sets the textarea rows for newlines.
  SetTextRows()
  {
    // <body>
    //   <!-- The dialog for debug or other display text. -->
    //   <dialog id="textDialog"
    //     style="border: none; padding: 0px">
    //     <textarea id="textArea" rows="25" cols="80" autocorrect="off"
    //       autocapitalize="off" spellcheck="false" readonly
    //       style="border: none; padding: 10px">
    //     </textarea>
    //   </dialog>
    // </body>

    const className = "ClassName";
    const methodName = "MethodName()";
    const valueName = "text";
    const value = "This is the\r\nthree line\r\ntext value.";
    const force = false;
    const textDialogID = "textDialog";
    const textAreaID = "textArea";
    LJC.ShowText(className, methodName, valueName, value, force, textDialogID
      , textAreaID);

    const eTextArea = LJC.Element(textAreaID);
    LJC.SetTextRows(eTextArea);
  }
  // #endregion

  // #region Binary Search Methods

  SortMethod(compare, compareTo)
  {
    return compare.Text.localeCompare(compareTo.Text);
  }

  CompareMethod(compare, compareTo)
  {
    return compare.Text.localeCompare(compareTo.Text);
  }

  // Returns the index of a search item in the array.
  BinarySearch()
  {
    class Item
    {
      Text = "";

      // Creates an object clone.
      Clone()
      {
        let retItem = new Item();
        retItem.Text = this.Text;
        return retItem;
      }
    }

    const items = [];
    let item = new Item();
    item.Text = "Second";
    items.push(item);
    item = new Item();
    item.Text = "First";
    items.push(item);
    item = new Item();
    item.Text = "Third";
    items.push(item);
    item = new Item();
    item.Text = "Fourth";
    items.push(item);

    let index = LJC.BinarySearch(items, this.SortMethod, this.CompareMethod);
  }

  // Returns the middle position of the count value.
  MiddlePosition()
  {

  }
  // #endregion

  // #region Other Methods

  // Creates JSON from the provided value.
  CreateJSON()
  {
    let object = {};
    object.Name = "First";
    object.Sequence = 1;
    const result = LJC.CreateJSON(object);

    const compare = "{\"Name\":\"First\",\"Sequence\":1}";
    LJC.CheckValues("CreateJSON()", result, compare);
  }

  // Creates the debug location text.
  Location()
  {
    const className = "Class";
    const methodName = "Method()";
    const valueName = "value";
    const result = LJC.Location(className, methodName, valueName);

    const compare = "Class.Method() value:";
    LJC.CheckValues("Location()", result, compare);
  }

  // Shows the service message.
  Message()
  {
    // <body>
    //   <!-- The dialog for debug or other display text. -->
    //   <dialog id="textDialog"
    //     style="border: none; padding: 0px">
    //     <textarea id="textArea" rows="25" cols="80" autocorrect="off"
    //       autocapitalize="off" spellcheck="false" readonly
    //       style="border: none; padding: 10px">
    //     </textarea>
    //   </dialog>
    // </body>

    const location = LJC.Location("Class", "Method()", "textValue");
    const textValue = "This is some text shown in a \"textarea\""
      + " dialog to display more text than allowed by an alert message"
      + " and to allow the copy to clipboard function."
      + "\r\nText that starts with \"\"ServiceName\":\","
      + " \"delete\", \"insert\", \"select\" or \"update\" will not display"
      + " unless the \"force\" parameter is set to \"true\"."
      + "\r\nAn alert dialog is displayed if the textDialogID or textAreaID"
      + " parameters are null."
      + "\r\nUse the Escape [ESC] key to exit the dialog."
    const force = false;
    const textDialogID = "textDialog";
    const textAreaID = "textArea";
    LJC.Message(location, textValue, force, textDialogID, textAreaID);
  }

  // Creates a mouse location object.
  MouseLocation()
  {
    const handler = this.MouseLocationHandler.bind(this);
    document.addEventListener("contextmenu", handler);

    // Right mouse button.
    let eventOptions = {
      bubbles: true,
      cancelable: true,
      button: 2,
      buttons: 2,
      view: window,
      clientY: 10,
    };
    let mouseEvent = new MouseEvent('contextmenu', eventOptions);
    document.dispatchEvent(mouseEvent);

    document.removeEventListener("contextmenu", handler);
  }

  MouseLocationHandler(mouseEvent)
  {
    const mouseLocation = LJC.MouseLocation(mouseEvent);
    const result = mouseLocation.Top;

    const compare = 10;
    LJC.CheckValues("MouseLocation()", result, compare);
  }

  // Parses JSON into an object.
  ParseJSON()
  {
    const json = "{\"Name\":\"First\",\"Sequence\":1}";
    const object = LJC.ParseJSON(json);
    const result = object.Name;

    const compare = "First";
    LJC.CheckValues("CreateJSON()", result, compare);
  }

  // Show text in textArea element.
  ShowText()
  {
    // <body>
    //   <!-- The dialog for debug or other display text. -->
    //   <dialog id="textDialog"
    //     style="border: none; padding: 0px">
    //     <textarea id="textArea" rows="25" cols="80" autocorrect="off"
    //       autocapitalize="off" spellcheck="false" readonly
    //       style="border: none; padding: 10px">
    //     </textarea>
    //   </dialog>
    // </body>

    const className = "ClassName";
    const methodName = "MethodName()";
    const valueName = "text";
    const value = "This is the\r\nthree line\r\ntext value.";
    const force = false;
    const textDialogID = "textDialog";
    const textAreaID = "textArea";
    LJC.ShowText(className, methodName, valueName, value, force, textDialogID
      , textAreaID);
  }

  // Copy collection items to an indexed array.
  ToArray()
  {
    class Item
    {
      Text = "";

      // Creates an object clone.
      Clone()
      {
        let retItem = new Item();
        retItem.Text = this.Text;
        return retItem;
      }
    }

    class Items extends LJCCollection
    {
      // Adds the supplied object to the list.
      AddObject(item)
      {
        let retItem = null;

        // This check is part of what makes it a strongly typed collection.
        if (item instanceof Item)
        {
          // _AddItem is only used here.
          retItem = this._AddItem(item);
        }
        return retItem;
      }
    }

    let tempItems = new Items();
    let item = new Item();
    item.Text = "First Object";
    tempItems.AddObject(item);
    item = new Item();
    item.Text = "Second Object";
    tempItems.AddObject(item);
    let items = tempItems.Clone(new Items());

    if (items instanceof LJCCollection)
    {
      const array = LJC.ToArray(items);
      if (LJC.HasElements(array))
      {
        const result = array[1].Text;

        const compare = "Second Object";
        LJC.CheckValues("ToArray()", result, compare);
      }
    }
    else
    {
      alert("ToArray(): Object does Not inherited from LJCCollection.");
    }
  }

  // Sets the element visibility.
  Visibility()
  {
    // <body>
    //   <div id="testDiv"></div>
    // </body>

    const elementID = "testDiv";
    let value = "hidden";
    LJC.Visibility(elementID, value);
    let element = LJC.Element(elementID);
    let result = element.style.visibility;

    let compare = "hidden";
    LJC.CheckValues("Visibility()", result, compare);

    value = "visible";
    LJC.Visibility(elementID, value);
    element = LJC.Element(elementID);
    result = element.style.visibility;

    compare = "visible";
    LJC.CheckValues("Visibility()", result, compare);
  }
  // #endregion
}