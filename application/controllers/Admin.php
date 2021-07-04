<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		// authCheck();
	}

	public function input_toko()
	{
		$this->load->view("input_toko");
	}

	public function ex_input_toko()
	{
		$config = [
			[
				'field' => 'kode',
				'label' => 'Kode toko',
				'rules' => 'required|trim|is_unique[toko.kode]',
				'errors' => [
					'required' => '{field} tidak boleh kosong!',
					'is_unique' => '{field} ini telah dipakai!',
				]
			],
			[
				'field' => 'nama',
				'label' => 'Nama toko',
				'rules' => 'required|trim',
				'errors' => [
					'required' => '{field} tidak boleh kosong!',
				]
			],
			[
				'field' => 'alamat',
				'label' => 'Alamat toko',
				'rules' => 'required|trim',
				'errors' => [
					'required' => '{field} tidak boleh kosong!',
				]
			],
		];

		// jd($this->input->post());

		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == false) {
			$result['status'] = "hold";
			$result['data_error'] = [
				'kode' => form_error('kode'),
				'nama' => form_error('nama'),
				'alamat' => form_error('alamat'),
			];
			// jd("cek");
		} else {
			extract($this->input->post());

			$data_input = [
				"kode" => $kode,
				"nama" => $nama,
				"alamat" => $alamat,
			];
			$extra = [
				"date_input" => date("Y-m-d H:i:s"),
				"date_last_scan" => "",
				"scan_count" => 0
			];

			if ($_FILES["foto_toko"]["error"] == 0) {
				$extra["foto"] = upload_single_file([
					'name' => 'foto_toko',
					'filename' => 'toko_' . seo_title($nama),
					'path' => 'assets/img/toko/',
					'old_image' => '',
					'allowed_types' => 'jpg|jpeg|png|JPG|PNG|JPEG',
					'max_size' => '0',
					'resize' => 500, //false or int value
				]);
			} else {
				$extra["foto"] = "default.png";
			}

			$data_input["ekstra"] = json_encode($extra);

			$this->db->insert("toko", $data_input);
			if ($this->db->affected_rows() > 0) {
				$result["alert"] = j_alert("Input Toko Berhasil!", "", "info");
				// barcode([
				// 	"filepath" => "",
				// 	"text" => $kode,
				// 	"size" => "40",
				// 	"orientation" => "horizontal",
				// 	"code_type" => "code128",
				// 	"print" => false,
				// 	"SizeFactor" => 1,
				// 	"downloadFile" => false,
				// 	"fileName" => "Barcode toko - " . $nama . " - " . $kode
				// ]);
				$result["status"] = "success";
			} else {
				$result["status"] = "error";
				$result["alert"] = j_alert("Input Toko Gagal!", "", "error");
			}
		}
		$result = array_merge($result, set_csrf());
		echo json_encode($result);
	}

	public function save_routes()
	{
		save_routes();
		echo "berhasil update routes";
	}

	public function tester()
	{
		$username = "fasilitator3";
		$password = "qwertyuiop";
		echo sha1($username . $password . "pil20wa20li");
	}
}