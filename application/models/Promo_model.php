<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo_model extends CI_Model {

	private $table = 'promo';

	public function create($data)
	{
		return $this->db->insert($this->table, $data);
	}

	public function read()
	{
		return $this->db->get($this->table);
	}

	public function update($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update($this->table, $data);
	}

	public function delete($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete($this->table);
	}

	public function getPromo($id)
	{
        //$this->db->select('promo.id, promo.nama, promo.nilai');
		//$this->db->like('nama', $search);
		//return $this->db->get($this->table)->result();

		$this->db->where('id', $id);
		return $this->db->get($this->table);

	}

	public function getPromoCode($search='')
	{
        $this->db->select('promo.id, promo.nama, promo.nilai');
		$this->db->like('nama', $search);
		return $this->db->get($this->table)->result();

	}

	public function search($search="")
	{
		$this->db->like('nama', $search);
		return $this->db->get($this->table)->result();
	}

}

/* End of file Promo_model.php */
/* Location: ./application/models/Promo_model.php */