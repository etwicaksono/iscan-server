<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DataTable_model extends CI_Model
{

    /*
#DOKUMENTASI PENGGUNAAN

    #PARAMETER WAJIB
        1. table            => $table
            *Berisi nama tabel
        2. column_search    => [$val, $val, $val]
            *Berisi nama kolom untuk pencarian
        3. column_order     => [$val, $val, $val]
            *Berisi urutan kolom untuk order
        4. order            => [$col => $method, $col => $method]
            *Berisi nama kolom dan metode untuk default order

    #PARAMETER OPSIONAL
        1. arr_group    => [$val, $val, $val]
            *Berisi array kolom untuk grouping
        2. arr_join     => [["table"=>$val, "cond"=>$val, "type"=> $val]]
            *Berisi array untuk pembentukan join
        3. c_filter     => [$col => $val, $col => $val]
            *Berisi kolom untuk penggunaan filter (WHERE/AND)
        4. c_select     => [$val, $val, $val]
            *Berisi kolom untuk penggunaan seleksi data (SELECT)
        5. flt_like     => [$col => $val, $col => $val]
            *Berisi kolom untuk penggunaan filter (LIKE)
        6. flt_or_where => [$col => $val, $col => $val]
            *Berisi kolom untuk penggunaan filter (OR)
        7. flt_where_in => [$col => $val, $col => $val]
            *Berisi kolom untuk penggunaan filter (IN)
        8. join         => ["table"=>$val, "cond"=>$val, "type"=> $val]
            *Berisi kolom dan kondisi untuk pembentukan join
        9. and_or_where =>
                        [ 
                            [
                                $type => [$col => $val]
                            ],
                            [
                                $type => [$col => $val],
                            ] 
                        ]
            *Sebuah array 3 layer dimana key layer kedua yaitu variabel $type diisi string 'or' atau 'and' kemudian layer ketiga
    
*/

    public function __construct()
    {
        parent::__construct();
        // $this->load->database();
    }

    private function _get_datatables_query($config)
    {
        extract($config);
        $this->db->from($table);

        if (isset($join)) { //'join' column
            $this->db->join($join['table'], $join['cond'], $join['type']);
        }

        if (isset($arr_join)) { //'join' column by array
            foreach ($arr_join as $val) {
                $this->db->join($val['table'], $val['cond'], $val['type']);
            }
        }

        if (isset($c_select)) { //'select' column
            $this->db->select($c_select);
        }

        if (isset($c_filter)) { //'where' filter
            foreach ($c_filter as $col => $val) {
                $this->db->where($col, $val);
            }
        }

        if (isset($flt_where_in)) { //'where_in' filter
            foreach ($flt_where_in as $col => $val) {
                $this->db->where_in($col, $val);
            }
        }

        if (isset($and_or_where)) { //'and_or_where' filter
            $layer1 = $and_or_where;
            foreach ($layer1 as $layer2) {
                foreach ($layer2 as $type => $config) {
                    if ($type == "or") {
                        foreach ($config as $col => $val) {
                            $this->db->or_where($col, $val);
                        }
                    } else {
                        foreach ($config as $col => $val) {
                            $this->db->where($col, $val);
                        }
                    }
                }
            }
        }

        if (isset($flt_or_where)) { //'or_where' filter
            foreach ($flt_or_where as $col => $val) {
                $this->db->or_where($col, $val);
            }
        }

        if (isset($flt_like)) { //'like' filter
            foreach ($flt_like as $col => $val) {
                $this->db->like($col, $val, "BOTH");
            }
        }

        if (isset($arr_group)) { //grouping
            $this->db->group_by($arr_group);
        }


        $i = 0;
        foreach ($column_search as $item) // loop column 
        {
            if ($_REQUEST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_REQUEST['search']['value'], "BOTH");
                } else {
                    $this->db->or_like($item, $_REQUEST['search']['value'], "BOTH");
                }

                if (count($column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if ($_REQUEST['order']['0']['column'] != 0) // here order processing
        {
            $this->db->order_by($column_order[$_REQUEST['order']['0']['column']], $_REQUEST['order']['0']['dir']);
        } else if (isset($order)) {
            foreach ($order as $col => $method) {
                $this->db->order_by($col, $method);
            }
        }
    }

    function get_datatables($config, $debug = false)
    {
        $this->_get_datatables_query($config);
        if ($_REQUEST['length'] != -1) $this->db->limit($_REQUEST['length'], $_REQUEST['start']);
        if ($debug == true) {
            ed($this->db->get_compiled_select());
        }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($config)
    {
        $this->_get_datatables_query($config);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($config)
    {
        extract($config);
        $this->db->from($table);
        return $this->db->count_all_results();
    }
}