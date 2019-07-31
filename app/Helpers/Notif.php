<?php

namespace App\Helpers;
use App\Models\Anggota\NotifModel AS NM;

Class Notif { 

	public static function push($id = null , $title = null , $message = null){

		$fcmUrl = 'https://fcm.googleapis.com/fcm/send'; 

		$notification = [
			"to" => '/topics/'.$id,
			"notification" => [
				'title'		=> $title,
				'body'		=> $message,
				'sound'		=> 'default',
				'priority'		=> 'high',
				"icon" => "ic_notification",
				"show_in_foreground" => true
			],
			"data" => [
				"action" => "com.kp.lemigas",
				"data" => [
					"key" => "value"
				],
				"message" => "Messaging Topic Message!",
			],
			"priority" => "high"
		]; 

		$headers = [
			'Authorization: key=AIzaSyBedkuZeFV_0Bg84ZxCf4A3v4z-LRtPQjE',
			'Content-Type: application/json'
		];

		// insert to log
		$insert = array(
			'topic' => $id,
			'title' => $title,
			'message' => $message
		);
		NM::create($insert);
		// end to log


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$fcmUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($notification));
		$result = curl_exec($ch);
		curl_close($ch); 

		return true;
	} 
}

