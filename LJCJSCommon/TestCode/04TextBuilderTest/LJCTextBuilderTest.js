"use strict";
// LJCTextBuilderTest.js

class LJCTextBuilderTest
{
  Run()
  {
    // Constructor Methods
    this.Constructor();

    // Data Class Methods
    this.ToString();

    // Add Text Methods
    this.AddLine();
    this.AddText();

    // Append Text Methods
    this.Line();
    this.Text();

    // Get Text Methods
    this.GetLine();
    this.GetText();

    // Other Get Text Methods
    this.GetIndented();
    this.GetIndentString();
    //this.GetWrapped();

    // Attribs Methods
    this.Attribs();
    this.GetAttribs();
    this.StartAttribs();
    this.StartXMLAttribs();
    this.TableAttribs();

    // Append Element Methods
    this.Begin();
    this.Create();
    this.End();

    // Get Element Methods
    this.GetBegin();
    this.GetCreate();
    this.GetEnd();

    // Other Methods
    this.AddChildIndent();
    this.AddIndent();
    this.EndsWithNewLine();
    this.HasText();
    this.IndentLength();
    this.StartWithNewLine();

    // Getters and Setters
    this.getIndentCount();
    this.setIndentCount();
  }

  // #region Constructor Methods

  // Initializes a class instance.
  Constructor()
  {
    const tb = new LJCTextBuilder();
    const result = tb.IndentCharCount;
    const compare = 2;
    LJC.CheckValues("Constructor()", result, compare);
  }
  // #endregion

  // #region Data Class Methods

  // Gets the built string.
  ToString()
  {
    const tb = new LJCTextBuilder();
    tb.Text("This is some text.");

    // Example Method:
    const result = tb.ToString();

    const compare = "This is some text.";
    LJC.CheckValues("ToString()", result, compare);
  }
  // #endregion

  // #region Add Text Methods

  // Appends a text line without modification.
  AddLine()
  {
    const tb = new LJCTextBuilder();

    // Example Method:
    tb.AddLine("This is an appended line.");

    tb.AddText(":");
    const result = tb.ToString();
    const compare = "This is an appended line.\r\n:";
    LJC.CheckValues("AddLine()", result, compare);
  }

  // Appends text without modification.
  AddText()
  {
    const tb = new LJCTextBuilder();

    // Example Method:
    tb.AddText("This is some appended text.");

    const result = tb.ToString();
    const compare = "This is some appended text.";
    LJC.CheckValues("AddText()", result, compare);
  }
  // #endregion

  // #region Append Text Methods

  // Appends a potentially indented text line to the builder.
  Line()
  {
    const tb = new LJCTextBuilder();
    tb.AddIndent();
    tb.Line("First Line");
    tb.Line("This is some indented text.");
    const result = tb.ToString();
    let compare = "  First Line\r\n";
    compare += "  This is some indented text.\r\n";
    LJC.CheckValues("Line()", result, compare);
  }

  // Appends the potentially indented text.
  Text()
  {
    const tb = new LJCTextBuilder();
    tb.AddIndent();
    tb.Text("First Line");
    tb.Text("This is some indented text.");
    const result = tb.ToString();
    let  compare = "  First Line\r\n";
    compare += "  This is some indented text.";
    LJC.CheckValues("Text()", result, compare);
  }
  // #endregion

  // #region Get Text Methods

  // Gets a modified text line.
  GetLine()
  {
    const tb = new LJCTextBuilder();
    tb.AddIndent();
    let result = tb.GetLine("First Line");
    result += tb.GetLine("This is some indented text.");
    let compare = "  First Line\r\n";
    compare += "  This is some indented text.\r\n";
    LJC.CheckValues("GetLine()", result, compare);
  }

  // Gets the potentially indented and wrapped text.
  GetText()
  {
    const tb = new LJCTextBuilder();
    tb.AddIndent();
    let result = tb.GetText("First Line");
    result += tb.GetText("This is some indented text.");
    let compare = "  First Line  This is some indented text.";
    LJC.CheckValues("GetText()", result, compare);
  }
  // #endregion

  // #region Other Get Text Methods

  // Gets a new potentially indented line.
  GetIndented()
  {
    const tb = new LJCTextBuilder();
    tb.AddIndent();
    const result = tb.GetIndented("This is some indented text.");
    const compare = "  This is some indented text.";
    LJC.CheckValues("GetIndented()", result, compare);
  }

  // Gets the current indent string.
  GetIndentString()
  {
    const tb = new LJCTextBuilder();
    tb.AddIndent();
    const result = tb.GetIndentString();
    const compare = "  ";
    LJC.CheckValues("GetIndentString()", result, compare);
  }

  // Appends added text and new wrapped line.
  GetWrapped()
  {
    const tb = new LJCTextBuilder();
    let text = "This is some wrapped text because it is longer than";
    text += " 80 characters and then some more just as an example.";
    const result = tb.GetWrapped(text);
    let compare = "This is some wrapped text because it is longer than";
    compare += " 80 characters and then some\r\n";
    compare += " more just as an example";
    LJC.CheckValues("GetWrapped()", result, compare);
  }
  // #endregion

  // #region Attribs Methods

  // Gets common element attributes.
  Attribs()
  {
    const tb = new LJCTextBuilder();
    const attribs = tb.Attribs("className", "idName");
    const id = attribs.Retrieve("id");
    const result = id.Value;
    const compare = "idName";
    LJC.CheckValues("Attribs()", result, compare);
  }

