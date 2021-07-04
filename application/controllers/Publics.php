<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Publics extends CI_Controller
{
	public function index()
	{
		$this->load->view("tester");
	}

	public function blocked()
	{
		$this->load->view("publics/forbidden");
	}

	public function tester()
	{
		// echo password_hash("9des2020", PASSWORD_DEFAULT);

		// For demonstration purposes, get pararameters that are passed in through $_GET or set to the default value
		$filepath = (isset($_GET["filepath"]) ? $_GET["filepath"] : "");
		$text = (isset($_GET["text"]) ? $_GET["text"] : "0");
		$size = (isset($_GET["size"]) ? $_GET["size"] : "20");
		$orientation = (isset($_GET["orientation"]) ? $_GET["orientation"] : "horizontal");
		$code_type = (isset($_GET["codetype"]) ? $_GET["codetype"] : "code128");
		$print = (isset($_GET["print"]) && $_GET["print"] == 'true' ? true : false);
		$sizefactor = (isset($_GET["sizefactor"]) ? $_GET["sizefactor"] : "1");

		// This function call can be copied into your project and can be made from anywhere in your code
		$config = [
			"filepath" => $filepath,
			"text" => $text,
			"size" => $size,
			"orientation" => $orientation,
			"code_type" => $code_type,
			"print" => $print,
			"SizeFactor" => $sizefactor,
			"downloadFile" => true,
			"fileName" => "tester"
		];

		barcode($config);
	}
}