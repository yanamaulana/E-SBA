<?php

/**
 * Models     : Datatables serverside based php
 * Modified   : Fauzan Falah
 * Website    : https://www.codekop.com/
 * 
 * 
 * 
 * 
 */
class m_DataTable extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_tables($tables, $cari, $iswhere)
    {
        // Ambil data yang di ketik user pada textbox pencarian
        $search = htmlspecialchars($_POST['search']['value']);
        // Ambil data limit per page
        $limit = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['length']}");
        // Ambil data start
        $start = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['start']}");

        $query = $tables;

        if (!empty($iswhere)) {
            $sql = $this->db->query("SELECT * FROM " . $query . " WHERE " . $iswhere);
        } else {
            $sql = $this->db->query("SELECT * FROM " . $query);
        }

        $sql_count = $sql->num_rows();

        $cari = implode(" LIKE '%" . $search . "%' OR ", $cari) . " LIKE '%" . $search . "%'";


        // Untuk mengambil nama field yg menjadi acuan untuk sorting
        $order_field = $_POST['order'][0]['column'];

        // Untuk menentukan order by "ASC" atau "DESC"
        $order_ascdesc = $_POST['order'][0]['dir'];
        $order = " ORDER BY " . $_POST['columns'][$order_field]['data'] . " " . $order_ascdesc;

        if (!empty($iswhere)) {
            $sql_data = $this->db->query("SELECT * FROM " . $query . " WHERE $iswhere AND (" . $cari . ")" . $order . " LIMIT " . $limit . " OFFSET " . $start);
        } else {
            $sql_data = $this->db->query("SELECT * FROM " . $query . " WHERE (" . $cari . ")" . $order . " LIMIT " . $limit . " OFFSET " . $start);
        }

        if (isset($search)) {
            if (!empty($iswhere)) {
                $sql_cari =  $this->db->query("SELECT * FROM " . $query . " WHERE $iswhere (" . $cari . ")");
            } else {
                $sql_cari =  $this->db->query("SELECT * FROM " . $query . " WHERE (" . $cari . ")");
            }
            $sql_filter_count = $sql_cari->num_rows();
        } else {
            if (!empty($iswhere)) {
                $sql_filter = $this->db->query("SELECT * FROM " . $query . "WHERE " . $iswhere);
            } else {
                $sql_filter = $this->db->query("SELECT * FROM " . $query);
            }
            $sql_filter_count = $sql_filter->num_rows();
        }
        $data = $sql_data->result_array();

        $callback = array(
            'draw' => $_POST['draw'], // Ini dari datatablenya
            'recordsTotal' => $sql_count,
            'recordsFiltered' => $sql_filter_count,
            'data' => $data
        );
        return json_encode($callback); // Convert array $callback ke json
    }

    function get_tables_where($tables, $cari, $where, $iswhere)
    {
        // Ambil data yang di ketik user pada textbox pencarian
        $search = htmlspecialchars($_POST['search']['value']);
        // Ambil data limit per page
        $limit = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['length']}");
        // Ambil data start
        $start = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['start']}");

        $setWhere = array();
        foreach ($where as $key => $value) {
            $setWhere[] = $key . "='" . $value . "'";
        }

        $fwhere = implode(' AND ', $setWhere);

        if (!empty($iswhere)) {
            $sql = $this->db->query("SELECT * FROM " . $tables . " WHERE $iswhere AND " . $fwhere);
        } else {
            $sql = $this->db->query("SELECT * FROM " . $tables . " WHERE " . $fwhere);
        }
        $sql_count = $sql->num_rows();

        $query = $tables;
        $cari = implode(" LIKE '%" . $search . "%' OR ", $cari) . " LIKE '%" . $search . "%'";

        // Untuk mengambil nama field yg menjadi acuan untuk sorting
        $order_field = $_POST['order'][0]['column'];

        // Untuk menentukan order by "ASC" atau "DESC"
        $order_ascdesc = $_POST['order'][0]['dir'];
        $order = " ORDER BY " . $_POST['columns'][$order_field]['data'] . " " . $order_ascdesc;

        if (!empty($iswhere)) {
            $sql_data = $this->db->query("SELECT * FROM " . $query . " WHERE $iswhere AND " . $fwhere . " AND (" . $cari . ")" . $order . " LIMIT " . $limit . " OFFSET " . $start);
        } else {
            $sql_data = $this->db->query("SELECT * FROM " . $query . " WHERE " . $fwhere . " AND (" . $cari . ")" . $order . " LIMIT " . $limit . " OFFSET " . $start);
        }

        if (isset($search)) {
            if (!empty($iswhere)) {
                $sql_cari =  $this->db->query("SELECT * FROM " . $query . " WHERE $iswhere AND " . $fwhere . " AND (" . $cari . ")");
            } else {
                $sql_cari =  $this->db->query("SELECT * FROM " . $query . " WHERE " . $fwhere . " AND (" . $cari . ")");
            }
            $sql_filter_count = $sql_cari->num_rows();
        } else {
            if (!empty($iswhere)) {
                $sql_filter = $this->db->query("SELECT * FROM " . $tables . " WHERE $iswhere AND " . $fwhere);
            } else {
                $sql_filter = $this->db->query("SELECT * FROM " . $tables . " WHERE " . $fwhere);
            }
            $sql_filter_count = $sql_filter->num_rows();
        }

        $data = $sql_data->result_array();

        $callback = array(
            'draw' => $_POST['draw'], // Ini dari datatablenya    
            'recordsTotal' => $sql_count,
            'recordsFiltered' => $sql_filter_count,
            'data' => $data
        );
        return json_encode($callback); // Convert array $callback ke json
    }

    function get_tables_query($query, $cari, $where, $iswhere)
    {
        // Ambil data dari POST Datatables
        $search = htmlspecialchars($_POST['search']['value']);
        $limit = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['length']}");
        $start = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['start']}");

        // Ambil data untuk sorting
        $order_field = $_POST['order'][0]['column'];
        $order_ascdesc = $_POST['order'][0]['dir'];

        // Pastikan ada ORDER BY untuk pagination OFFSET/FETCH di MSSQL
        // Menggunakan kolom yang dikirim dari DataTables
        $order_column = $_POST['columns'][$order_field]['data'];

        // Cek jika kolom orderable, jika tidak gunakan kolom default (misal SysId)
        // Walaupun DataTables menjamin ada, kita sediakan fallback
        if (empty($order_column)) {
            $order_column = 'SysId';
        }

        $order = " ORDER BY " . $order_column . " " . $order_ascdesc;

        // Klausa MSSQL untuk pagination
        $pagination_mssql = $order . " OFFSET " . $start . " ROWS FETCH NEXT " . $limit . " ROWS ONLY";

        // Persiapan klausa WHERE pencarian LIKE
        $cari_like = implode(" LIKE '%" . $search . "%' OR ", $cari) . " LIKE '%" . $search . "%'";

        // =================================================================
        // LOGIKA PENANGANAN KLAUSA WHERE KUSTOM ($where)
        // =================================================================

        if ($where != null) {
            $setWhere = array();
            foreach ($where as $key => $value) {
                // Gunakan db->escape() untuk keamanan jika nilai tidak pasti
                // Namun, karena ini string literal di query, kita pertahankan quote-nya
                $setWhere[] = $key . "='" . $value . "'";
            }
            $fwhere = implode(' AND ', $setWhere);
            $full_where_clause = " WHERE ";

            if (!empty($iswhere)) {
                $full_where_clause .= "$iswhere AND " . $fwhere;
            } else {
                $full_where_clause .= $fwhere;
            }

            // -----------------------------------------------------------
            // 1. Ambil Total Data (tanpa filter/limit)
            // -----------------------------------------------------------
            $sql = $this->db->query($query . $full_where_clause);
            $sql_count = $sql->num_rows();

            // -----------------------------------------------------------
            // 2. Ambil Data (dengan filter pencarian dan pagination MSSQL)
            // -----------------------------------------------------------
            $sql_data_query = $query . $full_where_clause . " AND (" . $cari_like . ")" . $pagination_mssql;
            $sql_data = $this->db->query($sql_data_query);

            // -----------------------------------------------------------
            // 3. Hitung Total Data yang difilter (dengan filter pencarian)
            // -----------------------------------------------------------
            if (!empty($search)) {
                $sql_cari_query = $query . $full_where_clause . " AND (" . $cari_like . ")";
                $sql_cari = $this->db->query($sql_cari_query);
                $sql_filter_count = $sql_cari->num_rows();
            } else {
                // Jika tidak ada pencarian, filtered count sama dengan total count
                $sql_filter_count = $sql_count;
            }

            $data = $sql_data->result_array();
        } else {
            // =================================================================
            // LOGIKA PENANGANAN TANPA KLAUSA WHERE KUSTOM
            // =================================================================
            $full_where_clause = "";
            if (!empty($iswhere)) {
                $full_where_clause = " WHERE $iswhere ";
            }

            // -----------------------------------------------------------
            // 1. Ambil Total Data (tanpa filter/limit)
            // -----------------------------------------------------------
            $sql = $this->db->query($query . $full_where_clause);
            $sql_count = $sql->num_rows();

            // -----------------------------------------------------------
            // 2. Ambil Data (dengan filter pencarian dan pagination MSSQL)
            // -----------------------------------------------------------
            $sql_data_query = $query . $full_where_clause;

            if (!empty($search)) {
                $sql_data_query .= (empty($full_where_clause) ? " WHERE " : " AND ") . "(" . $cari_like . ")";
            }
            $sql_data_query .= $pagination_mssql;

            $sql_data = $this->db->query($sql_data_query);

            // -----------------------------------------------------------
            // 3. Hitung Total Data yang difilter (dengan filter pencarian)
            // -----------------------------------------------------------
            if (!empty($search)) {
                $sql_cari_query = $query . $full_where_clause . (empty($full_where_clause) ? " WHERE " : " AND ") . "(" . $cari_like . ")";
                $sql_cari = $this->db->query($sql_cari_query);
                $sql_filter_count = $sql_cari->num_rows();
            } else {
                // Jika tidak ada pencarian, filtered count sama dengan total count
                $sql_filter_count = $sql_count;
            }

            $data = $sql_data->result_array();
        }

        // =================================================================
        // KEMBALIKAN CALLBACK DATATABLES
        // =================================================================
        $callback = array(
            'draw' => $_POST['draw'],
            'recordsTotal' => $sql_count,
            'recordsFiltered' => $sql_filter_count,
            'data' => $data
        );
        return json_encode($callback);
    }

    function get_tables_query_csrf($query, $cari, $where, $csrf_name, $csrf_hash)
    {
        // Ambil data yang di ketik user pada textbox pencarian
        $search = htmlspecialchars($_POST['search']['value']);
        // Ambil data limit per page
        $limit = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['length']}");
        // Ambil data start
        $start = preg_replace("/[^a-zA-Z0-9.]/", '', "{$_POST['start']}");

        if ($where != null) {
            $setWhere = array();
            foreach ($where as $key => $value) {
                $setWhere[] = $key . "='" . $value . "'";
            }

            $fwhere = implode(' AND ', $setWhere);

            $sql = $this->db->query($query . " WHERE " . $fwhere);
            $sql_count = $sql->num_rows();

            $cari = implode(" LIKE '%" . $search . "%' OR ", $cari) . " LIKE '%" . $search . "%'";

            // Untuk mengambil nama field yg menjadi acuan untuk sorting
            $order_field = $_POST['order'][0]['column'];

            // Untuk menentukan order by "ASC" atau "DESC"
            $order_ascdesc = $_POST['order'][0]['dir'];
            $order = " ORDER BY " . $_POST['columns'][$order_field]['data'] . " " . $order_ascdesc;

            $sql_data = $this->db->query($query . " WHERE " . $fwhere . " AND (" . $cari . ")" . $order . " LIMIT " . $limit . " OFFSET " . $start);
            $sql_filter = $this->db->query($query . " WHERE " . $fwhere);

            if (isset($search)) {
                $sql_cari =  $this->db->query($query . " WHERE " . $fwhere . " AND (" . $cari . ")");
                $sql_filter_count = $sql_cari->num_rows();
            } else {
                $sql_filter_count = $sql_filter->num_rows();
            }

            $data = $sql_data->result_array();
        } else {

            $sql = $this->db->query($query);
            $sql_count = $sql->num_rows();

            $cari = implode(" LIKE '%" . $search . "%' OR ", $cari) . " LIKE '%" . $search . "%'";

            // Untuk mengambil nama field yg menjadi acuan untuk sorting
            $order_field = $_POST['order'][0]['column'];

            // Untuk menentukan order by "ASC" atau "DESC"
            $order_ascdesc = $_POST['order'][0]['dir'];
            $order = " ORDER BY " . $_POST['columns'][$order_field]['data'] . " " . $order_ascdesc;

            $sql_data = $this->db->query($query . " WHERE (" . $cari . ")" . $order . " LIMIT " . $limit . " OFFSET " . $start);
            $sql_filter = $this->db->query($query);

            if (isset($search)) {
                $sql_cari =  $this->db->query($query . " WHERE (" . $cari . ")");
                $sql_filter_count = $sql_cari->num_rows();
            } else {
                $sql_filter_count = $sql_filter->num_rows();
            }

            $data = $sql_data->result_array();
        }

        $callback = array(
            'draw' => $_POST['draw'], // Ini dari datatablenya    
            'recordsTotal' => $sql_count,
            'recordsFiltered' => $sql_filter_count,
            'data' => $data
        );
        $callback[$csrf_name] = $csrf_hash;

        return json_encode($callback); // Convert array $callback ke json
    }
}
