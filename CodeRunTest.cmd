call %command%
set outSpec=%outRoot%\%outFolder%\%file%
del %outSpec% /q
move %outFile% %outSpec%
