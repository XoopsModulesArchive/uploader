<?php

// File Upload Center   ver. 1.4
//
// Copyright (C) 2001, 2002 by Sergey Korostel    skorostel@mail.ru
//modulased for xoops 2004 UNREGISTRED mailto:nedam@mail.ru http://chem.ovl.ru
//modulased for xoops 2004 UNREGISTRED mailto:nedam@mail.ru http://chem.ovl.ru
//modulased for xoops 2004 UNREGISTRED mailto:nedam@mail.ru http://chem.ovl.ru
//modulased for xoops 2004 UNREGISTRED mailto:nedam@mail.ru http://chem.ovl.ru
//----------------------------------------------------------------------------
//      FUNCTIONS
//----------------------------------------------------------------------------

function MsDosTimeToUNIX($DOSdate, $DOStime)
{
    $year = (($DOSdate & 65024) >> 9) + 1980;

    $month = ($DOSdate & 480) >> 5;

    $day = ($DOSdate & 31);

    $hours = ($DOStime & 63488) >> 11;

    $minutes = ($DOStime & 2016) >> 5;

    $seconds = ($DOStime & 31) * 2;

    return mktime($hours, $minutes, $seconds, $month, $day, $year);
}

function list_zip($filename)
{
    $fp = @fopen($filename, 'rb');

    if (!$fp) {
        return;
    }

    fseek($fp, -22, SEEK_END);

    // Get central directory field values

    $headersignature = 0;

    do { // Search header
        $data = fread($fp, 22);

        [$headersignature, $numberentries, $centraldirsize, $centraldiroffset] = array_values(unpack('Vheadersignature/x6/vnumberentries/Vcentraldirsize/Vcentraldiroffset', $data));

        fseek($fp, -23, SEEK_CUR);
    } while ((0x06054b50 != $headersignature) && (ftell($fp) > 0));

    if (0x06054b50 != $headersignature) {
        fclose($fp);

        return;
    }

    // Go to start of central directory

    fseek($fp, $centraldiroffset, SEEK_SET);

    // Read central dir entries

    //echo "<p><font face=\"$font\" size=\"3\" color=\"$normalfontcolor\">$mess[46]</font></p>";

    echo "<p><table class='odd'>";

    for ($i = 1; $i <= $numberentries; $i++) {
        // Read central dir entry

        $data = fread($fp, 46);

        [$arcfiletime, $arcfiledate, $arcfilesize, $arcfilenamelen, $arcfileattr] = array_values(unpack('x12/varcfiletime/varcfiledate/x8/Varcfilesize/Varcfilenamelen/x6/varcfileattr', $data));

        $filenamelen = fread($fp, $arcfilenamelen);

        $arcfiledatetime = MsDosTimeToUNIX($arcfiledate, $arcfiletime);

        echo "<tr class='even'>";

        // Print FileName

        echo '<td>';

        if (16 == $arcfileattr) {
            echo "<b>$filenamelen</b>";
        } else {
            echo $filenamelen;
        }

        echo '</td>';

        // Print FileSize column

        if (16 == $arcfileattr) {
            echo (string)$mess[48];
        } else {
            echo $arcfilesize;
        }

        echo '</td>';

        // Print FileDate column

        echo date($datetimeformat, $arcfiledatetime);

        echo '</td>';

        echo '</tr>';
    }

    echo '</table></p>';

    fclose($fp);
}

function verify_admin($login, $passw)
{
    return 1;
}

function place_header($message)
{
    global $mess, $infopage, $homeurl;

    global $adminloggedin;

    echo "
<table border=\"0\" class='even'>
   <tr>
      <td align=\"left\" class='odd' valign=\"middle\">
        <table border=\"0\" width=\"100%\">
          <tr>
            <th align=\"left\" valign=\"middle\" width=\"60%\">
              $message</th>
            <td align=\"right\" valign=\"middle\" width=\"40%\">
              <a href=\"$homeurl\">
                <img src=\"images/home.gif\" alt=\"$mess[25]\" border=\"0\"></a>
            </td>
          </tr>
        </table>
      </td>
   </tr>
</table>
<br>
";

    if ($adminloggedin) {
        echo '
   <script language="JavaScript">
   <!--
   function x () {
   return;
   }
   function AddString(addSmilie) {
     var revisedFileList;
     var currentFileList = document.DeleteFile.FileList.value;
     revisedFileList = currentFileList + addSmilie + "\\n";
     document.DeleteFile.FileList.value=revisedFileList;
     document.DeleteFile.FileList.focus();
     return;
   }
   //-->
   </script>
';
    }

    if (file_exists($infopage)) {
        include $infopage;
    }

    echo '<br>';
}

function is_viewable($filename)
{
    $retour = 0;

    if (eregi("\.txt$|\.sql$|\.php$|\.php3$|\.phtml$|\.htm$|\.html$|\.cgi$|\.pl$|\.js$|\.css$|\.inc$", $filename)) {
        $retour = 1;
    }

    return $retour;
}

