<?php


function logger($app = "", $event = "", $table = "", $id_row = "", $old_value = "", $new_value = "", $username = null, $error = null)
{
    $ci = get_instance();
    if ($username == null) {
        $username = $ci->session->username;
    }

    $data = [
        'created_at' => date('Y:m:d H:i:s'),
        'username' => $username,
        'konten' => json_encode([
            'app' => $app, //web, android
            'event' => $event, //insert, update, delete
            'table' => $table, //table's name
            'id_row' => $id_row, //id row of table
            'old_value' => $old_value, // existing data before execute query
            'new_value' => $new_value, // new inserted/updated 
            'error' => $error, // error log 
        ])
    ];
    $ci->db->insert('log', $data);
}

function isMobile()
{
    $useragent = $_SERVER['HTTP_USER_AGENT'];

    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
        // if mobile
        return true;
    } else {
        // if desktop
        return false;
    }
}


function mk_alert($title, $pesan, $icon)
{
    $ci = get_instance();
    $alert = '<div class="flash-data" data-title="' . $title . '" data-icon="' . $icon . '" data-pesan="' . $pesan . '"></div>';
    $ci->session->set_flashdata('flash', $alert);
}

function mk_alert2($title, $pesan, $icon)
{
    $ci = get_instance();
    $alert = '<div class="flash-data2" data-title="' . $title . '" data-icon="' . $icon . '" data-pesan="' . $pesan . '"></div>';
    $ci->session->set_flashdata('flash2', $alert);
}

function j_alert($title, $pesan, $icon)
{
    $alert = '<div class="flash-data" data-title="' . $title . '" data-icon="' . $icon . '" data-pesan="' . $pesan . '"></div>';
    return $alert;
}

function j_alert2($title, $pesan, $icon)
{
    $alert = '<div class="flash-data2" data-title="' . $title . '" data-icon="' . $icon . '" data-pesan="' . $pesan . '"></div>';
    return $alert;
}

function dd($param)
{
    var_dump($param);
    die;
}

function ed($param)
{
    echo ($param);
    die;
}

function jd($param)
{
    echo json_encode($param);
    die;
}


function is_logged_in()
{
    $ci = get_instance();
    if ($ci->session->userdata('username')) {
        return true;
    } else {
        return false;
    }
}

function seo_title($s)
{
    $c = [' '];
    $d = ['-', '/', '\\', ',', '.', '#', ':', ';', '\'', '"', '[', ']', '{', '}', ')', '(', '|', '`', '~', '!', '@', '%', '$', '^', '&', '*', '=', '?', '+', '–', '<', '>'];
    $s = str_replace($d, '', $s); // Hilangkan karakter yang telah disebutkan di array $d
    $s = strtolower(str_replace($c, '', $s)); // Ganti spasi dengan tanda - dan ubah hurufnya menjadi kecil semua
    return $s;
}

function authCheck()
{
    $ci = get_instance();
    if (!$ci->session->userdata('username')) {
        mk_alert('Anda harus login untuk mendapatkan hak akses!', '', 'error');
        redirect('login');
    } else {
        $id_role = $ci->session->id_role;
        $href = $ci->uri->segment(1);
        $userAccess = $ci->db->from("__app_user_access ua")
            ->join("__app_menu mn", "ua.id_menu = mn.id", "LEFT")
            ->where("ua.id_role", $id_role)
            ->where("href", $href)
            ->where("mn.is_active", 1)
            ->get();

        if ($userAccess->num_rows() < 1) {
            redirect('blocked');
        }
    }
}

function getInput()
{
    $ci = get_instance();
    $input = $ci->input->get();
    if (empty($input)) $input = $ci->input->post();
    return $input;
}

function isAjax()
{
    $ci = get_instance();
    if ($ci->input->is_ajax_request()) {
        return true;
    } else {
        redirect('Ajax/index');
    }
}

function refreshSession()
{
    $ci = get_instance();
    $user = $ci->db->get_where('user', ['id' => $ci->session->id])->row_array();
    $ci->session->set_userdata($user);
}

function is_unique($table, $column, $value)
{
    // return false jika telah digunakan, return true jika belum pernah digunakan
    $ci = get_instance();
    $data = $ci->db->where($column, $value)->get($table)->row_array();
    if (empty($data)) {
        return true;
    } else {
        return false;
    }
}

function getHari($datetime)
{
    $hari = date("w", strtotime($datetime));
    $seminggu = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
    $hari_ini = $seminggu[$hari];
    return $hari_ini;
}

