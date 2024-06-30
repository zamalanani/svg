<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		$this->load->model('promo_model');
	}

	public function index()
	{
		$this->load->view('promo');
	}

	public function read()
	{
		header('Content-type: application/json');
		if ($this->promo_model->read()->num_rows() > 0) {
			foreach ($this->promo_model->read()->result() as $promo) {
				$data[] = array(
					'nama' => $promo->nama,
					'alamat' => $promo->alamat,
					'keterangan' => $promo->keterangan,
					'action' => '<button class="btn btn-sm btn-success" onclick="edit('.$promo->id.')">Edit</button> <button class="btn btn-sm btn-danger" onclick="remove('.$promo->id.')">Delete</button>'
				);
			}
		} else {
			$data = array();
		}
		$promo = array(
			'data' => $data
		);
		echo json_encode($promo);
	}

	public function add()
	{
		$data = array(
			'nama' => $this->input->post('nama'),
			'alamat' => $this->input->post('alamat'),
			'telepon' => $this->input->post('telepon'),
			'keterangan' => $this->input->post('keterangan')
		);
		if ($this->promo_model->create($data)) {
			echo json_encode('sukses');
		}
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->promo_model->delete($id)) {
			echo json_encode('sukses');
		}
	}

	public function edit()
	{
		$id = $this->input->post('id');
		$data = array(
			'nama' => $this->input->post('nama'),
			'alamat' => $this->input->post('alamat'),
			'telepon' => $this->input->post('telepon'),
			'keterangan' => $this->input->post('keterangan')
		);
		if ($this->promo_model->update($id,$data)) {
			echo json_encode('sukses');
		}
	}
 
	public function get_promo()
	{
		 

		$id = $this->input->post('id');
		$promo = $this->promo_model->getPromo($id);
		if ($promo->row()) {
			echo json_encode($promo->row());
		}

	}

	public function get_promo_code()
	{

		header('Content-type: application/json');
		$disc = $this->input->post('nama');
		$search = $this->promo_model->getPromoCode($disc);
		foreach ($search as $disc) {
			$data[] = array(
				'id' => $disc->nilai,
				'text' => $disc->nama
				
			);
		}
		echo json_encode($data);

		
	}

	public function search()
	{
		header('Content-type: application/json');
		$promo = $this->input->post('promo');
		$search = $this->promo_model->search($promo);
		foreach ($search as $promo) {
			$data[] = array(
				'id' => $promo->id,
				'text' => $promo->nama
			);
		}
		echo json_encode($data);
	}

}

/* End of file Promo.php */
/* Location: ./application/controllers/Promo.php */