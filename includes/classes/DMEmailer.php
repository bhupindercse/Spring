<?php

include_once(dirname(__FILE__).'/../../includes/init.php');

class DMEmailer
{
	private $from             = array();
	private $to               = array();
	private $subject          = "";
	private $htmlMsg          = "";
	private $plainTextMsg     = "";
	private $addtionalHeaders = "";

	private $generalStyles    = "";
	private $bg               = "";
	private $fontSize         = "12px";
	private $fontFamily       = "Arial,sans-serif";
	
	private $headerStyles      = array();
	private $infoElementStyles = array();
	private $footerStyles      = array();
	
	private $header         = "";
	private $headerPlainTxt = "";
	private $body           = "";
	private $bodyPlainTxt   = "";
	private $infoElements   = array();
	private $footer         = "";
	private $footerPlainTxt = "";

	private $showErrors = 0;
	private $sendErrors = "";

	public function __construct($subject = ""){

		$this->subject = $subject;
		$this->bg = Config::get('absolute_url').'images/emails/bg.jpg';

		$this->generalStyles .= 'body { margin: 0; padding: 0; }';
		$this->generalStyles .= '@media only screen and (max-device-width: 480px){';
		$this->generalStyles .= 		'td[class="bg_spacer"]{ display: none !important; }';
		$this->generalStyles .= 		'td[class="middle_cell"]{ padding-top: 0 !important; }';
		$this->generalStyles .= 		'table[class="contentwrapper"]{ width: 100% !important; }';
		$this->generalStyles .= '}';

		$this->headerStyles = array(
			"color"            => "#FFF",
			"background-color" => "#204f5f",
			"font-size"        => "18px",
			"font-weight"      => "bold",
			"padding"          => "18px"
		);

		$this->infoElementStyles = array(
			"padding"        => "8px 8px",
			"color"          => "#204f5f",
			"font-size"      => "14px",
			"text-transform" => "uppercase"
		);

		$this->footerStyles = array(
			"background-color" => "#353535",
			"color"            => "#FFF",
			"font-size"        => "12px",
			"padding"          => "14px"
		);
	}

	// ===================================================================================
	//	SETTINGS
	// ===================================================================================
	private function showDebug($txt){
		
		if($this->showErrors){
			echo '<pre>';
			print_r($txt);
			echo '</pre>';
		}
	}

	public function setDebug($showErrors){
		$this->showErrors = $showErrors;
	}

	public function addSubject($subject = ""){
		$this->subject = $subject;
	}

	public function addAddress($address){
		if(!preg_match(Config::get('email_pattern'), $address))
			throw new Exception($address." is not a valid email address to send to.");

		$this->to[] = $address;
	}

	public function setFrom($addressUrl, $addressName = ""){
		if(!preg_match(Config::get('email_pattern'), $addressUrl))
			throw new Exception($address." is not a valid email address to send from.");

		$this->from['email'] = $addressUrl;
		$this->from['title'] = $addressName;
	}

	// ===================================================================================
	//	STYLES
	// ===================================================================================
	public function setFontSize($fontSize){
		$this->fontSize = $fontSize;
	}

	public function setFontFamily($fontFamily){
		$this->fontFamily = $fontFamily;
	}

	public function headerColor($color){
		$this->headerStyles['color'] = $color;
	}

	public function headerBackground($color){
		$this->headerStyles['background-color'] = $color;
	}

	public function addHeaderStyles($styleArray = array()){

		if(!is_array($styleArray))
			throw new Exception("Header style given is not an array.");

		foreach($styleArray as $key => $val){
			$this->headerStyles[$key] = $val;
		}
	}

	public function addFooterStyles($styleArray = array()){

		if(!is_array($styleArray))
			throw new Exception("Header style given is not an array.");

		foreach($styleArray as $key => $val){
			$this->footerStyles[$key] = $val;
		}
	}

	public function addInfoElementStyles($styleArray = array()){

		if(!is_array($styleArray))
			throw new Exception("Header style given is not an array.");
		if(!empty($this->body))
			throw new Exception("Cannot pass styles to body elements if already started.");

		foreach($styleArray as $key => $val){
			$this->infoElementStyles[$key] = $val;
		}
	}

	// ===================================================================================
	//	SECTIONS
	// ===================================================================================
	public function addHeader($txt){
		$this->header = $txt;

		$txtPieces = explode("<br>", $txt);
		$newTxt = "";
		foreach($txtPieces as $item){
			$newTxt .= $item.PHP_EOL;
		}

		$this->headerPlainTxt = strip_tags($newTxt);
	}

	public function addFooter($txt){
		$this->footer = $txt;

		$txtPieces = explode("<br>", $txt);
		$newTxt = "";
		foreach($txtPieces as $item){
			$newTxt .= $item.PHP_EOL;
		}

		$this->footerPlainTxt = strip_tags($newTxt);
	}

	public function addInfoElement($title, $content){
		
		if(preg_match(Config::get('phone_pattern'), $content))
			$content = '<a href="" style="color:#666;text-decoration:none;">'.$content.'</a>';

		$this->startBody();

		$this->body .= '<tr>';
		$this->body .= 	'<th align="left" valign="top" style="';
		foreach($this->infoElementStyles as $key => $val){
			$this->body .= $key.':'.$val.';';
		}
		$this->body .= 	'">'.$title.'</th>';
		$this->body .=	'<td align="left" valign="top" style="padding:8px 8px;font-size:12px;">'.$content.'</td>';
		$this->body .= '</tr>';

		$txtPieces = explode("<br>", $content);
		$newTxt = "";
		foreach($txtPieces as $item){
			$newTxt .= $item.PHP_EOL;
		}

		$this->bodyPlainTxt = strip_tags($newTxt).PHP_EOL;
	}

