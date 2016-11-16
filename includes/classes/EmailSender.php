<?php

class EmailSender
{
	public static function sendPlainAndHTMLMail($to, $from, $subject, $plainTextMsg, $HTMLMsg, $attachment_name = null, $addtionalHeaders = "")
	{
		// ===================================================
		//	GENERAL HEADERS
		// ===================================================
		$mime_boundary = md5(time());
		
		$headers = $addtionalHeaders;
		$headers .=	"From: ".$from['name'].' <'.$from['email'].'>'.PHP_EOL;
		$headers .= "MIME-Version: 1.0".PHP_EOL;
		$headers .= "Content-Type: multipart/alternative;boundary=".$mime_boundary.PHP_EOL;
		
		$msg = "This is a multipart MIME message.".PHP_EOL.PHP_EOL;
		
		// ===================================================
		//	PLAIN TEXT
		// ===================================================
		$msg = "--".$mime_boundary.PHP_EOL;
		$msg .= "Content-type: text/plain; charset=iso-8859-1".PHP_EOL.PHP_EOL;
		$msg .= $plainTextMsg.PHP_EOL.PHP_EOL;
		
		// ===================================================
		//	HTML
		// ===================================================
		$msg .= "--".$mime_boundary.PHP_EOL;
		$msg .= "Content-type: text/html; charset=iso-8859-1".PHP_EOL.PHP_EOL;
		$msg .= $HTMLMsg.PHP_EOL.PHP_EOL;
		
		// $msg .= "--".$mime_boundary."--".PHP_EOL.PHP_EOL;

		// ===================================================
		//	ATTACHMENTS
		// ===================================================
		if($attachment_name !== null)
		{
			$msg .= "--".$mime_boundary.PHP_EOL;

			$file = fopen($attachment_name['location'],'rb');
			$flsz = filesize($attachment_name['location']);              
			$data = fread($file, $flsz);
			fclose($file);

			// Now we need to encode it and split it into acceptable length lines
			$attachment_chunk = chunk_split(base64_encode($data));

			// echo '<div>'.$attachment_chunk.'</div>';

			$msg .= 'Content-Type: application/octet-stream; name="'.$attachment_name['name'].'"'.PHP_EOL;
			$msg .= 'Content-Transfer-Encoding: base64'.PHP_EOL;
			$msg .= 'Content-Disposition: attachment;'.PHP_EOL;
			$msg .= 'filename="'.$attachment_name['name'].'"'.PHP_EOL.PHP_EOL;
			$msg .= $attachment_chunk.PHP_EOL.PHP_EOL;

			$msg .= "--".$mime_boundary."--".PHP_EOL.PHP_EOL;
		}
		else
			$msg .= "--".$mime_boundary."--".PHP_EOL.PHP_EOL;
		
		// send email(s)
		$returnResult = array();
		foreach($to as $item)
		{
			$returnResult[] = @mail($item, $subject, $msg, $headers);
		}
		
		return $returnResult;
	}
}

?>