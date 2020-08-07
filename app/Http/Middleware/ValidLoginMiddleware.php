<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Helpers\Api;
use App\Helpers\RestCurl;
use App\Models\Anggota\AnggotaModel AS Anggota;
use App\Models\Anggota\LoginActivityModel AS LoginActivityModel;
use App\Helpers\Telegram;
use App\Helpers\Notif;

class ValidLoginMiddleware
{
    /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        $token  = "897658383:AAExyvHTM5Jzrw7EF0fF5XAheJnC9RSnVaw";	
        $chatId = "-384536993";
        $id_anggota = $request->id_anggota ?: 0;
        $get_anggota = Anggota::where('id' , $id_anggota)->select('noanggota','nama')->get()->first();
        
        try{

            $checkLogin = LoginActivityModel::where('id_anggota', $get_anggota->noanggota)->get()->first();

            if ($checkLogin > 0){
                
                /**
                * Telegram
                */
                $txt   ="#validasiorderlogin #success"."\n";
                $txt  .="| Nama : ". $get_anggota->nama ."\n";
                $txt  .="| No Anggota : ". $get_anggota->noanggota ."\n";
                $txt  .="| ID Anggota : ". $request->id_anggota ."\n";
                $telegram = new Telegram($token);
                $telegram->sendMessage($chatId, $txt, 'HTML');
                
                return $next($request);

            } else {
                throw new Exception("ga ada", 1);
            }
            
        } catch(Exception $e){
            
            /**
            * Telegram
            */
            $txt   ="#validasiorderlogin #gagal"."\n";
            $txt  .="| Nama : ". @$get_anggota->nama ."\n";
            $txt  .="| No Anggota : ". @$get_anggota->noanggota ."\n";
            $txt  .="| ID Anggota : ". $request->id_anggota ."\n";
            $telegram = new Telegram($token);
            $telegram->sendMessage($chatId, $txt, 'HTML');

            $result = Notif::push(
                @$get_anggota->noanggota,
                'Validasi akun dibutuhkan' , 
                'Silahkan datang atau telp ke Koperasi Pegawai Lemigas'
            );
            
            return;
        }


        
        
        
    }
}
