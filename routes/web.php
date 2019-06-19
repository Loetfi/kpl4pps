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


$router->post('/register', 'RegisterController@register');

// push notif 
$router->get('/push-notif', 'PushNotifController@push');

// profile 
$router->post('/profile/image', 'ProfileController@imageProfile');


// riwayat
$router->post('/riwayat/order', 'RiwayatOrderController@list');

// serba usaha 
$router->get('/serba-usaha/list', 'SerbaUsahaController@list');
$router->post('/serba-usaha/detail', 'SerbaUsahaController@detail');
$router->post('/serba-usaha/submit', 'SerbaUsahaController@submit');




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

$router->post('/auth', 'AuthController@auth');
$router->get('/auth/check', 'AuthController@check');
$router->get('/auth/refresh', 'AuthController@refresh');
  // forgot password
$router->put('/auth/forgot', 'AuthController@forgot');
$router->post('/reg', 'UsersController@register');
$router->post('/otp/validate', 'UsersController@otp_validate');

  // master group
$router->group(['prefix' => 'company'], function() use($router){
  $router->get('/get', 'CompanyController@get');
});
$router->group(['prefix' => 'mst'], function() use($router){
    // get list role
  $router->get('bank', 'BankController@get');
    // get list grade
  $router->get('grade', 'GradeController@list');
    // get list role
  $router->get('role', 'RoleController@list');
    // get list domicile
  $router->get('domicile', 'DomicileController@get');
    // get list marriage
  $router->get('marriage', 'MarriageController@get');
    // get list gender
  $router->get('gender', 'GenderController@get');
    // get list religion
  $router->get('religion', 'ReligionController@get');
    // get list workflow status
  $router->get('user/status', 'WorkflowstatusController@list');
});

  // generate nik
$router->get('/profile/generate-nik', 'ProfileController@GenerateNIK');

$router->group(['middleware'=>['authorize']], function() use($router){

  // authentication
  $router->group(['prefix' => 'auth'], function() use($router){
    $router->get('credentials', 'AuthController@credentials');
  });

    // master collection
  $router->group(['prefix' => 'company'], function() use($router){
    $router->get('/auth/get', 'CompanyController@auth_get');
  });

  $router->group(['prefix'=>'user'], function() use($router){
    $router->get('list', 'UsersController@list');

          // list approval user
    $router->get('approve/list', 'UsersController@approve_list');
          // approval by HR & Koperasi
    $router->put('approve', 'UsersController@approve');
          // reject by HR & Koperasi
    $router->put('reject', 'UsersController@reject');

          // deactive by HR & Koperasi
    $router->put('deactive', 'UsersController@deactive');  

          // detail user
    $router->get('detail', 'UsersController@detail');  



  });

  $router->post('/pu', function(Illuminate\Http\Request $request, App\Helpers\BlobStorage $blob) use($router){
    $blob::data([
      'source' => 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a6/Goofy.svg/330px-Goofy.svg.png',
      'path' => 'goofy.png'
    ]);
    if(!($res=$blob::upload()))
      dd($blob::error());
    else{
              // print_r($res); die();
      return response()->json($res,200);
    }
  });

      // get address of user 
  $router->group(['prefix'=>'profile'], function() use($router){
          // address
    $router->get('get-address-by-user', 'AddressController@GetListAddressOfUser');
    $router->put('update-address-by-user', 'AddressController@UpdateAddressOfUser');
    $router->post('create-address-by-user', 'AddressController@CreateAddressOfUser');
    $router->post('delete-address-by-user', 'AddressController@DeleteAddressOfUser');

          // profile 
    $router->get('get', 'ProfileController@GetUserProfile');
    $router->put('update', 'ProfileController@update');

          // change password 
    $router->put('change-password', 'ChangePasswordController@get');

          // upload Personal Picture 
    $router->put('photo/upload', 'ProfileController@UploadPhoto');

          // change bank profile 
    $router->put('bank/update', 'ProfileController@update_bank');

          // get user profile bank 
    $router->get('bank', 'ProfileController@bank_profile');

          // get user profile document 
    $router->get('document', 'ProfileController@document_profile');

          // get user profile salary 
    $router->get('salary', 'ProfileController@salary_profile');

          // get user profile company 
    $router->get('company', 'ProfileController@company_profile');

        // fullfillment
    $router->get('fullfillment', 'ProfileController@fullfillment');

          // Document profile group
    $router->group(['prefix' => 'document'], function() use($router){
            // add user document
      $router->post('add', 'UsersDocumentController@add_document');

            // add user document
      $router->put('update', 'UsersDocumentController@update_document');

            // delete user profile document 
      $router->put('delete', 'ProfileController@delete_doc');
    });
  });

          // dashboard
  $router->group(['prefix'=>'dashboard'], function() use($router){
          // get
    $router->get('get-completed-by-sbu-hr', 'DashboardController@TotalUserCompletedBySBUHR');
    $router->get('get-completed-by-kopadmin', 'DashboardController@TotalUserCompletedByKopAdmin');
    $router->get('get-pending-by-sbu-hr', 'DashboardController@TotalUserPendingBySBUHR');
    $router->get('get-pending-by-kopadmin', 'DashboardController@TotalUserPendingByKoperasiAdmin');

  });

      // for request salary
  $router->group(['prefix'=>'salary'], function() use($router){
    $router->post('req', 'UsersSalaryController@request_salary');
    $router->get('approve/list', 'UsersSalaryController@approve_list');
    $router->put('approve', 'UsersSalaryController@approve');
    $router->put('reject', 'UsersSalaryController@reject');
  });

      // register migrated member
  $router->post('reg/migrate', 'UsersController@migrated');

      // for company
  $router->group(['prefix'=>'company'], function() use($router){
    $router->get('user', 'CompanyController@company_profile');

        // add master company
    $router->post('add', 'CompanyController@add');

        // update master company
    $router->put('edit', 'CompanyController@edit');

        // delete master company
    $router->put('delete', 'CompanyController@delete');
  });

      // firebase token
  $router->group(['prefix' => 'fcm'], function() use($router){
        // get list update
    $router->put('update', 'UsersController@token');
  });
});


// firebase token
$router->group(['prefix' => 'user-token'], function() use($router){
  // user token 
  $router->post('token-get-individu', 'UserTokenController@GetIndividuData');
  $router->post('token-get-list', 'UserTokenController@GetListData');
});


  // location 
$router->get('/all-province', 'LocationsController@GetAllProvince');
$router->get('/get-city-by-province', 'LocationsController@GetCityByProvince');
$router->get('/get-kec-by-province-city', 'LocationsController@GetKecamatanByCityAndProvince');
$router->get('/get-kel-by-province-city-kec', 'LocationsController@GetKelurahanByCityAndProvinceAndKecamatan');

  ## enhance from lutfi 

// });



