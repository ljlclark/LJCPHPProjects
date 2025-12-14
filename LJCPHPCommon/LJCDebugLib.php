<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // LJCDebugLib.php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCTextFileLib.php";
  // LJCTextFileLib: LJCDebugWriter

  // Contains classes for debugging.
  /// LibName: LJCDebugLib
  //  Classes: LJCDebug

  // ***************
  // Provides methods for debugging.
  // Methods: BeginMethod(), BeginPrivateMethod(), Close(), EndMethod()
  //   , WritePrivateStartText(), WriteStartText(), WritePrivate, Write()
  //   , AddIndent(), IndentString(), WriteIndentCount()
  //   , getIndentCount(), setEnabled()
  /// <summary>Provides methods for debugging.</summary>
  class LJCDebug
  {

    public static function CreateLog(string $libName, string $className
      , bool $enabled = false): LJCDebug
    {
      $retLog = new LJCDebug($libName, $className, "w", $enabled);
      return $retLog;
    }

    public static function StaticMethodLog(string $debugLibName
      , string $className, string $methodName, bool $isPrivate = false
      , bool $isEnabled = false): LJCDebug
    {
      $retLog = LJCDebug::CreateLog($debugLibName, $className
        , isEnabled: false);
      if ($isPrivate)
      {
        $retLog->BeginPrivateMethod($methodName, enabled: $isEnabled);
      }
      else
      {
        $retLog->BeginMethod($methodName, enabled: $isEnabled);
      }
      return $retLog;
    }

    // ---------------
    // Constructors

    /// <summary>Initializes a class instance.</summary>
    public function __construct(string $debugLibName, string $debugClassName
      , string $mode = "w", bool $isEnabled = false)
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

    /// <summary>Begin the method comments.</summary>
    public function BeginMethod(string $methodName, bool $enabled = true)
    {
      if ($enabled)
      {
        $this->WriteStartText($methodName);
      }
    }

    /// <summary>Begin the method comments.</summary>
    public function BeginPrivateMethod(string $methodName, bool $enabled = true)
    {
      if ($enabled)
      {
        $this->WritePrivateStartText($methodName);
      }
    }

    /// <summary>Closes the writer.</summary>
    public function Close()
    {
      if (isset($this->Writer))
      {
        $this->Writer->Close();
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

    public function IsEnabled()
    {
      return $this->Enabled;
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
      if (!$isEnabled)
      {
        unset($this->Writer);
      }
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
    // Example Logging Methods - LJCDebug

    // Example static method file logging code.
    private static function StaticMethodFileLogging()
    {
      $debugLibName = "LJCTextBuilderLib";
      $className = "LJCTextBuilder";
      $methodName = "MethodName";
      $log = LJCDebug::StaticMethodLog($debugLibName, $className, $methodName
        , isPrivate: false, isEnabled: false);

      $debug->EndMethod();
    } // StaticMethodFileLogging()

    // Example static method output logging code.
    private static function StaticMethodLogging()
    {
      // Static method output logging.
      $className = "LJCAttribute";
      $methodName = "MethodName";
    }

    // Example constructor logging code.
    private static function cstr()
    {
      //include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
      //include_once "$prefix/LJCPHPCommon/LJCDebugLib.php";
      // LJCCommonLib: LJC
      // LJCDebugLib: LJCDebug

      // Set logging values.
      $this->ClassName = "LJCClass";
      $methodName = "constructor";
      // Output logging.
      //LJC::OutputDebugObject(__line__, $this->ClassName, $methodName
      //  , "\$object", $object);

      // File logging.
      $sourceLibName = "LJCLib";
      $this->Log = LJCDebug::CreateLog($sourceLibName, $this->ClassName
        , isEnabled: false);
      $this->Log->IncludePrivate = false;
      //$this->Log->Write(__line__." \$value = {$this->value}");

      // Property logging.
      $this->LogText = "";
      //$this->AddLogText(__line__, $this->ClassName, $methodName, "\$value"
      //  , $value);
    } // cstr()

    // Add to logging property.
    private function AddLogText(int $lineNumber, $methodName, $valueName
      , $value = null)
    {
      // Method property logging.
      $methodName = "{$methodName}()";
      $this->LogText .= LJC::OutputDebugObject($lineNumber, $this->ClassName
        , $methodName, $valueName, $value);
    } // AddLogText()

    // Example method file logging code.
    private function MethodFileLogging()
    {
      $methodName = "MethodName";

      // Method file logging.
      $this->Log->EndMethod();
    } // MethodFileLogging()

    // Example method property logging code.
    private function MethodLogging()
    {
      $methodName = "MethodName";
    } // MethodLogging()

    // ---------------
    // Properties - LJCDebug

    /// <summary>Gets or sets the Enabled value.</summary>
    public bool $IncludePrivate;

    // Gets or sets the Enabled value.
    private bool $Enabled;

    // The current indent count.
    private int $IndentCount;

    // The debug writer.
    private LJCDebugWriter $Writer;
  } // LJCDebug
?>
