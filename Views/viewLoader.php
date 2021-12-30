<?php

class viewLoader
{

	private static $error = false;

	static function loadView($path , $viewData = null)
	{
		$view_str = self::renderView($path);

		if(!is_null($viewData))
		{
			$view_str = self::loadElements($view_str , $viewData);
		}

		echo $view_str;
	}

	private static function loadElements($view_str , $viewData) {
		while(strpos($view_str, '[[') > 0)
		{
			$template = substr($view_str, strpos($view_str, '[[') + 2, (strpos($view_str, ']]',strpos($view_str, '[[')) - strpos($view_str, '[[') - 2));

//			if(strpos($template, 'TABLE') !== false)
//			{
//				$view_str = str_replace('[['.$template.']]', self::loadTable($viewData[$template]), $view_str);
//			}else if(strpos($template, 'SELECT') !== false)
//			{
//				$view_str = str_replace('[['.$template.']]', self::loadSelect($viewData[$template]), $view_str);
//			}else if(strpos($template, 'FORM') !== false)
//			{
//				$view_str = str_replace('[['.$template.']]', self::loadForm($viewData[$template]), $view_str);
//			}else if(strpos($template, 'BUTTON') !== false)
//			{
//				$view_str = str_replace('[['.$template.']]', self::loadButton($viewData[$template]), $view_str);
//			}else if(strpos($template, 'TEMPLATE') !== false)
//			{
//				$view_str = str_replace('[['.$template.']]', self::loadTemplate($viewData[$template]), $view_str);
//			}else
//			{
//				$view_str = str_replace('[['.$template.']]', $viewData[$template] , $view_str);
//			}

			switch(substr($template, strrpos($template, '_',) + 1))
			{
				case 'TABLE':
					$view_str = str_replace('[['.$template.']]', self::loadTable($viewData[$template]), $view_str);
					break;
				case 'SELECT':
					$view_str = str_replace('[['.$template.']]', self::loadSelect($viewData[$template]), $view_str);
					break;
				case 'FORM':
					$view_str = str_replace('[['.$template.']]', self::loadForm($viewData[$template]), $view_str);
					break;
				case 'BUTTON':
					$view_str = str_replace('[['.$template.']]', self::loadButton($viewData[$template]), $view_str);
					break;
				case 'TEMPLATE':
					$view_str = str_replace('[['.$template.']]', self::loadTemplate($viewData[$template]), $view_str);
					break;
				case 'ITERATION':
					$view_str = str_replace('[['.$template.']]', self::loadIteration($viewData[$template]), $view_str);
					break;
				case 'SEGMENT':
					if($viewData[$template][1] == '' || is_null($viewData[$template][1]))
					{
						self::$error = true;
					}
					$view_str = str_replace('[['.$template.']]', self::loadSegment($viewData[$template]), $view_str);
					break;
				default:
					$view_str = str_replace('[['.$template.']]', $viewData[$template] , $view_str);
					break;
			}
		}

		return $view_str;
	}

	private static function renderView($path) {
		ob_start();
		include($path);
		$var=ob_get_contents();
		ob_end_clean();
		return $var;
	}

	private static function loadTemplate($data) {
		$template_name = $data['template_name'];

		if(file_exists('resources/templates/'.$template_name))
		{
			$template = file_get_contents('resources/templates/'.$template_name);
//			$data['params'] = explode(',',substr($template, strpos($template, 'params:[{[') + 10, (strpos($template, ']}]',strpos($template, 'params:[{[')) - strpos($template, 'params:[{[') - 10)));
			$main = substr($template, strpos($template, 'main:[{[') + 8, (strpos($template, ']}]',strpos($template, 'main:[{[')) - strpos($template, 'main:[{[') - 8));
			$iteration_array = explode(';',substr($template, strpos($template, 'iteration:[{[') + 13, (strpos($template, ']}]',strpos($template, 'iteration:[{[')) - strpos($template, 'iteration:[{[') - 13)));
			$segment_array = explode(';',substr($template, strpos($template, 'segment:[{[') + 11, (strpos($template, ']}]',strpos($template, 'segment:[{[')) - strpos($template, 'segment:[{[') - 11)));
			$javascript = substr($template, strpos($template, 'javascript:[{[') + 14, (strpos($template, ']}]',strpos($template, 'javascript:[{[')) - strpos($template, 'javascript:[{[') - 14));
			$style = substr($template, strpos($template, 'style:[{[') + 9, (strpos($template, ']}]',strpos($template, 'style:[{[')) - strpos($template, 'style:[{[') - 9));

			$element_array = array_merge($iteration_array, $segment_array);

			foreach ($element_array as $element)
			{
				$temp = explode('::', $element);

				if(strlen($temp[0]) > 3)
				{
					$data[trim($temp[0])] = array(substr($temp[1], strpos($temp[1], '{') + 1, strpos($temp[1], '}') - 1) , $data[trim($temp[0])]);
				}
			}

			if(strlen($style) > 0)
			{
				echo '<style>'.$style.'</style>';
			}

			$main = self::loadElements($main, $data);

			if(strlen($javascript) > 0)
			{
				$main .= '<script>'.$javascript.'</script>';
			}

			return $main;
		}
	}

	private static function loadIteration($data) {
		$iteration_result = '';
		if(is_array($data[1]))
		{
			foreach ($data[1] as $element) {
				$element_str = $data[0];
				$result = self::loadElements($element_str,$element);
				$iteration_result .= $result;
			}
		}
		return $iteration_result;
	}

