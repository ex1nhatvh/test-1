<?php 

namespace App\Plugins\FurnitureManagementSystem\Includes\FMSUtil;

use Exceedone\Exment\Enums\SystemTableName;
use Exceedone\Exment\Model\CustomTable;

class FMSUtil{
	
	public function getTokenKey(){
		return "fms_api_token";
	}
	
	public function getRequestTable($furniture_type,$request_type){
		
		$table=TABLE_SOFT_FURNITURE_REQUEST_MOVE;
		
		return CustomTable::getEloquent($table);
	
	}
	
	public function getRequestTitle($furniture_type,$request_type){
		
		$title="";
		
		switch($furniture_type){
			
			case "soft":
				$title.="ソフトファニチャー/Soft Furniture";
				break;
			
			case "hard":
				$title.="ハードファニチャー/Hard Furniture";
				break;
		
		}
		
		switch($request_type){
			
			case "move":
				$title.="移動申請/Movement within Location";
				break;
			
			case "disposal":
				$title.="廃棄申請/Dispose";
				break;
			
			case "sale":
				$title.="売却申請/Resale";
				break;
			
			case "change-classification":
				$title.="区分変更申請/Change grade";
				break;
			
			case "carry-in":
				$title.="搬入申請/Move In";
				break;
			
			case "carry-out":
				$title.="搬出申請/Move Out";
				break;
			
			case "internal-move":
				$title.="拠点内移動申請/Movement within Location";
				break;
		
		}
		
		return $title;
	
	}
	
	public function getFloorRoomList($room_list){
		
		$floor_room_list=[];
		
		foreach($room_list as $room){
			
			$floor_name=$room->getValue("Floor_Name");
			
			if(!array_key_exists($floor_name,$floor_room_list))$floor_room_list[$floor_name]=[];
			
			$floor_room_list[$floor_name][]=["id"=>$room->id,"name"=>$room->getValue("Room_Numbe")];
		
		}
		
		return $floor_room_list;
	
	}
	
	public function getHardFloorRoomList($hard_furniture_list,$room_list){
		
		$floor_room_list=[];
		
		foreach($hard_furniture_list as $item){
			
			$room_id=array_get($item,'value.Room_Numbe_hard');
			
			foreach($room_list as $room){
				
				if($room->id==$room_id){
					
					$room_name=$room->getValue("HardFurniture_Room_Numbe");
					$floor_name=$room->getValue("HardFurniture_Floor_Name");
				
				}
			
			}
			
			$floor=$floor_name;
			
			if(!array_key_exists($floor,$floor_room_list))$floor_room_list[$floor]=[];
			$floor_room_list[$floor][]=[
				"id"=>$room_id,
				"name"=>$room_name,
				"HardFurniture_hard"=>array_get($item,'value.HardFurniture_hard'),
				"HardFurniture_Chair"=>array_get($item,'value.HardFurniture_Chair'),
				"HardFurniture_Peds"=>array_get($item,'value.HardFurniture_Peds'),
				"url"=>$item->getUrl()
			];
		
		}
		
		return $floor_room_list;
	
	}

}