@echo off
php -d upload_max_filesize=32M -d post_max_size=256M -d memory_limit=256M artisan serve %*