function is_image($filename)
{
    $retour = 0;

    if (eregi("\.png$|\.bmp$|\.jpg$|\.jpeg$|\.gif$", $filename)) {
        $retour = 1;
    }

    return $retour;
}

function is_browsable($filename)
{
    $retour = 0;

    if (eregi("\.zip$", $filename)) {
        $retour = 1;
    }

    return $retour;
}

function taille($filename)
{
    $taille = filesize($filename);

    if ($taille >= 1073741824) {
        $taille = round($taille / 1073741824 * 100) / 100 . ' Gb';
    } elseif ($taille >= 1048576) {
        $taille = round($taille / 1048576 * 100) / 100 . ' Mb';
    } elseif ($taille >= 1024) {
        $taille = round($taille / 1024 * 100) / 100 . ' Kb';
    } else {
        $taille .= ' b';
    }

    if (0 == $taille) {
        $taille = '-';
    }

    return $taille;
}

function unix_time()
{
    global $timeoffset;

    $tmp = time() + 3600 * $timeoffset;

    return $tmp;
}

function file_time($filename)
{
    global $timeoffset;

    $tmp = filemtime($filename) + 3600 * $timeoffset;

    return $tmp;
}

function DeleteFile($filename)
{
    if (file_exists((string)$filename)) {
        unlink((string)$filename);
    }                // Delete file

    if (file_exists("$filename.desc")) {
        unlink("$filename.desc");
    }       // Delete description

    if (file_exists("$filename.dlcnt")) {
        unlink("$filename.dlcnt");
    }      // Delete download counter
}

function remove_old_files()
{
    global $daysinhistory, $uploads_path;

    $time = 0;

    // Read timestamp (when system last time delete files)

    if (file_exists("$uploads_path/$$$.dlcnt")) {
        $fp = fopen("$uploads_path/$$$.dlcnt", 'rb');

        $time = fread($fp, 100); // read last scan date

        fclose($fp);
    }

    if (floor((time() - $time) / 86400) >= 1) { // If 1 day passed, then check & delete old files
        $time = time();

        // Remove all old files

        $handle = opendir($uploads_path);

        while ($filename = readdir($handle)) {
            if ('.' != $filename && '..' != $filename) {
                if (!is_dir("$uploads_path/$filename")) {
                    $file_modif_time = filemtime("$uploads_path/$filename");

                    if (floor(($time - $file_modif_time) / 86400) >= $daysinhistory) {
                        DeleteFile("$uploads_path/$filename");  // Delete file & all auxiliary files
                    }
                }
            }
        }

        closedir($handle);

        // Write new timestamp
        $fp = fopen("$uploads_path/$$$.dlcnt", 'w+b'); // write counter file
        fwrite($fp, $time, 100); //  write back
        fclose($fp);
    }
}

function DeleteFilesByList($list)
{
    global $uploads_path;

    $list = str_replace("\x0D", '', $list);

    $list = str_replace("\x0A", ';', $list);

    $filenames = explode(';', $list);

    $i = 0;

    while ($i < count($filenames)) {
        if ('' != $filenames[$i]) {
            DeleteFile("$uploads_path/" . $filenames[$i]);
        }

        $i++;
    }
}

function filedownloadcount($filename)
{
    if (file_exists("$filename.dlcnt")) {
        $fp = fopen("$filename.dlcnt", 'rb');

        $count = fread($fp, 15); // read counter file

        fclose($fp);

        return $count;
    }
  

    return 0;
}

function increasefiledownloadcount($filename)
{
    if ('.' != $filename && '..' != $filename) {
        $count = filedownloadcount($filename);

        $count += 1;      //  number of downloads + 1
        $fp = fopen("$filename.dlcnt", 'w+b'); // write counter file
        @flock($fp, LOCK_EX);    // Lock file in exclusive mode
        fwrite($fp, $count, 15); //  write back
        @flock($fp, LOCK_UN);    // Reset locking
        fclose($fp);
    }
}

