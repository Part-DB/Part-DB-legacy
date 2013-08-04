<?php
/*
	---------------------------------------------------------------------------------------

	File-Uploader (c) Udo Neist

	Part: class.xhr.php

	Info: Stellt die verschiedenen Funktionen für ein Upload-Manager zur Verfügung.

	---------------------------------------------------------------------------------------

    17.10.2012: Erste Version (Udo Neist)
    18.10.2012: Änderung bei der Ermittlung der Schreibrechte der Verzeichnisse (Udo Neist)
                Zielverzeichnis wird jetzt per Mime-Typ direkt ausgewählt (Udo Neist)
    27.10.2012: Umbenennung in xhr (Udo Neist)
*/

class xhr
{

	private $UploadDir;
	private $MimeUploadDir;
	private $flag;

	function __construct()
	{
		/*
			Konstruktor
		*/

		settype($this -> UploadDir,'string');
		settype($this -> MimeUploadDir,'array');
		settype($this -> flag,'bool');

		$this -> UploadDir = './tmp';
		$this -> MimeUploadDir = array();
		$this -> flag = false;

	}

	function setUploadDir($dir = '')
	{
		/*
			Setzt das Upload-Verzeichnis
		*/

		clearstatcache();
		$perms = substr(sprintf('%o',fileperms($dir)),-3);
		if (strlen($dir)>0 && ($perms=='775' || $perms=='777')) $this -> UploadDir = $dir;

		return $this -> UploadDir == $dir;

	}

	function getUploadDir()
	{
		/*
			Gibt das Upload-Verzeichnis zurück.
		*/

		return $this -> UploadDir;
	}

	function setMimeUploadDir($array = array())
	{
		/*
			Für jeden Mime-Typ kann man ein eigenes Zielverzeichnis wählen.

			Array: Mime-Typ => Verzeichnis
		*/

		$this -> flag = false;

		foreach ($array as $mime => $dir)
		{
			clearstatcache();
			$mdir = $this -> UploadDir.'/'.$dir;
			$perms = substr(sprintf('%o',fileperms($mdir)),-3);
			if (strlen($mdir)>0 && ($perms=='775' || $perms=='777'))
			{
				$this -> MimeUploadDir[$mime] = $mdir;
				$this -> flag = true;
			}
		}

		return $this -> flag;

	}

	function getMimeUploadDir()
	{
		/*
			Gibt das Array mit den per setMimeUploadDir() erzeugten Verzeichnissen zurück.
		*/

		return $this -> MimeUploadDir;

	}

	function moveUploadFile()
	{
		/*
			Upload
		*/

		if ($_FILES['File'])
		{
			if ($_FILES['File']['error']==0 && $_FILES['File']['size']>0)
			{
				$uploaddir = (($this -> MimeUploadDir[$_FILES['File']['type']])?$this -> MimeUploadDir[$_FILES['File']['type']]:$this -> UploadDir);

				if (move_uploaded_file($_FILES['File']['tmp_name'], $uploaddir.'/'.$_FILES['File']['name']))
				{
					$_FILES['File']['target'] = $uploaddir.'/'.$_FILES['File']['name'];
					return $_FILES['File'];
				}
			}
		}

	}

}

?>