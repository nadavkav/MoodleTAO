<?php 
$string['description'] = 'This method will repeatedly check for and process a specially-formatted text file in the location that you specify.The file is a comma separated file assumed to have four or six fields per line:
<pre>
*  operation, role, idnumber(user), idnumber(learning path) [, starttime, endtime]
where:
*  operation        = add | del
*  role             = student | teacher | teacheredit
*  idnumber(user)   = idnumber in the user table NB not id
*  idnumber(learning path) = idnumber in the learning path table NB not id
*  starttime        = start time (in seconds since epoch) - optional
*  endtime          = end time (in seconds since epoch) - optional
</pre>
It could look something like this:
<pre>
   add, student, 5, CF101
   add, teacher, 6, CF101
   add, teacheredit, 7, CF101
   del, student, 8, CF101
   del, student, 17, CF101
   add, student, 21, CF101, 1091115000, 1091215000
</pre>';


?>