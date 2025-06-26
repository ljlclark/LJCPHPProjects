<?php
  // Copyright (c) Lester J. Clark 2022 - All Rights Reserved
  // LJCGenTextLib.php
  declare(strict_types=1);
  
  // Get relative path to working folder.
  function RelativePrefix()
  {
    $retPrefix = "";
    $path = getcwd();
    $folders = explode("\\", $path);
    $count = count($folders);
    $file = $path."\Codeline.mark";
    while (!file_exists($file))
    {
      $retPrefix .= "../";
      $path = dirname($path);
      $count--;
      if ($count < 1)
      {
        throw new Exception("File 'Codeline/Codeline.mark' was not found.");
        break;
      }
      $file = $path."\Codeline.mark";
    }
    if (strlen($retPrefix) > 0)
    {
      $retPrefix = substr($retPrefix, 0, strlen($retPrefix) - 1);
    }
    return $retPrefix;
  }
?>
