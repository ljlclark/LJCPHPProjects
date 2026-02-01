"use strict";
// Copyright(c) Lester J. Clark and Contributors.
// Licensed under the MIT License.
// LJCTextBuilderLib.js
// <script src='../../../LJCJSCommon/LJCCommonLib.js'></script>
// <script src='../../../LJCJSCommon/LJCCollectionLib.js'></script>
// LJCCommonLib: LJC
// LJCCollectionLib: LJCCollection

// The Text Builder Class Library
/// <include path='members/LJCTextBuilderLib/*' file='Doc/LJCTextBuilder.xml'/>
/// LibName: LJCTextBuilderLib

// Represents a node or element attribute.
/// <include path='members/LJCAttribute/*' file='Doc/LJCAttribute.xml'/>
class LJCAttribute
{
  // #region Properties

  /// <summary>The item name.</summary>
  Name = "";

  /// <summary>The item value.</summary>
  Value = "";
  // #endregion

  // #region Static Methods

  // Creates a new object from simple object values.
  /// <include path='members/Copy/*' file='Doc/LJCAttribute.xml'/>
  static Copy(objItem)
  {
    let retAttrib = null;

    if (objItem != null)
    {
      retAttrib = new LJCAttribute();

      // Look for properties of simple object in object.
      for (let propertyName in objItem)
      {
        if (propertyName in retAttrib)
        {
          // Update new object properties from the simple object.
          retAttrib[propertyName] = objItem[propertyName];
        }
      }
    }
    return retAttrib;
  }
  // #endregion

  // #region Constructor Methods

  // Initializes an object instance.
  /// <include path='members/constructor/*' file='Doc/LJCAttribute.xml'/>
  constructor(name = "", value = "")
  {
    this.Name = name;
    this.Value = value;
  }
  // #endregion

  // #region Data Class Methods

  // Creates an object clone.
  /// <include path='members/Clone/*' file='Doc/LJCAttribute.xml'/>
  Clone()
  {
    const retAttribute = new LJCAttribute(this.Name, this.Value);
    return retAttribute;
  }
  // #endregion
}

// Represents a collection of node or element attributes.
/// <include path='members/LJCAttributes/*' file='Doc/LJCAttributes.xml'/>
class LJCAttributes extends LJCCollection
{
  // #region Static Methods - LJCAttributes

  // Creates a typed collection from an array of objects.
  /// <include path='members/ToCollection/*' file='Doc/LJCAttributes.xml'/>
  static ToCollection(arrItems)
  {
    let retAttribs = null;

    if (LJC.HasElements(arrItems))
    {
      retAttribs = new LJCAttributes();
      for (let objItem of arrItems)
      {
        // Create object from simple object.
        let attrib = LJCAttribute.Copy(objItem);
        retAttribs.AddObject(attrib);
      }
    }
    return retAttribs;
  }
  // #endregion

  // #region Collection Data Methods

  // Creates and adds the item to the list.
  /// <include path='members/Add/*' file='Doc/LJCAttributes.xml'/>
  Add(name, value = "")
  {
    let retAttrib = null;

    let attrib = new LJCAttribute(name, value);
    retAttrib = this.AddObject(attrib);
    return retAttrib;
  }

  // Adds the supplied item to the list.
  /// <include path='members/AddObject/*' file='Doc/LJCAttributes.xml'/>
  AddObject(attrib)
  {
    let retAttrib = null;

    // This check is part of what makes it a strongly typed collection.
    if (attrib instanceof LJCAttribute)
    {
      let process = true;
      if ("style" == attrib.Name.toLowerCase())
      {
        const uniqueColumns = this.UniqueColumns(attrib.Value);
        let foundAttrib = this.Retrieve(uniqueColumns);
        if (foundAttrib != null)
        {
          process = false;
          let mergedValue = this.MergeStyle(foundAttrib, attrib);
          if (LJC.HasText(mergedValue))
          {
            foundAttrib.Value = mergedValue;
          }
        }
      }

      if (process)
      {
        // _AddItem is only used here.
        retAttrib = this._AddItem(attrib);
      }
    }
    return retAttrib;
  }

