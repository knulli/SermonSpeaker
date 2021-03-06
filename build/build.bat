REM This will generate the zipfiles for SermonSpeaker in /build/packages
REM This needs the zip binaries from Info-Zip installed. An installer can be found http://gnuwin32.sourceforge.net/packages/zip.htm
setlocal
SET PATH=%PATH%;C:\Program Files (x86)\GnuWin32\bin
rmdir /q /s packages
mkdir packages
REM Component
cd ../com_sermonspeaker/
zip -r ../build/packages/com_sermonspeaker.zip *
REM Modules
cd ../mod_latestsermons/
zip -r ../build/packages/mod_latestsermons.zip *
cd ../mod_related_sermons/
zip -r ../build/packages/mod_related_sermons.zip *
cd ../mod_sermonarchive/
zip -r ../build/packages/mod_sermonarchive.zip *
cd ../mod_sermoncast/
zip -r ../build/packages/mod_sermoncast.zip *
cd ../mod_sermonspeaker/
zip -r ../build/packages/mod_sermonspeaker.zip *
REM Plugins
cd ../plg_content_sermonspeaker/
zip -r ../build/packages/plg_content_sermonspeaker.zip *
cd ../plg_editors_xtd_sermonspeaker/
zip -r ../build/packages/plg_editors_xtd_sermonspeaker.zip *
cd ../plg_finder_sermonspeaker/
zip -r ../build/packages/plg_finder_sermonspeaker.zip *
cd ../plg_sermonspeaker_search/
zip -r ../build/packages/plg_sermonspeaker_search.zip *
REM Package
cd ../build/packages/
copy ..\..\pkg_sermonspeaker.xml
zip pkg_sermonspeaker.zip *
del pkg_sermonspeaker.xml
