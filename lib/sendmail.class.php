<?php
/**
 * Simple Mailer Class
 */
class Mailer {
	public $fromName;
	public $fromEmail;
	public $toNames;
	public $toEmails;
	public $subject;
	public $message;

	/**
	 * UTF-8 문자열을 EUC-KR로 변환하여 EUC-KR MIME 헤더 형태로 변환한다.
	 * mb_encode_mimeheader()가 존재하지만, 효율적인 활용이 힘들다.
	 */
	private function encode_mimeheader($str) {
		return '=?EUC-KR?B?' . base64_encode(iconv('UTF-8', 'EUC-KR', $str)) . '?=';
	}

	public function send() {
		$recipient = array();
		for($i = 0; $i < count($this->toNames); $i++) {
			$recipient[] = $this->encode_mimeheader($this->toNames[$i]) . ' <' . $this->toEmails[$i] . '>';
		}
		$recipient = implode(',', $recipient);

		$sender = $this->encode_mimeheader($this->fromName) . ' <' . $this->fromEmail . '>';

		$subject = $this->encode_mimeheader($this->subject);

		$mail_header = array();
		$mail_header[] = 'Content-Type: text/html; charset=UTF-8';
		$mail_header[] = 'From: ' . $sender;
		$mail_header[] = 'X-Mailer: PHP';
		$mail_header = implode("\r\n", $mail_header);

		return mail($recipient, $subject, $this->message, $mail_header);
	}
}
