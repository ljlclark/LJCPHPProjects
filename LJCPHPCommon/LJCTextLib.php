<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCTextLib.php
  declare(strict_types=1);
  // Path: Codeline/LJCPHPCommon
  include_once "LJCCommonLib.php";
  // LJCCommonLib: LJCCommon

  /// <summary>The Common Text Output Class Library</summary>
  /// LibName: LJCTextLib
  //  Classes: LJCStringBuilder, LJCHTMLTableColumn, LJCHTMLWriter, LJCWriter
  //    , LJCDebugWriter

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
      $this->Debug = new LJCDebug("LJCTextLib", "LJCStringBuilder"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $this->StringValue = null;
    } // __construct()

    // ---------------
    // Public Methods - LJCStringBuilder

    // Appends a text line with indents.
    /// <include path='items/Line/*' file='Doc/LJCStringBuilder.xml'/>
    public function Line(?string $text, int $indent = 0
      , bool $addBreak = false)
    {
      $enabled = false;
      $this->Debug->BeginMethod("Line", $enabled);

      $this->Text($text, $indent, $addBreak);
      $this->Text("\r\n");

      $this->Debug->EndMethod($enabled);
    } // Line()

    // Appends text with indents.
    /// <include path='items/Text/*' file='Doc/LJCStringBuilder.xml'/>
    public function Text(?string $text, int $indent = 0
      , bool $addBreak = false)
    {
      $enabled = false;
      $this->Debug->BeginMethod("Text", $enabled);

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

      $this->Debug->EndMethod($enabled);
    } // Text()

    // Appends a text line with begin tag, end tag and indents.
    /// <include path='items/Tags/*' file='Doc/LJCStringBuilder.xml'/>
    public function Tags(string $tag, ?string $text, int $indent
      , bool $addBreak = false)
    {
      $enabled = false;
      $this->Debug->BeginMethod("Tags", $enabled);

      if ($text != null)
      {
        $this->Line("<$tag>$text</$tag>", $indent, $addBreak);
      }

      $this->Debug->EndMethod($enabled);
    } // Tags()

    /// <summary>Gets the current builder string length.</summary>
    public function Length() : int
    {
      $enabled = false;
      $this->Debug->BeginMethod("Length", $enabled);
      $retValue = 0;

      $text = $this->ToString();
      if ($text != null)
      {
        $retValue = strlen($text);
      }

      $this->Debug->EndMethod($enabled);
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
      $enabled = false;
      $this->Debug->BeginMethod("InitValue", $enabled);

      if (null == $this->StringValue)
      {
        $this->StringValue = "";
      }

      $this->Debug->EndMethod($enabled);
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
      $this->Debug = new LJCDebug("LJCTextLib", "LJCHTMLTableColumn"
        , "w", false);
      $this->Debug->IncludePrivate = true;

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
  // Static: Run(), Write(), WriteAll(), WriteFile(), WriteLine() 
  // Methods: FClose(), FWrite(), FWriteLine()
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

    // <summary>Writes an XML file.</summary>
    /// <include path='items/WriteFile/*' file='Doc/LJCWriter.xml'/>
    public static function WriteFile(string $text, string $fileSpec)
    {
      if ($text != null)
      {
        $writer = new self($fileSpec, "w");
        $writer->FWrite($text);
        $writer->FClose();
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
    public function __construct(string $fileName, string $mode)
    {
      $this->Debug = new LJCDebug("LJCTextLib", "LJCWriter"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $stream = fopen($fileName, $mode);
      $this->Stream = $stream;
    } // __construct()

    // ---------------
    // Public Methods - LJCWriter

    /// <summary>Closes the stream.</summary>
    public function FClose()
    {
      fclose($this->Stream);
    }

    // Writes file text with indents.
    /// <include path='items/FWrite/*' file='Doc/LJCWriter.xml'/>
    public function FWrite(string $text, int $indentCount = 0)
    {
      $enabled = false;
      $this->Debug->BeginMethod("FWrite", $enabled);

      if ($indentCount > 0)
      {
        fwrite($this->Stream, str_repeat("\t", $indentCount));
      }
      fwrite($this->Stream, "$text");

      $this->Debug->EndMethod($enabled);
    } // FWrite()

    // Writes a file text line with indents.
    /// <include path='items/FWriteLine/*' file='Doc/LJCWriter.xml'/>
    public function FWriteLine(string $text, int $indentCount = 0)
    {
      $this->FWrite("$text\r\n", $indentCount);
    } // FWriteLine()

    // ---------------
    // Class Data

    private bool $Enabled;
    private $Stream;
  } // LJCWriter

  // ***************
  // Contains Debug output methods.
  // Methods: Close(), Debug()
  /// <summary>Contains Debug output methods.</summary>
  class LJCDebugWriter
  {
    // ---------------
    // Constructors

    public function __construct(string $locName, $mode = "w")
    {
      $this->Debug = new LJCDebug("LJCTextLib", "LJCDebugWriter"
        , "w", false);
      $this->Debug->IncludePrivate = true;

      $fileName = $locName . "txt";
      if ($mode = "w")
      {
        $fileName = LJCCommon::GetDebugFileName("Debug", $locName);
      }
      $this->DebugWriter = new LJCWriter($fileName, $mode);
    } // __construct()

    // ---------------
    // Public Methods - LJCDebugWriter

    /// <summary>Close the stream.</summary>
    public function Close()
    {
      $this->DebugWriter->FClose();
    }

    /// <summary>Writes a Debug output line.</summary>
    /// <param name="$text"></param>
    /// <param name="$addLine"></param>
    public function Debug(string $text, bool $addLine = true) : void
    {
      $enabled = false;
      $this->Debug->BeginMethod("Debug", $enabled);

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

      $this->Debug->EndMethod($enabled);
    } // Debug()

    // The DebugWriter value.
    private ?LJCWriter $DebugWriter;
  } // LJCDebugWriter
?>
