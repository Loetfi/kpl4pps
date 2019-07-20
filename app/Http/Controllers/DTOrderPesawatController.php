<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Api;
use App\Models\Anggota\OrderPesawatModel as OrderPesawat;
use App\Models\Anggota\OrderListModel AS OrderList;
use Carbon\Carbon;
use DB;
class DTOrderPesawatController extends Controller
{
    // backend
	public function getData(Request $request)
	{
		try { 
			if(empty($request->json())) throw New \Exception('Params not found', 500);

			// $this->validate($request, [
			// 	'id_kategori'	=> 'required|integer',
			// 	'id_layanan'	=> 'required|integer'
			// ]);

			// $id_kategori = $request->id_kategori ? $request->id_kategori : 0;
			// $id_layanan = $request->id_layanan ? $request->id_layanan : 0;

            $res = DB::table('view_order')->where('id_kategori',1)->join('anggota','view_order.id_anggota','=','anggota.id')->orderBy('id_order','asc')->get();
            // OrderList::where('id_layanan',1)->where('id_kategori',1)->join('anggota','view_order_list.id_anggota','=','anggota.id')->join('apps_order_detail','apps_order_detail.id_order','=','view_order_list.id_order')   ->orderBy('id_order','asc')->get();

			$Message = 'Berhasil';
			$code = 200;
			$data = $res;
		} catch(Exception $e) {
			$res = 0;
			$Message = $e->getMessage();
			$code = 400;
			$data = '';
		}
		return Response()->json(Api::response($res?true:false,$Message, $data?$data:[]),isset($code)?$code:200);
    }
    

    public function getDatas(Request $request)
    {
        $columns = [
            0 => 'id_order',
            // 1 => 'CreatedAt',
            // 2 => 'FullName',
            // 3 => 'Email',
            // 4 => 'PhoneNumber',
            // 5 => 'TypeInspection',
            // 6 => 'Address',
            // 7 => 'StatusOrderId',
            // 1 => 'id_order'
        ];
        
        $totalData = OrderPesawat::count();


        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        // $order = $columns[$request->input('order.0.column')];
        // $dir   = $request->input('order.0.dir');
        $order = $columns[0];
        $dir   = 'asc';

        if(empty($request->input('search.value')))
        {
            $orders = OrderPesawat::offset($start)
                                    ->limit($limit)
                                    ->orderby($order,$dir)
                                    ->get();
        } 
        else 
        {
            $search = $request->input('search.value');

            $orders = OrderPesawat::where('tanggal_order','LIKE',"%{$search}%")
                                    ->orWhere('id_order', 'LIKE',"%{$search}%")
                                    ->orWhere('nama_penumpang', 'LIKE',"%{$search}%")
                                    ->orWhere('id_anggota', 'LIKE',"%{$search}%")
                                    // ->orWhere('PhoneNumber', 'LIKE',"%{$search}%")
                                    // ->orWhere('TypeInspection', 'LIKE',"%{$search}%")
                                    // ->orWhere('Address', 'LIKE',"%{$search}%")
                                    // ->orWhere('StatusOrderId', 'LIKE',"%{$search}%")
                                    ->offset($start)
                                    ->limit($limit)
                                    ->orderBy($order,$dir)
                                    // ->orderBy('BookingNumber','ASC')
                                    ->get();
            
            $totalFiltered = OrderPesawat::where('id_order','LIKE',"%{$search}%")
                                            // ->orWhere('PhoneNumber', 'LIKE',"%{$search}%")
                                            ->count();
        }

        $data = [];
        if(!empty($orders))
        {  
            $i = 1;
            foreach($orders as $key => $value) 
            {
                $rows['id_order']           = $value->id_order; 
                $rows['tanggal_order']      = Carbon::parse($value->tanggal_order)->format('Y/m/d').' '.Carbon::parse($value->tanggal_order)->format('H:i:s');
                $rows['id_anggota']         = $value->CreatedAt ? Carbon::parse($value->CreatedAt)->format('Y/m/d H:i') : false;
                $rows['nama_layanan']       = $value->nama_layanan;
                $rows['id_kategori']           = $value->id_kategori; 
                $rows['dari']           = $value->dari;
                $rows['ke']           = $value->ke;
                $rows['penumpang']           = $value->penumpang;
                $rows['waktu_keberangkatan']           = $value->waktu_keberangkatan;
                $rows['kursi_kelas']           = $value->kursi_kelas;
                $rows['nama_penumpang']           = $value->nama_penumpang;
                $rows['nama_layanan']           = $value->nama_layanan;
                
                $data[] = $rows;
            }
        }

        $json_data = [
            'draw'          => intval($request->input('draw')),
            'recordsTotal'  => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data'          => $data
        ];
        // return response()->json($json_data);
        return response()->json(Api::format('1',$json_data,'Datatables Order'), 200);
    }
}
