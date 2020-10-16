<?php

class MY_Controller extends CI_Controller
{
    protected $data;

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['Environment/Environment', 'Swapi']);
        $this->data['site_title'] = 'Southern Phone Programming Test';
    }

    protected function frontend_view($view)
    {
        $this->load->view('common/header', $this->data);
        $this->load->view($view, $this->data);
        $this->load->view('common/footer', $this->data);
    }
}
