<?php 



class EXT_COM_Image_Field extends SYS_Component
{
	//--------------------------------------------------------------------------

	function init()
	{
		$this->template->enable = FALSE;
		$this->load->extension('image_field');
	}

	//--------------------------------------------------------------------------

	function router($target_key)
	{
		require_once EXT_PATH . 'image_field/browser_emulator.lib' . EXT;

		$emulator = new LIB_Browser_Emulator();
		$emulator->base_url($this->image_field->base_url . $target_key . '/');
		

		// $emulator->get('https://www.google.com/search?q=test&hl=en&gbv=1&tbm=isch&ei=OJ7IT5OSBLPT4QSN_fjsDw&start=20&sa=N');
		$emulator->process();

		if ( ! empty($_GET['parse']))
		{
			echo $this->image_field->parse_content($target_key, &$emulator, &$_GET['field']);
		}
		else
		{
			$emulator->replace_js(TRUE);
			$emulator->replace_link(TRUE);
			$emulator->replace_form(TRUE);
			echo $emulator->parsed_content();
		}
		
	}
	
	//--------------------------------------------------------------------------
	
	function debug()
	{
		error_reporting(E_ALL);
		$this->view = FALSE;
		
		
		// инициализация cURL
		$ch = curl_init("http://ya.ru/");

		// получать заголовки
		curl_setopt($ch, CURLOPT_HEADER, 1);

		// Устанавливаем USER_AGENT
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

		// елси проверятся откуда пришел пользователь, то указываем допустимый заголовок HTTP Referer:
//		if ($this->referer())
//		{
//			curl_setopt ($ch, CURLOPT_REFERER, $this->referer());
//		}

//		if ($this->request_data() && $this->method() == 'post')
//		{
			// использовать метод POST
//			curl_setopt ($ch, CURLOPT_POST, 1);
//			
			// передаем поля формы
//			curl_setopt ($ch, CURLOPT_POSTFIELDS, $this->request_data_str());
//		}
//
		// сохранять информацию Cookie в файл, чтобы потом можно было ее использовать
//		curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->cookie_file());
//
//		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->cookie_file());
//		
		// возвращать результат работы
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);

		// не проверять SSL сертификат
//		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

		// не проверять Host SSL сертификата
//		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);

		// это необходимо, чтобы cURL не высылал заголовок на ожидание
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Expect:'));

		// выполнить запрос
		curl_exec ($ch);

		// получить результат работы
		$result = curl_multi_getcontent($ch);
	
		// закрыть сессию работы с cURL
		curl_close($ch);
		
		print_r($result);
	}
	
	//--------------------------------------------------------------------------
}