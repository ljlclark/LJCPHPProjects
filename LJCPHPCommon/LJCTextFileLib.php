<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCTextFileLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  include_once "$prefix/LJCPHPCommon/LJCCommonFileLib.php";
  //include_once "$prefix/LJCPHPCommon/LJCTextFileLib.php";
  // LJCCommonLib: LJCCommon

  /// <summary>The Common Text Output Class Library</summary>
  /// LibName: LJCTextFileLib
  //  Classes: LJCFileWriter, LJCDebugWriter

  // ***************
  // Contains console and file output methods.
  // Static: WriteFile() 
  // Methods: FClose(), FWrite(), FWriteLine()
  /// <include path='items/LJCWriter/*' file='Doc/LJCWriter.xml'/>
  class LJCFileWriter
  {
    // ---------------
    // Public Static Functions

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

    // ---------------
    // Constructors - LJCWriter

    /// <summary>Initializes an object instance.</summary>
    /// <param name="$stream">The stream object.</param>
    public function __construct(string $fileName, string $mode)
    {
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
  // Contains Debug output methods.
  // Methods: Close(), Debug()
  /// <summary>Contains Debug output methods.</summary>
  class LJCDebugWriter
  {
    // ---------------
    // Constructors

    public function __construct(string $locName, $mode = "w")
    {
      $fileName = $locName . "txt";
      $fileName = LJCCommonFile::GetDebugFileName("Debug", $locName);
      $this->DebugWriter = new LJCFileWriter($fileName, $mode);
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
    private ?LJCFileWriter $DebugWriter;
  } // LJCDebugWriter
?>
