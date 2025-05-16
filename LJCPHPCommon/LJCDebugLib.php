<?php
  // Copyright(c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDebugLib.php
  declare(strict_types=1);
  // Must refer to exact same file everywhere in codeline.
  // Path: LJCPHPProjectsDev/GenDoc/DocDataLib
  include_once "../../LJCPHPCommon/LJCTextLib.php";
  // LJCTextLib: LJCDebugWriter

  // Contains classes for debugging.
  /// LibName: LJCDebugLib
  //  LJCDebug

  // ***************
  // Provides methods for debugging.
  // Public: Close(), WritePrivateStartText(), WriteStartText()
  //  , WritePrivate, Write(), AddIndent(), IndentString(), WriteIndentCount()
  //  , getIndentCount(), setEnabled
  /// <summary>Provides methods for debugging.</summary>
  class LJCDebug
  {
    // ---------------
    // Constructors

    /// <summary>Initializes a class instance.</summary>
    public function __construct(string $debugLibName, string $debugClassName
      , string $mode = "w", bool $isEnabled = true)
    {
      // Instantiate properties with Pascal case.
      $this->DebugClassName = $debugClassName;
      $fullName = "";
      if (trim($debugLibName) != "")
      {
        $fullName = "$debugLibName.";
      }
      $fullName .= $debugClassName;
      $this->DebugFullName = $fullName;
      $this->Enabled = false;
      $this->IncludePrivate = false;
      $this->IndentCount = 0;
      $this->Mode = $mode;

      // Creates Writer if true.
      $this->setEnabled($isEnabled);
    }

    // ---------------
    // Start Text Methods - LJCDebug

    public function Close()
    {
      if (isset($this->Writer))
      {
        $this->Writer->Close();
      }
    }

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
    // Method Begin and End Methods - LJCDebug

    /// <summary>Begin the method comments.</summary>
    public function BeginMethod(string $methodName, bool $enable = true)
    {
      if ($enable)
      {
        $this->WriteStartText($methodName);
      }
    }

    /// <summary>Begin the method comments.</summary>
    public function BeginPrivateMethod(string $methodName, bool $enable = true)
    {
      if ($enable)
      {
        $this->WritePrivateStartText($methodName);
      }
    }

    /// <summary>End the method comments.</summary>
    public function EndMethod(bool $enabled = true)
    {
      if ($enabled)
      {
        $this->AddIndent(-1);
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
        $this->Writer = new LJCDebugWriter($this->DebugClassName, $this->Mode);
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