	private static function loadSegment($data) {
		if(!self::$error)
		{
			$result = self::loadElements($data[0],$data[1]);
		}
		else
		{
			self::$error = false;
			$result = '';
		}

		return $result;
	}

	private static function loadTable($data) {
		if(is_array($data))
		{
			$table = '<div class="table-responsive">';
			$table .= '<table class="table '.(isset($data['classes']) ? $data['classes']:'').'" '.(isset($data['id']) ? ' class="'.$data['id'].'"':'').' >';

			$thead = '<thead><tr>';
			if(isset($data['columns']))
			{
				foreach ($data['columns'] as $col)
				{
					$thead .= '<th '.(isset($col['classes']) ? ' class="'.$col['classes'].'"':'').' '.(isset($col['id']) ? ' class="'.$col['id'].'"':'').' >'.$col.'</th>';
				}
			}
			$thead .= '</tr></thead>';

			$tbody = '<tbody>';
			foreach ($data['rows'] as $row)
			{
				$tbody .= '<tr '.(isset($row['classes']) ? ' class="'.$row['classes'].'"':'').' '.(isset($row['id']) ? ' class="'.$row['id'].'"':'').' >';

				foreach($row as $td)
				{
					$tbody .= '<td '.(isset($td['classes']) ? ' class="'.$td['classes'].'"':'').' '.(isset($td['id']) ? ' class="'.$td['id'].'"':'').'>'.$td.'</td>';
				}

				$tbody .= '</tr>';
			}
			$tbody .= '</tbody>';

			$table .= $thead . $tbody;
			$table .= '</table>';
			$table .= '</div>';

		}
		else
		{
			$table = $data;
		}

		return $table;
    }

	private static function loadSelect($data) {
		if(is_array($data))
		{
			$dropdown = '<select name="'.$data['name'].'" class="form-control '.(isset($data['classes']) ? $data['classes']:'').'" '.(isset($data['id'])?'id="'.$data['id'].'"':'').' >';
			foreach ($data['options'] as $opt)
			{
				$dropdown .= '<option value="'.$opt['value'].'" '.($data['chosen'] == $opt['value']?'selected':'').' >'.$opt['name'].'</option>';
			}
			$dropdown .= '</select>';
		}
		else
		{
			$dropdown = '';
		}

		return $dropdown;
	}

	private static function loadButton($data)
	{
		if(is_array($data))
		{
			if(empty($data['link']))
			{
				$button = '<button type="'.$data['btn_type'].'" name ="'.$data['name'].'" class = "btn '.(isset($data['classes'])? $data['classes']:'').'" '.(isset($data['id'])? ' id = "'.$data['id'].'"':'').' '.(isset($data['btn_onClick'])?' onclick="'.$data['btn_onClick'].'"':'').' >'.(!isset($data['alt_name'])?$data['name']:$data['alt_name']).'</button>';
			}
			else
			{
				$button = '<a href="'.$data['link'].'" ><button type="'.$data['btn_type'].'" name ="'.$data['name'].'" class = "btn '.(isset($data['classes'])? $data['classes']:'').'" '.(isset($data['id'])? ' id = "'.$data['id'].'"':'').' '.(isset($data['btn_onClick'])?' onclick="'.$data['btn_onClick'].'"':'').' >'.(!isset($data['alt_name'])?$data['name']:$data['alt_name']).'</button></a>';
			}
		}
		else
		{
			$button = '';
		}

		return $button;
	}

	private static function loadForm($data)
	{
		$form = '<form method="'.$data['method'].'" action="'.$data['action'].'" '.(isset($data['classes']) ? ' class="'.$data['classes'].'"':'').' '.(isset($data['enctype'])?' enctype="'.$data['enctype'].'"':'').' >';
		$form .= '<div class="form-group">';
		foreach ($data['inputs'] as $input)
		{
			if($input['type'] != 'submit' && $input['type'] != 'button' && $input['type'] != 'hidden')
			{
				$form .= '<label for = "'.$input['name'].'" >'.ucfirst($input['name']).'</label>';
			}

			if($input['type'] == 'select')
			{
				$form .= self::loadSelect($input) .(!empty($input['err'])? '&nbsp<span class="err_msg">'.$input['err'].'</span>':''). '<br>'.PHP_EOL;
			}
			else if($input['type'] == 'textarea')
			{
				$form .= '<textarea name="'.$input['name'].'" class="form-control '.(isset($input['classes'])?$input['classes']:'').'" '.(isset($input['id'])?' id="'.$input['id'].'"':'').' >'.(isset($input['value'])?$input['value']:'').'</textarea>'.(!empty($input['err'])? '<span class="err_msg">'.$input['err'].'</span>':'').'<br>'.PHP_EOL;
			}
			else if($input['type'] == 'button')
			{
				$form .= self::loadButton($input) .(!empty($input['err'])? '&nbsp<span class="err_msg">'.$input['err'].'</span>':'').'<br><br>'.PHP_EOL;
			}
			else
			{
				$form .= ' <input type ="'.$input['type'].'" name ="'.$input['name'].'" id = "'.$input['name'].'" '.(isset($input['value'])? 'value = "'.$input['value'].'"' : '').' class = "form-control '.(isset($input['classes'])? $input['classes']:'').'" '.(isset($input['id'])? ' id = "'.$input['id'].'"':'').' >'.(!empty($input['err'])? '<span class="err_msg">'.$input['err'].'</span>':'').'<br>'.PHP_EOL;
			}
		}
		$form .= '</div>';
		$form .= '</form>';

		return $form;
	}

}

?>