  // Gets the attributes text.
  GetAttribs()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = new LJCAttributes();
    attribs.Add("Attribute1", "One");
    attribs.Add("Attribute2", "Two");
    const result = tb.GetAttribs(attribs, textState);
    const compare = " Attribute1=\"One\" Attribute2=\"Two\"";
    LJC.CheckValues("GetAttribs()", result, compare);
  }

  // Creates the HTML element attributes.
  StartAttribs()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = tb.StartAttribs()
    const result = tb.GetAttribs(attribs, textState);
    const compare = " lang=\"en\"";
    LJC.CheckValues("StartAttribs()", result, compare);
  }

  // Creates the XML element attributes.
  StartXMLAttribs()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = tb.StartXMLAttribs()
    const result = tb.GetAttribs(attribs, textState);
    let compare = " xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\"\r\n";
    compare += " xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"";
    LJC.CheckValues("StartXMLAttribs()", result, compare);
  }

  // Gets common table attributes.
  TableAttribs()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const className = "styleClass";
    const id = "idValue";
    const border = 1; // Default
    const borderspacing = 0; // Default
    const cellpadding = 2; // Default
    const attribs = tb.TableAttribs(border, borderspacing, cellpadding
      , className, id);
    const result = tb.GetAttribs(attribs, textState);
    let compare = " id=\"idValue\" class=\"styleClass\"\r\n";
    compare += " style=\"border: 1px solid; borderspacing: 0px;";
    compare += " cellpadding: 2px;\"";
    LJC.CheckValues("TableAttribs()", result, compare);
  }
  // #endregion

  // #region Append Element Methods

  // Appends the element begin tag.
  Begin()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = tb.Attribs("className");
    tb.Begin("div", textState, attribs);
    const result = tb.ToString();
    const compare = "<div class=\"className\">";
    LJC.CheckValues("Begin()", result, compare);
  }

  // Appends an element.
  Create()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = tb.Attribs("className");
    tb.Create("div", textState, null, attribs);
    const result = tb.ToString();
    const compare = "<div class=\"className\"></div>";
    LJC.CheckValues("Create()", result, compare);
  }

  // Appends the element end tag.
  End()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = tb.Attribs("className");
    tb.Begin("div", textState, attribs);
    tb.End("div", textState);
    const result = tb.ToString();
    const compare = "<div class=\"className\">\r\n</div>";
    LJC.CheckValues("Create()", result, compare);
  }
  // #endregion

  // #region Get Element Methods

  // Gets the element begin tag.
  GetBegin()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = tb.Attribs("className");
    const result = tb.GetBegin("div", textState, attribs);
    const compare = "<div class=\"className\">";
    LJC.CheckValues("GetBegin()", result, compare);
  }

  // Gets an element.
  GetCreate()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = tb.Attribs("className");
    const result = tb.GetCreate("div", null, textState, attribs);
    const compare = "<div class=\"className\"></div>";
    LJC.CheckValues("GetCreate()", result, compare);
  }

  // Gets the element end tag.
  GetEnd()
  {
    let textState = new LJCTextState();
    const tb = new LJCTextBuilder(textState);
    const attribs = tb.Attribs("className");
    let result = tb.GetBegin("div", textState, attribs);
    result += "\r\n";
    result += tb.GetEnd("div", textState);
    const compare = "<div class=\"className\">\r\n</div>";
    LJC.CheckValues("GetEnd()", result, compare);
  }
  // #endregion

  // #region Other Methods

  // Adds the new (child) indents.
  AddChildIndent()
  {

  }

  // Changes the IndentCount by the provided value.
  AddIndent()
  {
    const tb = new LJCTextBuilder();
    tb.AddIndent(2);
    const result = tb.GetText("This is indented text.");
    const compare = "    This is indented text.";
    LJC.CheckValues("AddIndent()", result, compare);
  }

  // Indicates if the builder text ends with a newline.
  EndsWithNewLine()
  {
    const tb = new LJCTextBuilder();
    tb.Text("This text ends with a newline.\r\n");
    const result = tb.EndsWithNewLine();
    const compare = true;
    LJC.CheckValues("EndsWithNewLine()", result, compare);
  }

  // Indicates if the builder has text.
  HasText()
  {
    const tb = new LJCTextBuilder();
    tb.Text("This is some text.");
    const result = tb.HasText();
    const compare = true;
    LJC.CheckValues("HasText()", result, compare);
  }

  // Gets the current indent length.
  IndentLength()
  {
    const tb = new LJCTextBuilder();
    tb.AddIndent(2);
    const result = tb.IndentLength();
    const compare = 4;
    LJC.CheckValues("HasText()", result, compare);
  }

  // Checks if the text can start with a newline.
  StartWithNewLine()
  {
    const tb = new LJCTextBuilder();
    tb.Text("First Line");
    const allowNewLine = true;
    let result = tb.StartWithNewLine(allowNewLine);
    let compare = true;
    LJC.CheckValues("StartWithNewLine()", result, compare);

    tb.Line();
    result = tb.StartWithNewLine(allowNewLine);
    compare = false;
    LJC.CheckValues("StartWithNewLine()", result, compare);
  }
  // #endregion

  // #region Getters and Setters.

  // Gets the indent count.
  getIndentCount()
  {
    const textBuilder = new LJCTextBuilder();
    //textBuilder.setIndentCount(2);
    textBuilder.AddIndent(2);
    const result = textBuilder.getIndentCount();
    const compare = 2;
    LJC.CheckValues("getIndentCount()", result, compare);
  }

  // Sets the indent count.
  setIndentCount()
  {
    const textBuilder = new LJCTextBuilder();
    //textBuilder.setIndentCount(2);
    textBuilder.AddIndent(2);
    const result = textBuilder.getIndentCount();
    const compare = 2;
    LJC.CheckValues("setIndentCount()", result, compare);
  }
  // #endregion
}