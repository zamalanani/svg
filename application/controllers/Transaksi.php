<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if ($this->session->userdata('status') !== 'login' ) {
			redirect('/');
		}
		 
		$this->load->model('transaksi_model');
		$this->load->model('transaksi_detail_model');
	}

	public function index()
	{
		$this->load->view('transaksi');
	}

	public function read()
	{
		// header('Content-type: application/json');
		if ($this->transaksi_model->read()->num_rows() > 0) {
			foreach ($this->transaksi_model->read()->result() as $transaksi) {
				$barcode = explode(',', $transaksi->barcode);
				$tanggal = new DateTime($transaksi->tanggal);
				$data[] = array(
					'tanggal' => $tanggal->format('d-m-Y H:i:s'),
					'nama_produk' => '<table>'.$this->transaksi_model->getProduk($barcode, $transaksi->qty).'</table>',
					'total_bayar' => $transaksi->total_bayar,
					'jumlah_uang' => $transaksi->jumlah_uang,
					'diskon' => $transaksi->diskon,
					'pelanggan' => $transaksi->pelanggan,
					'action' => '<a class="btn btn-sm btn-success" href="'.site_url('transaksi/cetak/').$transaksi->id.'">Print</a> <button class="btn btn-sm btn-danger" onclick="remove('.$transaksi->id.')">Delete</button>'
				);
			}
		} else {
			$data = array();
		}
		$transaksi = array(
			'data' => $data
		);
		echo json_encode($transaksi);
	}

	public function add()
	{
		$jmlbrg = $this->input->post('jmlbrg');
		 
		$produk = json_decode($this->input->post('produk'));
		$tanggal = new DateTime($this->input->post('tanggal'));
		$barcode = array();
		foreach ($produk as $produk) {
			$this->transaksi_model->removeStok($produk->id, $produk->stok);
			$this->transaksi_model->addTerjual($produk->id, $produk->terjual);
			array_push($barcode, $produk->id);
		}


		$data_detail_penjualan = [];
		$kodex = implode(',', $barcode);
		$kodexx = explode(',', $kodex);
		$qtyx = implode(',', $this->input->post('qty'));
		$qtyxx = explode(',', $qtyx);
		$hrgx = implode(',', $this->input->post('hrg'));
		$hrgxx = explode(',', $hrgx); 
		$dscx = implode(',', $this->input->post('dsc'));
		$dscxx = explode(',', $dscx);
		$subtotx = implode(',', $this->input->post('subtot'));
		$subtotxx = explode(',', $subtotx);

		for ($i=0; $i < $jmlbrg ; $i++) {
 
			$data_detail_penjualan[$i]['no_transaksi'] = $this->input->post('nota');
			$data_detail_penjualan[$i]['kode_barang'] = $kodexx[$i];
			$data_detail_penjualan[$i]['qty'] = $qtyxx[$i];
			$data_detail_penjualan[$i]['harga'] = $hrgxx[$i]; 
			$data_detail_penjualan[$i]['diskon'] = $dscxx[$i]; 
			$data_detail_penjualan[$i]['sub_jml'] = $subtotxx[$i]; 
		}

		$this->transaksi_detail_model->tambah($data_detail_penjualan);


		$data = array(
			'tanggal' => $tanggal->format('Y-m-d H:i:s'),
			'barcode' => implode(',', $barcode),
			'qty' => implode(',', $this->input->post('qty')),
			'total_bayar' => $this->input->post('total_bayar'),
			'jumlah_uang' => $this->input->post('jumlah_uang'),
			'diskon' => $this->input->post('diskon'),
			'pelanggan' => $this->input->post('pelanggan'),
			'nota' => $this->input->post('nota'),
			'kasir' => $this->session->userdata('id')
		);
		if ($this->transaksi_model->create($data)) {
			echo json_encode($this->db->insert_id());
		}

		$data = $this->input->post('form');
	}

	public function delete()
	{
		$id = $this->input->post('id');
		if ($this->transaksi_model->delete($id)) {
			echo json_encode('sukses');
		}
	}

	public function cetak($id)
	{
		$produk = $this->transaksi_model->getAll($id);
		
		$tanggal = new DateTime($produk->tanggal);
		$barcode = explode(',', $produk->barcode);
		$qty = explode(',', $produk->qty);

		$produk->tanggal = $tanggal->format('d m Y H:i:s');

		$dataProduk = $this->transaksi_model->getName($barcode);
		foreach ($dataProduk as $key => $value) {
			$value->total = $qty[$key];
			$value->harga = $value->harga * $qty[$key];
		}

		$data = array(
			'nota' => $produk->nota,
			'tanggal' => $produk->tanggal,
			'produk' => $dataProduk,
			'total' => $produk->total_bayar,
			'bayar' => $produk->jumlah_uang,
			'kembalian' => $produk->jumlah_uang - $produk->total_bayar,
			'kasir' => $produk->kasir
		);
		$this->load->view('cetak', $data);
	}

	public function penjualan_bulan()
	{
		header('Content-type: application/json');
		$day = $this->input->post('day');
		foreach ($day as $key => $value) {
			$now = date($day[$value].' m Y');
			if ($qty = $this->transaksi_model->penjualanBulan($now) !== []) {
				$data[] = array_sum($this->transaksi_model->penjualanBulan($now));
			} else {
				$data[] = 0;
			}
		}
		echo json_encode($data);
	}

	public function transaksi_hari()
	{
		header('Content-type: application/json');
		$now = date('d m Y');
		$total = $this->transaksi_model->transaksiHari($now);
		echo json_encode($total);
	}

	public function keranjang_barang(){
		$this->load->view('keranjang');
	}

	public function transaksi_terakhir($value='')
	{
		header('Content-type: application/json');
		$now = date('d m Y');
		foreach ($this->transaksi_model->transaksiTerakhir($now) as $key) {
			$total = explode(',', $key);
		}
		echo json_encode($total);
	}

}

/* End of file Transaksi.php */
/* Location: ./application/controllers/Transaksi.php */