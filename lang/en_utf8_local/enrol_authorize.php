<?php 
$string['adminemailexpiredteacher'] = 'If you have enabled manual-capture (see above) and teachers can manage the payments, they may also notified about pending orders expiring. This will send an email to each learning path teachers about the count of the pending orders to expire.';
$string['adminneworder'] = 'Dear Admin,
  You have received a new pending order:

   Order ID: $a->orderid
   Transaction ID: $a->transid
   User: $a->user
   Learning path: $a->course
   Amount: $a->amount

   SCHEDULED-CAPTURE ENABLED?: $a->acstatus

  If the scheduled-capture is active, the credit card is to be captured on $a->captureon
  and then the user is to be enrolled to learning path; otherwise it will be expired
  on $a->expireon and cannot be captured after this day.

  You can also accept/deny the payment to enrol the student immediately following this link:
  $a->url';

$string['adminteachermanagepay'] = 'Teachers can manage the payments of the learning path.';
$string['captureyes'] = 'The credit card will be captured and the student will be enrolled to the learning path. Are you sure?';
$string['choosemethod'] = 'If you know the enrolment key of the cource, please enter it below;<br />Otherwise you need to pay for this learning path.';
$string['costdefaultdesc'] = '<strong>In learning path settings, enter -1</strong> to use this default cost to learning path cost field.';
$string['description'] = 'The Authorize.net module allows you to set up paid learning paths via payment providers. If the cost for any learning path is zero, then students are not asked to pay for entry. Two ways to set the learning path cost (1) a site-wide cost as a default for the whole site or (2) a learning path setting that you can set for each learning path individually. The learning path cost overrides the site cost.<br /><br /><b>Note:</b> If you enter an enrolment key in the learning path settings, then students will also have the option to enrol using a key. This is useful if you have a mixture of paying and non-paying students.';
$string['paymentpending'] = 'Your payment is pending for this learning path with this order number $a->orderid.  See <a href=\'$a->url\'>Order Details</a>.';
$string['pendingordersemail'] = 'Dear admin,
$a->pending transactions for learning path \"$a->course\" will expire unless you accept payment within $a->days days.

This is a warning message, because you didn\'t enable scheduled-capture.
It means you have to accept or deny payments manually.

To accept/deny pending payments go to:
$a->url

To enable scheduled-capture, it means you will not receive any warning emails anymore, go to:

$a->enrolurl';

$string['pendingordersemailteacher'] = 'Dear teacher,
$a->pending transactions costed $a->currency $a->sumcost for learning path \"$a->course\"
will expire unless you accept payment with in $a->days days.

You have to accept or deny payments manually because of the admin hasn\'t enabled the scheduled-capture.

$a->url';

$string['welcometocoursesemail'] = 'Dear student,
Thanks for your payments. You have enrolled these learning paths:

$a->courses

You may edit your profile:
 $a->profileurl

You may view your payment details:
 $a->paymenturl';


?>