	private function startBody(){
		if(empty($this->body))
			$this->body .= '<table>';
	}

	// ===================================================================================
	//	SEND
	// ===================================================================================
	private function compileEmail(){

		$this->htmlMsg .= '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
		$this->htmlMsg .= '<html lang="en">';
		$this->htmlMsg .= 	'<head>';
		$this->htmlMsg .= 		'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
		$this->htmlMsg .= 		'<title>'.$this->subject.'</title>';
		$this->htmlMsg .= 		'<style>'.$this->generalStyles.'</style>';
		$this->htmlMsg .= 	'</head>';

		$this->htmlMsg .= 	'<body style="margin:0;padding:0;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust:none; background-image:url(\''.$this->bg.'\'); background-repeat:repeat;">';
		$this->htmlMsg .= 		'<table background="'.$this->bg.'" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:'.$this->fontFamily.';font-size:'.$this->fontSize.';color:#666;">';
		$this->htmlMsg .= 		'<tr>';
		$this->htmlMsg .= 			'<td class="bg_spacer" width="50"></td>';
		$this->htmlMsg .= 			'<td class="middle_cell" align="center" style="padding:14px 0 0;">';
		$this->htmlMsg .= 				'<table class="contentwrapper" width="800" border="0" cellpadding="0" cellspacing="0" style="background-color:#FFF;">';

		// Header
		if(!empty($this->header)){
			$this->htmlMsg .= '<tr>';
			$this->htmlMsg .= 	'<td align="left" style="';
			foreach($this->headerStyles as $key => $val){
				$this->htmlMsg .= $key.':'.$val.';';
			}
			$this->htmlMsg .=	'">';
			$this->htmlMsg .= 		$this->header;
			$this->htmlMsg .= 	'</td>';
			$this->htmlMsg .= '</tr>';

			$this->plainTextMsg .= $this->headerPlainTxt.PHP_EOL.PHP_EOL;
		}

		// Body
		if(!empty($this->body)){
			$this->htmlMsg .= '<tr>';
			$this->htmlMsg .=	'<td align="left" style="padding:14px 14px 60px;">';
			$this->htmlMsg .= 		'<table>';
			$this->htmlMsg .= 			$this->body;
			$this->htmlMsg .= 		'</table>';
			$this->htmlMsg .= 	'</td>';
			$this->htmlMsg .= '</tr>';

			$this->plainTextMsg .= $this->bodyPlainTxt.PHP_EOL.PHP_EOL;
		}
		
		$this->htmlMsg .= 				'</table>';
		$this->htmlMsg .= 			'</td>';
		$this->htmlMsg .= 			'<td class="bg_spacer" width="50"></td>';
		$this->htmlMsg .= 		'</tr>';

		// Footer
		if(!empty($this->footer)){
			$this->htmlMsg .= '<tr>';
			$this->htmlMsg .=	'<td colspan="3" align="center" style="';
			foreach($this->footerStyles as $key => $val){
				$this->htmlMsg .= $key.':'.$val.';';
			}
			$this->htmlMsg .= 	'">';
			$this->htmlMsg .=		$this->footer;
			$this->htmlMsg .=	'</td>';
			$this->htmlMsg .= '</tr>';

			$this->plainTextMsg .= $this->footerPlainTxt.PHP_EOL.PHP_EOL;
		}

		$this->htmlMsg .= 	'</table>';
		$this->htmlMsg .= '</body>';
		$this->htmlMsg .= '</html>';
	}

	private function sendPlainAndHTMLMail(){
		
		// ===================================================
		//	GENERAL HEADERS
		// ===================================================
		$mime_boundary = md5(time());
		
		$headers = $this->addtionalHeaders;
		$headers .=	"From: ".$this->from['title'].' <'.$this->from['email'].'>'.PHP_EOL;
		$headers .= "MIME-Version: 1.0".PHP_EOL;
		$headers .= "Content-Type: multipart/alternative;boundary=".$mime_boundary.PHP_EOL;
		
		$msg = "This is a multipart MIME message.".PHP_EOL.PHP_EOL;
		
		// ===================================================
		//	PLAIN TEXT
		// ===================================================
		$msg = "--".$mime_boundary.PHP_EOL;
		$msg .= "Content-type: text/plain; charset=iso-8859-1".PHP_EOL.PHP_EOL;
		$msg .= $this->plainTextMsg.PHP_EOL.PHP_EOL;
		
		// ===================================================
		//	HTML
		// ===================================================
		$msg .= "--".$mime_boundary.PHP_EOL;
		$msg .= "Content-type: text/html; charset=iso-8859-1".PHP_EOL.PHP_EOL;
		$msg .= $this->htmlMsg.PHP_EOL.PHP_EOL;
		
		$msg .= "--".$mime_boundary."--".PHP_EOL.PHP_EOL;
		
		// ===================================
		//	Send email(s)
		// ===================================
		foreach($this->to as $item){
			$err = @mail($item, $this->subject, $msg, $headers);

			$this->showDebug($err);

			if(!$err){
				$this->sendErrors .= "<div>Error sending email to ".$item.'</div>';
			}
		}
	}

	public function sendEmail(){

		$this->compileEmail();
		$this->sendPlainAndHTMLMail();

		if(!empty($this->sendErrors)){
			throw new Exception($this->sendErrors);
		}
	}
}

?>