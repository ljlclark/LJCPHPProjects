<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJC**Lib.php
  declare(strict_types=1);
  $devPath = "c:/Users/Les/Documents/Visual Studio 2022/LJCPHPProjects";
  require_once "$devPath/LJCPHPCommon/LJCCommonLib.php";
  require_once "$devPath/LJCPHPCommon/LJCTextLib.php";
  require_once "$devPath/LJCPHPCommon/LJCCollectionLib.php";

  // Contains Classes to - **.
  /// <include path='items/LJC**Lib/*' file='Doc/LJC**Lib.xml'/>
  /// LibName: LJC**Lib
  
  // ***************
  // Represents a collection of - objects**.
  /// <include path='items/LJC**/*' file='Doc/LJC**.xml'/>
  class LJC** extends LJCCollectionBase
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      $this->DebugWriter = new LJCDebugWriter("LJC**");
    }

    // Creates a Clone of the current object.
    /// <include path='items/Clone/*' file='../../CommonDoc/PHPDataClass.xml.xml'/>
    public function Clone() : self
    {
      $loc = "LJC**.Clone";
      $retValue = new self();
      foreach ($this->Items as $key => $item)
      {
        $retValue->AddObject($item);
      }
      unset($item);
      return $retValue;
    }

    // ---------------
    // Public Methods - LJC**

    // Adds an object and key value.
    /// <include path='items/AddObject/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function AddObject(LJCObjectClass $item, $key = null)
      : ?LJCObjectClass
    {
      if (null == $key)
      {
        $key = $item->ID;
      }
      $retValue = $this->AddItem($item, $key);
      return $retValue;
    }

    // Get the item by Key value.
    /// <include path='items/Get/*' file='../../CommonDoc/PHPCollection.xml'/>
    public function Get($key, bool $throwError = true) : ?LJCObjectClass
    {
      $retValue = $this->GetItem($key, $throwError);
      return $retValue;
    }

    // ---------------
    // Private Methods - LJC**

    // Output the debug value.
    private function Debug(string $text, bool $addLine = true) : void
    {
      $this->DebugWriter->Debug($text, $addLine);
    }
  }  // LJC**
?>