<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDebugLib.php
  declare(strict_types=1);
  $path = "../..";
  // Must refer to exact same file everywhere in codeline.
  include_once "$path/LJCPHPCommon/LJCTextLib.php";

  // Classes
  // File
  //   LJCDebug

  // Contains classes for debugging.
  /// LibName: LJCDebugLib

  // ***************
  /// <summary>Provides methods for debugging.</summary>
  class LJCDebug
  {
    // ---------------
    // Constructors

    /// <summary>Initializes a class instance.</summary>
    public function __construct(string $debugLibName, string $debugClassName
      , bool $isEnabled = true)
    {
      // Instantiate properties with Pascal case.
      $this->DebugClassName = $debugClassName;
      $fullName = "$debugLibName.$debugClassName";
      $this->DebugFullName = $fullName;
      $this->IncludePrivate = false;
      $this->IndentCount = 0;
      $this->setEnabled($isEnabled);
    }

    // ---------------
    // Start Text Methods - LJCDebug

    /// <summary>Writes the private debug section start text.</summary>
    public function WritePrivateStartText($startName, $addIndent = true
      , bool $newline = true)
    {
      if ($this->Enabled
        && $this->IncludePrivate)
      {
        $line = "";
        if ($addIndent)
        {
          $line .= $this->IndentString();
        }
        $line .= $startName;
        $this->Write($line, false, $newline);
        $this->AddIndent();
      }
    }

    /// <summary>Writes the debug section start text.</summary>
    public function WriteStartText(string $startName, bool $addIndent = true
      , bool $newline = true)
    {
      if ($this->Enabled)
      {
        $line = "";
        if ($addIndent)
        {
          $line .= $this->IndentString();
        }
        $line .= $this->StartText($startName);
        $this->Write($line, false, $newline);
        $this->AddIndent();
      }
    }

    // ---------------
    // Write Text Methods - LJCDebug

    /// <summary>Writes the private text.</summary>
    public function WritePrivate(string $text, bool $addIndent = true
      , bool $newLine = true)
    {
      if ($this->Enabled
        && $this->IncludePrivate)
      {
        $line;
        if ($addIndent)
        {
          $line .= $this-IndentString();
        }
        $line .= $text;
        $this.Write($line, false, $newline);
      }
    }

    /// <summary>Writes the text.</summary>
    public function Write(string $text, bool $addIndent = true
      , bool $newLine = true) : void
    {
      if ($this->Enabled)
      {
        $line = "";
        if ($addIndent)
        {
          $line .= $this->IndentString();
        }
        $line .= $text;
        $this->Writer->Debug($line, $newLine);
      }
    }

    // ---------------
    // Other Methods - LJCDebug

    /// <summary>Add a value to the IndentCount.</summary>
    public function AddIndent(int $count = 1)
    {
      if ($this->IndentCount + $count < 0)
      {
        $this->IndentCount = 0;
      }
      else
      {
        $this->IndentCount += $count;
      }
    }

    // Get the indent string.
    public function IndentString() : string
    {
      $retText = "";
      return str_pad($retText, $this->IndentCount * 2);
    }

    /// <summary>Writes the indent count.</summary>
    public function WriteIndentCount()
    {
      $indentCount = strval($this->getIndentCount());
      $this->Debug->Write("IndentCount = $indentCount", false);
    }

    // ---------------
    // Setter Methods - LJCDebug

    /// <summary>Getter for IndentCount.</summary>
    public function getIndentCount() : int
    {
      return $this->IndentCount;
    }

    /// <summary>Setter for Enabled.</summary>
    public function setEnabled(bool $isEnabled)
    {
      $this->Enabled = $isEnabled;
      if ($isEnabled
        && !isset($this->Writer))
      {
        $this->Writer = new LJCDebugWriter($this->DebugClassName);
      }
    }

    // ---------------
    // Private Methods - LJCDebug

    // Creates the main start text.
    private function StartText($startName) : string
    {
      $retText = "$this->DebugFullName.$startName():";
      return $retText;
    }

    // ---------------
    // Properties - LJCDebug

    /// <summary>Gets or sets the Enabled value.</summary>
    public bool $IncludePrivate;

    // Gets or sets the Enabled value.
    private bool $Enabled;

    // The current indent count.
    private int $IndentCount;
  } // LJCDebug
?>
