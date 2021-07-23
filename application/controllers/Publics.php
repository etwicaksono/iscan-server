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
		// $arr = [1, 2, 3, 4];

		// array_push($arr, 4, 5, 6);
		// dd(array_unique($arr));

		echo http_build_query(["list_toko" => json_encode([7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6])]);
	}
}