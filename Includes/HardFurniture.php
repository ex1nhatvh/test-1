<?php 
namespace App\Plugins\FurnitureManagementSystem\Includes;

use DateTime;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Exceedone\Exment\Model\CustomTable;
use Exceedone\Exment\Model\Workflow;
use Exceedone\Exment\Model\WorkflowAction;
use Exceedone\Exment\Model\WorkflowStatus;
use Exceedone\Exment\Model\WorkflowValue;
use Encore\Admin\Widgets\Form as WidgetForm;
use Exceedone\Exment\Model\CustomView;
use Illuminate\Http\Request;

if(!class_exists('HardFurniture')){
	final class HardFurniture{

		const CLASSNAME_CUSTOM_VALUE_GRID = 'block_custom_value_grid';
		const CLASSNAME_CUSTOM_VALUE_PREFIX = 'custom_value_';

		protected static $plugin=null;
		public static function setPlugin($plugin){
			self::$plugin=$plugin;
		}
		public static function registry(){
			$properties=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->get();
			
			return new Box("Hard Furniture Registry Initial Inventory Counting",
						   view('exment_furniture_management_system::common/property-select',
								['title'=>'ハードファニチャー初期現地登録',
								 'properties'=>$properties,
								 'routeUri'=>admin_url(self::$plugin->getRouteUri('hf-registry')),
								]
							   )
						  );
		}
		
		public static function registrySelectRoom($property_id){
			$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
			$room_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM);
			$room_list=$room_table->getValueModel()->where('parent_id',$property_id)->get();
			$hard_furniture_list=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->PropertyColumn_hard',$property_id)->get();
			$floor_room_list=self::getFloorRoomList($hard_furniture_list,$room_list);
			
			return new Box("Hard Furniture Registry Initial Inventory Counting",
						   view('exment_furniture_management_system::common/room-select',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('hf-registry')),
								 'property'=>$property,'floor_room_list'=>$floor_room_list,
								]
							   )
						  );
		}
		
		public static function registryRegist($property_id,$room_id){
			$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->Room_Numbe_hard',$room_id)->get()->first();
			$hard_furniture_data=["id"=>$hard_furniture->id,
								  "PropertyColumn_hard"=>array_get($hard_furniture->getValue("PropertyColumn_hard"),"value.PropertyColumn"),
								  "value"=>[
									  "HardFurniture_hard"=>["name"=>"Desk","count"=>array_get($hard_furniture,"value.HardFurniture_hard")?? 0],
									  "HardFurniture_Chair"=>["name"=>"Chair","count"=>array_get($hard_furniture,"value.HardFurniture_Chair")?? 0],
									  "HardFurniture_Peds"=>["name"=>"Peds","count"=>array_get($hard_furniture,"value.HardFurniture_Peds")?? 0],
									  "HardFurniture_Desk_Defective"=>["name"=>"DeskDefective","count"=>array_get($hard_furniture,"value.HardFurniture_Desk_Defective")?? 0],
									  "HardFurniture_Chair_Defective"=>["name"=>"ChairDefective","count"=>array_get($hard_furniture,"value.HardFurniture_Chair_Defective")?? 0],
									  "HardFurniture_Peds_Defective"=>["name"=>"PedsDefective","count"=>array_get($hard_furniture,"value.HardFurniture_Peds_Defective")?? 0],
								  ],
								  "HardFurniture_Room_Numbe"=>array_get($hard_furniture->getValue("Room_Numbe_hard"),"value.HardFurniture_Room_Numbe"),
								 ];
			
			return new Box("Hard Furniture Registry Initial Inventory Counting",
						   view('exment_furniture_management_system::hf-registry/regist',
								['title'=>'ハードファニチャー初期現地登録',
								 'routeUri'=>admin_url(self::$plugin->getRouteUri('hf-registry')),
								 'action_url'=>admin_url(self::$plugin->getRouteUri('hf-registry'))."/property/".$property_id."/room/".$room_id."/save/".$hard_furniture->id,
								 'hard_furniture'=>$hard_furniture_data,
								]
							   )
						  );
		}

		public static function onsiteMerge(Request $request)
        {
            // Create content
			$content = new Content();

            // Create form
			$form = new WidgetForm();
			$form->action(admin_url(self::$plugin->getRouteUri('hf-onsite')) . '/merge');
            $form->method('GET');
			$form->attribute('id', 'selectbox'.TABLE_PROPERTIES.TABLE_HARD_FURNITURE_INVENTORY);
            $form->disableReset();
            $form->disableSubmit();

            // Get property option
            $properties = CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->get();
            $property_option = [''=>''];
            
            foreach($properties as $property){
                $property_option[$property->id]=$property->getValue("PropertyColumn");
            }

            // Set date time range field
            $html = view('exment::form.field.datetimerange', [
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
            ])->render();

            $form->html($html);

            // Set select box and submit button field
            $html = view('exment::form.field.selectbox_and_submitbtn', [
                'property_option' => $property_option,
                'property_id' => $request->get('property_id'),
                'mergeDB' => 'LocationRoom',
            ])->render();

            $form->html($html);
			
			// Set row for content
			$content->row($form);

            ////////////////////
            // Get table
            $hardFurniture_inventory_table = CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY);
            
            // Get view to display
            $hardFurniture_inventory_view = CustomView::getDefault($hardFurniture_inventory_table);
            
            // Set up display table
            $grid_item = $hardFurniture_inventory_view->grid_item->modal(false);
            $grid_item->callback($grid_item->getCallbackFilter());
            
            $grid = $grid_item->grid();
			$grid->model()->orderBy('value->request_date', 'desc');
            if($request->get('property_id')) {
                $hardFurnitureDBRequests = CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->PropertyColumn_hard', $request->get('property_id'))->get();
                $Hardfurniture_Inventory_target = [];
                foreach($hardFurnitureDBRequests as $hardFurnitureDBRequest){
                    $Hardfurniture_Inventory_target[] = $hardFurnitureDBRequest->id;
                }
                
                $grid->model()->whereIn('value->Hardfurniture_Inventory_target', $Hardfurniture_Inventory_target);
            }
            
            // Set disable action and tools
            $grid->disableActions();
            $grid->tools(function (Grid\Tools $tools) {
                $tools->disableButtonInRight();
                $tools->disableBatchActions();
            });

            // Set view for grid
            $grid->setView('admin::grid.table-merge');

            // Set variable for view
            $grid->setVariableCustom('input_of_form', 'form_hard_btn_merge');

            // Exist request filter
            if ($request->get('start_date')) {
                $startDate = new DateTime($request->get('start_date'));
                $endDate = new DateTime($request->get('end_date'));
                $propertyId = $request->get('property_id');

                // Get merge row auto check
                $mereg_row = CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY)->getValueModel();
                $mereg_row = $mereg_row->where('value->request_date', '>=', $startDate);
                if($endDate){
                    $mereg_row = $mereg_row->where('value->request_date', '<=', $endDate);
                }
                if($propertyId){
                    $mereg_row = $mereg_row->whereIn('value->Hardfurniture_Inventory_target', $Hardfurniture_Inventory_target);
                }
                
                $mr = [];
                foreach($mereg_row->get() as $row){
                    $mr[] = $row->id;
                }

                // Set background for row when checked
                $grid->rows(function ($row) use($mr) {
                    if ( in_array($row->column('id'), $mr)) {
                        $row->setAttributes([ 'data-row-id' => $row->model()['id'] ]);
                        $row->style("background-color:#ffffd5");
                    }
                });
            }

            // Create row and set display table for this row
            $row = new Row($grid);
            $row->class([static::CLASSNAME_CUSTOM_VALUE_GRID, static::CLASSNAME_CUSTOM_VALUE_PREFIX . $hardFurniture_inventory_table->table_name]);

            // Add row for content
            $content->row($row);

            //////////////////////
            // Create button merge
            $form = new WidgetForm();
            $form->method('GET');
            $form->attribute('id', 'form_hard_btn_merge');

            if (isset($mr)) {
                // If check row merge, add request merge to action
                $mr = json_encode($mr);
				$form->html('
					<script>
						$(function() {
							var mr = ' . $mr . ';
							$.admin.grid.selects = {};
							mr.forEach((id) => {
								$.admin.grid.select(id);
								
								// Check ui
								$("tr[data-row-id=" + id + "]").iCheck("check");
							});
						});
					</script>'
				);

                // Auto set id when auto check chekcbox
                $form->html('
                    <script>
                        $(function() {
                            $("#form_hard_btn_merge").attr("action", "' . admin_url(self::$plugin->getRouteUri('hf-onsite')) . '/merge/table-merge?merge_row=" + $.admin.grid.selected().join());
                        });
                    </script>'
                );

                // When click change chekcbox
                $form->html('
                    <script>
                        $(function() {
                            $(".grid-row-checkbox").on("ifChanged", function(){
                                $("#form_hard_btn_merge").attr("action", "' . admin_url(self::$plugin->getRouteUri('hf-onsite')) . '/merge/table-merge?merge_row=" + $.admin.grid.selected().join()); 
                            });
                        });
                    </script>'
                );
            } else {
				// When click change chekcbox
				$form->html('<script>
					$(function() {
						$.admin.grid.selects = {};
						$(".grid-row-checkbox").on("ifChanged", function(){
							$("#form_hard_btn_merge").attr("action", "' . admin_url(self::$plugin->getRouteUri('hf-onsite')) . '/merge/table-merge?merge_row=" + $.admin.grid.selected().join()); 
						});
					});
					</script>'
				);
			}

            $form->disableReset();
            $form->disableSubmit();

            // Set button submit
            $html = '<button type="submit" form="form_hard_btn_merge" class="btn btn-default pull-right">申請</button>';
            $form->html($html);

            // Set form for row
            $row = new Row($form);
            $row->class(["pull-right"]);
            // Add row for content
            $content->row($row);

            return new Box("Soft Furniture Onsite Inventory Counting", view('exment_furniture_management_system::hf-onsite/merge', 
                            [
                                'title' => "ハードファニチャー棚卸",
                                'content' => $content,
                            ]
                        ));
        }
        
        public static function onsiteTableMerge(Request $request) {
            // Create content
			$content = new Content();

            // Get merge row
            $ids_before_merge = $request->get('merge_row');
            
			$arrayItems = array();
			$merge_item = array();
            $read_ids = array();
            $Inventory_Hard_comment = [];

            $rows = collect(explode(',', $ids_before_merge))->filter();
            $rows->each(function ($id) use (&$arrayItems, &$merge_item, &$read_ids, &$request, &$Inventory_Hard_comment) {
				// {EでチェックされているレコードID}.furnitures(WHERE Property AND Floor_Name_inventory AND Room_Numbe_Inventry)
                $hardFurnitureInventory = CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY)->getValueModel()->where('id', $id)->first();
                $property_hard = $hardFurnitureInventory->getValue('Hardfurniture_Inventory_target')->getValue('PropertyColumn_hard')->id;
                $floor_name_hard = $hardFurnitureInventory->getValue('Hardfurniture_Inventory_target')->getValue('Floor_Name_nard');
                $room_numbe_hard = $hardFurnitureInventory->getValue('Hardfurniture_Inventory_target')->getValue('Room_Numbe_hard')->id;
                if($property_hard && $floor_name_hard && $room_numbe_hard) {
                    $read_ids[] = $id;

                    // Get comment to insert inventory_Hard_comment column in HardFurniture_merge_apply_inv table
                    if($request->get('input_text_row_' . $id . '')){
                        $Inventory_Hard_comment[$id] = $request->get('input_text_row_' . $id . '');
                    }

                    $items = $room_numbe_hard . '-' . $hardFurnitureInventory->getValue('Desk_Inventory') . ',' . $hardFurnitureInventory->getValue('chair_Inventory') . ',' . $hardFurnitureInventory->getValue('Peds_Inventory');
                    // Desk、Chair、Pedsの数値が一致しない場合は全て残す
					if(!in_array($items, $arrayItems)) {
						$arrayItems[] = $items;
						$merge_item[] = $hardFurnitureInventory;
					} else if (in_array($items, $arrayItems)){
						$indexExistItems = array_search($items, $arrayItems);
                        // Desk、Chair、Pedsの数値が一致する場合は、最新のデータを残す
						if($merge_item[$indexExistItems]->getValue('request_date') <= $hardFurnitureInventory->getValue('request_date')) {
							// Unset old value
							unset($arrayItems[$indexExistItems]);
							unset($merge_item[$indexExistItems]);
							// Set new value for array
							$arrayItems[] = $items;
							$merge_item[] = $hardFurnitureInventory;
						}
					}
                }
            });
            
            foreach($merge_item as $item){
                $hardFurnitureMergeApplyInv = CustomTable::getEloquent(TABLE_HARD_FURNITURE_REQUEST_MERGE)->getValueModel()->where('value->id_before_merge', $item->id)->first();
                if(!$hardFurnitureMergeApplyInv){
                    // Insert HardFurniture_merge_apply_inventory table
                    $merge=CustomTable::getEloquent(TABLE_HARD_FURNITURE_REQUEST_MERGE)->getValueModel()->create();
                    $merge->setValue('id_before_merge', $item->id);
                    $merge->setValue('Desk_inventory', $item->getValue('Desk_Inventory'));
                    $merge->setValue('Chair_inventory', $item->getValue('chair_Inventory'));
                    $merge->setValue('Peds_inventory', $item->getValue('Peds_Inventory'));
                    $merge->setValue('Desk_Defective_Inventory', $item->getValue('Desk_Defective_Inventory'));
                    $merge->setValue('chair_Defective_Inventory', $item->getValue('chair_Defective_Inventory'));
                    $merge->setValue('peds_Defective_Inventory', $item->getValue('peds_Defective_Inventory'));
                    if($item->getValue('Hardfurniture_Inventory_target')){
                        $merge->setValue('Hardfurniture_Inventory_target', $item->getValue('Hardfurniture_Inventory_target')->id);
                    }
                    $merge->setValue('request_date', date('Y-m-d H:i:s'));
                    if(isset($Inventory_Hard_comment[$item->id])){
                        $merge->setValue('Inventory_Hard_comment', $Inventory_Hard_comment[$item->id]);
                    }
                    $merge->save();
					
					$table = CustomTable::getEloquent(TABLE_HARD_FURNITURE_REQUEST_MERGE);
					$workflow=WorkFlow::getWorkflowByTable($table);
						
					if($workflow) {
				
						$workflow_start_action=WorkflowAction::where('workflow_id',$workflow->id)->where('status_from','start')->get()->first();
						$workflow_status=WorkflowStatus::where('workflow_id',$workflow->id)->get();
						$workflow_start_status=WorkflowStatus::where('workflow_id',$workflow->id)->where('order',1)->get()->first();
						$status_to=$workflow_start_status->id;
						$morph_type=$table->table_name;
						$morph_id=$merge->id;
						$createData=['workflow_id'=>$workflow->id,'morph_type'=>$morph_type,'morph_id'=>$morph_id,'workflow_action_id'=>$workflow_start_action->id,'workflow_status_from_id'=>null,'workflow_status_to_id'=>$status_to,'latest_flg'=>1,];
						$created_workflow_value=WorkflowValue::create($createData);

					}
                }
            }
			
            // Get table merge
            $hardFurniture_merge_apply_inv_table = CustomTable::getEloquent(TABLE_HARD_FURNITURE_REQUEST_MERGE);
            
            // Get view to display
            $hardFurniture_merge_apply_inv_view = CustomView::getDefault($hardFurniture_merge_apply_inv_table);
            
            // Set up display table
            $grid_item = $hardFurniture_merge_apply_inv_view->grid_item->modal(false);
            $grid_item->callback($grid_item->getCallbackFilter());
            
            $grid = $grid_item->grid();
            $grid->model()->whereIn('value->id_before_merge', $read_ids)->orderBy('id', 'desc');
                
            // Set disable action and tools
            $uri = admin_url('data/' . TABLE_HARD_FURNITURE_REQUEST_MERGE);
            $grid->actions(function ($actions) use($uri) {
				$actions->setResource($uri);
				$actions->disableDelete();
                $actions->disableEdit();
			});
			$grid->tools(function (Grid\Tools $tools) {
				$tools->disableButtonInLeft();
				$tools->disableButtonInRight();
			});

            // Create row and set display table for this row
            $row = new Row($grid);
            $row->class([static::CLASSNAME_CUSTOM_VALUE_GRID, static::CLASSNAME_CUSTOM_VALUE_PREFIX . $hardFurniture_merge_apply_inv_table->table_name]);

            // Add row for content
            $content->row($row);

            // Set url is default action for button back
            $form = new WidgetForm();

            $form->disableReset();
            $form->disableSubmit();
            $form->html('
                    <script>
                        $(function() {
                            $(".back").attr("href", "' . admin_url(self::$plugin->getRouteUri('hf-onsite')) . '/merge");
                            $(".back").removeAttr("role");
                        });
                    </script>'
                );
            $content->row($form);

            return new Box("Soft Furniture Onsite Inventory Counting", view('exment_furniture_management_system::hf-onsite/table-merge', 
                            [
                                'title' => "ハードファニチャー棚卸",
                                'content' => $content,
                            ]
                        ));
        }

		public static function onsiteAfterMerge() {
            // Create content
			$content = new Content();
			
            // Get table merge
            $hardFurniture_merge_apply_inv_table = CustomTable::getEloquent(TABLE_HARD_FURNITURE_REQUEST_MERGE);
            
            // Get view to display
            $hardFurniture_merge_apply_inv_view = CustomView::getDefault($hardFurniture_merge_apply_inv_table);
            
            // Set up display table
            $grid_item = $hardFurniture_merge_apply_inv_view->grid_item->modal(false);
            $grid_item->callback($grid_item->getCallbackFilter());
            
            $grid = $grid_item->grid();
            $grid->model()->orderBy('id', 'desc');
                
            // Set disable action and tools
            $uri = admin_url('data/' . TABLE_HARD_FURNITURE_REQUEST_MERGE);
            $grid->actions(function ($actions) use($uri) {
				$actions->setResource($uri);
				$actions->disableDelete();
                $actions->disableEdit();
			});
			$grid->tools(function (Grid\Tools $tools) {
				$tools->disableButtonInLeft();
				$tools->disableButtonInRight();
			});

            // Create row and set display table for this row
            $row = new Row($grid);
            $row->class([static::CLASSNAME_CUSTOM_VALUE_GRID, static::CLASSNAME_CUSTOM_VALUE_PREFIX . $hardFurniture_merge_apply_inv_table->table_name]);

            // Add row for content
            $content->row($row);

            return new Box("Soft Furniture Onsite Inventory Counting", view('exment_furniture_management_system::hf-onsite/after-merge', 
                            [
                                'title' => "ハードファニチャー棚卸",
                                'content' => $content,
                            ]
                        ));
        }
		
		public static function registryResult($property_id,$room_id,$hard_furniture_id){
			
			$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
			$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
			$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($hard_furniture_id);
			$query=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->query();
			$query->where('value->PropertyColumn_hard','=',$property_id);$query->where('id','>',$hard_furniture_id);
			$query->orderBy('id','asc');$next_hard_furniture=$query->first();
			$query=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->query();
			$query->where('value->PropertyColumn_hard','=',$property_id);
			$query->where('id','<',$hard_furniture_id);$query->orderBy('id','desc');
			$prev_hard_furniture=$query->first();
			
			if($prev_hard_furniture){
				$prev_room=$prev_hard_furniture->getValue('Room_Numbe_hard');
				if($prev_room)$prev_hard_furniture_url=admin_url(self::$plugin->getRouteUri('hf-registry'))."/property/".$property_id."/room/".$prev_room->id;
			}
			
			if($next_hard_furniture){
				$next_room=$next_hard_furniture->getValue('Room_Numbe_hard');
				if($next_room)$next_hard_furniture_url=admin_url(self::$plugin->getRouteUri('hf-registry'))."/property/".$property_id."/room/".$next_room->id;
			}
			
			return new Box("Hard Furniture Registry Initial Inventory Counting",
						   view('exment_furniture_management_system::hf-registry/result',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								 'property'=>$property,
								 'room'=>$room,
								 'hard_furniture'=>$hard_furniture,
								 'next_hard_furniture_url'=>$next_hard_furniture_url ?? "",
								 'prev_hard_furniture_url'=>$prev_hard_furniture_url ?? ""
								]
							   )
						  );
		
		}
		
		public static function onsite(){
			
			$login_user=\Exment::user();
			$inventory_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY);
			$inventory_model=$inventory_table->getValueModel();
			$latest=$inventory_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('updated_at','desc')->first();
			
			if($latest){
				$draft_id=$latest->id;
				$hard_furniture=$latest->getValue("Hardfurniture_Inventory_target");
				if($hard_furniture){
					$hard_furniture_id=$hard_furniture->id;
					$property_id=$hard_furniture->getValue("PropertyColumn_hard")->id;
					$room_id=$hard_furniture->getValue("Room_Numbe_hard")->id;
				}
			}
			
			return new Box("Hard Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::hf-onsite/top',
								['title'=>"ハードファニチャー棚卸",
								 'routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								 'property_id'=>$property_id ?? null,
								 'room_id'=>$room_id ?? null,
								 'hard_furniture_id'=>$hard_furniture_id ?? null,
								 'draft_id'=>$draft_id ?? null,
								]
							   )
						  );
		
		}
		
		public static function onsiteSelectProperty(){
			
			$properties=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel()->get();
			
			return new Box("Hard Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::common/property-select',
								['title'=>'ハードファニチャー棚卸',
								 'properties'=>$properties,
								 'routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								]
							   )
						  );
		
		}
		
		public static function onsiteSelectRoom($property_id){
			
			$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
			$room_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM);
			$room_list=$room_table->getValueModel()->where('parent_id',$property_id)->get();
			$hard_furniture_list=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->PropertyColumn_hard',$property_id)->get();
			$floor_room_list=[];
			
			foreach($room_list as $room){
				
				$floor_name=$room->getValue("HardFurniture_Floor_Name");
				if(!array_key_exists($floor_name,$floor_room_list))$floor_room_list[$floor_name]=[];
				$floor_room_list[$floor_name][]=["id"=>$room->id,"name"=>$room->getValue("HardFurniture_Room_Numbe")];
			
			}
			
			return new Box("Hard Furniture Registry Initial Inventory Counting",
						   view('exment_furniture_management_system::common/room-select',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								 'property'=>$property,
								 'floor_room_list'=>$floor_room_list,
								]
							   )
						  );
		
		}
		
		public static function onsiteRegist($property_id,$room_id){
			
			$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
			$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
			$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->where('value->Room_Numbe_hard',$room_id)->get()->first();
			
			return new Box("Hard Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::hf-onsite/regist',
								['title'=>'ハードファニチャー棚卸',
								 'property'=>$property,
								 'room'=>$room,
								 'routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								 'hard_furniture'=>$hard_furniture,
								 'plugin'=>self::$plugin,
								]
							   )
						  );
		
		}
		
		public static function onsiteResult($property_id,$room_id,$hard_furniture_id,$hard_furniture_inventory_id){
			
			$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
			$room=CustomTable::getEloquent(TABLE_HARD_FURNITURE_ROOM)->getValueModel($room_id);
			$hard_furniture=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel($hard_furniture_id);
			$hard_furniture_inventory=CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY)->getValueModel($hard_furniture_inventory_id);
			$query=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->query();
			$query->where('value->PropertyColumn_hard','=',$property_id);
			$query->where('id','>',$hard_furniture_id);
			$query->orderBy('id','asc');
			$next_hard_furniture=$query->first();
			$query=CustomTable::getEloquent(TABLE_HARD_FURNITURE)->getValueModel()->query();
			$query->where('value->PropertyColumn_hard','=',$property_id);
			$query->where('id','<',$hard_furniture_id);
			$query->orderBy('id','desc');
			$prev_hard_furniture=$query->first();
			
			if($prev_hard_furniture){
				
				$prev_room=$prev_hard_furniture->getValue('Room_Numbe_hard');
				
				if($prev_room)$prev_hard_furniture_url=admin_url(self::$plugin->getRouteUri('hf-onsite'))."/property/".$property_id."/room/".$prev_room->id;
			
			}
			
			if($next_hard_furniture){
				
				$next_room=$next_hard_furniture->getValue('Room_Numbe_hard');
				
				if($next_room)$next_hard_furniture_url=admin_url(self::$plugin->getRouteUri('hf-onsite'))."/property/".$property_id."/room/".$next_room->id;
			
			}
			
			return new Box("Hard Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::hf-onsite/result',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								 'property'=>$property,
								 'room'=>$room,
								 'hard_furniture'=>$hard_furniture,
								 'next_hard_furniture_url'=>$next_hard_furniture_url ?? "",
								 'prev_hard_furniture_url'=>$prev_hard_furniture_url ?? "",
								 'hard_furniture_inventory'=>$hard_furniture_inventory,
								 'plugin_top_url'=>self::$plugin->getFullUrl(''),
								]
							   )
						  );
		
		}
		
		public static function onsiteRequestList(){
			
			$login_user=\Exment::user();
			$inventory_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY);
			$inventory_model=$inventory_table->getValueModel();
			$unrequested=$inventory_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('created_at','asc')->get()->first();
			$unrequested_new=$inventory_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('created_at','desc')->get()->first();
			$requested=$inventory_model->where('created_user_id',$login_user->base_user_id)->whereNotNull('value->request_date')->orderBy('value->request_date','desc')->get();
			$requested_date_list=[];
			
			foreach($requested as $item){
				
				$date=\Carbon\Carbon::parse($item->getValue('request_date'))->format('Y-m-d');
				
				if(!in_array($date,$requested_date_list,true))$requested_date_list[]=$date;
			
			}
			
			$inventories=$inventory_model->where('created_user_id',$login_user->base_user_id)->get();
			
			return new Box("Hard Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::hf-onsite/request',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								 'unrequested'=>$unrequested,
								 'unrequested_new'=>$unrequested_new,
								 'requested_date_list'=>$requested_date_list,
								]
							   )
						  );
		
		}
		
		public static function onsiteRequestedDetail($date){
			
			$login_user=\Exment::user();
			$request_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY);
			$request_model=$request_table->getValueModel();
			$startDate=\Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
			$endDate=\Carbon\Carbon::parse($date)->addDay()->format('Y-m-d H:i:s');
			$requested=$request_model->where('created_user_id',$login_user->base_user_id)->where('value->request_date','>=',$startDate)->where('value->request_date','<',$endDate)->orderBy('value->request_date','asc')->get();
			
			return new Box("Hard Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::hf-onsite/detail',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								 'request_list'=>$requested,
								]
							   )
						  );
		
		}
		
		public static function onsiteUnrequestedDetail(){
			
			$login_user=\Exment::user();
			$request_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY);
			$request_model=$request_table->getValueModel();
			$unrequested=$request_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('created_at','asc')->get();
			$unrequested_new=$request_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('created_at','desc')->get()->first();
			$request_id=$unrequested_new->id;
			
			$hard_furniture=$unrequested_new->getValue("Hardfurniture_Inventory_target");
			$hard_furniture_id=$hard_furniture->id;
			$property=$hard_furniture->getValue("PropertyColumn_hard");
			$property_id=$property->id;
			$room=$hard_furniture->getValue("Room_Numbe_hard");
			$room_id=$room->id;
			
			return new Box("Hard Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::hf-onsite/detail',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('hf-onsite')),
								 'request_list'=>$unrequested,
								 'return_url'=>admin_url(self::$plugin->getRouteUri('hf-onsite'))."/property/".$property_id."/room/".$room_id."/result/".$hard_furniture_id."/i/".$request_id,'has_unrequested'=>true,
								]
							   )
						  );
		
		}
		
		public static function onsiteRequest(){
			
			$login_user=\Exment::user();
			$inventory_table=CustomTable::getEloquent(TABLE_HARD_FURNITURE_INVENTORY);
			$inventory_model=$inventory_table->getValueModel();
			$unrequested_list=$inventory_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->get();
			
			foreach($unrequested_list as $inventory){
				
				$inventory->setValue('request_date',\Carbon\Carbon::now());
				$inventory->save();
				
				$workflow=WorkFlow::getWorkflowByTable($inventory_table);
				$workflow_start_action=WorkflowAction::where('workflow_id',$workflow->id)->where('status_from','start')->get()->first();
				$workflow_status=WorkflowStatus::where('workflow_id',$workflow->id)->get();
				$workflow_start_status=WorkflowStatus::where('workflow_id',$workflow->id)->where('order',1)->get()->first();
				$workflow_value=WorkflowValue::where('morph_type',$inventory_table->table_name)->where('created_user_id',$login_user->base_user_id)->get();
				$status_to=$workflow_start_status->id;
				$morph_type=$inventory_table->table_name;
				$morph_id=$inventory->id;
				$createData=['workflow_id'=>$workflow->id,
							 'morph_type'=>$morph_type,
							 'morph_id'=>$morph_id,
							 'workflow_action_id'=>$workflow_start_action->id,
							 'workflow_status_from_id'=>null,
							 'workflow_status_to_id'=>$status_to,
							 'latest_flg'=>1,
							];
				
				$created_workflow_value=WorkflowValue::create($createData);
			
			}
		
		}
		
		protected static function getFloorRoomList($hard_furniture_list,$room_list){
			
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
				
				$floor_room_list[$floor][]=["id"=>array_get($item,'id'),
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

}