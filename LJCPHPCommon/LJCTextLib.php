<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCTextLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  // LJCCommonLib: LJCCommon

  /// <summary>The Common Text Output Class Library</summary>
  /// LibName: LJCTextLib
  //  Classes: LJCStringBuilder, LJCHTMLTableColumn, LJCHTMLWriter, LJCWriter

  // ***************
  // Represents a built string value.
  // Methods: Line(), Text(), Tags(), Length(), ToString()
  /// <summary>Represents a built string value.</summary>
  class LJCStringBuilder
  {
    // ---------------
    // Constructors

    /// <summary>Initializes a class instance.</summary>
    public function __construct()
    {
      $this->StringValue = null;
    } // __construct()

    // ---------------
    // Public Methods - LJCStringBuilder

    // Appends a text line with indents.
    /// <include path='items/Line/*' file='Doc/LJCStringBuilder.xml'/>
    public function Line(?string $text, int $indent = 0
      , bool $addBreak = false)
    {
      $this->Text($text, $indent, $addBreak);
      $this->Text("\r\n");
    } // Line()

    // Appends text with indents.
    /// <include path='items/Text/*' file='Doc/LJCStringBuilder.xml'/>
    public function Text(?string $text, int $indent = 0
      , bool $addBreak = false)
    {
      $this->InitValue();
      if ($indent > 0)
      {
        //$this->StringValue .= str_repeat("\t", $indent);
        $this->StringValue .= str_repeat("  ", $indent);
      }

      if ($addBreak)
      {
        $index = LJCCommon::StrRPos($text, "\r\n");
        if ($index == strlen($text) - 2)
        {
          // Insert break before cr/lf.
          $text = chop($text, "\r\n") . "<br />\r\n";
        }
        else
        {
          $text .= "<br />";
        }
      }
      $this->StringValue .= $text;
    } // Text()

    // Appends a text line with begin tag, end tag and indents.
    /// <include path='items/Tags/*' file='Doc/LJCStringBuilder.xml'/>
    public function Tags(string $tag, ?string $text, int $indent
      , bool $addBreak = false)
    {
      if ($text != null)
      {
        $this->Line("<$tag>$text</$tag>", $indent, $addBreak);
      }
    } // Tags()

    /// <summary>Gets the current builder string length.</summary>
    public function Length() : int
    {
      $retValue = 0;

      $text = $this->ToString();
      if ($text != null)
      {
        $retValue = strlen($text);
      }
      return $retValue;
    } // Length()

    /// <summary>Gets the built string.</summary>
    public function ToString()
    {
      return $this->StringValue;
    } // ToString()

    // Initializes built string value.
    private function InitValue()
    {
      if (null == $this->StringValue)
      {
        $this->StringValue = "";
      }
    } // InitValue()

    // The built string value.
    private ?string $StringValue;
  } // LJCStringBuilder

  // ***************
  /// <summary>The HTML table column definition.</summary> 
  class LJCHTMLTableColumn
  {
    // ---------------
    // Constructors

    // Initializes an object instance.
    /// <include path='items/construct/*' file='Doc/LJCHTMLTableColumn.xml'/>
    public function __construct(string $columnName, string $headingName = null
      , string $style = null, string $width = null)
    {
      $this->ColumnName = $columnName;
      $this->HeadingName = $headingName;
      if (null == $headingName)
      {
        $this->HeadingName = $columnName;
      }
      $this->Style = $style;
      $this->Width = $width;
    } // __construct()

    /// <summary>The Column name.</summary> 
    public string $ColumnName;

    /// <summary>The Column Heading name.</summary> 
    public ?string $HeadingName;

    /// <summary>The Style value.</summary> 
    public ?string $Style;

    /// <summary>The Column width.</summary> 
    public ?string $Width;
  } // LJCHTMLTableColumn

  // ***************
  // Contains HTML output methods.
  // Static: WriteHeader(), WriteRow(), WriteAttribute()
  /// <summary>Contains HTML output methods.</summary> 
  class LJCHTMLWriter
  {
    // ---------------
    // Public Static Functions

    // Writes an HTML table header row.
    /// <include path='items/WriteHeader/*' file='Doc/LJCHTMLWriter.xml'/>
    public static function WriteHeader($columns, $width = null)
    {
      LJCWriter::WriteLine("<thead>", 1);
      LJCWriter::Write("<tr>", 2);
      foreach ($columns as $column)
      {
        LJCWriter::Write("<th");
        self::WriteAttribute("style", $column->Style);
        self::WriteAttribute("width", $column->Width);
        LJCWriter::Write(">$column->HeadingName</th>");
      }	
      LJCWriter::WriteLine("</tr>");
      LJCWriter::WriteLine("</thead>", 1);
    } // WriteHeader()

    // Writes an HTML table data row.
    /// <include path='items/WriteRow/*' file='Doc/LJCHTMLWriter.xml'/>
    public static function WriteRow($dataObject, $columns)
    {
      LJCWriter::Write("<tr>", 2);
      foreach ($columns as $column)
      {
        LJCWriter::Write("<td");
        self::WriteAttribute("style", $column->Style);
        self::WriteAttribute("width", $column->Width);
        $columnName = $column->ColumnName;

        // Get value with property that matches columnName.
        $value = (string)$dataObject->$columnName;

        LJCWriter::Write(">$value</td>");
      }
      LJCWriter::WriteLine("</tr>");
    } // WriteRow()

    // Writes the style property values.
    public static function WriteAttribute(string $name, ?string $value)
    {
      if ($value != null)
      {
        LJCWriter::Write(" $name='$value'");
      }
    } // WriteAttribute()
  } // LJCHTMLWriter

  // ***************
  // Contains console and file output methods.
  // Static: Run(), Write(), WriteAll(), WriteLine() 
  /// <include path='items/LJCWriter/*' file='Doc/LJCWriter.xml'/>
  class LJCWriter
  {
    // ---------------
    // Public Static Functions

    // Runs a program and returns the output.
    public static function Run($programName) : array
    {
      $lines = null;
      $status = null;
      exec($programName, $lines, $status);
      return $lines;
    } // Run()

    // Writes text with indents.
    /// <include path='items/Write/*' file='Doc/LJCWriter.xml'/>
    public static function Write(?string $text, int $indentCount = 0
      , bool $addBreak = false)
    {
      if (null == $text)
      {
        $text = "";
      }
      if ($indentCount > 0)
      {
        echo(str_repeat("\t", $indentCount));
      }
  
      if ($addBreak)
      {
        // if line ends with \r\n, remove it before adding the break
        // and then add \r\n at the end.
        $index = LJCCommon::StrRPos($text, "\r\n");
        $length = strlen($text);
        if ($index == $length - 2)
        {
          $text = chop($text, "\r\n") . "<br />\r\n";
        }
        else	
        {
          $text .= "<br />";
        }
      }
      echo $text;
    } // Write()

    // Writes text with indents.
    /// <include path='items/WriteAll/*' file='Doc/LJCWriter.xml'/>
    public static function WriteAll(array $lines, bool $addCRLF = true)
    {
      foreach ($lines as $line)
      {
        echo $line;
        if ($addCRLF)
        {
          echo "\r\n";
        }
      }
    } // WriteAll()

    // Writes a text line with indents.
    /// <include path='items/WriteLine/*' file='Doc/LJCWriter.xml'/>
    public static function WriteLine(?string $text, int $indentCount = 0
      , bool $addBreak = false)
    {
      self::Write($text, $indentCount, $addBreak);
      echo "\r\n";
    } // WriteLine()
  } // LJCWriter
?>
