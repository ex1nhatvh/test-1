<?php 
namespace App\Plugins\FurnitureManagementSystem;

use App\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Exceedone\Exment\Enums\FileType;
use Exceedone\Exment\Enums\SystemTableName;
use Exceedone\Exment\Enums\ValueType;
use Exceedone\Exment\Model\CustomTable;
use Exceedone\Exment\Model\CustomView;
use Exceedone\Exment\Model\File as ExmentFile;
use Exceedone\Exment\Model\PublicForm;
use Exceedone\Exment\Model\Workflow;
use Exceedone\Exment\Model\WorkflowAction;
use Exceedone\Exment\Model\WorkflowStatus;
use Exceedone\Exment\Model\WorkflowValue;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Exceedone\Exment\Services\Plugin\PluginPageBase;
use GuzzleHttp\Client;
use Exceedone\Exment\Notifications\MailSender;
use Exceedone\Exment\Enums\MailKeyName;

define('TABLE_PROPERTIES','PropertiesDB');
define('TABLE_SOFT_FURNITURE_ROOM','LocationRoom');
define('TABLE_HARD_FURNITURE_ROOM','LocationRoom_hard');
define('TABLE_SOFT_FURNITURE','SoftFurnitureDB');
define('TABLE_HARD_FURNITURE','HardFurnitureDB');
define('TABLE_SOFT_FURNITURE_INVENTORY','SoftFurniture_inventory');
define('TABLE_HARD_FURNITURE_INVENTORY','HardFurniture_inventory');
define('TABLE_SOFT_FURNITURE_REQUEST','SoftFurniture_apply_inventory');
define('TABLE_SOFT_FURNITURE_REQUEST_MERGE','SoftFurniture_merge_apply_inv');
define('TABLE_SOFT_FURNITURE_REQUEST_MOVE','SofFurniture_apply_for_Moving');
define('TABLE_SOFT_FURNITURE_REQUEST_DISPOSAL','SoftFurniture_apply_Disposal');
define('TABLE_SOFT_FURNITURE_REQUEST_SALE','SofFurniture_apply_for_Sale');
define('TABLE_SOFT_FURNITURE_REQUEST_CHANGE_CLASSIFICATION','SoftFurniture_apply_for_Change');
define('TABLE_HARD_FURNITURE_REQUEST_MOVE','apply_for_Moving');
define('TABLE_HARD_FURNITURE_REQUEST_DISPOSAL','HardFurniture_apply_Disposal');
define('TABLE_HARD_FURNITURE_REQUEST_SALE','apply_for_Sale');
define('TABLE_HARD_FURNITURE_REQUEST_CHANGE_CLASSIFICATION','apply_for_Change');
define('TABLE_SOFT_FURNITURE_REQUEST_CARRY_IN','SoftFurniture_apply_Carryin');
define('TABLE_HARD_FURNITURE_REQUEST_CARRY_IN','apply_for_Carryin');
define('TABLE_SOFT_FURNITURE_REQUEST_CARRY_OUT','SoftFurniture_apply_Carryout');
define('TABLE_HARD_FURNITURE_REQUEST_CARRY_OUT','apply_for_Carryout');
define('TABLE_SOFT_FURNITURE_REQUEST_INTERNAL_MOVE','Soft_F_apply_InternalMoving');
define('TABLE_HARD_FURNITURE_REQUEST_INTERNAL_MOVE','apply_for_InternalMoving');
define('TABLE_HARD_FURNITURE_REQUEST','apply_for_inventory');
define('TABLE_HARD_FURNITURE_REQUEST_MERGE','HardFurniture_merge_apply_inv');
define('TABLE_INVENTORY_REPORT','inventory_sheets');
define('COMMENT_ATTACHMENT_REQUEST_FROM','comment_attachment_RequestForm');

use App\Plugins\FurnitureManagementSystem\Includes\FMSRequest;
use App\Plugins\FurnitureManagementSystem\Includes\HardFurniture;
use App\Plugins\FurnitureManagementSystem\Includes\SoftFurniture;
use App\Plugins\FurnitureManagementSystem\Includes\FMSUtil\Facades\FMSUtil;
use App\Plugins\FurnitureManagementSystem\Includes\FMSTableRequest;

class Plugin extends PluginPageBase{
	
	protected $useCustomOption=true;
	
	function __construct($plugin){
		
		FMSRequest::setPlugin($plugin);
		FMSTableRequest::setPlugin($plugin);
		HardFurniture::setPlugin($plugin);
		SoftFurniture::setPlugin($plugin);
		parent::__construct($plugin);
	
	}
	
	public function index(){
		
		return $this->getIndexBox();
	
	}
	
	public function softFurnitureRegistry(){
		
		return SoftFurniture::registry()->render();
	
	}
	
	public function softFurnitureOnsite(){
		
		return SoftFurniture::onsite()->render();
	
	}

	public function softFurnitureOnsiteMerge(){
		if(request()->get('merge_row')){
			return SoftFurniture::onsiteTableMerge(request())->render();
		}
		return SoftFurniture::onsiteMerge(request())->render();
	}

	public function softFurnitureOnsiteAfterMerge(){
		return SoftFurniture::onsiteAfterMerge()->render();
	}
	
	public function softFurnitureOnsiteSelectProperty(){
		
		return SoftFurniture::onsiteSelectProperty()->render();
	
	}
	
	public function softFurnitureOnsiteSearchRoom($property_id){
		
		return SoftFurniture::onsiteSearchRoom($property_id)->render();
	
	}
	
	public function softFurnitureOnsiteRedirectToQR($property_id,$room_id){
		
		return redirect(admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id."/qr/");
	
	}
	
