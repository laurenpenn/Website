=== BackWPup ===
Contributors: danielhuesken, inpsyde, Bueltge, nullbyte
Tags: backup, database, file, ftp, xml, time, upload, multisite, cloud, dropbox, storage, amazon
Requires at least: 3.1
Tested up to: 3.5
Stable tag: 2.1.17
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WordPress Backup and more...

== Description ==

Do backups and more for your WordPress Blog.

* Database Backup
* WordPress XML Export
* Optimize Database
* Check\Repair Database
* File Backup
* Backups in zip, tar, tar.gz, tar.bz2 format
* Store backup to Folder
* Store backup to FTP Server
* Store backup to Amazon S3
* Store backup to Google Storage
* Store backup to Microsoft Azure (Blob)
* Store backup to RackSpaceCloud
* Store backup to Dropbox
* Store backup to SugarSync
* Send Log/Backup by Email
* Multisite Support only as Network Admin

** WP 3.1 and PHP 5.2.5 Required!! **

** NO WARRANTY SUPPLIED! **
** Test your Backups! **

== Installation ==

1. Download BackWPup Plugin.
2. Decompress and upload the contents of the archive into /wp-content/plugins/.
3. Activate the Plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==


== Screenshots ==
1. Job Page
2. Working Job
3. Logs Page
4. Backups Manage Page

== Changelog ==
= 2.1.17 =
* Changed name of sav button to prevent false css.
* added autocomplte off to some input fields.
* cheange api domain for google storage
* Updated AWS lib to 1.5.17.1

= 2.1.16 =
* Dropbox chunking will done in temp again.
* uses stream for compression
* Metaboxes can't disabled by the screen options
* Set mail charset
* Handling for Dropbox 503 errors
* removed oauth bypass Google Analytics by Yoast
* updatet AWS lib to 1.5.15
* bug fixes

= 2.1.15 =
* Fixed FTP folder bug
* Dropbox chunking will done in memory not in temp

= 2.1.14 =
* removed calling home function
* fixed missing ' in mysql dump
* now lager than 150MB uploads to Dropbox with there Beta API
* fixed bug in progress updates
* fixed problem with ftp dir can not empty
* Updated AWS lib to 1.5.11

= 2.1.13 =
* tweaked Gstorage upload thx Kevin
* fixed bug in xml file generation
* changed base64 function to prevent false positive massages from scanners
* removed executeable files from MS Azure SDK
* changed Dropbox oAuth see https://www.dropbox.com/developers/blog/20
* removed BackWPup Cron service
* changed authentication method for SugarSync please re login

= 2.1.12 =
* always check existing .htaccess in log/temp/backup folder
* set tmp folder to plugindir/tmp
* removed input_filter function
* fixed problem with mb_string funktion on job start
* reduced needed memory for FTP upload.
* Updated Google Storage Url's
* Updated AWS lib to 1.5.8.1

= 2.1.11 =
* fixed bug in contextual help
* Updated AWS lib to 1.5.6
* changes for WordPress 3.4

= 2.1.10 =
* typo fix
* add robots noindex,nofollow in log header
* trigger job start errors
* Updated AWS lib to 1.5.3
* added support for help functions

= 2.1.9 =
* bypass Google Analytics by Yoast oauth
* improved working displaying

= 2.1.8 =
* Updated AWS lib to 1.5.2
* Security improvements
* Updated MS Azure to 4.1.0
* Updated RSC lib to 1.7.10
* Updatet oAuth lib for Dropbox
* increased memory on db dump

= 2.1.7 =
* New Logo
* Fixed not displayed save button
* Updated AWS lib to 1.5.0
* some tweaks

= 2.1.6 =
* fixed Dropbox with no path problem
* fixed Dropbox path with witespace
* Updated AWS lib to 1.4.5
* 2. fix for security vulnerability

= 2.1.5 =
* Updated AWS lib to 1.4.4
* use my own dropbox lib again and chaned it to new api
* Fixed security vulnerability
* some littel fixes

= 2.1.4 =
* correct some values on job copy/export
* fixed warning on dropbox upload.
* WP cron Job only set is a job activated
* now a manualy job starts with ajax is 'define('ALTERNATE_WP_CRON', true);'
* removed runtime settings
* send log mails with full text
* added aditional checks for job running
* Updated AWS lib to 1.4.0.1

= 2.1.3 =
* Preformance improvment
* Uses now dropbox-php.com lib (ver. 0.4.2)
* Updated AWS lib to 1.4.0
* changed Plugin activation function, because on update or upload activate is not called
* bug fixes

= 2.1.2 =
* removed http class, makes to many problems

= 2.1.1 =
* Fiexd problem with translation
* Don't display dashboard Wigets on Mulitsite for not super amdins
* Some improvements for Multisite
* Updated AWS lib to 1.3.7
* Google Storage uses now AWS lib
* Added http basic autentication support
* now DB jobs uses new not selected tabels too
* bug fixes

