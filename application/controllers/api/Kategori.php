<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;
use SebastianBergmann\Environment\Console;

class Kategori extends RestController {

    function __construct()
    {
        parent::__construct();
        $this->load->model('Kategori_model','kategori');
    }

    function list_get() {
        $kategori = $this->kategori->get();
        return $this->response([
            'data' => $kategori,
        ], 200);
    }

}