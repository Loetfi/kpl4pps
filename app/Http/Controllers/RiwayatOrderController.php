<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota\RiwayatModel AS RiwayatModel;
use App\Models\Anggota\RiwayatOrderDetailModel AS RiwayatDetail;

use App\Helpers\Api;
use App\Helpers\RestCurl;

class RiwayatOrderController extends Controller
{
	public function list(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'anggota_id'     => 'required',
				'offset'         => 'required|integer',
				'limit'			=> 'required|integer'
			]);  

			$anggota_id = $request->anggota_id ? $request->anggota_id : 0;

			$data_res = RiwayatModel::where('id_anggota', $anggota_id)->skip($request->offset)->take($request->limit)->orderby('id_order','desc')->get();

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $data_res;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}

	 // detail
	public function detail(Request $request){
		try { 

			if(empty($request->json())) throw New \Exception('Params not found', 500);

			$this->validate($request, [
				'id_order'     => 'required|integer',
				'anggota_id'   => 'required',
			]);  

			$anggota_id = $request->anggota_id ? $request->anggota_id : 0;
			$id_order = $request->id_order ? $request->id_order : 0;
			$id_layanan = $request->id_layanan ? $request->id_layanan : 0;
			$id_kategori = $request->id_kategori ? $request->id_kategori : 0;
			$select = ['id_order'];
			if ($id_layanan == '5' and $id_kategori == '6') { // topup pulsa 
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','no_hp','nominal','provider'];
			} elseif ($id_layanan == '1' and $id_kategori == '1') { // pesawat 
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','dari','ke','penumpang','waktu_keberangkatan','kursi_kelas','nama_penumpang'];
			} elseif ($id_layanan == '1' and $id_kategori == '2') { // hotel
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','nama_hotel','check_in','check_out','tamu','rooms'];
			} elseif ($id_layanan == '1' and $id_kategori == '3') { // kereta
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','dari','ke','penumpang_dewasa','penumpang_balita','waktu_kedatangan'];
			} elseif ($id_layanan == '1' and $id_kategori == '4') { // bus
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','dari','ke','penumpang','waktu_kedatangan'];
			} elseif ($id_layanan == '1' and $id_kategori == '5') { // shuttle bus
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','dari','ke','penumpang','waktu_kedatangan','nama_shuttle'];
			} elseif ($id_layanan == '2' and $id_kategori == '7') { // shuttle bus
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','nama_barang','harga_barang','id_barang','qty'];
			} elseif ($id_layanan == '4' and $id_kategori == '10') { // sewa gedung
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book'];
			} elseif ($id_layanan == '4' and $id_kategori == '11') { // sewa pemancigan
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book'];
			} elseif ($id_layanan == '5' and $id_kategori == '6') { // pulsa
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','no_hp','nominal','provider'];
			} elseif ($id_layanan == '5' and $id_kategori == '12') { // paket data
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','no_hp','paket','provider'];
			} elseif ($id_layanan == '5' and $id_kategori == '8') { // token
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','no_hp','nominal_token','no_meter'];
			} elseif ($id_layanan == '5' and $id_kategori == '9') { // listrik tagihan
					$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','no_hp','no_meter'];
			} elseif ($id_layanan == '3' and $id_kategori == '13') { // simpanan
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','no_hp','jumlah_simpanan','store_ke','keterangan'];
			} elseif ($id_layanan == '3' and $id_kategori == '14') { // pinjaman
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','no_hp','nilai_pinjaman','store_ke','keterangan','tenor'];
			// pinjaman
			} elseif ($id_layanan == '6' and $id_kategori == '15') { 
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book','keterangan','pilihan_paket'];
			} elseif ($id_layanan == '6' and $id_kategori == '16') { 
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book','keterangan','pilihan_paket'];
			} elseif ($id_layanan == '6' and $id_kategori == '17') { 
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book','keterangan','pilihan_paket'];
			} elseif ($id_layanan == '6' and $id_kategori == '18') { 
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book','keterangan','pilihan_paket'];
			} elseif ($id_layanan == '6' and $id_kategori == '19') { 
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book','keterangan','pilihan_paket'];
			} elseif ($id_layanan == '6' and $id_kategori == '20') { 
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book','keterangan','pilihan_paket'];
			} elseif ($id_layanan == '6' and $id_kategori == '21') { 
				$select = ['telepon', 'ekstension','id_anggota','tanggal_order','id_layanan','approval','nama_kategori','id_kategori','gambar_kategori','nama_layanan','icon_layanan','id_order_detail','id_order','tanggal_book','keterangan','pilihan_paket'];
			} else {
					// 5 6 , 5 12, 5 8 , 5 9
				throw new \Exception("Tidak ditemukan kriteria order detail", 400);
			}

			$data_res = RiwayatDetail::where('id_anggota' , $anggota_id)->where('id_order',$id_order)->where('id_layanan',$id_layanan)->select($select)->where('id_kategori',$id_kategori)->get();

			foreach ($data_res as $res_data) {
				$ress['telepon'] = $res_data->telepon;
				$ress['ekstension'] = $res_data->ekstension;
				$ress['id_anggota'] = $res_data->id_anggota;
				$ress['tanggal_order'] = $res_data->tanggal_order;
				$ress['id_layanan'] = (int) $res_data->id_layanan;
				$ress['approval'] = $res_data->approval;
				$ress['nama_kategori'] = $res_data->nama_kategori;
				$ress['id_kategori'] = $res_data->id_kategori;
				$ress['gambar_kategori'] = $res_data->gambar_kategori;
				$ress['nama_layanan'] = $res_data->nama_layanan;
				$ress['icon_layanan'] = $res_data->icon_layanan;
				$ress['id_order_detail'] = $res_data->id_order_detail;
				$ress['id_order'] = $res_data->id_order;
				$ress['no_hp'] = $res_data->no_hp;
				$ress['nominal_token'] = (int) $res_data->nominal_token;
				$ress['no_meter'] = $res_data->no_meter;

				$result[] = $ress;
			}

			$Message = 'Berhasil';
			$code = 200;
			$res = 1;
			$data = $result;

		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
	}
}