  // Appends items.
  /// <include path='members/Append/*' file='Doc/LJCAttributes.xml'/>
  Append(attribs)
  {
    for(const attrib of attribs.ReadItems)
    {
      this.AddObject(attrib);
    }
  }
  // #endregion

  // #region Other Methods

  // Merges "style" attrib rules.
  /// <include path='members/MergeStyle/*' file='Doc/LJCAttributes.xml'/>
  MergeStyle(foundAttrib, newAttrib)
  {
    let retMergedRules = this.#SingleValue(foundAttrib, newAttrib);

    if (!LJC.HasText(retMergedRules))
    {
      // Get existing style rules.
      const foundValue = foundAttrib.Value.trim();
      const foundRules = foundValue.split(";");

      // Get new style rules.
      const newValue = newAttrib.Value.trim();
      let newRules = newValue.split(';');

      // Save previous rule unless overriden by new rule.
      for (const foundRule of foundRules)
      {
        if (foundRule != null)
        {
          // 0 = Property, 1 = Value.
          let values = foundRule.split(":");
          let property = LJCAttributes.#TrimElement(values, 0);

          // Check for override.
          const newRule = this.#FindRule(newRules, property);
          if (newRule != null)
          {
            let values = newRule.split(":");
            const index = this.#GetRuleIndex(newRules, property);
            newRules.splice(index, 1);
          }

          property = LJCAttributes.#TrimElement(values, 0);
          let value = LJCAttributes.#TrimElement(values, 1);
          retMergedRules += `${property}: ${value}; `;
        }
      }

      // Add remaining new rules.
      for (const newRule of newRules)
      {
        const values = newRule.split(":");
        const property = LJCAttributes.#TrimElement(values, 0);
        if (LJC.HasText(property))
        {
          const value = LJCAttributes.#TrimElement(values, 1);
          retMergedRules += `${property}: ${value}; `;
        }
      }
    }
    return retMergedRules;
  }

  // Get the unique columns data object.
  /// <include path='members/UniqueColumns/*' file='Doc/LJCAttributes.xml'/>
  UniqueColumns(value)
  {
    const retColumns = new LJCDataColumns();
    retColumns.AddValue("Name", value);
    return retColumns;
  }
  // #endregion

  // #region Private Methods

  // Trims element value or if null, returns null.
  static #TrimElement(values, index)
  {
    let retValue = null;

    if (values != null)
    {
      if (values.length > index)
      {
        retValue = values[index].trim();
      }
    }
    return retValue;
  }

  // Finds the rule with the supplied property name.
  #FindRule(rules, property)
  {
    let retRule = "";

    property = property.trim();
    for (const rule of rules)
    {
      let values = rule.split(":");
      if (values[0].trim() == property)
      {
        retRule = rule;
        break;
      }
    }
    return retRule;
  }

  // Finds the rule index with the supplied property name.
  #GetRuleIndex(rules, property)
  {
    let retIndex = -1;

    for (let index = 0; index < rules.length; index++)
    {
      const rule = rules[index];
      let values = rule.split(":");
      if (values[0].trim() == property)
      {
        retIndex = index;
        break;
      }
    }
    return retIndex;
  }

  // Returns the existing value if only one exists.
  // Otherwise returns an empty string.
  #SingleValue(foundAttrib, newAttrib)
  {
    let retRules = "";

    if (null == foundAttrib
      && newAttrib != null)
    {
      retRules = newAttrib.Value;
    }
    if (null == newAttrib
      && foundAttrib != null)
    {
      retRules = foundAttrib.Value;
    }
    return retRules;
  }
  // #endregion
}

// Represents a built string value.
/// <include path='members/LJCTextBuilder/*' file='Doc/LJCTextBuilder.xml'/>
class LJCTextBuilder
{
  // #region Properties

  // <summary>The indent character count.</summary>
  IndentCharCount = 0;

  /// <summary>Gets the current length.</summary>
  LineLength = 0;

