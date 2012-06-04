<?php


// 'img' => array(
// 'label'   => 'Изображение',
// 'field'   => array('callback' => 'ext.image_field.field', 'args'=>(array)'img'),
// 'rules'   => "callback[ext.image_field.upload,img]|callback[model.{$this->table}.upload,img]",
// ),



class EXT_Image_Field
{
	//--------------------------------------------------------------------------

	public $base_url = '/admin/image_field/';

	public $current_target = 'google';

	public $targets = array(
		'google' => array(
			'name'      => 'Google Images',
			'query'     => 'https://www.google.com/search?q=%s&hl=en&gbv=1&tbm=isch&ei=PUXJT9SJH-OI4gTiwtwy&start=%d&sa=N',
			'cut_str'   => '<table class="images_table"',
			'src_ereg'  => 'imgurl=(.*?)&amp;',
			'full_ereg' => 'imgrefurl=(.*?)&amp;',
			'size_ereg' => 'h=(\d+)&amp;w=(\d+)&amp;',
			'columns'   => 4,
			'page_iteration' => 20,
		),
		'yandex' => array(
			'name'      => 'Яндекс.Картинки',
			'query'     => 'http://images.yandex.ru/yandsearch?text=%s&p=%d&rpt=image',
			'cut_str'   => '<div id="result"',
			'src_ereg'  => 'img_url=(.*?)&amp;',
			'full_ereg' => FALSE,
			'columns'   => 4,
			'page_iteration' => 1,
		),
	);

	//--------------------------------------------------------------------------

	public function __construct()
	{
		sys::set_config_items(&$this, 'ext_image_field');
	}

	//--------------------------------------------------------------------------

	public function parse_content($target_key, &$emulator, &$field)
	{
		$emulator->replace_js(TRUE);
		$html      = $emulator->parsed_content();
		$target    = $this->targets[$target_key];
		$src_ereg  = $target['src_ereg'];
		$size_ereg = isset($target['size_ereg']) ? $target['size_ereg'] : FALSE;
		$full_ereg = isset($target['full_ereg']) ? $target['full_ereg'] : FALSE;
		$columns   = isset($target['columns']) ? $target['columns'] : 5;

		if (($pos = mb_strpos($html, $target['cut_str'])) === FALSE)
		{
			return $html;
		}

		$html = mb_substr($html, $pos);
		// if (($pos = mb_strpos($html, '>'))) $html = mb_substr($html, $pos+1);  // replace body

		// $html = preg_replace('@(onmousedown|onclick|style|mousedown)=(["\']).*?\2@siu', '', $html);
		// $html = preg_replace('@(onmousedown|onclick|style|mousedown)=[^ ]*[ ]?@siu', '', $html);
		// $html = preg_replace('@(\d+[\s]?(x|×|&times;)[\s]?\d+[^<]+)@siu', '<span class="eif_size">$1</span>', $html);

		$result = array();
		preg_match_all('@<a[^>]*?.*?</a>@siu', $html, $matches);
		
		$index = 0;
		$col   = 0;
		foreach ($matches[0] as $anchor)
		{
			preg_match_all('@(href|src)=(["\'])([^\2]+?)\2@siu', $anchor, $m);
			if ( empty($m[1]) || count($m[1])!=2) continue;

			$row[$m[1][0]] = $m[3][0];
			$row[$m[1][1]] = $m[3][1];

			if ( ! preg_match("@{$src_ereg}@siu", $row['href'])) continue;

			$url  = urldecode(preg_replace("@.*{$src_ereg}.*@siu", '$1', $row['href']));
			$size = $size_ereg ? preg_replace("@.*{$size_ereg}.*@siu", '$1&times;$2', $row['href']) : FALSE;
			$full = $full_ereg ? urldecode(preg_replace("@.*{$full_ereg}.*@siu", '$1', $row['href'])) : FALSE;
			$ext  = preg_replace('@.*?([^.]+)$@sui', '$1', $url);
			if (strlen($ext)>5) $ext = FALSE;

			// $size  = urldecode(preg_replace("@.*{$src_key}=(.*?)&amp;.*@siu", '$1', $row['href']));
			// $h   = urldecode(preg_replace("@.*{$src_key}=(.*?)&amp;.*@siu", '$1', $row['href']));

			$result[$col][] = (object)array(
				'str'   => $row['href'],
				'thumb' => $row['src'],
				'url'   => substr($url, 0, 4) !== 'http' ? 'http://' . $url : $url,
				'size'  => $size,
				'full'  => $full,
				'ext'   => $ext
			);
			$index++;
			if ($index%$columns == 0) $col++;
		}

		// die("<pre>" . htmlspecialchars(print_r($result, TRUE)));

		return sys::$lib->load->template(EXT_PATH . 'image_field/templates/result', array('data' => &$result, 'type'=>$target_key, 'columns'=>$columns, 'field'=>$field));
	}

	//--------------------------------------------------------------------------

	public function field($fieldname)
	{
		$title = isset(sys::$lib->form) ? sys::$lib->form->value('title') : FALSE;
		
		// if ($title)
		// {
		// 	$request = $this->base_url;
		// }

		// Template data
		$data['fieldname'] = $fieldname;
		// $data['request']   =& $request;
		$data['title']     =& $title;

		return sys::$lib->load->template(EXT_PATH . 'image_field/templates/field', $data);
	}

	//--------------------------------------------------------------------------

	public function upload($val, $callback, $field)
	{
		//Array ( [img] => Array ( [name] => preview.jpg [type] => image/jpeg [tmp_name] => /Applications/MAMP/tmp/php/php0E7q0C [error] => 0 [size] => 76432 ) )
		$eif_field = 'eif_' . $field;
		
		if (empty($_POST[$eif_field]))
		{
			return TRUE;
		}

		require_once EXT_PATH . 'image_field/browser_emulator.lib' . EXT;
		$emulator = new LIB_Browser_Emulator();
		$emulator->get($_POST[$eif_field]);
		
		$img = 'temp/' . uniqid();
		file_put_contents(ROOT_PATH . $img, $emulator->content());
		
		$name = pathinfo($_POST[$eif_field], PATHINFO_BASENAME);
		$type = $emulator->headers('content-type');
		$type = $emulator->headers('content-type');

		$_FILES[$field] = array(
			'name'     => $name ? $name : 'image',
			'type'     => $type ? $type : 'image/jpeg',
			'tmp_name' => ROOT_PATH . $img,
			'error'    => 0,
			'size'     => filesize(ROOT_PATH . $img)
		);

		// sys::$lib->form->set_value($field, ROOT_PATH . $img, TRUE);

		return ROOT_PATH . $img;
	}

	//--------------------------------------------------------------------------

}