function mimetype($filename)
{
    global $mess, $HTTP_USER_AGENT;

    if (!eregi('MSIE', $HTTP_USER_AGENT)) {
        $client = 'netscape.gif';
    } else {
        $client = 'html.gif';
    }

    if (is_dir($filename)) {
        $image = 'dossier.gif';
    } elseif (eregi("\.txt$", $filename)) {
        $image = 'txt.gif';
    } elseif (eregi("\.html$", $filename)) {
        $image = $client;
    } elseif (eregi("\.htm$", $filename)) {
        $image = $client;
    } elseif (eregi("\.doc$", $filename)) {
        $image = 'doc.gif';
    } elseif (eregi("\.pdf$", $filename)) {
        $image = 'pdf.gif';
    } elseif (eregi("\.xls$", $filename)) {
        $image = 'xls.gif';
    } elseif (eregi("\.gif$", $filename)) {
        $image = 'gif.gif';
    } elseif (eregi("\.jpg$", $filename)) {
        $image = 'jpg.gif';
    } elseif (eregi("\.bmp$", $filename)) {
        $image = 'bmp.gif';
    } elseif (eregi("\.png$", $filename)) {
        $image = 'gif.gif';
    } elseif (eregi("\.zip$", $filename)) {
        $image = 'zip.gif';
    } elseif (eregi("\.rar$", $filename)) {
        $image = 'rar.gif';
    } elseif (eregi("\.gz$", $filename)) {
        $image = 'zip.gif';
    } elseif (eregi("\.tgz$", $filename)) {
        $image = 'zip.gif';
    } elseif (eregi("\.z$", $filename)) {
        $image = 'zip.gif';
    } elseif (eregi("\.exe$", $filename)) {
        $image = 'exe.gif';
    } elseif (eregi("\.mid$", $filename)) {
        $image = 'mid.gif';
    } elseif (eregi("\.wav$", $filename)) {
        $image = 'wav.gif';
    } elseif (eregi("\.mp3$", $filename)) {
        $image = 'mp3.gif';
    } elseif (eregi("\.avi$", $filename)) {
        $image = 'avi.gif';
    } elseif (eregi("\.mpg$", $filename)) {
        $image = 'mpg.gif';
    } elseif (eregi("\.mpeg$", $filename)) {
        $image = 'mpg.gif';
    } elseif (eregi("\.mov$", $filename)) {
        $image = 'mov.gif';
    } elseif (eregi("\.swf$", $filename)) {
        $image = 'flash.gif';
    } else {
        $image = 'defaut.gif';
    }

    return $image;
}

function init($directory)
{
    global $uploads_path, $direction, $mess;

    if ('' == $directory) {
        $current_dir = $uploads_path;
    }

    if ('' == $direction) {
        $direction = 1;
    } else {
        if (1 == $direction) {
            $direction = 0;
        } else {
            $direction = 1;
        }
    }

    if ('' != $directory) {
        $current_dir = "$uploads_path/$directory";
    }

    if (!file_exists($uploads_path)) {
        echo "The root path is not correct. Check the settings<br><br><a href=\"index.php\">$mess[29]</a>\n";

        exit;
    }

    if (!is_dir($current_dir)) {
        echo "$mess[30]<br><br><a href=\"javascript:window.history.back()\">$mess[29]</a>\n";

        exit;
    }

    return $current_dir;
}

function assemble_tableaux($t1, $t2)
{
    global $direction;

    $liste = '';

    if (0 == $direction) {
        $tab1 = $t1;

        $tab2 = $t2;
    } else {
        $tab1 = $t2;

        $tab2 = $t1;
    }

    if (is_array($tab1)) {
        while (list($cle, $val) = each($tab1)) {
            $liste[$cle] = $val;
        }
    }

    if (is_array($tab2)) {
        while (list($cle, $val) = each($tab2)) {
            $liste[$cle] = $val;
        }
    }

    return $liste;
}

function txt_vers_html($chaine)
{
    $chaine = str_replace('&', '&amp;', $chaine);

    $chaine = str_replace('<', '&lt;', $chaine);

    $chaine = str_replace('>', '&gt;', $chaine);

    $chaine = str_replace('"', '&quot;', $chaine);

    return $chaine;
}

function show_hidden_files($filename)
{
    global $showhidden;

    $retour = 1;

    if ('.' == mb_substr($filename, 0, 1) && 0 == $showhidden) {
        $retour = 0;
    }

    return $retour;
}

