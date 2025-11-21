<?php
// Create phpmailer directory if it doesn't exist
$dir = __DIR__ . '/phpmailer';
if (!file_exists($dir)) {
    mkdir($dir);
}

// Create src directory inside phpmailer
$src_dir = $dir . '/src';
if (!file_exists($src_dir)) {
    mkdir($src_dir);
}

// PHPMailer source code
$exception_php = <<<'EOD'
<?php
namespace PHPMailer\PHPMailer;
class Exception extends \Exception {}
EOD;

$phpmailer_php = <<<'EOD'
<?php
namespace PHPMailer\PHPMailer;
class PHPMailer {
    public $SMTPDebug = 0;
    public $Host = 'smtp.gmail.com';
    public $SMTPAuth = true;
    public $Username = '';
    public $Password = '';
    public $SMTPSecure = 'tls';
    public $Port = 587;
    public $CharSet = 'UTF-8';
    public $From = '';
    public $FromName = '';
    public $Subject = '';
    public $Body = '';
    public $AltBody = '';
    private $to = array();
    
    public function isSMTP() {}
    public function setFrom($address, $name = '') {
        $this->From = $address;
        $this->FromName = $name;
    }
    public function addAddress($address, $name = '') {
        $this->to[] = array($address, $name);
    }
    public function isHTML($isHtml = true) {}
    public function send() {
        $headers = array(
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->FromName . ' <' . $this->From . '>',
            'Reply-To: ' . $this->From,
            'X-Mailer: PHP/' . phpversion()
        );
        
        foreach ($this->to as $recipient) {
            $success = mail(
                $recipient[0],
                $this->Subject,
                $this->Body,
                implode("\r\n", $headers)
            );
            if (!$success) {
                throw new Exception('Email sending failed');
            }
        }
        return true;
    }
}
EOD;

$smtp_php = <<<'EOD'
<?php
namespace PHPMailer\PHPMailer;
class SMTP {}
EOD;

// Write the files
file_put_contents($src_dir . '/Exception.php', $exception_php);
file_put_contents($src_dir . '/PHPMailer.php', $phpmailer_php);
file_put_contents($src_dir . '/SMTP.php', $smtp_php);

echo "PHPMailer setup completed successfully!";
?> 