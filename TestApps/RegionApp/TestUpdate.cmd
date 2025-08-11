set fromMain="C:\Users\Les\Documents\Visual Studio 2022\wwwrootDev"
set toMain=C:\inetpub\wwwroot

rem *** LJCPHPCommon ***
set path=LJCPHPCommon
set from=%fromMain%\%path%
set to=%toMain%\%path%
rem -----
set fileName=LJCCollectionLib.php
copy %from%\%fileName% %to%
rem -----
set fileName=LJCCommonLib.php
copy %from%\%fileName% %to%
rem -----
set fileName=LJCDataManagerLib.php
copy %from%\%fileName% %to%
rem -----
set fileName=LJCDBAccessLib.php
copy %from%\%fileName% %to%
rem -----
set fileName=LJCTextLib.php
copy %from%\%fileName% %to%

rem *** RegionApp ***
set path=RegionApp
set from=%fromMain%\%path%
set to=%toMain%\%path%
rem -----
set fileName=RegionConfigLib.php
copy %from%\%fileName% %to%

rem *** City ***
set path=RegionApp\City
set from=%fromMain%\%path%
set to=%toMain%\%path%
rem -----
set fileName=CityDAL.php
copy %from%\%fileName% %to%

rem *** City\Detail ***
set path=RegionApp\City\Detail
set from=%fromMain%\%path%
set to=%toMain%\%path%
rem -----
set fileName=CityData.php
copy %from%\%fileName% %to%
rem -----
set fileName=CityDetail.php
copy %from%\%fileName% %to%
rem -----
set fileName=CityDetailEvents.js
copy %from%\%fileName% %to%
rem -----
set fileName=CityDetailHead.html
copy %from%\%fileName% %to%
rem -----
set fileName=CityDetailTail.html
copy %from%\%fileName% %to%

rem *** City\List ***
set path=RegionApp\City\List
set from=%fromMain%\%path%
set to=%toMain%\%path%
rem -----
set fileName=CityList.php
copy %from%\%fileName% %to%
rem -----
set fileName=CityListEvents.js
copy %from%\%fileName% %to%
rem -----
set fileName=CityListHead.html
copy %from%\%fileName% %to%
rem -----
set fileName=CityListTable.php
copy %from%\%fileName% %to%
rem -----
set fileName=CityListTail.html
copy %from%\%fileName% %to%
pause