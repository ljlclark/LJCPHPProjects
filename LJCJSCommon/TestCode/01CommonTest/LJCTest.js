"use strict";
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
    this.IsBackTab();
    this.IsNumber();
    this.IsShiftOnly();
    this.IsSimpleType();
    this.IsString();
    this.IsTab();

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

    // Show Property Methods
    this.GetPropertyNames();
    this.GetPropertyOutput();
    this.GetStartText();
    this.ShowProperties();
    this.ShowSelectProperties();
  }

  CheckValues(methodName, result, compare)
  {
    if (result != compare)
    {
      let message = methodName;
      message += `\r\n${result}`;
      message += "\r\n !=";
      message += `\r\n${compare}`;
      alert(message)
    }
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
    const result = LJC.IsTab(keyDownEvent);

    const compare = this.CompareValue;
    this.CheckValues("AddEventHandler()", result, compare);
  }

  // Gets the average character width using the first selector element font.
  AverageCharWidth()
  {
    const selector = "div";
    const text = "This is sample text.";
    const result = LJC.AverageCharWidth(selector, text);

    // With font = "16px "Times New Roman"
    const compare = 6.22;
    this.CheckValues("AverageCharWidth()", result, compare);
  }

  // Gets the HTMLElement by ID.
  Element()
  {
    const elementID = "testDiv";
    const element = LJC.Element(elementID);

    let result = element.tagName;
    let compare = "DIV";
    this.CheckValues("Element()", result, compare);

    result = element.id;
    compare = "testDiv";
    this.CheckValues("Element()", result, compare);
  }

  // Gets the element ComputedStyle property.
  ElementStyle()
  {
    const element = testDiv;
    const propertyName = "color";
    const result = LJC.ElementStyle(element, propertyName);

    const compare = "rgb(0, 0, 0)";
    this.CheckValues("ElementStyle()", result, compare);
  }

  // Rounds and truncates to the provided place value.
  Round()
  {
    const value = 3.14159;
    const placeValue = 2;
    const result = LJC.Round(value, placeValue);

    const compare = 3.14;
    this.CheckValues("Round()", result, compare);
  }

  // Gets the first matching selector ComputedStyle property.
  SelectorStyle()
  {
    const selector = "div";
    const propertyName = "color";
    const result = LJC.SelectorStyle(selector, propertyName);

    const compare = "rgb(0, 0, 0)";
    this.CheckValues("SelectorStyle()", result, compare);
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
    this.CheckValues("TagElements()", result, compare);
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
    this.CheckValues("TagElements()", result, compare);
  }
  // #endregion

  // #region Check Value Methods

  // Checks if an array has elements.
  HasElements()
  {
    let arrValue = ["Value"];
    let result = LJC.HasElements(arrValue);
    let compare = true;
    this.CheckValues("HasElements()", result, compare);

    arrValue = [];
    result = LJC.HasElements(arrValue);
    compare = false;
    this.CheckValues("HasElements()", result, compare);
  }

  // Checks if an element has a value.
  HasElementValue()
  {
    const element = targetInput;
    LJC.SetValue("targetInput", "X");
    let result = LJC.HasElementValue(element);
    let compare = true;
    this.CheckValues("HasElementValue()1", result, compare);

    LJC.SetValue("targetInput", "  ");
    result = LJC.HasElementValue(element);
    compare = false;
    this.CheckValues("HasElementValue()", result, compare);
  }

  // Checks if the text has a value.
  HasText()
  {
    let text = "X";
    let result = LJC.HasText(text);
    let compare = true;
    this.CheckValues("HasText()", result, compare);

    text = "  ";
    result = LJC.HasText(text);
    compare = false;
    this.CheckValues("HasText()", result, compare);
  }

  // Checks keydown for a Backtab key.
  IsBackTab()
  {
    const elementID = "targetInput";
    const eventName = "keydown";
    const handler = this.IsBackTabHandler.bind(this);
    LJC.AddEvent(elementID, eventName, handler);

    // Tab
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
    let keyDownEvent = new KeyboardEvent('keydown', eventOptions);
    this.CompareValue = true;
    targetInput.dispatchEvent(keyDownEvent);

    // BackTab
    eventOptions.shiftKey = false;
    keyDownEvent = new KeyboardEvent('keydown', eventOptions);
    this.CompareValue = false;
    targetInput.dispatchEvent(keyDownEvent);

    let element = LJC.Element("targetInput");
    element.removeEventListener("keydown", handler);
  }

  IsBackTabHandler(keyDownEvent)
  {
    const result = LJC.IsBackTab(keyDownEvent);

    const compare = this.CompareValue;
    this.CheckValues("IsBackTab()", result, compare);
  }

  // Checks if the text is a number.
  IsNumber()
  {
    let number = 1;
    let result = LJC.IsNumber(number);
    let compare = true;
    this.CheckValues("IsNumber()", result, compare);

    number = "X";
    result = LJC.IsNumber(number);
    compare = false;
    this.CheckValues("IsNumber()", result, compare);
  }

  // Checks keydown for only a Shift key.
  IsShiftOnly()
  {
    const elementID = "targetInput";
    const eventName = "keydown";
    const handler = this.IsShiftOnlyHandler.bind(this);
    LJC.AddEvent(elementID, eventName, handler);

    // Tab
    let eventOptions = {
      key: "Shift",
      code: "",
      ctrlKey: false,
      shiftKey: true,
      altKey: false,
      metaKey: false,
      repeat: false,
      bubbles: true,
    };
    let keyDownEvent = new KeyboardEvent('keydown', eventOptions);
    this.CompareValue = true;
    targetInput.dispatchEvent(keyDownEvent);

    // BackTab
    eventOptions.key = "";
    eventOptions.shiftKey = false;
    keyDownEvent = new KeyboardEvent('keydown', eventOptions);
    this.CompareValue = false;
    targetInput.dispatchEvent(keyDownEvent);

    targetInput.removeEventListener("keydown", handler);
  }

  IsShiftOnlyHandler(keyDownEvent)
  {
    const result = LJC.IsShiftOnly(keyDownEvent);

    const compare = this.CompareValue;
    this.CheckValues("IsShiftOnly()", result, compare);
  }

  //Checks if the value is a primitive type.
  IsSimpleType()
  {
    let number = 1;
    let result = LJC.IsSimpleType(number);
    let compare = true;
    this.CheckValues("IsSimpleType()", result, compare);

    number = [];
    result = LJC.IsSimpleType(number);
    compare = false;
    this.CheckValues("IsSimpleType()", result, compare);
  }

  // Checks if the value is a string.
  IsString()
  {
    let number = "1";
    let result = LJC.IsString(number);
    let compare = true;
    this.CheckValues("IsSimpleType()", result, compare);

    number = 1;
    result = LJC.IsString(number);
    compare = false;
    this.CheckValues("IsString()", result, compare);
  }

  // Checks keydown for a Tab key.
  IsTab()
  {
    const elementID = "targetInput";
    const eventName = "keydown";
    const handler = this.IsTabHandler.bind(this);
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
    let keyDownEvent = new KeyboardEvent('keydown', eventOptions);
    this.CompareValue = true;
    targetInput.dispatchEvent(keyDownEvent);

    // BackTab
    eventOptions.key = "";
    eventOptions.code = "";
    keyDownEvent = new KeyboardEvent('keydown', eventOptions);
    this.CompareValue = false;
    targetInput.dispatchEvent(keyDownEvent);

    targetInput.removeEventListener("keydown", handler);
  }

  IsTabHandler(keyDownEvent)
  {
    const result = LJC.IsTab(keyDownEvent);

    const compare = this.CompareValue;
    this.CheckValues("IsTab()", result, compare);
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
    this.CheckValues("GetText()", result, compare);

    testDiv.innerText = "";
    result = LJC.GetText("testDiv");
    compare = "";
    this.CheckValues("GetText()", result, compare);
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
    this.CheckValues("GetValue()", result, compare);

    targetInput.value = "";
    result = LJC.GetValue("targetInput");
    compare = "";
    this.CheckValues("GetValue()", result, compare);
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
    this.CheckValues("SetText()", result, compare);

    LJC.SetText("testDiv", "");
    result = LJC.GetText("testDiv");
    compare = "";
    this.CheckValues("SetText()", result, compare);
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
    this.CheckValues("SetValue()", result, compare);

    LJC.SetValue(elementID, "");
    result = LJC.GetValue(elementID);
    compare = "";
    this.CheckValues("GetValue()", result, compare);
  }
  // #endregion

  // #region TextArea Methods

  // Sets the textarea rows for newlines.
  EventTextRows()
  {

  }

  // Gets the textarea columns.
  GetTextCols()
  {

  }

  // Sets the textarea rows for newlines.
  SetTextRows()
  {

  }
  // #endregion

  // #region Binary Search Methods

  // Returns the index of a search item in the array.
  BinarySearch()
  {

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
    this.CheckValues("CreateJSON()", result, compare);
  }

  // Creates the debug location text.
  Location()
  {
    const className = "Class";
    const methodName = "Method()";
    const valueName = "value";
    const result = LJC.Location(className, methodName, valueName);

    const compare = "Class.Method() value:";
    this.CheckValues("Location()", result, compare);
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
    this.CheckValues("MouseLocation()", result, compare);
  }

  // Parses JSON into an object.
  ParseJSON()
  {
    const json = "{\"Name\":\"First\",\"Sequence\":1}";
    const object = LJC.ParseJSON(json);
    const result = object.Name;

    const compare = "First";
    this.CheckValues("CreateJSON()", result, compare);
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
    const value = "This is the value.";
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
        this.CheckValues("ToArray()", result, compare);
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
    this.CheckValues("Visibility()", result, compare);

    value = "visible";
    LJC.Visibility(elementID, value);
    element = LJC.Element(elementID);
    result = element.style.visibility;

    compare = "visible";
    this.CheckValues("Visibility()", result, compare);
  }
  // #endregion

  // #region Show Property Methods

  // Gets the default property names.
  GetPropertyNames()
  {

  }

  // Gets the property output.
  GetPropertyOutput()
  {

  }

  // Get the property list start text.
  GetStartText()
  {

  }

  // Show the properties of an object that are not null or "" and do not start
  // with "on".
  ShowProperties()
  {

  }

  // Show selected properties of an object.
  ShowSelectProperties()
  {

  }
  // #endregion
}