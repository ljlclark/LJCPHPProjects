<?php
  // Copyright (c) Lester J. Clark and Contributors.
  // Licensed under the MIT License.
  // ParamCommentLib.php
  declare(strict_types=1);

  // Contains Classes to manage param comments.
  /// <include path='items/LJCParamCommentLib/*' file='Doc/LJCParamCommentLib.xml'/>
  /// LibName: LJCParamCommentLib
  // LJCParamComment

  // ***************
  // GetParam()
  /// <summary>Handles param comments.</summary>
  class LJCParamComment
  {
    // ---------------
    // Constructors

    /// <summary>Initializes an object instance.</summary>
    public function __construct()
    {
      // Instantiate properties with Pascal case.
      $this->Debug = new LJCDebug("LJCParamCommentLib", "LJCParamComment"
        , "w", false);
      $this->Debug->IncludePrivate = true;
    }

    // ---------------
    // Public Methods

    /// <summary>Creates a Param object from a param XML comment.</summary>
    /// <param name="$paramLine">The param line.</param>
    /// <returns>The Param object.</returns>
    public function GetParam(string $paramLine): ?LJCDocDataParam
    {
      // LJCComments.GetComment()
      $enabled = false;
      $this->Debug->BeginMethod("GetParam", $enabled);
      //$this->Debug->Write(__line__." Var = {$this->Var}");
      //LJC::Debug(__line__, "Var", $this->Var);
      $retValue = null;

      if ($paramLine != null)
      {
        $name = LJC::GetDelimitedString($paramLine, "name=\"", "\">");
        $summary = LJC::GetDelimitedString($paramLine, ">", "</");
        $retValue = new LJCDocDataParam($name, $summary);
      }

      $this->Debug->EndMethod($enabled);
      return $retValue;
    } // GetParam()
  }
?>