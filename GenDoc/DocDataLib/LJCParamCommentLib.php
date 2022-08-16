<?php
  // ParamCommentLib.php
  declare(strict_types=1);

  // Contains Classes to manage param comments.
  /// <include path='items/LJCParamCommentLib/*' file='Doc/LJCParamCommentLib.xml'/>
  /// LibName: LJCParamCommentLib

  // ***************
  /// <summary>Handles param comments.</summary>
  class LJCParamComment
  {
    // ---------------
    // Public Methods

    /// <summary>Creates a Param object from a param XML comment.</summary>
    /// <param name="$paramLine">The param line.</param>
    /// <returns>The Param object.</returns>
    public function GetParam(string $paramLine) : ?LJCDocDataParam
    {
      $retValue = null;

      if ($paramLine != null)
      {
        $name = LJCCommon::GetDelimitedString($paramLine, "name=\"", "\">");
        $summary = LJCCommon::GetDelimitedString($paramLine, ">", "</");
        $retValue = new LJCDocDataParam($name, $summary);
      }
      return $retValue;
    }
  }
?>