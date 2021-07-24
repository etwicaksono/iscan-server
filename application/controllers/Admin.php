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
				$result["status"] = "success";
			} else {
				$result["status"] = "error";
				$result["alert"] = j_alert("Input Toko Gagal!", "", "error");
			}
		}
		$result = array_merge($result, set_csrf());
		echo json_encode($result);
	}

	public function input_produk()
	{
		$data["toko"] = $this->db->order_by("nama")->get("toko")->result_array();
		$this->load->view("input_produk", $data);
	}

	public function ex_input_produk()
	{
		$config = [
			[
				'field' => 'id_toko',
				'label' => 'Kode toko',
				'rules' => 'required|trim',
				'errors' => [
					'required' => '{field} tidak boleh kosong!',
				]
			],
			[
				'field' => 'kode',
				'label' => 'Kode produk',
				'rules' => 'required|trim|callback_cek_kode_produk',
				'errors' => [
					'required' => '{field} tidak boleh kosong!',
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
				'field' => 'harga',
				'label' => 'Harga toko',
				'rules' => 'required|trim',
				'errors' => [
					'required' => '{field} tidak boleh kosong!',
				]
			],
			[
				'field' => 'deskripsi',
				'label' => 'Deskripsi produk',
				'rules' => 'required|trim',
				'errors' => [
					'required' => '{field} tidak boleh kosong!',
				]
			],
		];

		// jd($this->input->post());
		// jd($_FILES["foto_produk"]);
		// jd($_FILES);

		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() == false) {
			$result['status'] = "hold";
			$result['data_error'] = [
				'id_toko' => form_error('id_toko'),
				'kode' => form_error('kode'),
				'nama' => form_error('nama'),
				'deskripsi' => form_error('deskripsi'),
				'harga' => form_error('harga'),
			];
		} else {
			extract($this->input->post());

			$data_input = [
				"id_toko" => $id_toko,
				"kode" => $kode,
				"nama" => $nama,
				"harga" => $harga,
			];
			$extra = [
				"deskripsi" => $deskripsi,
				"date_input" => date("Y-m-d H:i:s"),
				"date_last_scan" => "",
				"scan_count" => 0
			];

			if ($_FILES["foto_produk"]["error"][0] == 0) {
				// $extra["foto"] = upload_single_file([
				// 	'name' => 'foto_produk',
				// 	'filename' => $id_toko . '_produk_' . seo_title($nama),
				// 	'path' => 'assets/img/produk/',
				// 	'old_image' => '',
				// 	'allowed_types' => 'jpg|jpeg|png|JPG|PNG|JPEG',
				// 	'max_size' => '0',
				// 	'resize' => 500, //false or int value
				// ]);
				$extra["foto"] = multiUploadFoto([
					"file" => $_FILES["foto_produk"],
					"param" => "foto_produk",
					"base_name" => $id_toko . '_produk_' . seo_title($nama),
					"relative_path" => "assets/img/produk/",
					"resize" => 500
				]);
			} else {
				$extra["foto"] = "default.png";
			}

			$data_input["ekstra"] = json_encode($extra);

			$this->db->insert("produk", $data_input);
			if ($this->db->affected_rows() > 0) {
				$result["alert"] = j_alert("Input Toko Berhasil!", "", "info");
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

	// ====================================================================================================
	// validasi
	public function cek_kode_produk()
	{
		$input = $this->input->post();
		$cek = $this->db->where(["id_toko" => $input["id_toko"], "kode" => $input["kode"]])->get("produk")->num_rows();
		if ($cek == 0) {
			return true;
		} else {
			$this->form_validation->set_message('cek_kode_produk', '{field} telah digunakan!');
			return false;
		}
	}
	// ====================================================================================================

	public function tester()
	{
		$data_toko = $this->db->where("id !=", 8)->get("toko")->result_array();
		$data_produk = $this->db->where("id !=", 3)->get("produk")->result_array();
		foreach ($data_toko as $toko) {
			foreach ($data_produk as $produk) {
				$data_input[] = [
					"id_toko" => $toko["id"],
					"kode" => $produk["kode"],
					"nama" => $produk["nama"],
					"harga" => $produk["harga"],
					"ekstra" => $produk["ekstra"],
				];
			}
		}
		// $this->db->insert_batch("produk", $data_input);
	}
}