  /// <summary>Gets or sets the character limit.</summary>
  LineLimit = 0;

  /// <summary>Indicates if the wrap functionality is enabled.</summary>
  WrapEnabled = false;

  // The built string value.
  #BuilderValue = "";

  // The current indent count.
  #IndentCount = 0;
  // #endregion

  // #region Constructor Methods

  // Initializes a class instance.
  /// <include path='members/constructor/*' file='Doc/LJCTextBuilder.xml'/>
  constructor(textState = null)
  {
    this.#BuilderValue = "";
    this.IndentCharCount = 2;
    this.#setIndentCount(0);
    if (textState != null)
    {
      this.AddIndent(textState.getIndentCount());
    }
    this.LineLength = 0;
    this.LineLimit = 80;
    this.WrapEnabled = false;
  }
  // #endregion

  // #region Data Class Methods

  // Gets the built string.
  /// <include path='members/ToString/*' file='Doc/LJCTextBuilder.xml'/>
  ToString()
  {
    return this.#BuilderValue;
  }
  // #endregion

  // #region Add Text Methods

  // Appends a text line without modification.
  /// <include path='members/AddLine/*' file='Doc/LJCTextBuilder.xml'/>
  AddLine(text = "")
  {
    const retText = `${text}\r\n`;

    this.#BuilderValue += retText;
    return retText;
  }

