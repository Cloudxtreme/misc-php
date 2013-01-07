<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>File uploader</title>
</head>
<body>
<?php

error_reporting(E_ALL);

include_once('conf.inc');

function my_debug($c)
{
	if ($c == TRUE)
	{
		echo "<table border=0><tr><td bgcolor=\"#FFFF77\"><b>DEBUG</b></td></tr><tr><td bgcolor=red><pre>";
	} else {
		echo "</pre></td></tr></table>";
	}
	return;
}

function extensionOf($n)
{
	$n = strtolower($n);
	$n = preg_replace("/.*\./", "", $n);
	return $n;
}

function id_generate($ext)
{
	global $main_path;
	global $uploads;
	do
	{
		$id = rand(100, 100000);
	} while (file_exists($main_path . "/" . $uploads . "/" . $id . "." . $ext));
	return $id;
}

if (isset($_POST['upload']))
{
	$z = array();
	if (isset($_POST['comment']) && ($_POST['comment'] != '')) $z['comment'] = $_POST['comment'];
	if (isset($_FILES['userfile']['error']) && ($_FILES['userfile']['error'] == 0))
	{
		$z['file']['name'] = $_FILES['userfile']['name'];
		$z['file']['size'] = $_FILES['userfile']['size'];
		$ext = extensionOf($z['file']['name']);
	} else {
		switch ($_FILES['userfile']['error'])
		{
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				echo "Błąd! Rozmiar pliku jest za duży."; return FALSE;
				break;
			case UPLOAD_ERR_PARTIAL:
				echo "Błąd! Tylko część pliku została przesłana."; return FALSE;
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				echo "Błąd! Nie można było zapisać pliku."; return FALSE;
				break;
		}
	}

	if (!isset($z['file']))
	{
		echo "Proszę wybrać plik do wysłania.<br>\n";
		return FALSE;
	}

	$z['id'] = id_generate($ext);
	$location['uploads'] = $main_path . "/" . $uploads;
	$z['file']['name'] = $location['uploads'] . "/" . $z['id'] . "." . $ext;

	if (file_exists($location['uploads']))
	{
		if ((!is_dir($location['uploads']) && !is_link($location['uploads'])) || !is_writable($location['uploads']))
		{
			echo "Błąd! Nie mogę zapisać pliku zgłoszenia. Skontaktuj się z administratorem serwisu.";
			return FALSE;
		}
	} else {
		if(!mkdir($location['uploads'], 0775))
		{
			echo "Błąd! Nie można zapisać pliku zgłoszenia. Skontaktuj się z administratorem serwisu.";
			return FALSE;
		}
	}

	if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $z['file']['name']))
	{
		echo "Błąd zapisu zgłoszenia! Skontaktuj się z obsługą serwisu.";
		return FALSE;
	}

	echo "<h3>Plik został wgrany. Dziękujemy!</h3>\n";
	echo "<a href=\"index.php\">Powrót</a>.\n";
	$mail_adm = "\nWitaj!\n\nKtoś wgrał plik o następujących danych:\n"
	      .  " Adres do pliku: " . $live_path . "/" . $z['id'] . "." . $ext .".\n";
	if (isset($z['comment']))
	{
		$mail_adm .= "\nKomentarz:\n--- POCZĄTEK ---\n";
		$mail_adm .= $z['comment'];
		$mail_adm .= "\n---- KONIEC ----\n";
	}
	$mail_adm = wordwrap($mail_adm, 70);
	$headers = 'From: ' . $email_from . "\r\n"
	. 'Reply-To: ' . $email_from . "\r\n"
	. 'X-Mailer: PHP/' . phpversion();
	mail($email_to, "Nowy plik!", $mail_adm, $headers);
} else {
?>
  <b>Welcome to file sharing script.</b><br>
  Maksymalny dopuszczalny rozmiar pliku: 50 megabajtów.
  <form action="index.php" method="post" enctype="multipart/form-data">
  <input type="hidden" name="MAX_FILE_SIZE" value="52428800" />
  Plik do wysłania: <input name="userfile" type="file" /><br />
  <br /><br />Tutaj możesz umieścić komentarz odnośnie wgrywanego pliku:<br />
  <textarea name="comment" cols="50" rows="10"></textarea><br />
  <input type="submit" name="upload" value="Send file!"/>
  </form>
<?php } ?>
</body>
</html>
