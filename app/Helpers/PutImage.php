<?php

namespace App\Helpers;

Class PutImage{

		public static function save($Url, $ImageName){
			try {
				$SaveImage = base_path().'/public/'.$ImageName;
				$response = file_put_contents($SaveImage, file_get_contents($Url));
				// $response = file_put_contents($img, file_get_contents($url));
				return TRUE;
			} catch (Exception $e) {
				return FALSE;
				// throw New \Exception('Params not found', 500);
			}
		} 

		public static function base64($base64 = null , $name = null)
		{
			try {
				
				$img = $base64;
				$img = str_replace('data:image/png;base64,', '', $img);
	            $img = str_replace(' ', '+', $img);
	            $dataC = base64_decode($img);
	            $ImageName = $name . time().'.jpg';
	            $Url = base_path().'/public/'.$ImageName;
	            $success = file_put_contents($Url, $dataC);

	            return true;

			} catch (Exception $e) {
				return false;
			}
		}
}
