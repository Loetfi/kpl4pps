<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


// $router->group(['middleware'=>['cors']], function() use($router){

$router->get('/', function () use ($router) {
      return $router->app->version();
  // return redirect('/api/documentation');
});
$router->get('/api', function () use ($router) {
      // return $router->app->version();
  return redirect('/api/documentation');
});

$router->post('/login', 'LoginController@login');
$router->post('/login-backend', 'LoginController@backend_login');

// backend 
$router->get('/backend/tokolist', 'TokoController@listData');
$router->get('/backend/toko/detailData', 'TokoController@detailData');
$router->post('/backend/toko/approval', 'TokoController@ProsesApproval');


$router->get('/backend/beritalist', 'BeritaController@listData');



// end

$router->post('/register', 'RegisterController@register');

// push notif 
$router->get('/push-notif', 'PushNotifController@push');

// profile 
$router->post('/profile/image', 'ProfileController@imageProfile');

// riwayat
$router->get('/riwayat/order', 'RiwayatOrderController@list');
$router->get('/riwayat/order/detail', 'RiwayatOrderController@detail');

// serba usaha 
$router->get('/serba-usaha/list', 'SerbaUsahaController@list');
$router->get('/serba-usaha/detail', 'SerbaUsahaController@detail');
$router->post('/serba-usaha/submit', 'SerbaUsahaController@submit');

// gedung teknologi
$router->get('/gedung/list', 'GedungController@list');
$router->post('/gedung/submit', 'GedungController@submit');
$router->get('/gedung/detail', 'GedungController@detail');
$router->get('/gedung/history', 'GedungController@history');
$router->get('/gedung/periode-booking', 'GedungController@periodeBooking');

// notification
$router->get('/notif/list', 'NotificationController@list');


// simpan pinjam
$router->get('/simpan/content-form', 'SimpanPinjamController@getContentSimpan');
$router->get('/pinjam/content-form', 'SimpanPinjamController@getContentPinjam');
$router->post('/simpan/store', 'SimpanPinjamController@submitSimpanan');
$router->post('/pinjam/store', 'SimpanPinjamController@submitPinjaman');





// promo
$router->get('/promo', 'PromoController@data');
$router->get('/promo/list', 'PromoController@list');
$router->post('/promo/add', 'PromoController@add');
$router->post('/promo/update', 'PromoController@update');
$router->post('/promo/delete', 'PromoController@delete');
$router->get('/promo/detail', 'PromoController@detail');


// travel
$router->post('/travel/pesawat', 'TravelController@pesawat');
$router->post('/travel/hotel', 'TravelController@hotel');
$router->post('/travel/kereta', 'TravelController@kereta');
$router->post('/travel/bus', 'TravelController@bus');
$router->post('/travel/shuttle', 'TravelController@shuttle');

// pulsa / paket data
$router->post('/pulsa', 'PulsaPaketController@pulsa');
$router->post('/paketdata', 'PulsaPaketController@paketdata');
$router->post('/listrik-token', 'ListrikController@token');
$router->post('/listrik-tagihan', 'ListrikController@tagihan');

// layanan menu beranda 
$router->get('/all-layanan', 'LayananController@all');

// master
$router->get('/master/agama', 'AgamaController@data');
$router->get('/master/bandara', 'PesawatController@bandara');
$router->get('/master/kota', 'KotaController@kota');
$router->get('/master/stasiun', 'StasiunController@data');


// berita 
$router->get('/berita', 'BeritaController@data');
$router->get('/berita/detail', 'BeritaController@detail');


// toko 
$router->get('/toko/list', 'TokoController@data');
$router->get('/toko/detail', 'TokoController@detail');
$router->get('/toko/related', 'TokoController@related');
$router->post('/toko/buy', 'TokoController@buy');
$router->get('/toko/searching', 'TokoController@searching');
$router->get('/toko/list_kategori', 'TokoController@list_kategori');
$router->get('/toko/pilih_kategori', 'TokoController@pilih_kategori');


// data 
$router->get('/data/order_pesawat', 'DTOrderPesawatController@getData');

$router->get('/data/orderlist', 'DTOrderController@getData');


  ## enhance from lutfi 

// });