function listing($current_dir)
{
    global $direction, $order;

    $totalsize = 0;

    $handle = opendir($current_dir);

    $list_dir = '';

    $list_file = '';

    while ($filename = readdir($handle)) {
        if ('.' != $filename && '..' != $filename
            && !eregi('.desc$', $filename)        // Test for description
            && !eregi('.dlcnt$', $filename)        // Test for download counter
            && 1 == show_hidden_files($filename)) {
            $filesize = filesize("$current_dir/$filename");

            $totalsize += $filesize;

            if (is_dir("$current_dir/$filename")) {
                //      if($order=="mod") {$list_dir[$filename]=filemtime("$current_dir/$filename");}
                //      else {$list_dir[$filename]=$filename;}
            } else {
                if ('nom' == $order) {
                    $list_file[$filename] = mimetype("$current_dir/$filename");
                } elseif ('taille' == $order) {
                    $list_file[$filename] = $filesize;
                } elseif ('mod' == $order) {
                    $list_file[$filename] = filemtime("$current_dir/$filename");
                } elseif ('rating' == $order) {
                    $list_file[$filename] = filedownloadcount("$current_dir/$filename");
                } else {
                    $list_file[$filename] = mimetype("$current_dir/$filename", 'image');
                }
            }
        }
    }

    closedir($handle);

    if (is_array($list_file)) {
        if ('nom' == $order) {
            if (0 == $direction) {
                ksort($list_file);
            } else {
                krsort($list_file);
            }
        } elseif ('mod' == $order) {
            if (0 == $direction) {
                arsort($list_file);
            } else {
                asort($list_file);
            }
        } elseif ('rating' == $order || 'type' == $order) {
            if (0 == $direction) {
                asort($list_file);
            } else {
                arsort($list_file);
            }
        } else {
            if (0 == $direction) {
                ksort($list_file);
            } else {
                krsort($list_file);
            }
        }
    }

    //      if(is_array($list_dir))

    //              {

    //              if($order=="mod") {if($direction==0){arsort($list_dir);}else{asort($list_dir);}}

    //              else {if($direction==0){ksort($list_dir);}else{krsort($list_dir);}}

    //              }

    $liste = assemble_tableaux($list_dir, $list_file);

    if ($totalsize >= 1073741824) {
        $totalsize = round($totalsize / 1073741824 * 100) / 100 . ' Gb';
    } elseif ($totalsize >= 1048576) {
        $totalsize = round($totalsize / 1048576 * 100) / 100 . ' Mb';
    } elseif ($totalsize >= 1024) {
        $totalsize = round($totalsize / 1024 * 100) / 100 . ' Kb';
    } else {
        $totalsize .= ' b';
    }

    return [$liste, $totalsize];
}

function contents_dir($current_dir)
{
    global $direction, $order, $directory, $totalsize, $mess;

    global $file_out_max_caracters, $showallfiles;

    global $comment_max_caracters, $adminloggedin, $datetimeformat;

    $prev_currentdate = getdate(0);

    $day_passed = 0;

    // Read directory

    [$liste, $totalsize] = listing($current_dir);

    if (is_array($liste)) {
        while (list($filename, $mime) = each($liste)) {
            if (is_dir("$current_dir/$filename")) {
                $lien = "index.php?direction=$direction&order=$order&directory=";

                if ('' != $directory) {
                    $lien .= "$directory/";
                }

                $lien .= $filename;

                $affiche_copier = 'non';
            } else {
                $lien = '';

                if ('' != $directory) {
                    $lien .= "$directory/";
                }

                $lien .= $filename;

                $lien = "javascript:popup('$lien')";

                $affiche_copier = 'oui';
            }

            $file_modif_time = file_time("$current_dir/$filename");

            if ('mod' == $order) {
                $currentdate = getdate($file_modif_time);

                if (($currentdate['year'] != $prev_currentdate['year'])
                    || ($currentdate['mon'] != $prev_currentdate['mon'])
                    || ($currentdate['mday'] != $prev_currentdate['mday'])) {
                    if ((1 == $day_passed) && (0 == $showallfiles)) {
                        // Print "Show all days message"

                        echo "
    <tr class='tr.even td' valign=\"top\">
      <td align=\"right\" colspan=\"5\">
        <div class='head'align=\"left\"><img src=\"images/calendar.gif\">
          <a href=\"index.php?showallfiles=1\">$mess[44]</a></div>
      </td>
    </tr>\n";

                        break;
                    }

                    // Print day stamp

                    $prev_currentdate = $currentdate;

                    echo "
      <tr class='head' valign=\"top\">
        <td align=\"right\" colspan=\"5\">
          <div class='head'align=\"left\"><img src=\"images/calendar.gif\">\n";

                    $month = $currentdate['mon'];

                    $mday = $currentdate['mday'];

                    $year = $currentdate['year'];

                    echo "$mess[$month] $mday, $year";

                    echo "      </div>
        </td>
      </tr>\n";

                    $day_passed += 1;
                }
            }

            echo "
    <tr class='odd' valign=\"top\">
      <td align=\"right\" width=\"95%\">
        <div align=\"left\">
           <img src=\"images/" . mimetype("$current_dir/$filename") . "\"align=\"ABSMIDDLE\" border=\"0\">\n";

            if (is_viewable($filename) || is_image($filename) || is_browsable($filename) || is_dir("$current_dir/$filename")) {
                echo "<a href=\"$lien\">";
            }

            echo mb_substr($filename, 0, $file_out_max_caracters);

            if (is_viewable($filename) || is_image($filename) || is_browsable($filename) || is_dir("$current_dir/$filename")) {
                echo "</a>\n";
            }

            echo "   </div>
      </td>
      <td align=\"right\" width=\"95%\" nowrap>
        <div class='header' align=\"left\">";

            if ($adminloggedin) { // If logged as admin, add delete file link
                echo "&nbsp;
      <a href=\"javascript: x()\" onClick=\"AddString('$filename');\">
        <img src=\"images/delete.gif\" border=\"0\"></a>";
            }

            echo "        <a href=\"index.php?action=downloadfile&filename=$filename\">
             <img src=\"images/download.gif\"
             alt=\"$mess[23]\" width=\"20\" height=\"20\" border=\"0\"></a>";

            echo filedownloadcount("$current_dir/$filename");

            echo "    </div>
      </td>
      <td align=\"right\" width=\"95%\" nowrap>
        <div align=\"left\">\n";

            echo taille("$current_dir/$filename");

            echo "    </div>
      </td>
      <td align=\"right\" width=\"95%\" nowrap>
        <div align=\"left\">\n";

            echo date($datetimeformat, $file_modif_time);

            echo "  </div>
      </td>
      <td align=\"right\" width=\"95%\">
        <div align=\"left\">
          <p>\n";

            // Load description

            if (file_exists("$current_dir/$filename.desc")) {
                $fp = fopen("$current_dir/$filename.desc", 'rb');

                $contents = fread($fp, $comment_max_caracters);  // read first 300 bytes

                fclose($fp);

                $contents = str_replace('&', '&amp;', $contents);

                $contents = str_replace('<', '&lt;', $contents);

                $contents = str_replace('>', '&gt;', $contents);

                $contents = str_replace('"', '&quot;', $contents);

                $contents = str_replace("\x0D", '', $contents);

                $contents = str_replace("\x0A", ' ', $contents);

                echo $contents;
            }

            echo "    </p>
        </div>
      </td>
    </tr>\n";
        }
    }
}