= 2.1.0 =
* No more sessions and curl needed
* Respect open_basdir for temp folder
* Dropbox changes, better uploads, but needs many memory
* Added Multiseite support (only for Network Admin)
* Uses now ‘WP_TEMP_DIR’ for getting temp folder
* Updated AWS lib to 1.3.6
* Job runs now in UTC time. Time outputs will convert.
* Many languge strings changed for better translation
* Fixed double job run in same time
* Removed cache prevention for cron to resolve problems with W3 Total Cache
* Readded Support for WordPress 3.1

= 2.0.3 =
* Reimplemt deletion of backups in a folder
* Hopfuly fix of 'temp is not writeable'
* Fixed 'Black Screen'
* more error output on XML export
* satus on pcl zip create is back and use off the wordpress class

= 2.0.2 =
* fixed bug Class 'ZipArchive' not found now
* ficed bug in chnge logfile folder
* replase spaces bei _ in dropboxfolder
* some more bug fixes

= 2.0.1 =
* Zip file creation now faster agin but no stat output
* serverl bug fixes

= 2.0.0 =
* PHP Sessions, curl and PHP version 5.2.4 required!
* Wordpress 3.2 required!
* Using the system temp dir now
* Updated AWS lib to 1.3.5
* Updated RSC lib to 1.7.9
* Updated MS Azure lib to 3.0.0
* Added Google storage as destination
* Reworked GUI (WordPress Dropboxes, working screen options, ....)
* Complete new job working ot of Wordpress (less memory,automatic restart,...)
* Added easyer job sheduling selection

= 1.7.8 =
* Updated AWS SDK to ver.1.3.5 for Amazon S3
* some extra outputs on job start
* resart Script execution time on many job operations.
* some changes too prevent cache addons
* prevent job run twice on same time on background

= 1.7.7 =
* cleanup brocken buckupfiels on job start

= 1.7.6 =
* fix problem with a losing sql connection on job end

= 1.7.5 =
* fix problems in cron calculation

= 1.7.4 =
* jobs not longer work ever... max. time is 5 min.
* hopfuly fix for dropbox upload
* fix dropbox auth deletion
* fixed bug in Sugarsync qouta

= 1.7.3 =
* Fixed Dropbox PLAINTEXT signatre
* Updated pod
* Added/updated German translation (thx David Decker)

= 1.7.2 =
* try to disable Cache plugins for working job
* more dropbox improvements
* fixed Curl error on WP-Export
* fixed dashbord wigedt shown for all users
* bug fixes

= 1.7.1 =
* Bugfix on make new jobs
* Bugfix on job run with dbdump
* Bugfix on Backup Bulk actions

= 1.7.0 =
* Improved Dropbox referer handling
* Sycurity fix (thanks to Phil Taylor - Sense of Security)
* Added SugarSync support
* general improvements
* bug fixes

= 1.6.2 =
* Dropbox improvements and bug fixes

= 1.6.1 =
* Now use web OAuth login for DropBox! Best thanks to Tijs Verkoyen for his great DropBox class.
* Only DropBox OAuth tokens are saved!
* Check DropBox Quota/Upload Filesize on Job run
* fixed bug in .tar with file/folder names longer than 100 chars
* changed user capability back to '10' when working with WP lower than 3.0
* bug fixes for old WP versions
* English text updates! Best thanks to Marcy Capron.
* general improvements
* bug fixes

= 1.6.0 =
* new DropBox class to use all functions (download, delete, list)
* added useful links in job edit page
* renamed functions.php to resolve problems arising from other plugins
* general improvements

= 1.5.5 =
* Updated AWS SDK to ver.1.2.6 for Amazon S3
* Added AWS Region "Northeast" (Japan)
* Added Microsoft Azure (Blob) as backup destination
* bug fixes

= 1.5.2 =
* changes for user checking
* removed plugin init action

= 1.5.1 =
* changed user capability from '10' to 'export'
* Updated AWS SDK to ver.1.2.5 for Amazon S3

= 1.5.0 =
* use AWS SDK ver.1.2.4 now for Amazon S3
* Update Rackspase cloud files to ver.1.7.6
* Added job setting import/export
* Download link for last backup in jobs tab
* Link for last log in jobs tab
* Logs can now be compressed
* Backup destinations can now be disabled (see help)
* Bug fixes and improvements

= 1.4.1 =
* DropBox changes
* fixed problem on send log with email
* Security fix (thanks Massa Danilo)

= 1.4.0 =
* make SSL-FTP as option
* added DropBox support (zlli)

= 1.3.6 =
* long file list no longer displayed in logs.
* Added option to see detailed file list
* removed FTP Alloc command
* set FTP normal mode if passive mode disabled
* remove FTP helper function and use FTP PHP functions
* spend file list 2MB free memory

= 1.3.5 =
* fixed problem with folder include
* added option to deactivate FTP passive mode
* fixed bug for parsing errors because PHP 5 move PHP 5 functions in a seperate file

= 1.3.4 =
* fixed warning in send mail
* bug fixes

= 1.3.3 =
* fixed bug with clear only displayed
* fixed bug with Parse Error for some PHP versions

