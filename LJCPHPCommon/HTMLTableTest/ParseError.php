<?php
  declare(strict_types=1);
  include_once "LJCRoot.php";
  $prefix = RelativePrefix();
  include_once "$prefix/LJCPHPCommon/LJCCommonLib.php";
  // LJCCommonLib: LJCCommon

  $fileName = "php_errors.log";
  if (file_exists($fileName))
  {
    $contents = file_get_contents($fileName);
    $lines = explode("\n", $contents);
    foreach($lines as $line)
    {
      if (HasValue($line))
      {
        $remainder = rtrim($line);
        $begin = Peel($remainder, "error:", $remainder);
        $error = Peel($remainder, " in ", $remainder);
        $error = trim(substr($error, 0, strlen($error) - 3));
        $codeline = Peel($remainder, "2022", $remainder);
        $folder = RevPeel($remainder, "\\", $remainder);
        $remainder = "in " . rtrim($remainder);

        echo("{$begin}\r\n");
        echo("{$error}\r\n");
        echo("{$remainder}\r\n");
        echo("{$codeline}\r\n");
        echo("    {$folder}\r\n");
        echo("\r\n");
      }
    }
  }

  function HasValue($text) : bool
  {
    $retValue = false;

    if ($text != null
      && strlen($text) > 0)
    {
      $retValue = true;
    }
    return $retValue;
  }

  function Peel($text, $value, &$remainder) : string
  {
    $retText = "";
    $remainder = $text;

    $index = LJCCommon::StrPos($text, $value);
    if ($index != -1)
    {
      $index += strlen($value);
      $retText = substr($text, 0, $index);
      $remainder = substr($text, $index);
    }
    return $retText;
  }

  function RevPeel($text, $value, &$remainder) : string
  {
    $retText = "";
    $remainder = $text;

    $index = LJCCommon::StrRPos($text, $value);
    if ($index != -1)
    {
      $index += strlen($value);
      $retText = substr($text, 0, $index);
      $remainder = substr($text, $index);
    }
    return $retText;
  }
?>
