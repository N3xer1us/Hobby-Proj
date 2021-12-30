<?php

define('UPLOAD_DIR', 'resources/uploads/');
define('ALLOWED_EXT', array('jpg','jpeg','png'));

class configUpload
{
	static function upload($file) {
		$upload_file = UPLOAD_DIR . basename($file['name']);

		$file_ext = pathinfo($upload_file,PATHINFO_EXTENSION);

		if(in_array($file_ext, ALLOWED_EXT))
		{
			if(move_uploaded_file($file['tmp_name'], $upload_file))
			{
				return basename($file['name']);
			}
			else
			{
				return 'The was an error uploading the image';
			}
		}
		else
		{
			return 'Illegal file extention';
		}
	}

	static function get_file_url($file)
	{
		if(!is_null($file) && $file != '')
		{
			return '/coursework/'.UPLOAD_DIR . $file;
		}
		else
		{
			return '';
		}
	}
}

?>