	public function softFurnitureOnsiteReadQR($property_id,$room_id,$read_ids=null){
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::common/read-qr',
							['title'=>'ソフトファニチャー棚卸',
							 'routeUri'=>admin_url($this->getRouteUri('sf-onsite')),
							 'property_id'=>$property_id,
							 'room_id'=>$room_id,
							 'qr_action'=>"continue",
							 'read_ids'=>$read_ids,
							 'confirm_link'=>admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id."/confirm/",
							 'input_link'=>admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id."/input/",
							]
						   )
					  );
	
	}
	
	public function softFurnitureOnsiteInput($property_id,$room_id,$read_ids=null){
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::sf-onsite/id-input',
							['title'=>'ソフトファニチャー棚卸',
							 'routeUri'=>admin_url($this->getRouteUri('sf-onsite')),
							 'property_id'=>$property_id,
							 'room_id'=>$room_id,
							 'read_ids'=>$read_ids,
							 'confirm_link'=>admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id."/confirm/",
							]
						   )
					  );
	
	}
	
	public function softFurnitureOnsiteConfirm($property_id,$room_id,$read_ids=""){
		
		$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
		$room=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel($room_id);
		$result_list=$this->getSoftFurnitureOnsiteQRResult($property_id,$room_id,$read_ids,$isFailExists);
		$action_url=admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id."/save";
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::sf-onsite/qr-result',
							['routeUri'=>admin_url($this->getRouteUri('sf-onsite')),
							 'property_id'=>$property_id,
							 'room_id'=>$room_id,
							 'result_list'=>$result_list,
							 'property'=>$property,
							 'room'=>$room,
							 'action_url'=>$action_url,
							 'soft_furniture_result_ids'=>$read_ids,
							 'is_fail_exists'=>$isFailExists??null,'read_ids'=>$read_ids,
							]
						   )
					  );
	
	}
	
	public function softFurnitureOnsiteSave($property_id,$room_id){
		
		$params=request()->only("soft_furniture_ids","soft_furniture_results");
		$ids=$params["soft_furniture_ids"]??[];
		$results=$params["soft_furniture_results"]??[];
		$l=count($ids);
		$saveResult=[];
		$model=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_INVENTORY)->getValueModel();
		
		for($i=0;$i<$l;$i++){
			
			$model=$model->create();
			$model->setValue('JudgmentResult',$results[$i]);
			$model->setValue('Property_Inventory',$property_id);
			$model->setValue('Room_Inventory',$room_id);
			$model->setValue('Softfurniture_Inventory_target',$ids[$i]);
			$model->save();
			
			$saveResult[]=$model->id;
		
		}
		
		$model=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST)->getValueModel();
		$model=$model->create();
		$model->setValue('Property',$property_id);$model->setValue('Room_Numbe_inventory',$room_id);
		$model->setValue('soft_furniture_inventories',$saveResult);
		$model->save();
		admin_toastr(trans('admin.save_succeeded'));
		
		return redirect(admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id."/result/".$model->id);
	
	}
	
	public function softFurnitureOnsiteResult($property_id,$room_id,$request_id){
		
		$request_data=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST)->getValueModel($request_id);
		$inventories=$request_data->getValue("soft_furniture_inventories");
		$property_id=$request_data->getValue("Property")->id;
		$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
		$room_id=$request_data->getValue("Room_Numbe_inventory")->id;
		$room_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM);
		$room=$room_table->getValueModel($room_id);$result_list=$this->getSoftFurnitureOnsiteQRResultFromInventories($inventories,$isFailExists);
		$query=$room_table->getValueModel()->query();
		$query->where('parent_id','=',$property_id);
		$query->where('id','>',$room_id);
		$query->orderBy('id','asc');
		$next_room=$query->first();
		$query=$room_table->getValueModel()->query();
		$query->where('parent_id','=',$property_id);
		$query->where('id','<',$room_id);
		$query->orderBy('id','desc');
		$prev_room=$query->first();
		
		if($prev_room){
			
			$prev_room_url=admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$prev_room->id;
		
		}
		
		if($next_room){
			
			$next_room_url=admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$next_room->id;
		
		}
		
		$return_url=admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id;
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::sf-onsite/result',
							['routeUri'=>admin_url($this->getRouteUri('sf-onsite')),
							 'property_id'=>$property_id,
							 'room_id'=>$room_id,
							 'result_list'=>$result_list,
							 'property'=>$property,
							 'room'=>$room,
							 'prev_room_url'=>$prev_room_url??"",
							 'next_room_url'=>$next_room_url??"",
							 'return_url'=>$return_url,
							 'is_fail_exists'=>$isFailExists??null,
							 'plugin_top_url'=>$this->getPluginUri(''),
							]
						   )
					  );
	
	}
	
	public function softFurnitureOnsiteReinputRedirectToQR($request_id){
		
		return redirect(admin_url($this->getRouteUri('sf-onsite'))."/reinput/".$request_id."/qr");
	
	}
	
	public function softFurnitureOnsiteReinputQR($request_id,$read_ids=null){
		
		$request_data=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST)->getValueModel($request_id);
		$inventories=$request_data->getValue("soft_furniture_inventories");
		
		if(!$read_ids){
			
			$ids=[];
			
			foreach($inventories as $inventory){
				
				$judge=$inventory->getValue("JudgmentResult");
				
				switch($judge){
					
					case "移動先要確認":
						
						break;
					
					default:
						
						$ids[]=$inventory->getValue("Softfurniture_Inventory_target")->id;
						
						break;
				
				}
			
			}
			
			if($ids)return redirect(admin_url($this->getRouteUri('sf-onsite'))."/reinput/".$request_id."/qr/".implode(",",$ids));
		
		}
		
		$property=$request_data->getValue("Property");
		$property_id=$property->id;
		$room=$request_data->getValue("Room_Numbe_inventory");
		$room_id=$room->id;
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::common/read-qr',
							['title'=>'ソフトファニチャー棚卸',
							 'routeUri'=>admin_url($this->getRouteUri('sf-onsite')),
							 'qr_action'=>"continue",
							 'read_ids'=>$read_ids,
							 'confirm_link'=>admin_url($this->getRouteUri('sf-onsite'))."/reinput/".$request_id."/confirm/",
							 'input_link'=>admin_url($this->getRouteUri('sf-onsite'))."/reinput/".$request_id."/input/",
							]
						   )
					  );
	
	}
	
	public function softFurnitureOnsiteReinput($request_id,$read_ids=null){
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::sf-onsite/id-input',
							['title'=>'ソフトファニチャー棚卸',
							 'routeUri'=>admin_url($this->getRouteUri('sf-onsite')),
							 'read_ids'=>$read_ids,
							 'confirm_link'=>admin_url($this->getRouteUri('sf-onsite'))."/reinput/".$request_id."/confirm/",
							]
						   )
					  );
	
	}
	
	public function softFurnitureOnsiteReinputConfirm($request_id,$read_ids=""){
		
		$request_data=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST)->getValueModel($request_id);
		$inventories=$request_data->getValue("soft_furniture_inventories");
		$property=$request_data->getValue("Property");
		$property_id=$property->id;
		$room=$request_data->getValue("Room_Numbe_inventory");
		$room_id=$room->id;
		$result_list=$this->getSoftFurnitureOnsiteQRResult($property_id,$room_id,$read_ids,$isFailExists);
		$action_url=admin_url($this->getRouteUri('sf-onsite'))."/reinput/".$request_id."/update";
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::sf-onsite/qr-result',
							['routeUri'=>admin_url($this->getRouteUri('sf-onsite')),
							 'property_id'=>$property_id,
							 'room_id'=>$room_id,
							 'result_list'=>$result_list,
							 'property'=>$property,
							 'room'=>$room,
							 'action_url'=>$action_url,
							 'soft_furniture_result_ids'=>$read_ids,
							 'is_fail_exists'=>$isFailExists??null,
							 'method'=>'put',
							 'add_input_url'=>admin_url($this->getRouteUri('sf-onsite'))."/reinput/".$request_id."/qr/".$read_ids,
							]
						   )
					  );
	
	}
	
	public function softFurnitureOnsiteUpdate($request_id){
		
		$request_data=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST)->getValueModel($request_id);
		$property=$request_data->getValue("Property");
		$property_id=$property->id;
		$room=$request_data->getValue("Room_Numbe_inventory");
		$room_id=$room->id;
		$params=request()->only("soft_furniture_ids","soft_furniture_results");
		$ids=$params["soft_furniture_ids"];
		$results=$params["soft_furniture_results"];
		$l=count($ids);
		$saveResult=[];
		$model=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_INVENTORY)->getValueModel();
		
		for($i=0;$i<$l;$i++){
			
			$model=$model->create();
			$model->setValue('JudgmentResult',$results[$i]);
			$model->setValue('Property_Inventory',$property_id);
			$model->setValue('Room_Inventory',$room_id);
			$model->setValue('Softfurniture_Inventory_target',$ids[$i]);
			$model->save();
			$saveResult[]=$model->id;
		
		}
		
		$request_data=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST)->getValueModel($request_id);
		$request_data->setValue('soft_furniture_inventories',$saveResult);
		$request_data->save();
		admin_toastr(trans('admin.save_succeeded'));
		
		return redirect(admin_url($this->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id."/result/".$request_data->id);
	
	}
	
	public function softFurnitureOnsiteRequestList(){
		
		return SoftFurniture::onsiteRequestList()->render();
	
	}
	
	public function softFurnitureOnsiteRequestedDetail($date){
		
		return SoftFurniture::onsiteRequestedDetail($date)->render();
	
	}
	
	public function softFurnitureOnsiteUnrequestedDetail(){
		
		return SoftFurniture::onsiteUnrequestedDetail()->render();
	
	}
	
	public function softFurnitureOnsiteRequest(){
		
		SoftFurniture::onsiteRequest();admin_toastr('申請しました');
		return redirect(admin_url($this->getRouteUri('sf-onsite/request')));
	
	}
	
	private function getSoftFurnitureOnsiteQRResult($property_id,$room_id,$read_ids,&$isFailExists){
		
		$params=$read_ids;
		$read_ids=explode(',',$params);
		$sf_model=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel();
		$read_data=$sf_model->whereIn('id',$read_ids)->get();
		$room_data=$sf_model->where('value->Room_Numbe',$room_id)->get();
		$result_list=[];
		
		foreach($room_data as $soft_furniture){
			
			$result_list[$soft_furniture->id]=[];
			$result="NG";
			
			foreach($read_data as $from_qr){
				
				if($soft_furniture->id==$from_qr->id){
					
					$result="正常";
					
					break;
				
				}
			
			}
			
			if($result=='正常'){
				
				$result_list[$soft_furniture->id]['result']=$result;
				$result_list[$soft_furniture->id]['in_room']=self::getSoftFurnitureOnsiteResult($soft_furniture);
				$result_list[$soft_furniture->id]['from_qr']=self::getSoftFurnitureOnsiteResult($from_qr);
			
			}
			else{
				
				$result_list[$soft_furniture->id]['result']=$soft_furniture->getValue("SoftFurniture_Status")??"移動先要確認";
				$result_list[$soft_furniture->id]['in_room']=self::getSoftFurnitureOnsiteResult($soft_furniture);
				$isFailExists=true;
			
			}
		
		}
		
		foreach($read_data as $from_qr){
			
			if(!array_key_exists($from_qr->id,$result_list)){
				
				$result_list[$from_qr->id]=[];
				$result_list[$from_qr->id]['result']="棚卸移動";
				$result_list[$from_qr->id]['from_qr']=self::getSoftFurnitureOnsiteResult($from_qr);
				$isFailExists=true;
			
			}
		
		}
		
		ksort($result_list);
		
		return $result_list;
	
	}
	
	private function getSoftFurnitureOnsiteQRResultFromInventories($inventories,&$isFailExists){
		
		$result_list=[];
		
		foreach($inventories as $inventory){
			
			$soft_furniture=$inventory->getValue("Softfurniture_Inventory_target");
			$result_list[$soft_furniture->id]=[];
			$result_list[$soft_furniture->id]['result']=$inventory->getValue('JudgmentResult');
			
			if($result_list[$soft_furniture->id]['result']=="正常"){
				
				$result_list[$soft_furniture->id]['in_room']=self::getSoftFurnitureOnsiteResult($soft_furniture);
				$result_list[$soft_furniture->id]['from_qr']=self::getSoftFurnitureOnsiteResult($soft_furniture);
			
			}
			elseif($result_list[$soft_furniture->id]['result']=="移動先要確認"){
				
				$result_list[$soft_furniture->id]['in_room']=self::getSoftFurnitureOnsiteResult($soft_furniture);
				$isFailExists=true;
			
			}
			else{
				
				$result_list[$soft_furniture->id]['from_qr']=self::getSoftFurnitureOnsiteResult($soft_furniture);
				$isFailExists=true;
			
			}
		
		}
		
		ksort($result_list);
		
		return $result_list;
	
	}
	
	protected static function getSoftFurnitureOnsiteResult($data){
		
		$property_column=$data->getValue("PropertyColumn");
		
		if($property_column)$property_column=$property_column->getValue("PropertyColumn");
		
		$room_name="";
		$floor_name="";
		$room_column=$data->getValue("Room_Numbe");
		
		if($room_column){
			
			$room_name=$room_column->getValue("Room_Numbe");
			$floor_name=$room_column->getValue("Floor_Name");
		
		}
		
		$sku_column=$data->getValue("Item_Identifier_number");
		
		if($sku_column)$sku_column=$sku_column->getValue("Item_Identifier");
		
		return['id'=>$data->id,
			   '拠点/Location'=>$property_column ?? "",
			   '部屋番号/Room Number'=>$room_name ?? "",
			   'フロア/Floor'=>$floor_name ?? "",
			   'SKU'=>$sku_column ?? "",
			   'PO'=>$data->getValue("PO_Number")?? "",
			   'Photo'=>ExmentFile::getUrl($data->getValue("Photo_Softfurniture"))?? ""];
	
	}
	
	public function hardFurnitureRegistry(){
		
		return HardFurniture::registry()->render();
	
	}
	
	public function hardFurnitureOnsite(){
		
		return HardFurniture::onsite()->render();
	
	}

	public function hardFurnitureOnsiteMerge(){
		if(request()->get('merge_row')){
			return HardFurniture::onsiteTableMerge(request())->render();
		}
		return HardFurniture::onsiteMerge(request())->render();
	}

	public function hardFurnitureOnsiteAfterMerge(){
		return HardFurniture::onsiteAfterMerge()->render();
	}
	
	public function hardFurnitureOnsiteSelectProperty(){
		
		return HardFurniture::onsiteSelectProperty()->render();
	
	}
	
	public function hardFurnitureOnsiteSelectRoom($property_id){
		
		return HardFurniture::onsiteSelectRoom($property_id)->render();
	
	}
	
	public function hardFurnitureRegistrySelectRoom($property_id){
		
		return HardFurniture::registrySelectRoom($property_id)->render();
	
	}
	
	public function hardFurnitureRegistryRegist($property_id,$room_id){
		
		return HardFurniture::registryRegist($property_id,$room_id)->render();
	
	}
	
	public function hardFurnitureRegistrySave($property_id,$room_id,$hard_furniture_id){
		
		$request=request();
		$model=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($hard_furniture_id);
		$model->setValue('HardFurniture_hard',$request->get('HardFurniture_hard'));
		$model->setValue('HardFurniture_Chair',$request->get('HardFurniture_Chair'));
		$model->setValue('HardFurniture_Peds',$request->get('HardFurniture_Peds'));
		$model->setValue('HardFurniture_Desk_Defective',$request->get('HardFurniture_Desk_Defective'));
		$model->setValue('HardFurniture_Chair_Defective',$request->get('HardFurniture_Chair_Defective'));
		$model->setValue('HardFurniture_Peds_Defective',$request->get('HardFurniture_Peds_Defective'));
		$model->save();
		
		admin_toastr(trans('admin.save_succeeded'));
		
		return redirect(admin_url($this->getRouteUri('hf-registry'))."/property/".$property_id."/room/".$room_id."/result/".$hard_furniture_id);
	
	}
	
	public function hardFurnitureRegistryResult($property_id,$room_id,$hard_furniture_id){
		
		return HardFurniture::registryResult($property_id,$room_id,$hard_furniture_id)->render();
	
	}
	
	public function hardFurnitureOnsiteRegist($property_id,$room_id){
		
		return HardFurniture::onsiteRegist($property_id,$room_id)->render();
	
	}
	
	public function hardFurnitureOnsiteSave($property_id,$room_id,$hard_furniture_id){
		
		$request=request();
		$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($hard_furniture_id);
		$desk=intval($hard_furniture->getValue('HardFurniture_hard'));
		$chair=intval($hard_furniture->getValue('HardFurniture_Chair'));
		$peds=intval($hard_furniture->getValue('HardFurniture_Peds'));
		$inventory=CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY)->getValueModel()->create();
		$inventory->setValue('Desk_Inventory',$request->get('HardFurniture_hard'));
		$inventory->setValue('chair_Inventory',$request->get('HardFurniture_Chair'));
		$inventory->setValue('Peds_Inventory',$request->get('HardFurniture_Peds'));
		$inventory->setValue('Desk_Defective_Inventory',intval($request->get('HardFurniture_hard'))-$desk);
		$inventory->setValue('chair_Defective_Inventory',intval($request->get('HardFurniture_Chair'))-$chair);
		$inventory->setValue('peds_Defective_Inventory',intval($request->get('HardFurniture_Peds'))-$peds);
		$inventory->setValue('Hardfurniture_Inventory_target',$hard_furniture_id);
		$inventory->save();admin_toastr(trans('admin.save_succeeded'));
		
		return redirect(admin_url($this->getRouteUri('hf-onsite'))."/property/".$property_id."/room/".$room_id."/result/".$hard_furniture_id."/i/".$inventory->id);
	
	}
	
	public function hardFurnitureOnsiteResult($property_id,$room_id,$hard_furniture_id,$hard_furniture_inventory_id){
		
		return HardFurniture::onsiteResult($property_id,$room_id,$hard_furniture_id,$hard_furniture_inventory_id)->render();
	
	}
	
	public function hardFurnitureOnsiteRequestList(){
		
		return HardFurniture::onsiteRequestList()->render();
	
	}
	
	public function hardFurnitureOnsiteRequestDetail($request_id){
		
		return HardFurniture::onsiteRequestDetail($request_id)->render();
	
	}
	
	public function hardFurnitureOnsiteRequest(){
		
		HardFurniture::onsiteRequest();
		admin_toastr('申請しました');
		
		return redirect(admin_url($this->getRouteUri('hf-onsite/request')));
	
	}
	
	public function hardFurnitureOnsiteRequestedDetail($date){
		
		return HardFurniture::onsiteRequestedDetail($date)->render();
	
	}
	
	public function hardFurnitureOnsiteUnrequestedDetail(){
		
		return HardFurniture::onsiteUnrequestedDetail()->render();
	
	}
	
	public function requestTop(){
		
		return FMSRequest::top()->render();
	
	}
	
	public function requestForm($furniture_type,$request_type){
		
		$request=request()->all();
		
		switch($request_type){
			
			case "move":
				
				return FMSRequest::move($furniture_type,$request_type,$this->getRouteName('requestSave'),$request)->render();
				
				break;
			
			case "disposal":
				
				return FMSRequest::disposal($furniture_type,$request_type,$this->getRouteName('requestSave'),$request)->render();
				
				break;
			
			case "sale":
				
				return FMSRequest::sale($furniture_type,$request_type,$this->getRouteName('requestSave'),$request)->render();
				
				break;
			
			case "change-classification":
				
				return FMSRequest::changeClassification($furniture_type,$request_type,$this->getRouteName('requestSave'),$request)->render();
				
				break;
			
			case "carry-in":
				
				return FMSRequest::carryIn($furniture_type,$request_type,$this->getRouteName('requestSave'),$request)->render();
				
				break;
			
			case "carry-out":
				
				return FMSRequest::carryOut($furniture_type,$request_type,$this->getRouteName('requestSave'),$request)->render();
				
				break;
			
			case "internal-move":
				
				return FMSRequest::internalMove($furniture_type,$request_type,$this->getRouteName('requestSave'),$request)->render();
				
				break;
			
			default:
				
				redirect(admin_url($this->getRouteUri('request')));
				
				break;
		
		}
	
	}

	public function requestTable($furniture_type,$request_type, $id = null){
		$request=request()->all();
		if($id != null){
			if(request()->getMethod() === 'GET') {
				// Page form
				return FMSTableRequest::form($furniture_type,$request_type, $id,$this->getRouteName('requestSave'),$request);
			} else {
				// Save
				return FMSTableRequest::saveCommentAttachment($furniture_type,$request_type,$this->getRouteName('requestSave'), $id, request());
			}
		}
		// Page list
		return FMSTableRequest::index($furniture_type,$request_type,$this->getRouteName('requestSave'),$request)->render();
	}
	
	public function requestSelectProperty($furniture_type,$request_type){
		
		$request=request()->all();
		$properties=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->get();
		$next_url=admin_url($this->getRouteUri('request'))."/".$furniture_type."/".$request_type."/property/";
		
		return new Box("Furniture Management System",
					   view('exment_furniture_management_system::request/property-select',
							['title'=>FMSUtil::getRequestTitle($furniture_type,$request_type),
							'properties'=>$properties,
							 'next_url'=>$next_url,
							 'request'=>$request,
							 'furniture_type'=>$furniture_type,
							 'request_type'=>$request_type,
							]
						   )
					  );
	
	}
	
	public function requestSelectRoom($furniture_type,$request_type,$property_id){
		
		$request=request()->all();
		$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
		
		if($furniture_type=="hard"){
			
			$room_list=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel()->where('parent_id',$property_id)->get();
			$hard_furniture_list=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->PropertyColumn_hard',$property_id)->get();
			$floor_room_list=FMSUtil::getHardFloorRoomList($hard_furniture_list,$room_list);
		
		}
		else {
			
			$room_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel()->where('parent_id',$property_id)->get();
			$floor_room_list=FMSUtil::getFloorRoomList($room_list);
			
		}
		
		$next_url=admin_url($this->getRouteUri('request'))."/".$furniture_type."/".$request_type;
		
		return new Box("Furniture Management System",
					   view('exment_furniture_management_system::request/room-select',
							['title'=>FMSUtil::getRequestTitle($furniture_type,$request_type),
							 'property'=>$property,
							 'property_id'=>$property_id,
							 'floor_room_list'=>$floor_room_list,
							 'next_url'=>$next_url,
							 'request'=>$request,
							 'furniture_type'=>$furniture_type,
							 'request_type'=>$request_type,
							 'routeUri'=>$this->getPluginUri('request'),
							]
						   )
					  );
	
	}
	
	public function requestSave($furniture_type,$request_type){
		
		$request=request()->all();
		$id = FMSRequest::save($furniture_type,$request_type,$request);
		admin_toastr('申請しました');
		
		return redirect(admin_url($this->getRouteUri('request'))."/".$furniture_type."/".$request_type."/".$id);
	
	}
	
	public static function getTable($furniture_type,$request_type){
		
		$table=null;
		
		if($furniture_type="hard"){
			
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
			
			}
			
		}
		
		if($table)return CustomTable::getEloquent($table);
		return $table;
	
	}
	
	public function requestMove($furniture_type){
		
		$properties=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->all();
		$property_option=[''=>''];
		
		foreach($properties as $property){
			
			$property_option[$property->id]=$property->getValue("PropertyColumn");
		
		}
		
		$users=CustomTable::getEloquent(SystemTableName::USER)->getValueModel()->all();
		$user_option=[''=>''];
		
		foreach($users as $user){
			
			$user_option[$user->id]=$user->getValue("user_name");
		
		}
		
		$organizations=CustomTable::getEloquent(SystemTableName::ORGANIZATION)->getValueModel()->all();
		$organization_option=['0'=>'なし'];
		
		foreach($organizations as $organization){
			
			$organization_option[$organization->id]=$organization->getValue("organization_name");
		
		}
		
		$request_model=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST_MOVE)->getValueModel()->create();
		
		return new Box("Furniture Management System",
					   view('exment_furniture_management_system::request/move',
							['title'=>'申請内容を入力してください',
							 'property_option'=>$property_option,
							 'user_option'=>$user_option,
							 'organization_option'=>$organization_option,
							 'route_name'=>$this->getRouteName('requestMoveSave'),
							 'furniture_type'=>$furniture_type,
							 'request_model'=>$request_model,
							]
						   )
					  );
	
	}
	
	public function requestDisposal($furniture_type){
		
		return new Box("Furniture Management System",
					   view('exment_furniture_management_system::request/disposal',
							['title'=>'申請内容を入力してください',
							]
						   )
					  );
	
	}
	
	public function requestSale($furniture_type){
		
		return new Box("Furniture Management System",
					   view('exment_furniture_management_system::request/sale',
							['title'=>'申請内容を入力してください',
							]
						   )
					  );
	
	}
	
	public function requestChangeClassification($furniture_type){
		
		return new Box("Furniture Management System",
					   view('exment_furniture_management_system::request/change-classification',
							['title'=>'申請内容を入力してください',
							]
						   )
					  );
	
	}
	
	public function furnitureMap(){
		
		$properties=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->all();
		
		return new Box("Hard Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::furniture-map/property-select',
							['title'=>'拠点・部屋/エリア別家具配置(Furniture Location)',
							 'routeUri'=>$this->getPluginUri('furniture-map'),
							 'properties'=>$properties,
							]
						   )
					  );
	
	}
	
	public function furnitureMapSelectRoom($furniture_type,$property_id){
		
		$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
		
		if($furniture_type=="hard"){
			
			$room_list=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel()->where('parent_id',$property_id)->get();
			$hard_furniture_list=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->PropertyColumn_hard',$property_id)->get();
			$floor_room_list=FMSUtil::getHardFloorRoomList($hard_furniture_list,$room_list);
		
		}
		else {
			
			$room_list=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM)->getValueModel()->where('parent_id',$property_id)->get();
			$floor_room_list=FMSUtil::getFloorRoomList($room_list);
			
		}
		
		$next_url=$this->getPluginUri('furniture-map')."/".$furniture_type."/property/".$property_id."/room/";
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::furniture-map/room-select',
							['title'=>'拠点・部屋/エリア別家具配置(Furniture Location)',
							 'routeUri'=>$this->getPluginUri('furniture-map'),
							 'property'=>$property,
							 'floor_room_list'=>$floor_room_list,
							 'next_url'=>$next_url,
							]
						   )
					  );
	
	}
	
	public function furnitureMapShow($furniture_type,$property_id,$room_id){
		
		$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
		
		if($furniture_type=="hard"){
			
			$room_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM);
			$room=$room_table->getValueModel($room_id);
			$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->Room_Numbe_hard',$room_id)->get()->first();
		
		}
		else {
			
			$room_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM);
			$room=$room_table->getValueModel($room_id);
			$soft_furniture=CustomTable::getEloquent(TABLE_SOFT_FURNITURE)->getValueModel()->where('value->Room_Numbe',$room_id)->get();
			
		}
		
		$query=$room_table->getValueModel()->query();
		$query->where('parent_id','=',$property_id);
		$query->where('id','>',$room_id);
		$query->orderBy('id','asc');
		$next_room=$query->first();
		$query=$room_table->getValueModel()->query();
		$query->where('parent_id','=',$property_id);$query->where('id','<',$room_id);
		$query->orderBy('id','desc');
		$prev_room=$query->first();
		$routeUri=$this->getPluginUri('furniture-map');
		
		if($prev_room){
			
			$prev_room_url=$routeUri."/".$furniture_type."/property/".$property_id."/room/".$prev_room->id;
		
		}
		
		if($next_room){
			
			$next_room_url=$routeUri."/".$furniture_type."/property/".$property_id."/room/".$next_room->id;
		
		}
		
		return new Box("Soft Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::furniture-map/show',
							['plugin_top_url'=>$this->getPluginUri(''),
							 'routeUri'=>$routeUri,
							 'furniture_type'=>$furniture_type,
							 'property'=>$property,
							 'room'=>$room,
							 'prev_room_url'=>$prev_room_url??"",
							 'next_room_url'=>$next_room_url??"",
							 'soft_furniture_list'=>$soft_furniture??null,
							 'hard_furniture'=>$hard_furniture??null,
							]
						   )
					  );
	
	}
	
	public function reportTop(){
		
		$table=CustomTable::getEloquent(TABLE_INVENTORY_REPORT);
		$reports=$table->getValueModel()->whereNotNull('value->file')->get();
		
		return new Box("",view('exment_furniture_management_system::report/top',
							   ['title'=>'棚卸表・償却資産台帳出力',
								'reports'=>$reports,
								'create_url'=>$this->getPluginUri('report/create/setting'),
								'delete_url'=>$this->getPluginUri('report/delete'),
							   ]
							  )
					  );
	
	}
	
	public function reportCreateSetting(){
		
		$properties=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->all();
		$property_option=[];
		
		foreach($properties as $property){
			
			$property_option[$property->id]=$property->getValue('PropertyColumn');
		
		}
		
		return new Box("",view('exment_furniture_management_system::report/setting',
							   ['title'=>'棚卸表・償却資産台帳出力',
								'properties'=>$properties,
								'property_option'=>$property_option,
								'action_url'=>$this->getPluginUri('report/create'),
							   ]
							  )
					  );
	
	}
	
	public function reportSelectProperty(){
		
		$properties=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->all();
		
		return new Box("",view('exment_furniture_management_system::report/property-select',
							   ['title'=>'棚卸表・償却資産台帳出力',
								'properties'=>$properties,
								'next_url'=>$this->getPluginUri('report/create/property'),
							   ]
							  )
					  );
	
	}
	
	public function reportSetTerm($property_id){
		
		return new Box("",view('exment_furniture_management_system::report/term-select',
							   ['title'=>'棚卸表・償却資産台帳出力',
								'property_id'=>$property_id,
								'action_url'=>$this->getPluginUri('report/create'),
							   ]
							  )
					  );
	
	}
	
	public function reportCreate(){
		
		$param=request()->only(['property_ids','start_date','end_date','furniture_type']);
		
		if(!array_get($param,'property_ids')||!array_get($param,'start_date')||!array_get($param,'end_date')||(array_get($param,'furniture_type')!='soft'&&array_get($param,'furniture_type')!='hard')){
			
			admin_toastr('棚卸表を作成できませんでした','error');
			
			return redirect($this->getPluginUri('report'));
		
		}
		
		$result=false;
		
		if(array_get($param,'furniture_type')=='soft')$result=$this->createReportSoft($param);
		elseif(array_get($param,'furniture_type')=='hard')$result=$this->createReportHard($param);
		
		if($result){
			
			admin_toastr('棚卸表を作成しました');
			
			return redirect($this->getPluginUri('report'));
		
		}
		else{
			
			admin_toastr('棚卸表を作成できませんでした','error');
			
			return redirect($this->getPluginUri('report'));
		
		}
	
	}
	
	protected function createReportSoft($param){
		
		$property_ids=$param['property_ids'];
		$request_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST);
		$workflow=WorkFlow::getWorkflowByTable($request_table);
		
		if(!$workflow)return false;
		
		$workflow_status=WorkflowStatus::where('workflow_id',$workflow->id)->where('completed_flg',1)->get()->first();
		$workflow_value_list=WorkflowValue::where('morph_type',$request_table->table_name)->where('workflow_status_to_id',$workflow_status->id)->get()->pluck('morph_id')->all();
		$request_model=$request_table->getValueModel();
		$requests=$request_model->whereIn("value->Property",$property_ids)->whereIn('id',$workflow_value_list)->where('created_at','>=',$param['start_date'])->where('created_at','<',\Carbon\Carbon::parse($param['end_date'])->addDay())->get()->all();
		$property_table=CustomTable::getEloquent(TABLE_PROPERTIES);
		$properties_list=$property_table->getValueModel()->whereIn('id',$property_ids)->get()->pluck('value.PropertyColumn')->all();
		$properties_str_list=implode(' | ',$properties_list);
		$users=CustomTable::getEloquent(SystemTableName::USER)->getValueModel()->get();
		$table=CustomTable::getEloquent(TABLE_INVENTORY_REPORT);
		$model=$table->getValueModel()->create();
		$now=\Carbon\Carbon::now();
		$template_path=dirname(__FILE__)."/resources/template/template_soft.xlsx";
		$output_path=dirname(__FILE__)."/resources/template/temp_soft.xlsx";
		$save_file_name='棚卸表-'.$now->format('YmdHis').'-soft.xlsx';
		$spreadsheet=IOFactory::load($template_path);
		$sheet=$spreadsheet->getActiveSheet();
		$objStyle=$sheet->getStyle('J1:L1');
		$objStyle->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
		$objStyle=$sheet->getStyle('B7:L7');
		$objStyle->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
		$sheet->setCellValue('C3','#'.$model->id);
		$sheet->setCellValue('C4',$properties_str_list);
		$sheet->setCellValue('C5',\Carbon\Carbon::parse($param['start_date'])->format('Y年m月d日')." - ".\Carbon\Carbon::parse($param['end_date'])->format('Y年m月d日'));
		$count=8;
		$odd=true;
		$background_color='FFCFE2F3';
		
		foreach($requests as $request){
			
			$objStyle=$sheet->getStyle('B'.$count.':L'.$count);
			
			if($odd){
				
				$objFill=$objStyle->getFill();
				$objFill->setFillType(Fill::FILL_SOLID);
				$objFill->getStartColor()->setARGB($background_color);
			
			}
			
			$borderStyle=['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_HAIR,'color'=>['rgb'=>'000000']]]];
			$objStyle->applyFromArray($borderStyle);
			$id=$request->id;
			$input_date=\PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($request->created_at);
			$soft_furniture_inventories=$request->getValue("soft_furniture_inventories");
			$status_ok=0;
			$status_ng=0;
			
			foreach($soft_furniture_inventories as $inventory){
				
				$status=$inventory->getValue('JudgmentResult');
				
				if($status=='正常')$status_ok++;
				else $status_ng++;
			
			}
			
			$property=$request->getValue("Property");
			$property_name=$property->getValue("PropertyColumn");
			$manager=$property->getValue("community_manager");
			$manager_name=$manager?$manager->getValue("user_name"):"";
			$sheet->setCellValue('B'.$count,$id);
			$objStyle=$sheet->getStyle('B'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('C'.$count,$input_date);
			$objStyle=$sheet->getStyle('C'.$count);
			$objStyle->getNumberFormat()->setFormatCode('yyyy"年"m"月"d"日";@');
			$sheet->setCellValue('D'.$count,$property_name);
			$sheet->setCellValue('E'.$count,$status_ok+$status_ng);
			$objStyle=$sheet->getStyle('E'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('F'.$count,$status_ok);
			$objStyle=$sheet->getStyle('F'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('G'.$count,$status_ng);
			$objStyle=$sheet->getStyle('G'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('H'.$count,$manager_name);
			$objStyle=$sheet->getStyle('H'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			
			$count++;
			$odd=!$odd;
		
		}
		
		$writer=\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet,"Xlsx");
		$writer->save($output_path);
		$file=file_get_contents($output_path);
		self::setFile('file',$file,$table,$model,$save_file_name);
		
		return $model->save();
	
	}
	
	protected function createReportHard($param){
		
		$property_ids=$param['property_ids'];
		$inventory_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY);
		$workflow=WorkFlow::getWorkflowByTable($inventory_table);
		
		if(!$workflow)return false;
		
		$workflow_status=WorkflowStatus::where('workflow_id',$workflow->id)->where('completed_flg',1)->get()->first();$workflow_value_list=WorkflowValue::where('morph_type',$inventory_table->table_name)->where('workflow_status_to_id',$workflow_status->id)->get()->pluck('morph_id')->all();
		$target_list=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->whereIn('value->PropertyColumn_hard',$property_ids)->get()->pluck('id')->all();
		$inventory_model=$inventory_table->getValueModel();
		$inventories=$inventory_model->whereIn("value->Hardfurniture_Inventory_target",$target_list)->whereIn('id',$workflow_value_list)->where('created_at','>=',$param['start_date'])->where('created_at','<',\Carbon\Carbon::parse($param['end_date'])->addDay())->get()->all();
		$property_table=CustomTable::getEloquent(TABLE_PROPERTIES);
		$properties_list=$property_table->getValueModel()->whereIn('id',$property_ids)->get()->pluck('value.PropertyColumn')->all();
		$properties_str_list=implode(' | ',$properties_list);
		$users=CustomTable::getEloquent(SystemTableName::USER)->getValueModel()->get();
		$table=CustomTable::getEloquent(TABLE_INVENTORY_REPORT);
		$model=$table->getValueModel()->create();
		$now=\Carbon\Carbon::now();
		$template_path=dirname(__FILE__)."/resources/template/template_hard.xlsx";
		$output_path=dirname(__FILE__)."/resources/template/temp_hard.xlsx";
		$save_file_name='棚卸表-'.$now->format('YmdHis').'-hard.xlsx';
		$spreadsheet=IOFactory::load($template_path);
		$sheet=$spreadsheet->getActiveSheet();
		$objStyle=$sheet->getStyle('K1:M1');
		$objStyle->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
		$objStyle=$sheet->getStyle('B7:M7');
		$objStyle->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
		$sheet->setCellValue('C3','#'.$model->id);
		$sheet->setCellValue('C4',$properties_str_list);
		$sheet->setCellValue('C5',\Carbon\Carbon::parse($param['start_date'])->format('Y年m月d日')." - ".\Carbon\Carbon::parse($param['end_date'])->format('Y年m月d日'));
		$count=8;
		$odd=true;
		$background_color='FFCFE2F3';
		
		foreach($inventories as $inventory){
			
			$objStyle=$sheet->getStyle('B'.$count.':M'.($count+2));
			
			if($odd){
				
				$objFill=$objStyle->getFill();
				$objFill->setFillType(Fill::FILL_SOLID);
				$objFill->getStartColor()->setARGB($background_color);
			
			}
			
			$borderStyle=['borders'=>['allBorders'=>['borderStyle'=>Border::BORDER_HAIR,'color'=>['rgb'=>'000000']]]];
			$objStyle->applyFromArray($borderStyle);
			$id=$inventory->id;
			$input_date=\PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($inventory->created_at);
			$hard_furniture=$inventory->getValue("Hardfurniture_Inventory_target");
			$property=$hard_furniture->getValue("PropertyColumn_hard");
			$room=$hard_furniture->getValue("Room_Numbe_hard");
			$property_name=$property->getValue("PropertyColumn");
			$manager=$property->getValue("community_manager");
			$manager_name=$manager?$manager->getValue("user_name"):"";
			$desk=$inventory->getValue("Desk_Inventory");
			$chair=$inventory->getValue("chair_Inventory");
			$peds=$inventory->getValue("Peds_Inventory");
			$theoretical_desk=$desk-$inventory->getValue("Desk_Defective_Inventory");
			$theoretical_chair=$chair-$inventory->getValue("chair_Defective_Inventory");
			$theoretical_peds=$peds-$inventory->getValue("peds_Defective_Inventory");
			$diff_desk=$theoretical_desk-$desk;
			$diff_chair=$theoretical_chair-$chair;
			$diff_peds=$theoretical_peds-$peds;
			$sheet->setCellValue('B'.$count,$id);
			$objStyle=$sheet->getStyle('B'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('C'.$count,$input_date);
			$objStyle=$sheet->getStyle('C'.$count);
			$objStyle->getNumberFormat()->setFormatCode('yyyy"年"m"月"d"日";@');
			$sheet->setCellValue('D'.$count,$property_name);
			$sheet->setCellValue('E'.$count,"椅子");
			$objStyle=$sheet->getStyle('E'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('F'.$count,$theoretical_chair);
			$objStyle=$sheet->getStyle('F'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('G'.$count,$chair);
			$objStyle=$sheet->getStyle('G'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('H'.$count,$diff_chair);
			$objStyle=$sheet->getStyle('H'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$objStyle->getNumberFormat()->setFormatCode('+#,##0;-#,##0;#,##0');
			$sheet->setCellValue('I'.$count,$manager_name);
			$objStyle=$sheet->getStyle('I'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$count++;
			$sheet->setCellValue('B'.$count,$id);$objStyle=$sheet->getStyle('B'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('C'.$count,$input_date);
			$objStyle=$sheet->getStyle('C'.$count);
			$objStyle->getNumberFormat()->setFormatCode('yyyy"年"m"月"d"日";@');
			$sheet->setCellValue('D'.$count,$property_name);
			$sheet->setCellValue('E'.$count,"デスク");
			$objStyle=$sheet->getStyle('E'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('F'.$count,$theoretical_desk);
			$objStyle=$sheet->getStyle('F'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('G'.$count,$desk);
			$objStyle=$sheet->getStyle('G'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('H'.$count,$diff_desk);
			$objStyle=$sheet->getStyle('H'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$objStyle->getNumberFormat()->setFormatCode('+#,##0;-#,##0;#,##0');
			$sheet->setCellValue('I'.$count,$manager_name);
			$objStyle=$sheet->getStyle('I'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$count++;
			$sheet->setCellValue('B'.$count,$id);
			$objStyle=$sheet->getStyle('B'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('C'.$count,$input_date);
			$objStyle=$sheet->getStyle('C'.$count);
			$objStyle->getNumberFormat()->setFormatCode('yyyy"年"m"月"d"日";@');
			$sheet->setCellValue('D'.$count,$property_name);
			$sheet->setCellValue('E'.$count,"袖ワゴン");
			$objStyle=$sheet->getStyle('E'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('F'.$count,$theoretical_peds);
			$objStyle=$sheet->getStyle('F'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('G'.$count,$peds);
			$objStyle=$sheet->getStyle('G'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$sheet->setCellValue('H'.$count,$diff_peds);
			$objStyle=$sheet->getStyle('H'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$objStyle->getNumberFormat()->setFormatCode('+#,##0;-#,##0;#,##0');
			$sheet->setCellValue('I'.$count,$manager_name);
			$objStyle=$sheet->getStyle('I'.$count);
			$objStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
			$count++;
			$odd=!$odd;
		
		}
		
		$writer=\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet,"Xlsx");
		$writer->save($output_path);
		$file=file_get_contents($output_path);
		self::setFile('file',$file,$table,$model,$save_file_name);
		
		return $model->save();
	
	}
	
	public function reportDelete(){
		
		$param=request()->only('report_id');
		
		if(!$param)return redirect($this->getPluginUri('report'));
		
		$report=CustomTable::getEloquent(TABLE_INVENTORY_REPORT)->getValueModel(array_get($param,'report_id'));
		$report->delete();
		admin_toastr('1件削除しました');
		
		return redirect($this->getPluginUri('report'));
	
	}
	
	protected function setFile($column_name,$file,$table,$request_model,$file_name){
		
		$column=\Exceedone\Exment\Model\CustomColumn::getEloquent($column_name,$table->table_name);
		$fileInfo=ExmentFile::storeAs(FileType::CUSTOM_VALUE_COLUMN,$file,$table->table_name,$file_name)->saveCustomValue($request_model->id,$column->id,$table);$request_model->setValue($column_name,$fileInfo->path);
	
	}
	
	public function floorMap(){
		
		$properties=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->all();
		
		return new Box("Hard Furniture Onsite Inventory Counting",
					   view('exment_furniture_management_system::floor-map/select-property',
							['title'=>'フロアマップ',
							 'properties'=>$properties,
							]
						   )
					  );
	
	}
	
	public function apiSetting(){
		
		$query=http_build_query(['client_id'=>$this->plugin->getCustomOption('client_id'),'redirect_uri'=>$this->plugin->getCustomOption('redirect_url'),'response_type'=>'code','scope'=>'plugin',]);
		
		return redirect(admin_url('oauth/authorize?').$query);
	
	}
	
	public function apiCallback(){
		
		$param=request()->only("code");
		$http=new Client;
		$option=['form_params'=>['grant_type'=>'authorization_code','client_id'=>$this->plugin->getCustomOption('client_id'),'client_secret'=>$this->plugin->getCustomOption('client_secret'),'redirect_uri'=>$this->plugin->getCustomOption('redirect_url'),'code'=>$param['code'],],];
		
		if(isDevEnv())$option['verify']=false;
		
		$response=$http->post(admin_url('oauth/token'),$option);
		$json_str=(string) $response->getBody();
		$json=json_decode((string) $response->getBody(),true);
		
		return new Box("Furniture Management System",
					   view('exment_furniture_management_system::common/api_callback',
							['json_str'=>$json_str,]
						   )
					  );
	
	}
	
	protected function getIndexBox(){
		
		$box=new Box("",view('exment_furniture_management_system::index',['routeUri'=>$this->getPluginUri('')]));
		
		return $box;
	
	}
	
	public function setCustomOptionForm(&$form){
		
		$form->textarea('request_dialog','申請確認ダイアログ')->help('パラメータ変数について。 ${request:furniture_type} は申請のハード/ソフトファニチャーと置き換えます。 ${request:request_type} は申請種類と置き換えます。');
		$form->text('client_id','Client ID')->help('画面ログイン形式APIのClient IDを入力してください。');
		$form->text('client_secret','Client Secret')->help('画面ログイン形式APIのClient Secretを入力してください。');
		$form->text('redirect_url','Client Secret')->help('画面ログイン形式APIのリダイレクトURLを入力してください。');
		$form->text('bearer_token','Bearerトークン')->help('APIキー形式のBearerトークンを入力してください。（Expire in 365d）');
		$form->text('environment','環境');
	
	}
	
	protected function getPluginUri($res=''){
		
		return $this->plugin->getFullUrl($res);
	
	}
	
	public function getRouteName($func,$method="post"){
		
		return "exment.plugins.".$this->plugin->id.".".$method.".".$func;
	
	}
	
	public function isDevEnv(){
		
		return $this->plugin->getCustomOption('environment')=="development";
	
	}
	
	public function test(){
		
		if(!$this->isDevEnv())return;
		echo "<pre>";
		print_r($result??"");
		echo "</pre>";
		die();
	
	}
	
	public function testtest($furniture_id) {
		$furnitures=CustomTable::getEloquent('SoftFurnitureDB')->getValueModel($furniture_id);
		$value = $furnitures->getValue('Item_Identifier_number')->getValue('Item_Identifier');
		return response()->json(['sku' => $value, 'po' => $furnitures->getValue('PO_Number')]);
	}
}