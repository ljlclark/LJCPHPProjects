<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCTextLib.php
  declare(strict_types=1);
  // Must refer to exact same file in codeline.
  include_once "LJCCommonLib.php";
  
  // Classes
  // LJCCommonLib
  //   LJCCommon
  // File
  //   LJCStringBuilder
  //   LJCHTMLTableColumn
  //   LJCHTMLWriter
  //   LJCWriter
  //   LJCDebugWriter

  /// <summary>The Common Text Output Class Library</summary>
  /// LibName: LJCTextLib

  // ***************
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

    // Appends text with indents.
    /// <include path='items/Append/*' file='Doc/LJCStringBuilder.xml'/>
    public function Append(?string $text, int $indent = 0
      , bool $addBreak = false)
    {
      $this->InitValue();
      if ($indent > 0)
      {
        $this->StringValue .= str_repeat("\t", $indent);
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
    } // Append()

    // Appends a text line with indents.
    /// <include path='items/AppendLine/*' file='Doc/LJCStringBuilder.xml'/>
    public function AppendLine(?string $text, int $indent = 0
      , bool $addBreak = false)
    {
      $this->Append($text, $indent, $addBreak);
      $this->Append("\r\n");
    } // AppendLine()

    // Appends a text line with begin tag, end tag and indents.
    /// <include path='items/AppendTags/*' file='Doc/LJCStringBuilder.xml'/>
    public function AppendTags(string $tag, ?string $text, int $indent
      , bool $addBreak = false)
    {
      if ($text != null)
      {
        $this->AppendLine("<$tag>$text</$tag>", $indent, $addBreak);
      }
    } // AppendTags()

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

    // <summary>Writes an XML file.</summary>
    /// <include path='items/WriteFile/*' file='Doc/LJCWriter.xml'/>
    public static function WriteFile(string $text, string $fileSpec)
    {
      if ($text != null)
      {
        $stream = fopen($fileSpec, "w");
        $writer = new self($stream);
        $writer->FWrite($text);
        fclose($stream);
      }
    } // WriteFile()

    // Writes a text line with indents.
    /// <include path='items/WriteLine/*' file='Doc/LJCWriter.xml'/>
    public static function WriteLine(?string $text, int $indentCount = 0
      , bool $addBreak = false)
    {
      self::Write($text, $indentCount, $addBreak);
      echo "\r\n";
    } // WriteLine()

    // ---------------
    // Constructors - LJCWriter

    /// <summary>Initializes an object instance.</summary>
    /// <param name="$stream">The stream object.</param>
    public function __construct($stream)
    {
      $this->Stream = $stream;
    } // __construct()

    // ---------------
    // Public Methods - LJCWriter

    // Writes file text with indents.
    /// <include path='items/FWrite/*' file='Doc/LJCWriter.xml'/>
    public function FWrite(string $text, int $indentCount = 0)
    {
      if ($indentCount > 0)
      {
        fwrite($this->Stream, str_repeat("\t", $indentCount));
      }
      fwrite($this->Stream, "$text");
    } // FWrite()

    // Writes a file text line with indents.
    /// <include path='items/FWriteLine/*' file='Doc/LJCWriter.xml'/>
    public function FWriteLine(string $text, int $indentCount = 0)
    {
      $this->FWrite("$text\r\n", $indentCount);
    } // FWriteLine()

    // ---------------
    // Class Data
    private $Stream;
  } // LJCWriter

  // ***************
  /// <summary>Contains Debug output methods.</summary>
  class LJCDebugWriter
  {
    // ---------------
    // Constructors

    public function __construct(string $locName)
    {
      $fileName = LJCCommon::GetDebugFileName("Debug", $locName);
      $outputStream = fopen($fileName, "w");
      $this->DebugWriter = new LJCWriter($outputStream);
    } // __construct()

    // ---------------
    // Public Methods - LJCDebugWriter

    /// <summary>Writes a Debug output line.</summary>
    /// <param name="$text"></param>
    /// <param name="$addLine"></param>
    public function Debug(string $text, bool $addLine = true) : void
    {
      if ($this->DebugWriter != null)
      {
        if ($addLine)
        {
          $this->DebugWriter->FWriteLine("$text");
        }
        else
        {
          $this->DebugWriter->FWrite("$text");
        }
      }
    } // Debug()

    // The DebugWriter value.
    private ?LJCWriter $DebugWriter;
  } // LJCDebugWriter
?>
