<?php
//ฑþตÞฝ่รึ
require_once('girl.inc');
echo mb_convert_encoding(file_get_contents('./tmpl/mail.html'),"EUC-JP","UTF-8");
?>