<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		header('Content-Type:application/json');
		$this->load->helper("api");
	}
	public function index()
	{
		die("welcome to iscan");
	}

	public function toko()
	{
		$table = "toko";
		$response = [];

		if ($this->input->server("REQUEST_METHOD") == "POST" && !empty($_POST)) {
		} else if ($this->input->server("REQUEST_METHOD") == "GET") {
			$input = $this->input->get();

			switch ($input["type"]) {
				case "all":
					$result = $this->db->order_by("nama")->get($table)->result_array();
					if ($result) {
						$response = result_all_data($table, $result);
					} else {
						$response = data_not_found($table);
					}
					break;
				case "some":
					$toko = json_decode($input["list_toko"]);
					if (!empty($toko)) {
						$query = "
									SELECT
									`id`,
									`kode`,
									`nama`,
									`alamat`,
									CONCAT('" . base_url() . "','assets/img/toko/',JSON_UNQUOTE(JSON_EXTRACT(ekstra,'$.foto'))) foto,
									`ekstra`
									FROM
									" . $table . " WHERE id IN (" . implode(",", $toko) . ")
									ORDER BY FIELD(id," . implode(",", $toko) . ") DESC";
						$result = $this->db->query($query)->result_array();
						if ($result) {
							$response = result_all_data($table, $result);
						} else {
							$response = data_not_found($table);
						}
					} else {
						$response = result_all_data($table, []);
					}
					break;
				case "single":
					$query = "
					SELECT
					`id`,
					`kode`,
					`nama`,
					`alamat`,
					CONCAT('" . base_url() . "','assets/img/toko/',JSON_UNQUOTE(JSON_EXTRACT(ekstra,'$.foto'))) foto,
					JSON_EXTRACT(ekstra, '$.scan_count') scan_count,
					`ekstra`
					FROM
					" . $table . " WHERE kode = " . $input["barcode"];
					$result = $this->db->query($query)->row_array();

					if ($result) {
						$result["kode"] = implode(" ", str_split($result["kode"], 4));
						$this->update_scan_count($table, $result["id"], $result["scan_count"]);
						$response = data_found($table, $result);
					} else {
						$response = data_not_found($table);
					}
					break;
				default:
					break;
			}
		} else {
			$response = unknown_method();
		}

		echo json_encode($response);
	}

	public function produk()
	{
		$table = "produk";
		$response = [];

		if ($this->input->server("REQUEST_METHOD") == "POST" && !empty($_POST)) {
		} else if ($this->input->server("REQUEST_METHOD") == "GET") {
			$input = $this->input->get();

			switch ($input["type"]) {
				case "all":
					$result = $this->db->order_by("nama")->get($table)->result_array();

					if ($result) {
						$response = result_all_data($table, $result);
					} else {
						$response = data_not_found($table);
					}
					break;
				case "some":
					break;
				case "single":
					$result = $this->db->query("
					SELECT
					`id`,
					`id_toko`,
					`kode`,
					`nama`,
					`harga`,
					JSON_UNQUOTE(JSON_EXTRACT(ekstra,'$.deskripsi')) deskripsi,
					JSON_EXTRACT(ekstra,'$.foto') foto,
					JSON_EXTRACT(ekstra,'$.scan_count') scan_count,
					`ekstra`
					FROM
					" . $table . " WHERE kode = " . $input["barcode"] . " AND id_toko = " . $input["id_toko"])
						->row_array();

					if ($result) {
						$foto = json_decode($result["foto"]);
						$temp = [];
						foreach ($foto as $f) {
							$temp[] = base_url("assets/img/produk/" . $f);
						}

						$result["foto"] = $temp;
						$result["kode"] = implode(" ", str_split($result["kode"], 4));
						$this->update_scan_count($table, $result["id"], $result["scan_count"]);

						$response = data_found($table, $result);
					} else {
						$response = data_not_found($table);
					}
					break;
				default:
					break;
			}
		} else {
			$response = unknown_method();
		}

		echo json_encode($response);
	}

	public function _template()
	{
		$table = "_template";
		$response = [];

		if ($this->input->server("REQUEST_METHOD") == "POST" && !empty($_POST)) {
		} else if ($this->input->server("REQUEST_METHOD") == "GET") {
		} else {
			$response = unknown_method();
		}

		echo json_encode($response);
	}

	private function update_scan_count($table, $id, $scan_count)
	{
		$query = "UPDATE " . $table . " SET ekstra = JSON_MERGE_PATCH(ekstra, '{\"scan_count\":" . ($scan_count + 1) . ",\"date_last_scan\":\"" . date("Y-m-d H:i:s") . "\"}') WHERE id = " . $id;
		$this->db->query($query);
	}

	public function tester()
	{
		$data_input = [
			"id_toko" => "id_toko",
			"kode" => "kode",
			"nama" => "nama",
			"harga" => "harga",
			"ekstra" => [
				"deskripsi" => "deskripsi",
				"date_input" => date("Y-m-d H:i:s"),
				"date_last_scan" => "",
				"scan_count" => 0,
				"photo" => ["tester1.jpg", "tester2.jpg", "tester3.jpg", "tester3.jpg",]
			]
		];

		echo json_encode($data_input);
	}
}