  // Appends text without modification.
  /// <include path='members/AddText/*' file='Doc/LJCTextBuilder.xml'/>
  AddText(text)
  {
    if (this.#TextLength(text) > 0)
    {
      this.#BuilderValue += text;
    }
  }
  // #endregion

  // #region Append Text Methods

  // Appends a potentially indented text line to the builder.
  /// <include path='members/Line/*' file='Doc/LJCTextBuilder.xml'/>
  Line(text = null, addIndent = true, allowNewLine = true)
  {
    const retText = this.GetLine(text, addIndent, allowNewLine);

    this.#BuilderValue += retText;
    return retText;
  }

  // Appends the potentially indented text.
  /// <include path='members/Text/*' file='Doc/LJCTextBuilder.xml'/>
  Text(text, addIndent = true, allowNewLine = true)
  {
    const retText = this.GetText(text, addIndent, allowNewLine);

    if (this.#TextLength(retText) > 0)
    {
      this.#BuilderValue += retText;
    }
    return retText;
  }
  // #endregion

  // #region Get Text Methods

  // Gets a modified text line.
  /// <include path='members/GetLine/*' file='Doc/LJCTextBuilder.xml'/>
  GetLine(text = null, addIndent = true, allowNewLine = true)
  {
    let retLine = this.GetText(text, addIndent, allowNewLine);

    retLine += "\r\n";
    return retLine;
  }

  // Gets the potentially indented and wrapped text.
  /// <include path='members/GetText/*' file='Doc/LJCTextBuilder.xml'/>
  /// <ParentGroup>GetText</ParentGroup>
  GetText(text, addIndent = true, allowNewLine = true)
  {
    let retText = "";

    // Start with newline if text exists.
    if (this.StartWithNewLine(allowNewLine))
    {
      retText = "\r\n";
    }

    if (LJC.HasText(text))
    {
      retText += text;

      if (addIndent)
      {
        // Recreate string.
        retText = this.GetIndented(text);
      }

      if (this.StartWithNewLine(allowNewLine))
      {
        // Recreate string.
        retText = "\r\n";
        if (addIndent)
        {
          retText += this.GetIndentString();
        }
        retText += text;
      }

      if (this.WrapEnabled)
      {
        retText = this.GetWrapped(retText);
      }
    }
    return retText;
  }
  // #endregion

  // #region Other Get Text Methods

  // Gets a new potentially indented line.
  /// <include path='members/GetIndented/*' file='Doc/LJCTextBuilder.xml'/>
  GetIndented(text)
  {
    let retText = "";

    // Allow add of blank characters.
    if (text != null)
    {
      retText = this.GetIndentString();
      retText += text;
    }
    return retText;
  }

  // Gets the current indent string.
  /// <include path='members/GetIndentString/*' file='Doc/LJCTextBuilder.xml'/>
  GetIndentString()
  {
    let retValue = " ".repeat(this.IndentLength());
    return retValue;
  }

  // Appends added text and new wrapped line.
  /// <include path='members/GetWrapped/*' file='Doc/LJCTextBuilder.xml'/>
  GetWrapped(text)
  {
    let retText = text;

    let buildText = "";
    let workText = text;
    let lineLength = this.LineLength;
    const lineLimit = this.LineLimit;
    let totalLength = lineLength + this.#TextLength(workText);
    if (totalLength < lineLimit)
    {
      // No wrap.
      this.LineLength += this.#TextLength(text);
    }

    while (totalLength > lineLimit)
    {
      // Index where text can be added to the current line
      // and the remainder is wrapped.
      let wrapIndex = this.#WrapIndex(workText);
      if (wrapIndex > -1)
      {
        // Adds leading space if line exists and wrapIndex > 0.
        const addText = this.#GetAddText(retText, wrapIndex);
        buildText += `${addText}\r\n`;

        // Next text up to LineLimit - prepend length without leading space.
        const wrapText = this.#WrapText(workText, wrapIndex);
        const indentString = this.GetIndentString();
        const lineText = `${indentString}${wrapText}`;
        this.LineLength = lineText.length;
        buildText += lineText;

        // End loop unless there is more text.
        totalLength = 0;

        // Get index of next section.
        let nextIndex = wrapIndex + wrapText.length;
        if (!workText.startsWith(","))
        {
          // Adjust for removed leading space.
          nextIndex++;
        }

        // Get next work text if available.
        if (nextIndex < workText.length)
        {
          const tempText = workText.substring(nextIndex);
          workText = tempText;
          totalLength = lineLength + this.#TextLength(workText);
        }
      }
    }

    if (buildText != null
      && buildText.length > 0)
    {
      retText = buildText;
    }
    return retText;
  }
  // #endregion

  // #region Attribs Methods

  // Gets common element attributes.
  /// <include path='members/Attribs/*' file='Doc/LJCTextBuilder.xml'/>
  Attribs(className = null, id = null)
  {
    const retAttribs = new LJCAttributes();

    if (LJC.HasText(id))
    {
      retAttribs.Add("id", id);
    }
    if (LJC.HasText(className))
    {
      retAttribs.Add("class", className);
    }
    return retAttribs;
  }

  // Gets the attributes text.
  /// <include path='members/GetAttribs/*' file='Doc/LJCTextBuilder.xml'/>
  GetAttribs(attribs, textState)
  {
    let retText = "";

    if (LJC.HasItems(attribs))
    {
      const tb = new LJCTextBuilder(textState);
      for (let attrib of attribs.ReadItems)
      {
        const name = attrib.Name;
        const value = attrib.Value;

        if (tb.HasText())
        {
          // Wrap line for large attribute value.
          if (LJC.HasText(value)
            && value.length > 35)
          {
            tb.AddText(`\r\n${this.GetIndentString()}`);
          }
        }

        // [ AttribName="Value"]
        tb.AddText(` ${name}`);
        if (LJC.HasText(value))
        {
          tb.AddText(`=\"${value}\"`);
        }
      }
      retText = tb.ToString();
    }
    return retText;
  }

  // Creates the HTML element attributes.
  /// <include path='members/StartAttribs/*' file='Doc/LJCTextBuilder.xml'/>
  StartAttribs()
  {
    const retAttribs = new LJCAttributes();

    retAttribs.Add("lang", "en");
    //$retAttribs->Add("xmlns", "http://www.w3.org/1999/xhtml");
    return retAttribs;
  }

  // Creates the XML element attributes.
  /// <include path='members/StartXMLAttribs/*' file='Doc/LJCTextBuilder.xml'/>
  StartXMLAttribs()
  {
    const retAttribs = new LJCAttributes();

    retAttribs.Add("xmlns:xsd", "http://www.w3.org/2001/XMLSchema");
    retAttribs.Add("xmlns:xsi"
      , "http://www.w3.org/2001/XMLSchema-instance");
    return retAttribs;
  }

  // Gets common table attributes.
  /// <include path='members/TableAttribs/*' file='Doc/LJCTextBuilder.xml'/>
  TableAttribs(border = 1, borderSpacing = 0, cellPadding = 2, className = null
      , id = null)
  {
    const retAttribs = this.Attribs(className, id);

    let value = String(border);
    let style = `border: ${value}px solid;`;
    value = String(borderSpacing);
    style += ` borderspacing: ${value}px;`;
    value = String(cellPadding);
    style += ` cellpadding: ${value}px;`;

    retAttribs.Add("style", style);
    return retAttribs;
  }
  // #endregion

  // #region Append Element Methods

  // Appends the element begin tag.
  /// <include path='members/Begin/*' file='Doc/LJCTextBuilder.xml'/>
  Begin(name, textState, attribs = null, addIndent = true
    , childIndent = true)
  {
    const createText = this.GetBegin(name, textState, attribs, addIndent
      , childIndent);
    this.Text(createText, false);

    // Use AddChildIndent after beginning an element.
    if (childIndent)
    {
      this.AddChildIndent(createText, textState);
    }

    // Append Method
    this.#UpdateState(textState);
    return createText;
  }

  // Appends an element.
  /// <include path='members/Create/*' file='Doc/LJCTextBuilder.xml'/>
  Create(name, textState, text = "", attribs = null, addIndent = true
    , childIndent = true, isEmpty = false, close = true)
  {
    // Adds the indent string.
    const createText = this.GetCreate(name, text, textState, attribs
      , addIndent, childIndent, isEmpty, close);
    this.Text(createText, false);
    if (!close)
    {
      // Use AddChildIndent after beginning an element.
      this.AddChildIndent(createText, textState);
    }

    // Append Method
    this.#UpdateState(textState);
    return createText;
  }

  // Appends the element end tag.
  /// <include path='members/End/*' file='Doc/LJCTextBuilder.xml'/>
  End(name, textState, addIndent = true)
  {
    const createText = this.GetEnd(name, textState, addIndent);
    this.Text(createText, false);

    // Append Method
    this.#UpdateState(textState);
    return createText;
  }
  // #endregion

  // #region Get Element Methods

  // Gets the element begin tag.
  /// <include path='members/GetBegin/*' file='Doc/LJCTextBuilder.xml'/>
  GetBegin(name, textState, attribs = null, addIndent = true
    , childIndent = true)
  {
    const tb = new LJCTextBuilder(textState);

    const createText = this.GetCreate(name, "", textState, attribs
      , addIndent, childIndent, false, false);
    tb.Text(createText, false);

    // Only use AddChildIndent() if additional text is added in this method.
    const retValue = tb.ToString();
    return retValue;
  }

  // Gets an element.
  /// <include path='members/GetCreate/*' file='Doc/LJCTextBuilder.xml'/>
  GetCreate(name, text, textState, attribs = null, addIndent = true
    , childIndent = true, isEmpty = false, close = true)
  {
    textState.ChildIndentCount = 0; // ?
    const tb = new LJCTextBuilder(textState);

    // Start text with the opening tag.
    tb.Text(`<${name}`, addIndent);
    const getText = this.GetAttribs(attribs, textState);
    tb.AddText(getText);
    if (isEmpty)
    {
      tb.AddText(" /");
      close = false;
    }
    tb.AddText(">");

    // Content is added if not an empty element.
    const isWrapped = false;
    let refIsWrapped = { Value: isWrapped };
    if (!isEmpty
      && LJC.HasText(text))
    {
      const content = this.#Content(text, textState, isEmpty, refIsWrapped);
      tb.AddText(content);
    }

    // Close the element.
    if (close)
    {
      if (refIsWrapped.Value)
      {
        tb.Line();
        tb.AddText(this.GetIndentString());
      }
      tb.AddText(`</${name}>`);
    }

    // Increment ChildIndentCount if not empty and not closed.
    if (!isEmpty
      && !close
      && childIndent)
    {
      textState.ChildIndentCount++;
    }

    const retElement = tb.ToString();
    return retElement;
  }

  // Gets the element end tag.
  /// <include path='members/GetEnd/*' file='Doc/LJCTextBuilder.xml'/>
  GetEnd(name, textState, addIndent = true)
  {
    const tb = new LJCTextBuilder(textState);

    this.#AddSyncIndent(tb, textState, -1);
    tb.Text(`</${name}>`, addIndent);

    const retElement = tb.ToString();
    return retElement;
  }
  // #endregion

  // #region Other Methods

  // Adds the new (child) indents.
  /// <include path='members/AddChildIndent/*' file='Doc/LJCTextBuilder.xml'/>
  AddChildIndent(createText, textState)
  {
    const childIndentCount = textState.ChildIndentCount;

    if (this.#TextLength(createText) > 0
      && childIndentCount > 0)
    {
      this.AddIndent(childIndentCount);
      const indentCount = textState.getIndentCount() + childIndentCount;
      textState.setIndentCount(indentCount);
      textState.ChildIndentCount = 0;
    }
  }

  // Changes the IndentCount by the provided value.
  /// <include path='members/AddIndent/*' file='Doc/LJCTextBuilder.xml'/>
  AddIndent(increment = 1)
  {
    let indentCount = this.getIndentCount() + increment;
    this.#setIndentCount(indentCount);
    return this.getIndentCount;
  }

  // Indicates if the builder text ends with a newline.
  /// <include path='members/EndsWithNewLine/*' file='Doc/LJCTextBuilder.xml'/>
  EndsWithNewLine()
  {
    let retValue = false;

    const builderValue = this.#BuilderValue;
    length = builderValue.length;
    if (length > 0)
    {
      if ("\n" == builderValue[length - 1])
      {
        retValue = true;
      }
    }
    return retValue;
  }

  // Gets a current LJCTextState object.
  /// <include path='members/GetTextState/*' file='Doc/LJCTextBuilder.xml'/>
  GetTextState()
  {
    const indentCount = this.getIndentCount();
    const retState = new LJCTextState(indentCount);
    return retState;
  }

  // Indicates if the builder has text.
  /// <include path='members/HasText/*' file='Doc/LJCTextBuilder.xml'/>
  HasText()
  {
    let retValue = false;

    if (this.#BuilderValue.length > 0)
    {
      retValue = true;
    }
    return retValue;
  }

  // Gets the current indent length.
  /// <include path='members/IndentLength/*' file='Doc/LJCTextBuilder.xml'/>
  IndentLength()
  {
    return this.getIndentCount() * this.IndentCharCount;
  }

  // Checks if the text can start with a newline.
  /// <include path='members/StartWithNewLine/*' file='Doc/LJCTextBuilder.xml'/>
  StartWithNewLine(allowNewLine)
  {
    let retValue = false;

    if (allowNewLine
      && this.HasText()
      && !this.EndsWithNewLine())
    {
      retValue = true;
    }
    return retValue;
  }
  // #endregion

  // #region Private Methods

  // Adds indent to builders and sync object.
  #AddSyncIndent(tb, state, value = 1)
  {
    this.AddIndent(value);
    tb.AddIndent(value);
    const indentCount = state.getIndentCount() + value;
    state.setIndentCount(indentCount);
  }

  // Creates the content text.
  #Content(text, textState, isEmpty, refIsWrapped)
  {
    let retValue = "";

    // Add text content.
    refIsWrapped.Value = false;
    if (!isEmpty
      && LJC.HasText(text))
    {
      if (text.length > 80 - this.IndentLength())
      {
        refIsWrapped.Value = true;
        retValue += "\r\n";
        this.#AddSyncIndent(this, textState);
        const textValue = this.GetText(text);
        retValue += textValue;
        this.#AddSyncIndent(this, textState, -1);
        retValue += "\r\n";
        this.LineLength = 0;
      }
      else
      {
        retValue += text;
      }
    }
    return retValue;
  }

  // Gets the text to add to the existing line.
  #GetAddText(text, addLength)
  {
    let retText = text.substring(0, addLength);

    if (this.LineLength > 0
      && addLength > 0)
    {
      // Add a leading space.
      retText = ` ${retText}`;
    }
    return retText;
  }

  // Gets the text length if not null.
  #TextLength(text)
  {
    let retLength = 0;

    if (text != null)
    {
      retLength = text.length;
    }
    return retLength;
  }

  // Updates the text state values.
  #UpdateState(textState)
  {
    if (textState != null)
    {
      this.#setIndentCount(textState.getIndentCount());
    }
  }

  // Calculates the index at which to wrap the text.
  #WrapIndex(text)
  {
    let retIndex = -1;

    const totalLength = this.LineLength + this.#TextLength(text);
    if (totalLength > this.LineLimit)
    {
      // Length of additional characters that fit in LineLimit.
      // Only get up to next LineLimit length;
      let currentLength = this.LineLength;
      if (currentLength > this.LineLimit)
      {
        currentLength = this.LineLimit;
      }
      const wrapLength = this.LineLimit - currentLength;

      // Get wrap point in allowed length.
      // Wrap on a space.
      retIndex = text.lastIndexOf(" ", wrapLength);
      if (-1 == retIndex)
      {
        // Wrap index not found; Wrap at new text.
        retIndex = 0;
      }
    }
    return retIndex;
  }

  // Get next text up to LineLimit without leading space.
  #WrapText(text, wrapIndex)
  {
    let retText = "";

    let nextLength = text.length - wrapIndex;

    // Leave room for prepend text.
    if (nextLength <= this.LineLimit - this.IndentLength())
    {
      // Get text at the wrap index.
      retText = text.substring(wrapIndex, nextLength);
      if (retText.startsWith(" "))
      {
        // Remove leading space.
        retText = retText.substring(1);
      }
    }
    else
    {
      // Get text from next section.
      let startIndex = wrapIndex;
      let tempText = text.substring(startIndex);
      if (tempText.startsWith(" "))
      {
        tempText = tempText.substring(1);
        startIndex++;
      }
      let nextLength = this.LineLimit - this.IndentLength;
      nextLength = tempText.lastIndexOf(" ", nextLength);
      retText = text.substring(startIndex, nextLength);
    }
    return retText;
  }
  // #endregion

  // #region Getters and Setters

  // Gets the indent count.
  /// <include path='members/getIndentCount/*' file='Doc/LJCTextBuidler.xml'/>
  getIndentCount()
  {
    return this.#IndentCount;
  }

  // Sets the indent count.
  #setIndentCount(count)
  {
    if (count >= 0)
    {
      this.#IndentCount = count;
    }
  }
  // #endregion
}

/// <summary>Represents the text state.</summary>
class LJCTextState
{
  // #region Properties

  /// <summary>The current Child IndentCount value.</summary>
  ChildIndentCount = 0;

  /// <summary>Indicates if the current builder has text.</summary>
  HasText = false;

  /// <summary>The current IndentCount value.</summary>
  IndentCount = 0;
  // #endregion

  // #region Constructor Methods

  // Initializes an object instance.
  /// <include path='members/constructor/*' file='Doc/LJCTextState.xml'/>
  constructor(indentCount = 0, hasText = false)
  {
    this.setIndentCount(indentCount);
    this.HasText = hasText;
    this.ChildIndentCount = 0;
  }
  // #endregion

  // #region Getters and Setters

  // Gets the indent count.
  /// <include path='members/getIndentCount/*' file='Doc/LJCTextState.xml'/>
  getIndentCount()
  {
    return this.IndentCount;
  }

  // Sets the indent count.
  /// <include path='members/setIndentCount/*' file='Doc/LJCTextState.xml'/>
  setIndentCount(count)
  {
    if (count >= 0)
    {
      this.IndentCount = count;
    }
  }
  // #endregion
}