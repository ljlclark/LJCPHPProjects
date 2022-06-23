# LJCPHPProjects
These projects were used by myself to learn PHP. With several decades of full time development in C#, quite a bit of experience
with C and some exposure to C++; my coding is heavily influenced by strongly typed, object oriented languages and Pascal case
and Camel case names. To help keep things familiar for me it will be noticed that much of this influence has been brought forward
to my code in PHP.

GenDoc

A technical code HTML documentation generator.

One of my first projects was to write a CodeDoc generator in PHP to read my PHP files and generate CodeDoc HTML. These CodeDoc
pages are found in the WebSites repository, folder CodeDoc/LJCPHPCodeDoc. Much of the internal code XML Documentation syntax was
borrowed from C#.

GenTextLib

A generic template driven code or text generator.

LJCPHPCommon

Some classes can be used in multiple PHP projects. The following are the PHP files that contain these classes.

LJCCommonLib.php

There are certain functions that are used in multiple projects. These are coded as static functions in LJCCommonLib.

LJCDbAccessLib.php

The Database Access functions are encapsulated in class LJCDbAccess.

LJCDataManagerLib.php

A high-level, message based Data Access approach is encapsulated in class LJCDataManager.

LJCCollectionLib.php

A base class that can be used to extend another class to represent a strongly-typed collection of objects.

LJCTextLib.php

Code for writing output to a file or stdout.
