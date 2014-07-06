ms\_cms
======

An experimental content management system.

0. Unfortunatelly, database schema is lost.

  This work can probably only serve as a reference.

1. Check `config.php`

2. Put [CKEditor](http://ckeditor.com/) and [KCFinder](http://kcfinder.sunhater.com/) into `manage/`.

3. `var/` is the upload directory, do check permissions and ownerships.

   It should be mode 0777 and owner `HTTPD_USER` for the entire tree.

   Also check `manage/kcfinder/config.php` for absolute paths.

4. Might need to hack in `manage/kcfinder/core/uploader.php`.
