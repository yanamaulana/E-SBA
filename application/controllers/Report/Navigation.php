<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Navigation extends CI_Controller
{
    private $layout = 'layout';

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }

    public function index()
    {
        $this->data['page_title'] = "Samick Report Navigation";
        $this->data['page_content'] = "Report/navigation";
        $this->data['script_page'] =  '<script src="' . base_url() . 'assets/Report/navigation.js?v=' . filemtime(FCPATH . 'assets/Report/navigation.js') . '"></script>';

        $this->load->view($this->layout, $this->data);
    }

    public function validate_mis_password()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $password = $this->input->post('password');
        $userId = $this->session->userdata('sys_sba_userid');

        if (empty($password) || empty($userId)) {
            echo json_encode(['status' => 'error', 'message' => 'Sesi tidak valid atau password kosong.']);
            return;
        }

        $user = $this->db->select('User_Pass_Txt')
            ->from('ERPQview_User_Employee')
            ->where('User_ID', $userId)
            ->where_in('position_id', ['6390', '6391', '6392', '6430'])
            ->get()
            ->row();

        if ($user && $user->User_Pass_Txt === $password) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Password yang Anda masukkan salah.']);
        }
    }
}