function list_dir($current_dir)
{
    global $directory, $url_path, $uploads_path, $mess, $direction;

    global $order, $totalsize;

    if (eregi("\.\.", $directory)) {
        $directory = '';
    }

    $current_dir = init($directory);

    //$base_nom_rep=str_replace($uploads_path,"",$current_dir);

    //if($base_nom_rep==""){$base_nom_rep="/";}

    if (1 == $direction) {
        $direction = 0;
    } else {
        $direction = 1;
    }

    if (1 == $direction) {
        $direction = 0;
    } else {
        $direction = 1;
    }

    echo "<script language=\"javascript\">\n";

    echo "function popup(lien) {\n";

    echo "var fen=window.open('index.php?action=view&filename='+lien,'filemanager','status=yes,scrollbars=yes,resizable=yes,width=500,height=400');\n";

    echo "}\n";

    echo "</script>\n";

    $lien = '';

    if ('' != $directory) {
        $lien = '&directory=' . $directory;
    }

    echo "
  <table class='outer'>
    <tr class='itemHead'>
      <td align=\"right\" valign=\"middle\" width=\"95%\">
        <div align=\"left\">$mess[15]
          <a href=\"index.php?order=nom&direction=$direction" . $lien . "\">\n";

    if ('nom' == $order || '' == $order) {
        echo "<img src=\"images/fleche${direction}.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";
    } else {
        echo "<img src=\"images/fleche.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";
    }

    echo "    </b></div>
      </td>
      <td align=\"right\" valign=\"middle\" width=\"95%\" nowrap>
        <div align=\"left\">$mess[16]<b>
          <a href=\"index.php?order=rating&direction=$direction\">\n";

    if ('rating' == $order) {
        echo "<img src=\"images/fleche${direction}.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";
    } else {
        echo "<img src=\"images/fleche.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";
    }

    echo "    </b></b></div>
      </td>
      <td align=\"right\" valign=\"middle\" width=\"95%\" nowrap>
        <div align=\"left\">$mess[17]
          <a href=\"index.php?order=taille&direction=$direction\">\n";

    if ('taille' == $order) {
        echo "<img src=\"images/fleche${direction}.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";
    } else {
        echo "<img src=\"images/fleche.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";
    }

    echo "    </b></div>
      </td>
      <td align=\"right\" valign=\"middle\" width=\"95%\" nowrap>
        <div align=\"left\">$mess[18]
          <a href=\"index.php?order=mod&direction=$direction\">\n";

    if ('mod' == $order) {
        echo "<img src=\"images/fleche${direction}.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";
    } else {
        echo "<img src=\"images/fleche.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";
    }

    echo "    </b></div>
      </td>
      <td align=\"right\" valign=\"middle\" width=\"95%\">
        <div align=\"left\">$mess[19]</b></div>
      </td>
    </tr>\n";

    if (1 == $direction) {
        $direction = 0;
    } else {
        $direction = 1;
    }

    if ('' != $directory) {
        $nom = dirname($directory);

        echo "<tr><td><a href=\"index.php?direction=$direction&order=$order";

        if ($directory != $nom && '.' != $nom) {
            echo "&directory=$nom";
        }

        echo "\"><img src=\"images/parent.gif\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\" border=\"0\">$mess[24]</a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
    }

    contents_dir($current_dir);

    echo "
    <tr class='even' valign=\"top\">
      <td align=\"right\" colspan=\"5\">
        <b>$mess[43]</b>: $totalsize</td>
    </tr>
  </table>
<br>
<!-- File upload script is writen by Sergey Korostel   mailto:skorostel@mail.ru  //-->
<br>
\n";
}

