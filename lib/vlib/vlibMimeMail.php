<?php

// +------------------------------------------------------------------------+
// | PHP version 5.x, tested with 5.1.4, 5.1.6, 5.2.6                       |
// +------------------------------------------------------------------------+
// | Copyright (c) 2002-2008 Kelvin Jones, Claus van Beek, Stefan Deussen   |
// +------------------------------------------------------------------------+
// | Authors: Kelvin Jones, Claus van Beek, Stefan Deussen                  |
// +------------------------------------------------------------------------+

// check to avoid multiple including of class
if (!defined('vlibMimeMailClassLoaded'))
{
    define('vlibMimeMailClassLoaded', 1);

    // get the functionality of the vlibCommon class.
    include_once(dirname(__FILE__) . '/vlibCommon/common.php');

    // get error reporting functionality.
    include_once(dirname(__FILE__) . '/vlibMimeMail/error.php');

    // get the Swift functionality
    include_once(dirname(__FILE__) . '/Swift.php');
    include_once(dirname(__FILE__) . '/Swift/Connection/SMTP.php');

    /**
     * vlibMimeMail Class is used send mime encoded mail messages.
     * Has been replaced by Swift Mailer since 2008-10-31.
     *
     * @since 22/04/2002
     * @author Kelvin Jones, Claus van Beek, Stefan Deußen
     * @package vLIB
     * @access public
     */


    class vlibMimeMail
    {

    /*-----------------------------------------------------------------------------\
    |                                 ATTENTION                                    |
    |  Do not touch the following variables. vlibTemplate will not work otherwise. |
    \-----------------------------------------------------------------------------*/

        /** lists To addresses */
        var $sendto = array();

        /** lists Cc addresses */
        var $sendcc = array();

        /** lists Bcc addresses */
        var $sendbcc = array();

        /** from address */
        var $fromEmail = '';
        var $fromName = '';

        /** reply to address */
        var $replyToEmail = '';
        var $replytoName = '';

        /** subject */
        var $subject = '';

        /** paths of attached files */
        var $attachments = array();

        /** attachment mime-types */
        var $mimetypes = array();

        /** attachment dispositions */
        var $dispositions = array();

        /** attachment content IDs */
        var $contentIDs = array();

        /** list of message headers */
        var $xheaders = array();

        /** message priorities */
        var $priorities = array( '1 (Highest)', '2 (High)', '3 (Normal)', '4 (Low)', '5 (Lowest)' );
        var $messagePriority = 3; // Normal

        /** character set of plain text message */
        var $charset = "us-ascii";
        var $ctencoding = "7bit";

        /** character set of html message */
        var $htmlcharset = "us-ascii";
        var $htmlctencoding = "7bit";

        /** Request receipt?? */
        var $receipt = 0;

        /** Do we need to encode when we send?? */
        var $doencoding = 0;

        /** whether to try and fix the windows bug fix */
        var $apply_windows_bugfix = false;
        var $all_emails = array(); // if above is true, will contain all addies

        /** whether to verify all email addresses (by regex) */
        var $checkAddress = true;

        /** mail body */
        var $htmlbody = '';
        var $body = '';

         /** Swift object */
        var $swift = null;
        var $swift_connection = null;
        var $swift_recipients = null;
    /*-----------------------------------------------------------------------------\
    |                           public functions                                   |
    \-----------------------------------------------------------------------------*/


        /**
         * FUNCTION: autoCheck
         *
         * Sets auto checking to the value of $bool.
         *
         * @param bool $bool true/false
         * @access public
         */
        function autoCheck ($bool) {
            $this->checkAddress = ($bool);
        }

        /**
         * FUNCTION: to
         *
         * Sets the To header for the message.
         *
         * @param string $toEmail email address
         * @param string $toName Name of recipient [optional]
         * @access public
         */
        function to($toEmail, $toName = null)
        {
            if (!$toEmail)
            {
                return false;
            }

            if ($this->checkAddress and !$this->validateEmail($toEmail))
            {
                vlibMimeMailError::raiseError('VM_ERROR_BADEMAIL', FATAL, 'To: ' . $toEmail);
            }

            $this->swift_recipients->addTo($toEmail, $toName);
        }

        /**
         * FUNCTION: clearTo
         *
         * Clears all to headers set.
         *
         * @access public
         */
        function clearTo()
        {
                $this->swift_recipients->flushTo();
        }

        /**
         * FUNCTION: cc
         *
         * Sets the Cc header for the message.
         *
         * @param string $ccEmail email address
         * @param string $ccName Name of recipient [optional]
         * @access public
         */
        function cc($ccEmail, $ccName = null)
        {
            if (!$ccEmail)
                {
                    return false;
                }
            if ($this->checkAddress and !$this->validateEmail($ccEmail))
                {
                    vlibMimeMailError::raiseError('VM_ERROR_BADEMAIL', FATAL, 'Cc: '.$ccEmail);
                }
                $this->swift_recipients->addCc($ccEmail, $ccName);
        }

        /**
         * FUNCTION: clearCc
         *
         * Clears all cc headers set.
         *
         * @access public
         */
        function clearCc()
        {
                $this->swift_recipients->flushCc();
        }

        /**
         * FUNCTION: bcc
         *
         * Sets the Bcc header for the message.
         *
         * @param string $bccEmail email address
         * @param string $bccName Name of recipient [optional]
         * @access public
         */
        function bcc($bccEmail, $bccName = null)
        {
            if (!$bccEmail)
                {
                    return false;
                }
            if ($this->checkAddress and !$this->validateEmail($bccEmail))
                {
                    vlibMimeMailError::raiseError('VM_ERROR_BADEMAIL', FATAL, 'Bcc: '.$bccEmail);
                }
                $this->swift_recipients->addBcc($bccEmail, $bccName);
        }

        /**
         * FUNCTION: clearBcc
         *
         * Clears all bcc headers set.
         *
         * @access public
         */
        function clearBcc()
        {
            $this->swift_recipients->flushBcc();
        }

        /**
         * FUNCTION: clearAll
         *
         * Clears all To, Bcc and Cc headers set.
         *
         * @access public
         */
        function clearAll()
        {
            $this->swift_recipients->flushTo();
            $this->swift_recipients->flushCc();
            $this->swift_recipients->flushBcc();
        }

        /**
         * FUNCTION: from
         *
         * Sets the From header for the message.
         *
         * @param string $fromEmail email address
         * @param string $fromName Name of recipient [optional]
         * @access public
         */
        function from($fromEmail, $fromName = null)
        {
            if (!$fromEmail)
                {
                    return false;
                }
            if ($this->checkAddress and !$this->validateEmail($fromEmail))
                {
                    vlibMimeMailError::raiseError('VM_ERROR_BADEMAIL', FATAL, 'From: '.$fromEmail);
                }
                $this->fromEmail = $fromEmail;
                $this->fromName = $fromName;
        }

        /**
         * FUNCTION: replyTo
         *
         * Sets the ReplyTo header for the message.
         *
         * @param string $replytoEmail email address
         * @param string $replytoName Name of recipient [optional]
         * @access public
         */
        function replyTo($replytoEmail, $replytoName = null)
        {
            if (!$replytoEmail)
                {
                    return false;
                }
            if ($this->checkAddress and !$this->validateEmail($replytoEmail))
                {
                    vlibMimeMailError::raiseError('VM_ERROR_BADEMAIL', FATAL, 'ReplyTo: '.$replytoEmail);
                }
                $this->replyToEmail = $replytoEmail;
                $this->replyToName = $replytoName;
        }

        /**
         * FUNCTION: subject
         *
         * Sets the subject for this message.
         *
         * @param string $subject
         * @access public
         */
        function subject($subject)
        {
                $this->subject = $subject;
        }

        /**
         * FUNCTION: body
         *
         * Sets the Body of the message.
         * If you're sending a mail with special characters, be sure to define the
         * charset.
         *  i.e. $mail->body('ce message est en français.', 'iso-8859-1');
         *
         * @param string $body plain text as the body
         * @param string $charset
         * @access public
         */
        function body($body, $charset='')
        {
            $this->body = $body;
            if ($charset != '')
                {
                $this->charset = strtolower($charset);
            }
        }

        /**
         * FUNCTION: htmlBody
         *
         * Sets the HTML Body of the message.
         * You can you the body() function or htmlBody() or both just to be certain that
         * the user will be able to see the stuff you're sending.
         *
         * If you're sending a mail with special characters, be sure to define the
         * charset.
         *  i.e. $mail->htmlbody('ce message est en français.', 'iso-8859-1');
         *
         * @param string $htmlbody html text as the body
         * @param string $charset
         * @access public
         */
        function htmlBody($htmlbody, $charset='')
        {
            $this->htmlbody = $htmlbody;

            if ($charset != '')
                {
                $this->htmlcharset = strtolower($charset);
            }
        }

        /**
         * FUNCTION: attach
         *
         * Attach a file to the message. Defaults the disposition to 'attachment',
         * you can also use 'inline' which the client will try to show in the message.
         * Mime-types can be handled by vlibMimeMail by the list found in
         * vlibCommon/mime_types.php.
         *
         * @param string $filename full path of the file to attach
         * @param string $disposition inline or attachment
         * @param string $mimetype MIME-type of the file. defaults to 'application/octet-stream'
         * @access public
         */
        function attach($filename, $disposition = 'attachment', $mimetype=null, $cid=null) {
            if ($mimetype == null) $mimetype = vlibCommon::getMimeType($filename);
            $this->attachments[] = $filename;
            $this->mimetypes[] = $mimetype;
            $this->dispositions[] = $disposition;
            if (!$cid) {
                srand((double)microtime()*96545624);
                $cid = md5(uniqid(rand())).'@vlib.mimemail';
            }
            $this->contentIDs[] = $cid;
            return $cid;
        }

        /**
         * FUNCTION: organization
         *
         * Sets the Organization header for the message.
         *
         * @param string $org organization name
         * @access public
         */
        function organization($org)
        {
            if (!empty($org)) $this->xheaders['Organization'] = $org;
        }

        /**
         * FUNCTION: receipt
         *
         * To request a receipt you must call this function with a true value.
         * And you must call $this->from() or $this->replyTo() before sending the mail.
         *
         * @param bool $bool true/false
         * @access public
         */
        function receipt($bool=true)
        {
            $this->receipt = ($bool);
        }

        /**
         * FUNCTION: priority
         *
         * Sets the Priority header for the message.
         * usage: $mail->priority(1); // highest setting
         * view documentation for priority levels.
         *
         * @param string $priority
         * @access public
         */
        function priority($priority)
        {
            if (!is_int($priority))
            {
                $priority = settype($priority, 'integer');
            }

            if (!isset($this->priorities[$priority-1]))
            {
                return false;
            }

            $this->messagePriority = $priority;

            return true;
        }

        /**
         * FUNCTION: validateEmail
         *
         * Validates an email address and return true or false.
         *
         * @param string $address email address
         * @return bool true/false
         * @access public
         */
        function validateEmail($address)
        {
            return preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9_-]+(\.[_a-z0-9-]+)+$/i',$address);
        }

        /**
         * FUNCTION: send
         *
         * Sends the mail.
         *
         * @return boolean true on success, false on failure
         * @access public
         */
        function send()
        {
            return $this->_sendMail();
        }

        /**
         * FUNCTION: get
         *
         * Returns the whole e-mail, headers and message. Can be used to display the
         * message in pain text or for debugging.
         *
         * @return string message
         * @access public
         */
        function get()
        {
            $this->_buildMail();

            $mail = 'To: ' . $this->xheaders['To'] . "\n";
            $mail .= 'Subject: ' . $this->xheaders['Subject']."\n";
            $mail .= $this->headers . "\n";
            $mail .= $this->fullBody;

            return $mail;
        }

        /**
        * FUNCTION: vlibMimeMail [contsructor]
        */
        function __construct($host = '', $username = '', $password = '', $port = 25)
        {
            if (empty($host))
            {
                vlibMimeMailError::raiseError('VM_ERROR_HOST_IS_EMPTY', FATAL);
            }

            // construct Swift connection
            $this->swift_connection = new Swift_Connection_SMTP($host, $port);

            // set optional username
            if (!empty($username))
            {
                $this->swift_connection->setUsername($username);
            }

            // set optional password
            if (!empty($password))
            {
                $this->swift_connection->setpassword($password);
            }

            // construct Swift object
            $this->swift = new Swift($this->swift_connection);

            // construct Swift recipients object
            $this->swift_recipients = new Swift_RecipientList();
        }

        /**
        * FUNCTION: _sendMail
        *
        * Proccesses all headers and attachments ready for sending.
        *
        * @access private
        */
        function _sendMail()
        {
            $message = new Swift_Message($this->subject);

            if (empty($this->sendto) and (empty($this->body) and empty($this->htmlbody)))
            {
                vlibMimeMailError::raiseError('VM_ERROR_CANNOT_SEND', FATAL);
            }

            // Attachments
            for($index = 0;$index < sizeof($this->attachments); $index++)
            {
                $message->attach(new Swift_Message_Attachment(
                    new Swift_File($this->attachments[$index]),
                    $this->attachments[$index],
                    $this->mimetypes[$index]));
            }

            // ReplyTo if exists
            if (!empty($this->replyToEmail))
            {
                $message->setReplyTo(new Swift_Address($this->replyToEmail, $this->replyToName));
            }

            // attach body if exist
            if (!empty($this->body))
            {
                if (empty($this->htmlbody) and sizeof($this->attachments) == 0)
                {
                    $message->setData($this->body);
                    Swift_ClassLoader::load('Swift_Message_Encoder');

                    if (Swift_Message_Encoder::instance()->isUTF8($this->body))
                    {
                        $message->setCharset('utf-8');
                    }
                    else
                    {
                        $message->setCharset('iso-8859-1');
                    }
                }
                else
                {
                    $message->attach(new Swift_Message_Part($this->body, 'text/plain'));
                }
            }
            // attach HTML body if exist
            if (!empty($this->htmlbody))
            {
                $message->attach(new Swift_Message_Part($this->htmlbody, 'text/html'));
            }
            return $this->swift->send($message, $this->swift_recipients, new Swift_Address($this->fromEmail, $this->fromName));
        }

        /**
         * FUNCTION: _buildMail
         *
         * Proccesses all headers and attachments ready for sending.
         *
         * @access private
         */
        function _buildMail() {

            if (empty($this->sendto) and (empty($this->body) and empty($this->htmlbody))) {
                vlibMimeMailError::raiseError('VM_ERROR_CANNOT_SEND', FATAL);
            }

            // build the headers
            $this->headers = "";

            $this->xheaders['To']  = implode(',', $this->sendto);

            $cc_header_name  = ($this->apply_windows_bugfix) ? 'cc': 'Cc';
            if (!empty($this->sendcc))  $this->xheaders[$cc_header_name]  = implode(',', $this->sendcc);
            if (!empty($this->sendbcc)) $this->xheaders['Bcc'] = implode(',', $this->sendbcc);

            if ($this->receipt) {
                if (isset($this->xheaders['Reply-To'])) {
                    $this->xheaders['Disposition-Notification-To'] = $this->xheaders['Reply-To'];
                }
                elseif (isset($this->xheaders['From'])) {
                    $this->xheaders['Disposition-Notification-To'] = $this->xheaders['From'];
                }
            }

            if ($this->charset != '') {
                $this->xheaders['Mime-Version'] = '1.0';
                $this->xheaders['Content-Type'] = 'text/plain; charset='.$this->charset;
                $this->xheaders['Content-Transfer-Encoding'] = $this->ctencoding;
            }
            $this->xheaders['X-Mailer'] = 'vlibMimeMail';

            // setup the body ready for sending
            $this->_setBody();

            foreach ($this->xheaders as $head => $value) {
                $rgx = ($this->apply_windows_bugfix) ? 'Subject' : 'Subject|To'; // don't strip out To header for bugfix
                if (!preg_match('/^'.$rgx.'$/i', $head)) $this->headers .= $head.': '.strtr($value, "\r\n", ' ')."\n";
            }
        }

        /**
         * FUNCTION: _build_attachments
         *
         * Checks and encodes all attachments.
         *
         * @access private
         */
        function _build_attachments() {
            $sep = chr(13).chr(10);
            $ata = array();
            $k=0;

            for ($i=0; $i < count( $this->attachments); $i++) {
                $filename = $this->attachments[$i];
                $basename = basename($filename);
                $mimetype = $this->mimetypes[$i];
                $disposition = $this->dispositions[$i];
                $contentID = $this->contentIDs[$i];
                if (preg_match('/^[a-zA-Z]+:\/\//', $filename)) { // figure out if local or remote
                    $upart = parse_url($filename);
                    $newfilename = $upart['scheme'].'://';
                    if (!empty($upart['user'])) $newfilename .= $upart['user'].':'.$upart['pass'].'@';
                    $newfilename .= $upart['host'];
                    if (!empty($upart['port'])) $newfilename .= ':'.$upart['port'];
                    $newfilename .= '/';
                    if (!empty($upart['path'])) {
                        $upart['path'] = substr($upart['path'], 1);
                        $newpath = explode('/', $upart['path']);
                        for($i=0; $i<count($newpath); $i++) $newpath[$i] = rawurlencode($newpath[$i]);
                        $newfilename .= implode('/',$newpath);
                    }
                    if (!empty($upart['query'])) $newfilename .= '?'.urlencode($upart['query']);
                    if (!empty($upart['fragment'])) $newfilename .= ':'.$upart['fragment'];

                  $fp = fopen($newfilename, 'rb');
                  while(!feof($fp)) $data .= fread($fp,1024);
              }
              else {
                  if (!file_exists($filename)) {
                      vlibMimeMailError::raiseError('VM_ERROR_NOFILE', FATAL, $filename);
                  }
                  $fp = fopen($filename, 'rb');
                  $data = fread($fp, filesize($filename));
              }
                $subhdr = '--'.$this->boundary."\nContent-type: ".$mimetype.";\n\tname=\"".$basename."\"\nContent-Transfer-Encoding: base64\nContent-Disposition: ".$disposition.";\n\tfilename=\"".$basename."\"\n";
                if ($contentID) $subhdr .= 'Content-ID: <'.$contentID.">\n";
                $ata[$k++] = $subhdr;
                $ata[$k++] = chunk_split(base64_encode($data))."\n\n";
                fclose($fp);
            }
            $this->fullBody .= "\n".implode($sep, $ata);
        }

    } // class vlibMimeMail
} // << end if (!defined())..

?>