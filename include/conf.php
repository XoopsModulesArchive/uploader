<?php
//
// Copyright (C) 2001, 2002 by Sergey Korostel skorostel@mail.ru
// XOOPSed 2004 by UNREGISTRED nedam@mail.ru
//-----------------------------------------------------------------------------------------------------------------------------------------
// VARS TO MODIFY / ПЕРЕМЕННЫЕ ДЛЯ НАСТРОЙКИ
// See comments in English & Russian.
//-----------------------------------------------------------------------------------------------------------------------------------------
// Link to homepage
// Ссылка на домашнюю страницу
$homeurl = 'http://br.xoopscube.org/html/modules/uploader/';
// URL where upload center will be installed (do not left the trailing slash)
// URL где скрипт будет установлен (на забудьте завершающий слэш)
//$installurl="http://br.xoopscube.org/html/modules/uploader/";
$installurl = 'http://br.xoopscube.org/html/modules/uploader/';
// Path and URL to uploads folder
// Путь к директории, куда будут загружаться файлы
//$uploads_path="/home/localhost/www/x2/modules/uploader/files";
//$url_path="http://localhost/x2/modules/uploader/files";
$uploads_path = '/home/groups/x/xo/underpop/htdocs/upload';
$url_path = 'http://br.xoopscube.org/upload';
// Default language
// Язык по-умолчанию
// English : en
// Russian : ru
$dft_language = 'pt-br';
// Maximum allowed filesize to upload (Kilobytes)
// Note: server also has upload size limit
// Максимально допустимый размер файла для закачки (Килобайт)
// Заметка: сервер тоже имеет ограничение на принимаемый размер файла
$maxalowedfilesize = 4096; // 4Mb
// Format of date & time
// Формат вывода даты и времени
$datetimeformat = 'd.m.Y H:i';
// Max number chars for file and directory names
// Максимальное число символов для файлов и каталогов
$file_name_max_caracters = 150;
// Max number chars for filename in tables
// Максимально видимое число символов для файлов
$file_out_max_caracters = 40;
// Max number chars for comment
// Максимальное число символов в комментарии
$comment_max_caracters = 300;
// Regular expression defines which files can't be uploaded
// Регулярное выражение, определяющее, какие файлы нельзя загрузить с помощью этого скрипта
$rejectedfiles = '^index.|.desc$|.dlcnt$|.php$|.php3$|.cgi$|.pl$';
// Show hidden files: Yes=1, No=0
// Показывать скрытые файлы: Да=1, Нет=0
$showhidden = 1;
// Delete files older then $daysinhistory days
// Сколько дней должно пройти, прежде, чем скрипт начнет удалять файлы
$daysinhistory = 100;