function deldir($location)
{
    if (is_dir($location)) {
        $all = opendir($location);

        while ($file = readdir($all)) {
            if (is_dir("$location/$file") && '..' != $file && '.' != $file) {
                deldir("$location/$file");

                if (file_exists("$location/$file")) {
                    rmdir("$location/$file");
                }

                unset($file);
            } elseif (!is_dir("$location/$file")) {
                if (file_exists("$location/$file")) {
                    unlink("$location/$file");
                }

                unset($file);
            }
        }

        closedir($all);

        rmdir($location);
    } else {
        if (file_exists((string)$location)) {
            unlink((string)$location);
        }
    }
}

function normalize_nom_filename($nom)
{
    global $file_name_max_caracters;

    $nom = stripslashes($nom);

    $nom = str_replace("'", '', $nom);

    $nom = str_replace('"', '', $nom);

    $nom = str_replace('"', '', $nom);

    $nom = str_replace('&', '', $nom);

    $nom = str_replace(',', '', $nom);

    $nom = str_replace(';', '', $nom);

    $nom = str_replace('/', '', $nom);

    $nom = str_replace('\\', '', $nom);

    $nom = str_replace('`', '', $nom);

    $nom = str_replace('<', '', $nom);

    $nom = str_replace('>', '', $nom);

    $nom = str_replace(':', '', $nom);

    $nom = str_replace('*', '', $nom);

    $nom = str_replace('|', '', $nom);

    $nom = str_replace('?', '', $nom);

    $nom = str_replace('§', '', $nom);

    $nom = str_replace('+', '', $nom);

    $nom = str_replace('^', '', $nom);

    $nom = str_replace('(', '', $nom);

    $nom = str_replace(')', '', $nom);

    $nom = str_replace('=', '', $nom);

    $nom = str_replace('$', '', $nom);

    $nom = str_replace('%', '', $nom);

    $nom = mb_substr($nom, 0, $file_name_max_caracters);

    return $nom;
}

//----------------------------------------------------------------------------
//      Shows complete page
//----------------------------------------------------------------------------

function show_contents()
{
    global $current_dir, $directory, $url_path, $uploads_path, $mess, $direction;

    global $order, $totalsize, $showallfiles;

    global $adminloggedin, $loginname, $password;

    echo "<center>\n";

    list_dir($current_dir);

    echo "  <table border=\"0\" width=\"85%\" class='odd' cellpadding=\"4\" cellspacing=\"1\">\n";

    echo "    <tr>\n";

    if (!$adminloggedin) {
        //{

        //echo "      <th align=\"left\" class='even' valign=\"middle\">$mess[20]</th>\n";

        //}

        echo "      <th class='even'align=\"left\" valign=\"middle\">$mess[20]
			<th class='even'align=\"rite\" valign=\"middle\">
             <a href=\"admin/\">
                <img src=\"images/delete.gif\" alt=\"Admin\" border=\"0\"></a>
            </th>\n";
    } else {
        echo "      <th align=\"left\" class='head' valign=\"middle\">Delete files:</th>\n";
    }

    echo "    </tr>\n";

    echo "    <tr>\n";

    echo "        <td align=\"left\" class='odd' valign=\"middle\">\n";

    if (!$adminloggedin) {
        echo "        <form name=\"upload\" action=\"index.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">\n";

        echo "          <input type=\"hidden\" name=\"action\" value=\"upload\">\n";

        echo "          <input type=\"hidden\" name=\"directory\" value=\"$directory\">\n";

        echo "          <input type=\"hidden\" name=\"order\" value=\"$order\">\n";

        echo "          <input type=\"hidden\" name=\"direction\" value=\"$direction\">\n";

        echo "          <input type=\"hidden\" name=\"showallfiles\" value=\"$showallfiles\">\n";

        echo "          <table border=\"0\" width=\"100%\" cellpadding=\"4\">\n";

        echo "            <tr>\n";

        echo "              <td align=\"left\" width=\"15%\">$mess[21]</td>\n";

        echo "              <td align=\"left\" width=\"85%\">\n";

        echo "                <input type=\"file\" name=\"userfile\" size=\"50\">\n";

        echo "              </td>\n";

        echo "            </tr>\n";

        echo "            <tr> \n";

        echo "              <td align=\"left\" width=\"15%\">$mess[22]</td>\n";

        echo "              <td align=\"left\" width=\"85%\">\n";

        echo "                <textarea name=\"description\" cols=\"50\" rows=\"3\"></textarea>\n";

        echo "              </td>\n";

        echo "            </tr>\n";

        echo "            <tr>\n";

        echo "              <td align=\"left\" width=\"100%\" colspan=\"2\">\n";

        echo "                <input type=\"submit\" value=\"$mess[20]\">\n";

        echo "            </a></td>\n";

        echo "           </tr>\n";

        echo "          </table>\n";

        echo "        </form>\n";
    } else {
        echo "        <form name=\"DeleteFile\" action=\"index.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">\n";

        echo "          <input type=\"hidden\" name=\"action\" value=\"deletefiles\">\n";

        echo "          <input type=\"hidden\" name=\"directory\" value=\"$directory\">\n";

        echo "          <input type=\"hidden\" name=\"order\" value=\"$order\">\n";

        echo "          <input type=\"hidden\" name=\"direction\" value=\"$direction\">\n";

        echo "          <input type=\"hidden\" name=\"showallfiles\" value=\"$showallfiles\">\n";

        echo "          <input type=\"hidden\" name=\"loginname\" value=\"$loginname\">\n";

        echo "          <input type=\"hidden\" name=\"password\" value=\"$password\">\n";

        echo "          <textarea name=\"FileList\" cols=\"70\" rows=\"10\"></textarea>\n";

        echo "          <input type=\"submit\" value=\"Delete\">\n";

        echo "        </form>\n";
    }

    echo "        </td>\n";

    echo "    </tr>\n";

    echo "    </table>\n";

    echo "</center>\n";
}

