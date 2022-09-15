<?php 
namespace App\Plugins\FurnitureManagementSystem\Includes;

use Encore\Admin\Widgets\Box;
use Exceedone\Exment\Model\CustomTable;
use Exceedone\Exment\Model\File as ExmentFile;
use Exceedone\Exment\Enums\SystemTableName;
use Exceedone\Exment\Enums\FileType;
use Exceedone\Exment\Model\Workflow;
use Exceedone\Exment\Model\WorkflowAction;
use Exceedone\Exment\Model\WorkflowStatus;
use Exceedone\Exment\Model\WorkflowValue;
use Exceedone\Exment\Notifications\MailSender;
use Exceedone\Exment\Enums\MailKeyName;

if(!class_exists('FMSRequest')){
	
	final class FMSRequest{
		
		protected static $plugin=null;

		const SUBMIT_SAVE_WORKFLOW = 0;
		const SUBMIT_NOT_SAVE_WORKFLOW = 1;
		
		public static function setPlugin($plugin){
			
			self::$plugin=$plugin;
		
		}
		
		public static function top(){
			
			return new Box("Soft Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::request/top',
								['title'=>"各種申請フォーム",
								 'routeUri'=>admin_url(self::$plugin->getRouteUri('request')),
								]
							   )
						  );
		
		}
		
		
		public static function move($furniture_type,$request_type,$routeName,$request){
			
			$property_option=["0"=>""];
			$room_option=["0"=>""];
			
			self::getCommonOptions($users,$user_option,$noti_user_option,$priority_option);
			
			if($furniture_type == "ather") {
				$set_type = "soft";
			}
			else {
				$set_type = $furniture_type;
			}
			
			$request_model=self::getTable($set_type,$request_type)->getValueModel();
			
			self::getOrganizations($organizations,$organization_option);
			
			$time_option[0] = "";
			$time_option[1] = "平日日中";
			$time_option[2] = "平日夜間";
			$time_option[3] = "週末日中";
			
			if(array_get($request,"select_type")=="common"){
				
				$request['value']['Property']=intval(array_get($request,"select_property"));
				$request['value']['Room_Number_move_from']=intval(array_get($request,"select_room"));
			
			}
			elseif(array_get($request,"select_type")=="move_to"){
				
				$request['value']['PropertyColumn_move_to']=intval(array_get($request,"select_property"));
				$request['value']['Room_Number_move_to']=intval(array_get($request,"select_room"));
			
			}
			
			$property_id=array_get($request,"value.Property");
			
			if($property_id){
				
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
				$request['value']['Address']=$property->getValue('address');
				$request['value']['Conditions']=$property->getValue('Carry-inCarry-outconditions');
			
			}
			
			$property_id=array_get($request,"value.PropertyColumn_move_to");
			
			if($property_id){
				
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
			
			}
			
			if(array_get($request,"value.Room_Number_move_from")){
				
				$room_id=array_get($request,"value.Room_Number_move_from");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_move_from']=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
					$request['value']['Floor_Name_move_from']=$room->getValue('HardFurniture_Floor_Name');
				
				}
				else {
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_move_from']=$room->getValue('Floor_Name');
					
				}
			
			}
			
			if(array_get($request,"value.Room_Number_move_to")){
				
				$room_id=array_get($request,"value.Room_Number_move_to");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_move_to']=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
				
				}
				else {
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_move_to']=$room->getValue('Floor_Name');
					
				}
			
			}
			
			$soft_furniture_list=[];
			$hard_furniture=null;
			
			if(array_get($request,"value.Room_Number_move_from")){
				
				if($furniture_type=="soft"){
					
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where('value->Room_Numbe',array_get($request,"value.Room_Number_move_from"))->get();
				
				}
				elseif($furniture_type=="hard"){
					
					$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->HardFurniture_Room_Numbe',array_get($request,"value.Room_Number_move_from"));
					
					if($hard_furniture){
						
						$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($request['value']['Room_Number_move_from']);
						$request["value"]["Desk"]=isset($request["value"]["Desk"])?$request["value"]["Desk"]:$hard_furniture->getValue('HardFurniture_hard');
						$request["value"]["Chair"]=isset($request["value"]["Chair"])?$request["value"]["Chair"]:$hard_furniture->getValue('HardFurniture_Chair');
						$request["value"]["Peds"]=isset($request["value"]["Peds"])?$request["value"]["Peds"]:$hard_furniture->getValue('HardFurniture_Peds');
						$request["value"]["DeskDefective"]=isset($request["value"]["DeskDefective"])?$request["value"]["DeskDefective"]:$hard_furniture->getValue('HardFurniture_Desk_Defective');
						$request["value"]["ChairDefective"]=isset($request["value"]["ChairDefective"])?$request["value"]["ChairDefective"]:$hard_furniture->getValue('HardFurniture_Chair_Defective');
						$request["value"]["PedsDefective"]=isset($request["value"]["PedsDefective"])?$request["value"]["PedsDefective"]:$hard_furniture->getValue('HardFurniture_Peds_Defective');
					
					}
				
				}
				else {
					
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where('value->Room_Numbe',array_get($request,"value.Room_Number_move_from"))->get();
					
				}
			
			}
			
			$dialog_text=self::getDialogText($furniture_type,$request_type);
			
			return new Box("Request Form",view('exment_furniture_management_system::request/move',
											   ['title'=>"申請内容を入力してください",
												'routeUri'=>admin_url(self::$plugin->getRouteUri('request')),
												'property_option'=>$property_option,
												'priority_option'=>$priority_option,
												'user_option'=>$user_option,
												'noti_user_option'=>$noti_user_option,
												'room_option'=>$room_option,
												'organization_option'=>$organization_option,
												'time_option'=>$time_option,
												'soft_furniture_list'=>$soft_furniture_list,
												'hard_furniture'=>$hard_furniture,
												'request_model'=>$request_model,
												'furniture_type'=>$furniture_type,
												'request_type'=>$request_type,
												'route_name'=>$routeName,
												'request'=>$request,
												'dialog_text'=>$dialog_text,
											   ]
											  )
						  );
		
		}
		
		public static function disposal($furniture_type,$request_type,$routeName,$request){
			
			$property_option=["0"=>""];
			$room_option=["0"=>""];
			
			self::getCommonOptions($users,$user_option,$noti_user_option,$priority_option);
			
			if($furniture_type == "ather") {
				$set_type = "soft";
			}
			else {
				$set_type = $furniture_type;
			}
			
			$request_model=self::getTable($set_type,$request_type)->getValueModel();
			self::getOrganizations($organizations,$organization_option);
			
			$time_option[0] = "";
			$time_option[1] = "平日日中";
			$time_option[2] = "平日夜間";
			$time_option[3] = "週末日中";
			
			if(array_get($request,"select_type")=="common"){
				
				$request['value']['Property']=intval(array_get($request,"select_property"));
				$request['value']['Room_Number_Disposal']=intval(array_get($request,"select_room"));
			
			}
			
			if(array_get($request,"value.Property")){
				
				$property_id=array_get($request,"value.Property");
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
				$request['value']['Address']=$property->getValue('address');
				$request['value']['Conditions']=$property->getValue('Carry-inCarry-outconditions');
			
			}
			
			if(array_get($request,"value.Room_Number_Disposal")){
				
				$room_id=array_get($request,"value.Room_Number_Disposal");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_Disposal']=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
					$request['value']['Floor_Name_Disposal']=$room->getValue('HardFurniture_Floor_Name');
				
				}
				else {
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_Disposal']=$room->getValue('Floor_Name');
				}
			
			}
			
			$soft_furniture_list=[];
			$hard_furniture=null;
			
			if(array_get($request,"value.Room_Number_Disposal")){
				
				if($furniture_type=="soft"){
					
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where(function($query){$query->whereNull('value->SoftFurniture_Status')->orWhere('value->SoftFurniture_Status','<>',"廃棄");})->where('value->Room_Numbe',array_get($request,"value.Room_Number_Disposal"))->get();
				
				}
				elseif($furniture_type=="hard"){
					
					$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->HardFurniture_Room_Numbe',array_get($request,"value.Room_Number_Disposal"));
					
					if($hard_furniture){
						
						$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($request['value']['Room_Number_Disposal']);
						$request["value"]["Desk"]=isset($request["value"]["Desk"])?$request["value"]["Desk"]:$hard_furniture->getValue('HardFurniture_hard');
						$request["value"]["Chair"]=isset($request["value"]["Chair"])?$request["value"]["Chair"]:$hard_furniture->getValue('HardFurniture_Chair');
						$request["value"]["Peds"]=isset($request["value"]["Peds"])?$request["value"]["Peds"]:$hard_furniture->getValue('HardFurniture_Peds');
						$request["value"]["DeskDefective"]=isset($request["value"]["DeskDefective"])?$request["value"]["DeskDefective"]:$hard_furniture->getValue('HardFurniture_Desk_Defective');
						$request["value"]["ChairDefective"]=isset($request["value"]["ChairDefective"])?$request["value"]["ChairDefective"]:$hard_furniture->getValue('HardFurniture_Chair_Defective');
						$request["value"]["PedsDefective"]=isset($request["value"]["PedsDefective"])?$request["value"]["PedsDefective"]:$hard_furniture->getValue('HardFurniture_Peds_Defective');
					
					}
				
				}
				else {
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where(function($query){$query->whereNull('value->SoftFurniture_Status')->orWhere('value->SoftFurniture_Status','<>',"廃棄");})->where('value->Room_Numbe',array_get($request,"value.Room_Number_Disposal"))->get();
				}
			
			}
			
			$dialog_text=self::getDialogText($furniture_type,$request_type);
			
			return new Box("Request Form",view('exment_furniture_management_system::request/disposal',
											   ['title'=>"申請内容を入力してください",
												'routeUri'=>admin_url(self::$plugin->getRouteUri('request')),
												'property_option'=>$property_option,
												'priority_option'=>$priority_option,
												'user_option'=>$user_option,
												'noti_user_option'=>$noti_user_option,
												'room_option'=>$room_option,
												'organization_option'=>$organization_option,
												'time_option'=>$time_option,
												'soft_furniture_list'=>$soft_furniture_list,
												'hard_furniture'=>$hard_furniture,
												'request_model'=>$request_model,
												'furniture_type'=>$furniture_type,
												'request_type'=>$request_type,
												'route_name'=>$routeName,
												'request'=>$request,
												'dialog_text'=>$dialog_text,
											   ]
											  )
						  );
		
		}
		
		public static function sale($furniture_type,$request_type,$routeName,$request){
			
			$property_option=["0"=>""];
			$room_option=["0"=>""];
			self::getCommonOptions($users,$user_option,$noti_user_option,$priority_option);
			$request_model=self::getTable($furniture_type,$request_type)->getValueModel();
			self::getOrganizations($organizations,$organization_option);
			
			$time_option[0] = "";
			$time_option[1] = "平日日中";
			$time_option[2] = "平日夜間";
			$time_option[3] = "週末日中";
				
			
			
			if(array_get($request,"select_type")=="common"){
				
				$request['value']['Property']=intval(array_get($request,"select_property"));
				$request['value']['Room_Number_Sale']=intval(array_get($request,"select_room"));
			
			}
			
			if(array_get($request,"value.Property")){
				
				$property_id=array_get($request,"value.Property");
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
				$request['value']['Address']=$property->getValue('address');
				$request['value']['Conditions']=$property->getValue('Carry-inCarry-outconditions');
			
			}
			
			if(array_get($request,"value.Room_Number_Sale")){
				
				$room_id=array_get($request,"value.Room_Number_Sale");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_Sale']=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
					$request['value']['Floor_Name_Sale']=$room->getValue('HardFurniture_Floor_Name');
				
				}
			
			}
			
			$soft_furniture_list=[];
			$hard_furniture=null;
			
			if(array_get($request,"value.Room_Number_Sale")){
				
				if($furniture_type=="soft"){
					
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where(function($query){$query->whereNull('value->SoftFurniture_Status')->orWhere('value->SoftFurniture_Status','<>',"売却");})->where('value->Room_Numbe',array_get($request,"value.Room_Number_Sale"))->get();
				
				}
				elseif($furniture_type=="hard"){
					
					$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->HardFurniture_Room_Numbe',array_get($request,"value.Room_Number_Sale"));
					
					if($hard_furniture){
						
						$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($request['value']['Room_Number_Sale']);
						$request["value"]["Desk"]=isset($request["value"]["Desk"])?$request["value"]["Desk"]:$hard_furniture->getValue('HardFurniture_hard');
						$request["value"]["Chair"]=isset($request["value"]["Chair"])?$request["value"]["Chair"]:$hard_furniture->getValue('HardFurniture_Chair');
						$request["value"]["Peds"]=isset($request["value"]["Peds"])?$request["value"]["Peds"]:$hard_furniture->getValue('HardFurniture_Peds');
						$request["value"]["DeskDefective"]=isset($request["value"]["DeskDefective"])?$request["value"]["DeskDefective"]:$hard_furniture->getValue('HardFurniture_Desk_Defective');
						$request["value"]["ChairDefective"]=isset($request["value"]["ChairDefective"])?$request["value"]["ChairDefective"]:$hard_furniture->getValue('HardFurniture_Chair_Defective');
						$request["value"]["PedsDefective"]=isset($request["value"]["PedsDefective"])?$request["value"]["PedsDefective"]:$hard_furniture->getValue('HardFurniture_Peds_Defective');
					
					}
				
				}
			
			}
			
			$dialog_text=self::getDialogText($furniture_type,$request_type);
			
			return new Box("Request Form",view('exment_furniture_management_system::request/sale',
											   ['title'=>"申請内容を入力してください",
												'routeUri'=>admin_url(self::$plugin->getRouteUri('request')),
												'property_option'=>$property_option,
												'priority_option'=>$priority_option,
												'user_option'=>$user_option,
												'noti_user_option'=>$noti_user_option,
												'room_option'=>$room_option,
												'organization_option'=>$organization_option,
												'time_option'=>$time_option,
												'soft_furniture_list'=>$soft_furniture_list,
												'hard_furniture'=>$hard_furniture,
												'request_model'=>$request_model,
												'furniture_type'=>$furniture_type,
												'request_type'=>$request_type,
												'route_name'=>$routeName,
												'request'=>$request,
												'dialog_text'=>$dialog_text,
											   ]
											  )
						  );
		
		}
		
		public static function changeClassification($furniture_type,$request_type,$routeName,$request){
			
			$property_option=["0"=>""];
			$room_option=["0"=>""];
			self::getCommonOptions($users,$user_option,$noti_user_option,$priority_option);
			$request_model=self::getTable($furniture_type,$request_type)->getValueModel();
			self::getOrganizations($organizations,$organization_option);
			
			if(array_get($request,"select_type")=="common"){
				
				$request['value']['Property']=intval(array_get($request,"select_property"));
				$request['value']['Room_Number_Change']=intval(array_get($request,"select_room"));
			
			}
			
			if(array_get($request,"value.Property")){
				
				$property_id=array_get($request,"value.Property");
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
				$request['value']['Address']=$property->getValue('address');
				$request['value']['Conditions']=$property->getValue('Carry-inCarry-outconditions');
			
			}
			
			if(array_get($request,"value.Room_Number_Change")){
				
				$room_id=array_get($request,"value.Room_Number_Change");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_Change']=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
					$request['value']['Floor_Name_Change']=$room->getValue('HardFurniture_Floor_Name');
				
				}
			
			}
			
			$soft_furniture_list=[];
			$hard_furniture=null;
			
			if(array_get($request,"value.Room_Number_Change")){
				
				if($furniture_type=="soft"){
					
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where('value->Room_Numbe',array_get($request,"value.Room_Number_Change"))->get();
				
				}
				elseif($furniture_type=="hard"){
					
					$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->HardFurniture_Room_Numbe',array_get($request,"value.Room_Number_Change"));
					
					if($hard_furniture){
						
						$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($request['value']['Room_Number_Change']);
						$request["value"]["DeskDefective"]=isset($request["value"]["DeskDefective"])?$request["value"]["DeskDefective"]:$hard_furniture->getValue('HardFurniture_Desk_Defective');
						$request["value"]["ChairDefective"]=isset($request["value"]["ChairDefective"])?$request["value"]["ChairDefective"]:$hard_furniture->getValue('HardFurniture_Chair_Defective');
						$request["value"]["PedsDefective"]=isset($request["value"]["PedsDefective"])?$request["value"]["PedsDefective"]:$hard_furniture->getValue('HardFurniture_Peds_Defective');
					
					}
				
				}
			
			}
			
			$dialog_text=self::getDialogText($furniture_type,$request_type);
			
			return new Box("Request Form",view('exment_furniture_management_system::request/change-classification',
											   ['title'=>"申請内容を入力してください",
												'routeUri'=>admin_url(self::$plugin->getRouteUri('request')),
												'property_option'=>$property_option,
												'priority_option'=>$priority_option,
												'user_option'=>$user_option,
												'noti_user_option'=>$noti_user_option,
												'room_option'=>$room_option,
												'soft_furniture_list'=>$soft_furniture_list,
												'hard_furniture'=>$hard_furniture,
												'request_model'=>$request_model,
												'furniture_type'=>$furniture_type,
												'request_type'=>$request_type,
												'route_name'=>$routeName,
												'request'=>$request,
												'dialog_text'=>$dialog_text,
											   ]
											  )
						  );
		
		}
		
		public static function carryIn($furniture_type,$request_type,$routeName,$request){
			
			$property_option=["0"=>""];
			$room_option=["0"=>""];
			$floor_option=["0"=>""];
			self::getCommonOptions($users,$user_option,$noti_user_option,$priority_option);
			$request_model=self::getTable($furniture_type,$request_type)->getValueModel();
			self::getOrganizations($organizations,$organization_option);
			
			$time_option[0] = "";
			$time_option[1] = "平日日中";
			$time_option[2] = "平日夜間";
			$time_option[3] = "週末日中";
			
			if(array_get($request,"select_type")=="common"){
				
				$request['value']['Property']=intval(array_get($request,"select_property"));
				$request['value']['Room_Number_move_from']=intval(array_get($request,"select_room"));
				
				$request['value']['PropertyColumn_Carryin']=intval(array_get($request,"select_property"));
				$request['value']['Room_Numbe_Carryin']=intval(array_get($request,"select_room"));
			
			}
			elseif(array_get($request,"select_type")=="carry_in"){
				
				$request['value']['PropertyColumn_Carryin']=intval(array_get($request,"select_property"));
				$request['value']['Room_Numbe_Carryin']=intval(array_get($request,"select_room"));
				
				
			
			}
			
			$property_id=array_get($request,"value.Property");
			
			if($property_id){
				
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
				$request['value']['Address']=$property->getValue('address');
				$request['value']['Conditions']=$property->getValue('Carry-inCarry-outconditions');
				
				
			
			}
			
			$property_id=array_get($request,"value.PropertyColumn_Carryin");
			
			if($property_id){
				
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
			
			}
			
			if(array_get($request,"value.Room_Numbe_Carryin")){
				
				$room_id=array_get($request,"value.Room_Numbe_Carryin");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_Carryin']=$room_id;
					$floor_option[$room_id]=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
					$request['value']['Floor_Name_Carryin']=$room_id;
					$floor_option[$room_id]=$room->getValue('HardFurniture_Floor_Name');
				
				}
			
			}
			
			$soft_furniture_list=[];
			$hard_furniture=null;
			
			if(array_get($request,"value.Room_Numbe_Carryin")){
				
				if($furniture_type=="soft"){
					
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where('value->Room_Numbe',array_get($request,"value.Room_Numbe_Carryin"))->get();
				
				}
				elseif($furniture_type=="hard"){
					
					$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->HardFurniture_Room_Numbe',array_get($request,"value.Room_Numbe_Carryin"));
					
					if($hard_furniture){
						
						$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($request['value']['Room_Numbe_Carryin']);
						$request["value"]["Desk"]=isset($request["value"]["Desk"])?$request["value"]["Desk"]:$hard_furniture->getValue('HardFurniture_hard');
						$request["value"]["Chair"]=isset($request["value"]["Chair"])?$request["value"]["Chair"]:$hard_furniture->getValue('HardFurniture_Chair');
						$request["value"]["Peds"]=isset($request["value"]["Peds"])?$request["value"]["Peds"]:$hard_furniture->getValue('HardFurniture_Peds');
					
					}
				
				}
			
			}
			
			$dialog_text=self::getDialogText($furniture_type,$request_type);
			
			return new Box("Request Form",view('exment_furniture_management_system::request/carry-in',
											   ['title'=>"申請内容を入力してください",
												'routeUri'=>admin_url(self::$plugin->getRouteUri('request')),
												'property_option'=>$property_option,
												'priority_option'=>$priority_option,
												'user_option'=>$user_option,
												'noti_user_option'=>$noti_user_option,
												'room_option'=>$room_option,
												'floor_option'=>$floor_option,
												'organization_option'=>$organization_option,
												'time_option'=>$time_option,
												'soft_furniture_list'=>$soft_furniture_list,
												'hard_furniture'=>$hard_furniture,
												'request_model'=>$request_model,
												'furniture_type'=>$furniture_type,
												'request_type'=>$request_type,
												'route_name'=>$routeName,
												'request'=>$request,'dialog_text'=>$dialog_text,
											   ]
											  )
						  );
		
		}
		
		public static function carryOut($furniture_type,$request_type,$routeName,$request){
			
			$property_option=["0"=>""];
			$room_option=["0"=>""];
			$floor_option=["0"=>""];
			self::getCommonOptions($users,$user_option,$noti_user_option,$priority_option);
			$request_model=self::getTable($furniture_type,$request_type)->getValueModel();
			self::getOrganizations($organizations,$organization_option);
			
			$time_option[0] = "";
			$time_option[1] = "平日日中";
			$time_option[2] = "平日夜間";
			$time_option[3] = "週末日中";
			
			if(array_get($request,"select_type")=="common"){
				
				$request['value']['Property']=intval(array_get($request,"select_property"));
				$request['value']['Room_Number_move_from']=intval(array_get($request,"select_room"));
				
				$request['value']['PropertyColumn_Carryout']=intval(array_get($request,"select_property"));
				$request['value']['Room_Numbe_Carryout']=intval(array_get($request,"select_room"));
				$request['value']['Floor_Name_Carryout_from']=intval(array_get($request,"select_room"));
			
			}
			elseif(array_get($request,"select_type")=="carry_out"){
				
				$request['value']['PropertyColumn_Carryout']=intval(array_get($request,"select_property"));
				$request['value']['Room_Numbe_Carryout']=intval(array_get($request,"select_room"));
				$request['value']['Floor_Name_Carryout_from']=intval(array_get($request,"select_room"));
			
			}
			
			$property_id=array_get($request,"value.Property");
			
			if($property_id){
				
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
				$request['value']['Address']=$property->getValue('address');
				$request['value']['Conditions']=$property->getValue('Carry-inCarry-outconditions');
			
			}
			
			$property_id=array_get($request,"value.PropertyColumn_Carryout");
			
			if($property_id){
				
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
			
			}
			
			if(array_get($request,"value.Room_Numbe_Carryout")){
				
				$room_id=array_get($request,"value.Room_Numbe_Carryout");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_move_from']=$room_id;
					$floor_option[$room_id]=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
					$request['value']['Floor_Name_move_from']=$room_id;
					$floor_option[$room_id]=$room->getValue('HardFurniture_Floor_Name');
				
				}
			
			}
			
			$soft_furniture_list=[];
			$hard_furniture=null;
			
			if(array_get($request,"value.Room_Numbe_Carryout")){
				
				if($furniture_type=="soft"){
					
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where('value->Room_Numbe',array_get($request,"value.Room_Numbe_Carryout"))->get();
				
				}
				elseif($furniture_type=="hard"){
					
					$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->HardFurniture_Room_Numbe',array_get($request,"value.Room_Numbe_Carryout"));
					
					if($hard_furniture){
						
						$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($request['value']['Room_Numbe_Carryout']);
						$request["value"]["Desk"]=isset($request["value"]["Desk"])?$request["value"]["Desk"]:$hard_furniture->getValue('HardFurniture_hard');
						$request["value"]["Chair"]=isset($request["value"]["Chair"])?$request["value"]["Chair"]:$hard_furniture->getValue('HardFurniture_Chair');
						$request["value"]["Peds"]=isset($request["value"]["Peds"])?$request["value"]["Peds"]:$hard_furniture->getValue('HardFurniture_Peds');
						$request["value"]["DeskDefective"]=isset($request["value"]["DeskDefective"])?$request["value"]["DeskDefective"]:$hard_furniture->getValue('HardFurniture_Desk_Defective');
						$request["value"]["ChairDefective"]=isset($request["value"]["ChairDefective"])?$request["value"]["ChairDefective"]:$hard_furniture->getValue('HardFurniture_Chair_Defective');
						$request["value"]["PedsDefective"]=isset($request["value"]["PedsDefective"])?$request["value"]["PedsDefective"]:$hard_furniture->getValue('HardFurniture_Peds_Defective');
					
					}
				
				}
			
			}
			
			$dialog_text=self::getDialogText($furniture_type,$request_type);
			
			return new Box("Request Form",view('exment_furniture_management_system::request/carry-out',
											   ['title'=>"申請内容を入力してください",
												'routeUri'=>admin_url(self::$plugin->getRouteUri('request')),
												'property_option'=>$property_option,
												'priority_option'=>$priority_option,
												'user_option'=>$user_option,
												'noti_user_option'=>$noti_user_option,
												'room_option'=>$room_option,
												'floor_option'=>$floor_option,
												'organization_option'=>$organization_option,
												'time_option'=>$time_option,
												'soft_furniture_list'=>$soft_furniture_list,
												'hard_furniture'=>$hard_furniture,
												'request_model'=>$request_model,
												'furniture_type'=>$furniture_type,
												'request_type'=>$request_type,
												'route_name'=>$routeName,
												'request'=>$request,
												'dialog_text'=>$dialog_text,
											   ]
											  )
						  );
		
		}
		
		public static function internalMove($furniture_type,$request_type,$routeName,$request){
			
			$property_option=["0"=>""];
			$room_option=["0"=>""];
			$floor_option=["0"=>""];
			self::getCommonOptions($users,$user_option,$noti_user_option,$priority_option);
			$request_model=self::getTable($furniture_type,$request_type)->getValueModel();
			self::getOrganizations($organizations,$organization_option);
			
			$time_option[0] = "";
			$time_option[1] = "平日日中";
			$time_option[2] = "平日夜間";
			$time_option[3] = "週末日中";
			
			if(array_get($request,"select_type")=="common"){
				
				$request['value']['Property']=intval(array_get($request,"select_property"));
				$request['value']['Room_Numbe_internalmove']=intval(array_get($request,"select_room"));
				$request['value']["Room_Numbe_internalmove_to"]=null;
				$request['value']['Floor_Name_internalmove_to']=null;
			
			}
			elseif(array_get($request,"select_type")=="internal_move_to"){
				
				$request['value']['Room_Numbe_internalmove_to']=intval(array_get($request,"select_room"));
			
			}
			
			$property_id=array_get($request,"value.Property");
			
			if($property_id){
				
				$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
				$property_option[$property_id]=$property->getValue('PropertyColumn');
				$request['value']['Address']=$property->getValue('address');
				$request['value']['Conditions']=$property->getValue('Carry-inCarry-outconditions');
			
			}
			
			if(array_get($request,"value.Room_Numbe_internalmove")){
				
				$room_id=array_get($request,"value.Room_Numbe_internalmove");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_internalmove_from']=$room_id;
					$floor_option[$room_id]=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
					$request['value']['Floor_Name_internalmove_from']=$room_id;
					$floor_option[$room_id]=$room->getValue('HardFurniture_Floor_Name');
				
				}
			
			}
			
			if(array_get($request,"value.Room_Numbe_internalmove_to")){
				
				$room_id=array_get($request,"value.Room_Numbe_internalmove_to");
				
				if($furniture_type=="soft"){
					
					$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('Room_Numbe');
					$request['value']['Floor_Name_internalmove_to']=$room_id;
					$floor_option[$room_id]=$room->getValue('Floor_Name');
				
				}
				elseif($furniture_type=="hard"){
					
					$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
					$room_option[$room_id]=$room->getValue('HardFurniture_Room_Numbe');
					$request['value']['Floor_Name_internalmove_to']=$room_id;
					$floor_option[$room_id]=$room->getValue('HardFurniture_Floor_Name');
				
				}
			
			}
			
			$soft_furniture_list=[];
			$hard_furniture=null;
			
			if(array_get($request,"value.Room_Numbe_internalmove")){
				
				if($furniture_type=="soft"){
					
					$soft_furniture_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where('value->Room_Numbe',array_get($request,"value.Room_Numbe_internalmove"))->get();
				
				}
				elseif($furniture_type=="hard"){
					
					$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->HardFurniture_Room_Numbe',array_get($request,"value.Room_Numbe_internalmove"));
					
					if($hard_furniture){
						
						$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($request['value']['Room_Numbe_internalmove']);
						$request["value"]["Desk"]=isset($request["value"]["Desk"])?$request["value"]["Desk"]:$hard_furniture->getValue('HardFurniture_hard');
						$request["value"]["Chair"]=isset($request["value"]["Chair"])?$request["value"]["Chair"]:$hard_furniture->getValue('HardFurniture_Chair');
						$request["value"]["Peds"]=isset($request["value"]["Peds"])?$request["value"]["Peds"]:$hard_furniture->getValue('HardFurniture_Peds');
						$request["value"]["DeskDefective"]=isset($request["value"]["DeskDefective"])?$request["value"]["DeskDefective"]:$hard_furniture->getValue('HardFurniture_Desk_Defective');
						$request["value"]["ChairDefective"]=isset($request["value"]["ChairDefective"])?$request["value"]["ChairDefective"]:$hard_furniture->getValue('HardFurniture_Chair_Defective');
						$request["value"]["PedsDefective"]=isset($request["value"]["PedsDefective"])?$request["value"]["PedsDefective"]:$hard_furniture->getValue('HardFurniture_Peds_Defective');
					
					}
				
				}
			
			}
			
			$dialog_text=self::getDialogText($furniture_type,$request_type);
			
			return new Box("Request Form",view('exment_furniture_management_system::request/internal-move',
											   ['title'=>"申請内容を入力してください",
												'routeUri'=>admin_url(self::$plugin->getRouteUri('request')),
												'property_option'=>$property_option,
												'priority_option'=>$priority_option,
												'user_option'=>$user_option,
												'noti_user_option'=>$noti_user_option,
												'room_option'=>$room_option,
												'floor_option'=>$floor_option,
												'organization_option'=>$organization_option,
												'time_option'=>$time_option,
												'soft_furniture_list'=>$soft_furniture_list,
												'hard_furniture'=>$hard_furniture,
												'request_model'=>$request_model,
												'furniture_type'=>$furniture_type,
												'request_type'=>$request_type,
												'route_name'=>$routeName,
												'request'=>$request,
												'dialog_text'=>$dialog_text,
											   ]
											  )
						  );
		
		}
		
		public static function save($furniture_type,$request_type,$request){
			
			$value=array_get($request,"value")??[];
			$table=self::getTable($furniture_type,$request_type);
			
			if(!$table)return;
			
			$request_model=$table->getValueModel()->create();
			$request_model->setValue('Title',array_get($value,"Title"));
			$request_model->setValue('Property',array_get($value,"Property"));
			$request_model->setValue('Address',array_get($value,"Address"));
			$request_model->setValue('Conditions',array_get($value,"Conditions"));
			$request_model->setValue('Priority',array_get($value,"Priority"));
			$request_model->setValue('Manager',array_get($value,"Manager"));
			$request_model->setValue('FieldPersonnel',array_get($value,"FieldPersonnel"));
			$request_model->setValue('FieldPersonnelTel',array_get($value,"FieldPersonnelTel"));
			$request_model->setValue('Comment',array_get($value,"Comment"));
			$request_model->setValue('sendMail_user', $request['sendMail_user']);
			
			$user = CustomTable::getEloquent('user');
			foreach($request['sendMail_user'] as $param) {
				if(!is_null($param)) {
					$value = $user->getValueModel($param);

					$sender = MailSender::make('workflow_notify_cc', $value->getValue('email'))
						->prms([
							'system.site_name' => 'Inventory Management System'
						]);
					$sender->sendMail();
				}
			}
			
			if($request_type=="move"){
				
				$request_model->setValue('Room_Number_move_from',array_get($value,"Room_Number_move_from"));
				$request_model->setValue('Floor_Name_move_from',array_get($value,"Floor_Name_move_from"));
				$request_model->setValue('PropertyColumn_move_to',array_get($value,"PropertyColumn_move_to"));
				$request_model->setValue('Room_Number_move_to',array_get($value,"Room_Number_move_to"));
				$request_model->setValue('Floor_Name_move_to',array_get($value,"Floor_Name_move_to"));
				$request_model->setValue('organization_name_move_vender',array_get($value,"organization_name_move_vender"));
				$request_model->setValue('Preferred_date_move',array_get($value,"Preferred_date_move"));
				$request_model->setValue('Preferred_date_move2',array_get($value,"Preferred_date_move2"));
				$request_model->setValue('Preferred_time_move',array_get($value,"Preferred_time_move"));
				$request_model->setValue('Candidate_date_move1',array_get($value,"Candidate_date_move1"));
				$request_model->setValue('Candidate_date_move2',array_get($value,"Candidate_date_move2"));
				$request_model->setValue('Candidate_date_move3',array_get($value,"Candidate_date_move3"));
				$column_name='addfile_drawing_move';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$column_name='addfile_operating_move';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$request_model->setValue('memo_move',array_get($value,"memo_move"));
				$request_model->setValue('Estimate_only',array_get($value,"Estimate_only"));
				$request_model->setValue('Enter_the_amount',array_get($value,"Enter_the_amount"));
				$column_name='addfile_estimate_move';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$request_model->setValue('PONumber_move',array_get($value,"PONumber_move"));
			
			}
			elseif($request_type=="disposal"){
				
				$request_model->setValue('Room_Number_Disposal',array_get($value,"Room_Number_Disposal"));
				$request_model->setValue('Floor_Name_Disposal',array_get($value,"Floor_Name_Disposal"));
				$request_model->setValue('organization_Disposal_vender',array_get($value,"organization_Disposal_vender"));
				$request_model->setValue('Preferred_date_Disposal',array_get($value,"Preferred_date_Disposal"));
				$request_model->setValue('Preferred_date_Disposal2',array_get($value,"Preferred_date_Disposal2"));
				$request_model->setValue('Preferred_time_Disposal',array_get($value,"Preferred_time_Disposal"));
				$request_model->setValue('Candidate_date_Disposal1',array_get($value,"Candidate_date_Disposal1"));
				$request_model->setValue('Candidate_date_Disposal2',array_get($value,"Candidate_date_Disposal2"));
				$request_model->setValue('Candidate_date_Disposal3',array_get($value,"Candidate_date_Disposal3"));
				$column_name='addfile_drawing_Disposal';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				
				$column_name='addfile_certificate_Disposal';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$column_name='addfile_manifest_Disposal';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$request_model->setValue('memo_Disposal',array_get($value,"memo_Disposal"));
				$request_model->setValue('Estimate_only',array_get($value,"Estimate_only"));
				$request_model->setValue('Enter_the_amount',array_get($value,"Enter_the_amount"));
				$request_model->setValue('PONumber_disposal',array_get($value,"PONumber_disposal"));
			
			}
			elseif($request_type=="sale"){
				
				$request_model->setValue('Room_Number_Sale',array_get($value,"Room_Number_Sale"));
				$request_model->setValue('Floor_Name_Sale',array_get($value,"Floor_Name_Sale"));
				$request_model->setValue('organization_Sale_vender',array_get($value,"organization_Sale_vender"));
				$request_model->setValue('Preferred_date_Sale',array_get($value,"Preferred_date_Sale"));
				$request_model->setValue('Preferred_date_Sale2',array_get($value,"Preferred_date_Sale2"));
				$request_model->setValue('Preferred_time_Sale',array_get($value,"Preferred_time_Sale"));
				$request_model->setValue('Candidate_date_Sale1',array_get($value,"Candidate_date_Sale1"));
				$request_model->setValue('Candidate_date_Sale2',array_get($value,"Candidate_date_Sale2"));
				$request_model->setValue('Candidate_date_Sale3',array_get($value,"Candidate_date_Sale3"));
				$column_name='addfile_drawing_Sale';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$column_name='addfile_operating_Sale';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$column_name='addfile_receipt_Sale';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$request_model->setValue('memo_Sale',array_get($value,"memo_Sale"));
				$request_model->setValue('Estimate_only',array_get($value,"Estimate_only"));
				$request_model->setValue('Enter_the_amount',array_get($value,"Enter_the_amount"));
				$request_model->setValue('PONumber_sale',array_get($value,"PONumber_sale"));
			
			}
			elseif($request_type=="change-classification"){
				
				$request_model->setValue('Room_Number_Change',array_get($value,"Room_Number_Change"));
				$request_model->setValue('Floor_Name_Change',array_get($value,"Floor_Name_Change"));
			
			}
			elseif($request_type=="carry-in"){
				
				if($furniture_type=="hard"){
					$request_model->setValue('Property_Carryin_MIMO_Hard', 41);
					$request_model->setValue('Room_Carryin_MIMO_Hard', 4498);
					$request_model->setValue('Floor_Carryin_MIMO_Hard', 1);
				}
				else {
					$request_model->setValue('Property_Carryin_MIMO_Soft', 41);
					$request_model->setValue('Room_Carryin_MIMO_Soft', 4498);
					$request_model->setValue('Floor_Carryin_MIMO_Soft', 1);
				}
				
				$request_model->setValue('PropertyColumn_Carryin',array_get($value,"PropertyColumn_Carryin"));
				$request_model->setValue('Room_Numbe_Carryin',array_get($value,"Room_Numbe_Carryin"));
				$request_model->setValue('Floor_Name_Carryin',array_get($value,"Floor_Name_Carryin"));
				$request_model->setValue('organization_Carryin_vender',array_get($value,"organization_Carryin_vender"));
				$request_model->setValue('Preferred_date_Carryin',array_get($value,"Preferred_date_Carryin"));
				$request_model->setValue('Preferred_date_Carryin2',array_get($value,"Preferred_date_Carryin2"));
				$request_model->setValue('Preferred_time_Carryin',array_get($value,"Preferred_time_Carryin"));
				$request_model->setValue('Candidate_date_Carryin1',array_get($value,"Candidate_date_Carryin1"));
				$request_model->setValue('Candidate_date_Carryin2',array_get($value,"Candidate_date_Carryin2"));
				$request_model->setValue('Candidate_date_Carryin3',array_get($value,"Candidate_date_Carryin3"));
				$column_name='addfile_drawing_Carryin';$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$column_name='addfile_operating_Carryin';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$request_model->setValue('furniture_id_1',array_get($value,"furniture_id_1"));
				$request_model->setValue('furniture_id_2',array_get($value,"furniture_id_2"));
				$request_model->setValue('furniture_id_3',array_get($value,"furniture_id_3"));
				$request_model->setValue('furniture_id_4',array_get($value,"furniture_id_4"));
				$request_model->setValue('furniture_id_5',array_get($value,"furniture_id_5"));
				$request_model->setValue('furniture_id_6',array_get($value,"furniture_id_6"));
				$request_model->setValue('furniture_id_7',array_get($value,"furniture_id_7"));
				$request_model->setValue('furniture_id_8',array_get($value,"furniture_id_8"));
				$request_model->setValue('furniture_id_9',array_get($value,"furniture_id_9"));
				$request_model->setValue('furniture_id_10',array_get($value,"furniture_id_10"));
				$request_model->setValue('memo_Carryin',array_get($value,"memo_Carryin"));
				$request_model->setValue('Estimate_only',array_get($value,"Estimate_only"));
				$request_model->setValue('Enter_the_amount',array_get($value,"Enter_the_amount"));
				$request_model->setValue('No_workrequest_check',array_get($value,"No_workrequest_check"));
				$request_model->setValue('PONumber_carryin',array_get($value,"PONumber_carryin"));
			
			}
			elseif($request_type=="carry-out"){
				
				if($furniture_type=="hard"){
					$request_model->setValue('Property_Carryout_MIMO_Hard', 41);
					$request_model->setValue('Room_Carryout_MIMO_Hard', 4498);
					$request_model->setValue('Floor_Carryout_MIMO_Hard', 1);
				}
				else {
					$request_model->setValue('Property_Carryout_MIMO_Soft', 41);
					$request_model->setValue('Room_Carryout_MIMO_Soft', 4498);
					$request_model->setValue('Floor_Carryout_MIMO_Soft', 1);
				}
				
				$request_model->setValue('Floor_Name_Carryout_from',array_get($value,"Floor_Name_Carryout_from"));
				$request_model->setValue('PropertyColumn_Carryout',array_get($value,"PropertyColumn_Carryout"));
				$request_model->setValue('Room_Numbe_Carryout',array_get($value,"Room_Numbe_Carryout"));
				$request_model->setValue('organization_Carryout_vender',array_get($value,"organization_Carryout_vender"));
				$request_model->setValue('Preferred_date_Carryout',array_get($value,"Preferred_date_Carryout"));
				$request_model->setValue('Preferred_date_Carryout2',array_get($value,"Preferred_date_Carryout2"));
				$request_model->setValue('Preferred_time_Carryout',array_get($value,"Preferred_time_Carryout"));
				$request_model->setValue('Candidate_date_Carryout1',array_get($value,"Candidate_date_Carryout1"));
				$request_model->setValue('Candidate_date_Carryout2',array_get($value,"Candidate_date_Carryout2"));
				$request_model->setValue('Candidate_date_Carryout3',array_get($value,"Candidate_date_Carryout3"));
				$column_name='addfile_drawing_Carryout';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$column_name='addfile_operating_Carryout';$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$request_model->setValue('memo_Carryout',array_get($value,"memo_Carryout"));
				$request_model->setValue('Estimate_only',array_get($value,"Estimate_only"));
				$request_model->setValue('Enter_the_amount',array_get($value,"Enter_the_amount"));
				$request_model->setValue('No_workrequest_check',array_get($value,"No_workrequest_check"));
				$request_model->setValue('PONumber_carryout',array_get($value,"PONumber_carryout"));
			
			}
			elseif($request_type=="internal-move"){
				
				$request_model->setValue('Room_Numbe_internalmove',array_get($value,"Room_Numbe_internalmove"));
				$request_model->setValue('Floor_Name_internalmove_from',array_get($value,"Floor_Name_internalmove_from"));
				$request_model->setValue('Room_Numbe_internalmove_to',array_get($value,"Room_Numbe_internalmove_to"));
				$request_model->setValue('Floor_Name_internalmove_to',array_get($value,"Floor_Name_internalmove_to"));
				$request_model->setValue('organization_inmove_vender',array_get($value,"organization_inmove_vender"));
				$request_model->setValue('Preferred_date_internalmove',array_get($value,"Preferred_date_internalmove"));
				$request_model->setValue('Preferred_date_internalmove2',array_get($value,"Preferred_date_internalmove2"));
				$request_model->setValue('Preferred_time_internalmove',array_get($value,"Preferred_time_internalmove"));
				$request_model->setValue('Candidate_date_internalmove1',array_get($value,"Candidate_date_internalmove1"));
				$request_model->setValue('Candidate_date_internalmove2',array_get($value,"Candidate_date_internalmove2"));
				$request_model->setValue('Candidate_date_internalmove3',array_get($value,"Candidate_date_internalmove3"));
				$column_name='addfile_drawing_internalmove';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$column_name='addfile_operating_internalmove';
				$file=array_get($value,$column_name);
				if($file)self::setFile($column_name,$file,$table,$request_model);
				$request_model->setValue('memo_internalmove',array_get($value,"memo_internalmove"));
				$request_model->setValue('Estimate_only',array_get($value,"Estimate_only"));
				$request_model->setValue('Enter_the_amount',array_get($value,"Enter_the_amount"));
				$request_model->setValue('No_workrequest_check',array_get($value,"No_workrequest_check"));
				$request_model->setValue('PONumber_internalmove',array_get($value,"PONumber_internalmove"));
			
			}
			
			if($furniture_type=="soft"){
				
				$request_model->setValue('SoftFurniture',array_get($value,"SoftFurniture"));
			
			}
			elseif($furniture_type=="hard"){
				
				if($request_type!="change-classification"){
					
					$request_model->setValue('Desk',array_get($value,"Desk"));
					$request_model->setValue('Chair',array_get($value,"Chair"));
					$request_model->setValue('Peds',array_get($value,"Peds"));
				
				}
				
				if($request_type!="carry-in"){
					
					$request_model->setValue('DeskDefective',array_get($value,"DeskDefective"));
					$request_model->setValue('ChairDefective',array_get($value,"ChairDefective"));
					$request_model->setValue('PedsDefective',array_get($value,"PedsDefective"));
				
				}
			
			}
			
			$request_model->save();
			
			$workflow=WorkFlow::getWorkflowByTable($table);
			
			//if($workflow&&!($furniture_type=="soft"&&$request_type=="move")){
			  if($workflow && $request['submit_not_save_workflow'] == static::SUBMIT_SAVE_WORKFLOW) {
				
				$workflow_start_action=WorkflowAction::where('workflow_id',$workflow->id)->where('status_from','start')->get()->first();
				$workflow_status=WorkflowStatus::where('workflow_id',$workflow->id)->get();
				$workflow_start_status=WorkflowStatus::where('workflow_id',$workflow->id)->where('order',1)->get()->first();
				$status_to=$workflow_start_status->id;
				$morph_type=$table->table_name;
				$morph_id=$request_model->id;
				$createData=['workflow_id'=>$workflow->id,'morph_type'=>$morph_type,'morph_id'=>$morph_id,'workflow_action_id'=>$workflow_start_action->id,'workflow_status_from_id'=>null,'workflow_status_to_id'=>$status_to,'latest_flg'=>1,];
				$created_workflow_value=WorkflowValue::create($createData);
			
			}
			
			return $request_model->id;
		
		}
		
		private static function getTable($furniture_type,$request_type){
			
			$table=null;
			
			if($furniture_type=="hard"){
				
				switch($request_type){
					
					case "move":
						
						$table=TABLE_HARD_FURNITURE_REQUEST_MOVE;
						
						break;
					
					case "disposal":
						
						$table=TABLE_HARD_FURNITURE_REQUEST_DISPOSAL;
						
						break;
					
					case "sale":
						
						$table=TABLE_HARD_FURNITURE_REQUEST_SALE;
						
						break;
					
					case "change-classification":
						
						$table=TABLE_HARD_FURNITURE_REQUEST_CHANGE_CLASSIFICATION;
						
						break;
					
					case "carry-in":
						
						$table=TABLE_HARD_FURNITURE_REQUEST_CARRY_IN;
						
						break;
					
					case "carry-out":
						
						$table=TABLE_HARD_FURNITURE_REQUEST_CARRY_OUT;
						
						break;
					
					case "internal-move":
						
						$table=TABLE_HARD_FURNITURE_REQUEST_INTERNAL_MOVE;
						
						break;
				
				}
			
			}
			else {
				
				switch($request_type){
					
					case "move":
						
						$table=TABLE_SOFT_FURNITURE_REQUEST_MOVE;
						
						break;
					
					case "disposal":
						
						$table=TABLE_SOFT_FURNITURE_REQUEST_DISPOSAL;
						
						break;
					
					case "sale":
						
						$table=TABLE_SOFT_FURNITURE_REQUEST_SALE;
						
						break;
					
					case "change-classification":
						
						$table=TABLE_SOFT_FURNITURE_REQUEST_CHANGE_CLASSIFICATION;
						
						break;
					
					case "carry-in":
						
						$table=TABLE_SOFT_FURNITURE_REQUEST_CARRY_IN;
						
						break;
					
					case "carry-out":
						
						$table=TABLE_SOFT_FURNITURE_REQUEST_CARRY_OUT;
						
						break;
					
					case "internal-move":
						
						$table=TABLE_SOFT_FURNITURE_REQUEST_INTERNAL_MOVE;
						
						break;
				
				}
			
			}
			
			
			if($table)return CustomTable::getEloquent($table);
			
			return $table;
		
		}
		
		protected static function getCommonOptions(&$users,&$user_option,&$noti_user_option,&$priority_option){
			
			$users=CustomTable::getEloquent(SystemTableName::USER)->getValueModel()->all();
			$user_option=[''=>''];
			$noti_user_option=[''=>''];
			
			foreach($users as $user){
				
				$user_option[$user->id]=$user->getValue("user_name");
				$noti_user_option[$user->id]=$user->getValue("user_name");
			
			}
			
			$priority_option=[''=>'','緊急'=>'緊急','優先'=>'優先','通常'=>'通常','低い'=>'低い'];
		
		}
		
		protected static function setFile($column_name,$file,$table,$request_model){
			
			$column=\Exceedone\Exment\Model\CustomColumn::getEloquent($column_name,$table->table_name);
			$fileInfo=ExmentFile::storeAs(FileType::CUSTOM_VALUE_COLUMN,$file,$table->table_name,$file->getClientOriginalName())->saveCustomValue($request_model->id,$column->id,$table);
			$request_model->setValue($column_name,$fileInfo->path);
		
		}
		
		protected static function getOrganizations(&$organizations,&$organization_option){
			
			$organizations=CustomTable::getEloquent(SystemTableName::ORGANIZATION)->getValueModel()->all();
			$organization_option=['0'=>'なし'];
			
			foreach($organizations as $organization){
				
				
				
				if($organization->column_6cd3d276867dddeb4c52 == 10) {
				
					$organization_option[$organization->id]=$organization->getValue("organization_name");
					
				}
			
			}
		
		}
		
		protected static function getFurnitures(&$furnitures,&$furniture_option){
			
			$furnitures=CustomTable::getEloquent('SoftFurnitureDB')->getValueModel()->all();
			$furniture_option=['0'=>'なし'];
			
			foreach($furnitures as $furniture){
				
				$furniture_option[$furniture->id]=$furniture->getValue("Item_Identifier_number");
			
			}
		
		}
		
		protected static function getTimes(&$times,&$times_option){
			
		}
		
		
		protected static function getDialogText($furniture_type,$request_type){
			
			$text=self::$plugin->getCustomOption('request_dialog')??"";
			
			switch($request_type){
				
				case 'move':
					
					$replace_request_type="移動変更";
					
					break;
				
				case 'disposal':
					
					$replace_request_type="廃棄";
					
					break;
				
				case 'sale':
					
					$replace_request_type="売却";
					
					break;
				
				case 'change-classification':
					
					$replace_request_type="在庫区分変更";
					
					break;
				
				case 'carry-in':
					
					$replace_request_type="搬入";
					
					break;
				
				case 'carry-out':
					
					$replace_request_type="搬出";
					
					break;
				
				case 'internal-move':
					
					$replace_request_type="拠点内移動";
					
					break;
				
				default:
					
					$replace_request_type="";
			
			}
			
			switch($furniture_type){
				
				case 'soft':
					
					$replace_furniture_type="ソフトファニチャー";
					
					break;
				
				case 'hard':
					
					$replace_furniture_type="ハードファニチャー";
					
					break;
					
				case 'ather':
					
					$replace_furniture_type = "ソフトファニチャー";
				
				default:
					
					$replace_request_type="";
			
			}
			
			$text=str_replace('${request:furniture_type}',$replace_furniture_type,$text);
			$text=str_replace('${request:request_type}',$replace_request_type,$text);
			
			return $text;
		
		}
	
	}

}