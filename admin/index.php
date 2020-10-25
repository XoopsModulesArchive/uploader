<?php

require dirname(__DIR__, 3) . '/include/cp_header.php';
echo '
<html> <b><big><b> <big><big>LOGIN PAGE</big></big> </b> </big> </b> <br>
<big><b><i><b> <b>PUSH THE LOGIN BUTTON</b> </b> </i> </b> </big>
<form name="logonsystem" action="../index.php" enctype="multipart/form-data" method="post" style="margin: 0">
<input type="hidden" name="action" value="adminlogin">
<input type="hidden" name="directory" value="">
<input type="hidden" name="order" value="mod">
<input type="hidden" name="direction" value="0">
<input type="hidden" name="showallfiles" value="1">
<table border="0" width="100%" cellpadding="4">
<tr>
<td align="left" width="100%" colspan="2">
<input type="submit" value="Login">
</td>
</tr>
</table>
</form>
</td>
</tr>
</table>
<p>&nbsp;</p>
</div>
</body>
</html>
<br>';