function getBulan($datetime)
{
    $bulan = date("n", strtotime($datetime));
    switch ($bulan) {
        case 1:
            return "Januari";
            break;
        case 2:
            return "Februari";
            break;
        case 3:
            return "Maret";
            break;
        case 4:
            return "April";
            break;
        case 5:
            return "Mei";
            break;
        case 6:
            return "Juni";
            break;
        case 7:
            return "Juli";
            break;
        case 8:
            return "Agustus";
            break;
        case 9:
            return "September";
            break;
        case 10:
            return "Oktober";
            break;
        case 11:
            return "November";
            break;
        case 12:
            return "Desember";
            break;
    }
}

function getTanggal($datetime)
{
    $d = date("d", strtotime($datetime));
    $F = getBulan($datetime);
    $Y = date("Y", strtotime($datetime));
    return $d . " " . $F . " " . $Y;
}

function get_tanggal_jam($datetime)
{
    $d = date("d", strtotime($datetime));
    $F = getBulan($datetime);
    $Y = date("Y H:i", strtotime($datetime));
    return $d . " " . $F . " " . $Y;
}

function cek_if_exist($param)
{
    if (isset($param)) {
        return $param;
    } else {
        return "";
    }
}

function cetak($str)
{
    echo htmlentities($str, ENT_QUOTES, 'UTF-8');
}

function xss_flt($str)
{
    return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

function left_trim($prefix, $str)
{
    if (substr($str, 0, strlen($prefix) == $prefix)) {
        $str = substr($str, strlen($prefix));
    }
    return $str;
    /*
    contoh:
    prefix = bla_
    string = bla_bla_bla_tes_bla_bla_
    output = bla_tes_bla_bla_
    */
}

function duplicate_checker($table, $column, $value, $key_binding)
{
    // return true jika ada duplikat, return false jika tidak ada duplikat atau data yang sama adalah data lama
    $ci = get_instance();
    $data = $ci->db->where($column, $value)->get($table)->row_array();
    if (empty($data)) {
        return false;
    } else if ($data[$key_binding['col']] == $key_binding['val']) {
        return false;
    } else {
        return true;
    }
}

function getDatetime($datetime)
{
    // mengubah format d-m-Y H:i menjadi Y-m-d H:i:00
    if ($datetime != "") {
        $temp = explode(' ', $datetime);
        $date = $temp[0];
        $time = $temp[1];


        $date = explode('-', $date);
        $day = $date[0];
        $month = $date[1];
        $year = $date[2];

        $result = $year . "-" . $month . "-" . $day . " " . $time . ":00";
        return $result;
    }
}

function dateParser($date)
{
    // mengubah d-m-Y menjadi Y-m-d
    if ($date != "") {
        $temp = explode('/', $date);
        $day = $temp[0];
        $month = $temp[1];
        $year = $temp[2];
        return $year . "-" . $month . "-" . $day;
    }
}


function remover_per_item($path, $target)
{
    if (file_exists(FCPATH . $path . "/" . $target) && $target != "") {
        unlink(FCPATH . $path . "/" . $target);
    }
}

function removerByArray($param, $oldImage)
{
    if ($param == 'lampiran_peminjaman') {
        $path = FCPATH . 'assets/uploaded/lampiran_pengajuan/';
    }

    foreach ($oldImage as $val) {
        if (file_exists($path . $val) && $val != "") {
            unlink($path . $val);
        }
    }
}

function returnDates($fromdate, $todate, $dateFormat)
{
    if ($fromdate != null && $todate != null) {
        $fromdate = \DateTime::createFromFormat($dateFormat, $fromdate);
        $todate = \DateTime::createFromFormat($dateFormat, $todate);
        $datePeriod = new \DatePeriod(
            $fromdate,
            new \DateInterval('P1D'),
            $todate->modify('+1 day')
        );

        $result = [];
        foreach ($datePeriod as $date) {
            $result[] =  $date->format('Y-m-d H:i:s');
        }
        return $result;
    } else {
        return [];
    }
}


function get_thumb($img)
{
    $temp = explode(".", $img);
    if (count($temp) > 1) {
        $thumb = $temp[0] . "_thumb." . $temp[1];
        return $thumb;
    } else {
        return $img;
    }
}


function get_app_config()
{
    $ci = get_instance();
    return $ci->db->get("__app_config")->row_array();
}

function set_csrf()
{
    $ci = get_instance();
    $csrf = [
        'name' => $ci->security->get_csrf_token_name(),
        'hash' => $ci->security->get_csrf_hash()
    ];
    return $csrf;
}

function print_csrf()
{

    $ci = get_instance();
    $csrf = [
        'name' => $ci->security->get_csrf_token_name(),
        'hash' => $ci->security->get_csrf_hash()
    ];

    echo "<input type='hidden' name='" . $csrf['name'] . "' value='" . $csrf['hash'] . "' id='csrf' />";
}