= 1.3.2 =
* added changable backup file prefix
* bug fixes

= 1.3.1 =
* added file and DB size information
* removed "LOCK TABLE" in sql dumps
* fixed bug in automatic job abortion
* fixed bug in ABSPATH if it '/'
* fixed bug in save settings
* fixed bugs if no jobs exists
* added link to clear running jobs

= 1.3.0 =
* added S3 new region codes for bucket creation
* added S3 REDUCED REDUNDANCY support on backups
* jobs will be aborted after 10 min. and can't run twice
* use curl for xml dump and copy if curl not works
* increased min. PHP version to 5.2.0, because then all works
* use linux cron based scheduling times
* added rackspacecloud.com support
* use WP 3.1 table creation
* added plugin checks for folder and new scheduling

= 1.2.1 =
* fixed "Wrong parameter count for array_unique()" for old php version
* added php version to log header
* added mysql version to log header

= 1.2.0 =
* Backup file size now in log file
* Paged Logs Table
* added Backup Archives Page
* Grammar fixes
* Bug fixes

= 1.1.1 =
* fixed "S3 lib not found" bug again.
* improved reschedule on activation problem.

= 1.1.0 =
* added function to check/update job settings
* added no Ajax bucket list to job page
* changed error handling a bit and remove PHP errors that can't handled
* fixed problem with not compiled --enable-memory-limit in PHP
* removed setting for memory limit use WP filter and default now (256M)
* now a time limit of 5 mins. is set again for job execution but it will be reseted on every message. (prevent never ending jobs.)
* added a shutdown function if __destruct not called for job
* added more flexible Backup file selection

= 1.0.10 =
* fix  "Undefined index: dbshortinsert"

= 1.0.9 =
* change s3 class to hide warnings
* add option to make MySQL INSERTs shorter (smaller dump file size.)
* add requirements checks
* Ajaxed S3 bucket selection in job settings
* add S3 Bucket can made in job settings

= 1.0.8 =
* fix temp backup file not deleted if no destination folder
* some folder fixes
* removed some not used code

= 1.0.7 =
* added button in Help
* Fixed bug on S3 file deletion
* get files form S3 now faster for file deletion

= 1.0.6 =
* fixed false massage an send mail with backup
* removed test code for blank screen and fixed it!

= 1.0.5 =
* some ABSPATH changes

= 1.0.4 =
* fixed bugs in DB restore
* use WP functions to get Plugin dirs

= 1.0.3 =
* hopefully fixed a cache problem on run now

= 1.0.2 =
* fixed bug for file excludes

= 1.0.1 =
* fixed bug for https

= 1.0.0 =
* now WordPress Exports to XML can made
* new backup files formats tar, tar.gz, tar.bz2
* all job types can be created in one job
* added PHP zip extension support (use PclZip only if not supported)
* removed PclZip trace code
* fixed time display and schedule bugs
* added some security
* Maintenance Mode on MySQL Operations
* new Design on some Pages

= 0.8.1 =
* use global var instead of constant for log file
* PCLZip Trace included with setting for log Level

= 0.8.0 =
* Fixed not working default settings on settings page
* create .htaccess on Apache and index.html on other Webserver
* fixed global for $wp_version
* set max execution time to 0 for unlimited
* use WP function to generate options tables
* Backup file list and zip creation changes
* Added support for Amazon S3
* Only works with PHP 5 now
* Complete rewrite of job as PHP5 class
* PHP errors now in backup log
* Log now in files

= 0.7.2 =
* make FTP any more robust
* increased memory for Zip Files
* make date with date_i18n

= 0.7.1 =
* FTP Connection test changes
* no Errors in Log for FTP ALLO command.

= 0.7.0 =
* set ftp Connection timeout to 10 sec
* fix bug for DB tables exclude
* DB Backup in MySQL Client encoding now
* Fixed missing ; in DB Backup
* Added tool DB Restore with automatic Blog URL/Path change

= 0.6.5 =
* Prevent direct file loading
* job working in iFrame
* colored logs
* HTML fixes
* spell check

= 0.6.4 =
* New option to delete old logs
* Backup file deletion separated form logs deletion
* make dashboard widget smaller
* added massages
* bug fixes

= 0.6.3 =
* use ftp_row for login and other commands
* Add option to send only email on errors
* Internal structure changes
* Add option to disable WP-Cron and use host's cron
* bug fixes

= 0.6.2 =
* Added setting for memory_limit if needed
* Added setting for Max. Script execution time
* Added job option to make Max file size for sending via mail
* bug fixes and little improvements

= 0.6.1 =
* Added setting for send email type.
* Optimize memory usage again
* Fixed Bug that kept cron from working

= 0.6.0 =
* Add Dashboard Widget
* Add Database Check
* Add Backup file transfer to FTP Server
* Save log files in own database table
* Optimize Memory usage
* Optimize File system access
* DB dump with own function
* fixed some Bugs

= 0.5.5 =
* removed log files. Log now stored in Database

= 0.5.0 =
* Initial release