//----------------------------------------------------------------------------
//      MAIN
//----------------------------------------------------------------------------

header('Expires: Mon, 03 Jan 2000 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
include 'include/conf.php';
$adminloggedin = 0;
if (!isset($loginname)) {
    $loginname = '';
}
if (!isset($password)) {
    $password = '';
}
if (!isset($language)) {
    $language = '';
}
if (!isset($order)) {
    $order = '';
}
if (!isset($action)) {
    $action = '';
}
if (!isset($showallfiles)) {
    $showallfiles = 0;
}  // Show only files for last day
if (!isset($current_dir)) {
    $current_dir = '';
}
if ('' == $language) {
    $language = $dft_language;
}
if ('' == $order) {
    $order = 'mod';
}
switch ($action) {
    //----------------------------------------------------------------------------
    //      Administrator LogIn
    //----------------------------------------------------------------------------

    case 'adminlogin':
        include '../../mainfile.php';
        $xoopsOption['show_rblock'] = 0;
        require XOOPS_ROOT_PATH . '/header.php';
        require "include/${language}.php";
        $adminloggedin = verify_admin($loginname, $password);
        include '../../mainfile.php';
        $xoopsOption['show_rblock'] = 0;
        include '../../mainfile.php';
        if ($adminloggedin) {
            place_header('You logged sucessfully.');
        } else {
            place_header("Can't log in as $loginname.");
        }
        show_contents();
        break;
        //----------------------------------------------------------------------------
        //      Delete files
        //----------------------------------------------------------------------------
        include 'admin/admin_header.php';
    case 'deletefiles':
        require "include/${language}.php";
        $adminloggedin = verify_admin($loginname, $password);
        include '../../mainfile.php';
        $xoopsOption['show_rblock'] = 0;
        require XOOPS_ROOT_PATH . '/header.php';
        {
            DeleteFilesByList($FileList);
            place_header("$loginname: files succesfully deleted.");
        }
        show_contents();
        break;
        require_once XOOPS_ROOT_PATH . '/footer.php';
    //----------------------------------------------------------------------------
    //      Change Language
    //----------------------------------------------------------------------------
    case 'savelanguage':
        $language = $_GET['language'];
        setcookie('language', $language, time() + 31536000);  // 1 year
        require "include/${language}.php";
        include '../../mainfile.php';
        $xoopsOption['show_rblock'] = 0;
        require XOOPS_ROOT_PATH . '/header.php';
        place_header($mess[41]);
        show_contents();
        break;
    //----------------------------------------------------------------------------
    //      DOWNLOAD
    //----------------------------------------------------------------------------
    case 'downloadfile':
        require "include/${language}.php";
        $Nomfilename = basename($filename);
        $taille = filesize("$uploads_path/$filename");
        increasefiledownloadcount("$uploads_path/$filename");
        header("Content-Type: application/force-download; name=\"$Nomfilename\"");
        header('Content-Transfer-Encoding: binary');
        header("Content-Length: $taille");
        header("Content-Disposition: attachment; filename=\"$Nomfilename\"");
        header('Expires: 0');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        readfile("$uploads_path/$filename");
        exit();
        break;
    //----------------------------------------------------------------------------
    //      VIEW & PRINT
    //----------------------------------------------------------------------------
    case 'view':
        require "include/${language}.php";
        include '../../mainfile.php';
        include '../../mainfile.php';
        $nomdufilename = basename($filename);
        echo "<html>\n";
        echo "<head><title>$mess[26] : " . $nomdufilename . "</title></head>\n";
        $fp = @fopen((string)$headerpage, 'rb');
        if ($fp) {
            while (!feof($fp)) {
                $buffer = fgets($fp, 4096);

                if (eregi('<body', $buffer)) {
                    $tmp = preg_split('<', $buffer);

                    while (list($cle, $val) = each($tmp)) {
                        if (eregi('body', $val)) {
                            $val = str_replace('>', '', $val);

                            $val = str_replace(chr(10), '', $val);

                            $val = str_replace(chr(13), '', $val);

                            echo "<$val onload=\"self.focus()\">\n";
                        }
                    }

                    break;
                }
            }

            fclose($fp);
        }
        echo "<center>$mess[26] : ";
        echo '<img src="images/' . mimetype("$uploads_path/$filename") . "\" align=\"ABSMIDDLE\">\n";
        echo '<b>' . $nomdufilename . "</b><br><br><HR>\n";
        echo "<a href=\"javascript:window.print()\"><img src=\"images/imprimer.gif\" alt=\"$mess[27]\" border=\"0\"></a>\n";
        echo "<a href=\"javascript:window.close()\"><img src=\"images/back.gif\" alt=\"$mess[28]\" border=\"0\"></a>\n";
        echo "<br>\n";
        echo '<HR><br>';
        if (!is_image($filename)) {
            echo "</center>\n";

            if (is_browsable($filename)) {
                list_zip("$uploads_path/$filename");
            } else {
                $fp = @fopen("$uploads_path/$filename", 'rb');

                if ($fp) {
                    //       echo "<font face=\"$font\" color=\"$normalfontcolor\" size=\"1\">\n";

                    while (!feof($fp)) {
                        $buffer = fgets($fp, 4096);

                        $buffer = txt_vers_html($buffer);

                        $buffer = str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $buffer);

                        echo $buffer . '<br>';
                    }

                    fclose($fp);

                    echo "\n";
                } else {
                    echo "$mess[31] : $uploads_path/$filename";
                }
            }

            echo "<center>\n";
        } else {
            echo "<img src=\"$url_path/$filename\">\n";
        }
        echo "<HR>\n";
        echo "<a href=\"javascript:window.print()\"><img src=\"images/imprimer.gif\" alt=\"$mess[27]\" border=\"0\"></a>\n";
        echo "<a href=\"javascript:window.close()\"><img src=\"images/back.gif\" alt=\"$mess[28]\" border=\"0\"></a>\n";
        echo "<HR></center>\n";
        echo "</body>\n";
        echo "</html>\n";
        exit;
        break;
    //----------------------------------------------------------------------------
    //      UPLOAD
    //----------------------------------------------------------------------------
    case 'upload':
        require "include/${language}.php";
        $message = $mess[40];
        $directory_source = "/$directory";
        $destination = $uploads_path . $directory_source;
        if (0 != $userfile_size) {
            $size_kb = $userfile_size / 1024;
        } else {
            $size_kb = 0;
        }
        if ('none' == $userfile) {
            $message = $mess[34];
        }
        if ('none' != $userfile && 0 != $userfile_size) {
            $userfile_name = normalize_nom_filename($userfile_name);

            // Try if file exists

            if (file_exists("$destination/$userfile_name")
                || // Or file is script
                eregi($rejectedfiles, $userfile_name)
                || ($size_kb > $maxalowedfilesize)) {
                if ($size_kb > $maxalowedfilesize) {
                    $message = "$mess[38] <b>$userfile_name</b> $mess[50] ($maxalowedfilesize Kb)!";
                } elseif (eregi($rejectedfiles, $userfile_name)) {  // If file is script
                    $message = "$mess[49] <b>$userfile_name</b> !";
                } else {
                    $message = "$mess[38] <b>$userfile_name</b> $mess[39]";
                }
            } else {
                // Save description

                if (0 != mb_strlen($description)) {
                    $fp = fopen("$destination/$userfile_name.desc", 'wb');

                    fwrite($fp, $description);

                    fclose($fp);
                }

                if (!move_uploaded_file($userfile, "$destination/$userfile_name")) { //        if (!copy($userfile, "$destination/$userfile_name"))
                    $message = "$mess[33] $userfile_name";
                } else {
                    $message = "$mess[36] <b>$userfile_name</b> $mess[37]";
                }
            }
        }
        include '../../mainfile.php';
        $xoopsOption['show_rblock'] = 0;
        require XOOPS_ROOT_PATH . '/header.php';
        place_header($message);
        show_contents();
        break;
    //----------------------------------------------------------------------------
    //      DEFAULT
    //----------------------------------------------------------------------------

    default:
        require "include/${language}.php";
        remove_old_files();
        include '../../mainfile.php';
        $xoopsOption['show_rblock'] = 0;
        require XOOPS_ROOT_PATH . '/header.php';
        show_contents();
        break;
}
require_once XOOPS_ROOT_PATH . '/footer.php';
