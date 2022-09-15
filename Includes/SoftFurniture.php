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
use Illuminate\Support\Facades\DB;

if(!class_exists('SoftFurniture')){
	
	final class SoftFurniture{

		const CLASSNAME_CUSTOM_VALUE_GRID = 'block_custom_value_grid';
		const CLASSNAME_CUSTOM_VALUE_PREFIX = 'custom_value_';
		
		protected static $plugin=null;
		
		public static function setPlugin($plugin){
			self::$plugin=$plugin;
		}
		
		public static function registry(){
			
			return new Box('Soft Furniture Registry Initial Inventory Counting',
						   view('exment_furniture_management_system::common/read-qr',['title'=>"ソフトファニチャー初期現地登録",'qr_action'=>"url",])
						  );
		
		}
		
		public static function onsite(){
			
			$login_user=\Exment::user();
			$inventory_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST);
			$inventory_model=$inventory_table->getValueModel();
			$latest=$inventory_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('updated_at','desc')->first();
			
			if($latest){
				
				$draft_id=$latest->id;
				$property_id=$latest->getValue("Property")->id;
				$room_id=$latest->getValue("Room_Numbe_inventory")->id;
			
			}
			
			return new Box("Soft Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::sf-onsite/top',
								['title'=>"ソフトファニチャー棚卸",'routeUri'=>admin_url(self::$plugin->getRouteUri('sf-onsite')),'property_id'=>$property_id ?? null,'room_id'=>$room_id ?? null,'draft_id'=>$draft_id ?? null,]
							   )
						  );
		
		}

		public static function onsiteMerge(Request $request)
        {
            // Create content
			$content = new Content();

            // Create form
			$form = new WidgetForm();
			$form->action(admin_url(self::$plugin->getRouteUri('sf-onsite')) . '/merge');
            $form->method('GET');
			$form->attribute('id', 'selectbox'.TABLE_PROPERTIES);
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
				'mergeDB' => 'SoftFurnitureDB',
            ])->render();

            $form->html($html);
			
			// Set row for content
			$content->row($form);

			/////////////////////
			// Get table
			$softFurniture_inventory_table = CustomTable::getEloquent(TABLE_SOFT_FURNITURE_INVENTORY);
                
			// Get view to display
			$softFurniture_inventory_view = CustomView::getDefault($softFurniture_inventory_table);
			
			// Set up display table
			$grid_item = $softFurniture_inventory_view->grid_item->modal(false);
			$grid_item->callback($grid_item->getCallbackFilter());
			
			$grid = $grid_item->grid();

			$softFurniture_apply_inventory_table = CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST);
			
			$softfurniture_inventories = [];

			if($request->get('property_id')) {
				$startDate = new DateTime($request->get('start_date'));
                $endDate = new DateTime($request->get('end_date'));
				
				$softFurniture_apply_inventory_values = $softFurniture_apply_inventory_table->getValueModel()->where('value->Property', $request->get('property_id'))->where('value->request_date', '>=', $startDate)->where('value->request_date', '<=', $endDate)->orderBy('value->request_date', 'desc')->get();
				foreach($softFurniture_apply_inventory_values as $softFurniture_apply_inventory_value) {
					foreach($softFurniture_apply_inventory_value->getValue('soft_furniture_inventories') as $softfurniture_inventorie){
						if(!in_array($softfurniture_inventorie->id, $softfurniture_inventories)){
							array_push($softfurniture_inventories, $softfurniture_inventorie->id);
								#$softfurniture_inventories[] = $softfurniture_inventorie->id;
						}
					}
				}
            } else {
				$softFurniture_apply_inventory_values = $softFurniture_apply_inventory_table->getValueModel()->orderBy('value->request_date', 'desc')->get();
				foreach($softFurniture_apply_inventory_values as $softFurniture_apply_inventory_value) {
					foreach($softFurniture_apply_inventory_value->getValue('soft_furniture_inventories') as $softfurniture_inventorie){
						if(!in_array($softfurniture_inventorie->id, $softfurniture_inventories)){
							$softfurniture_inventories[] = $softfurniture_inventorie->id;
						}
					}
				}
			}

			$referenceIdsStr = implode(',', $softfurniture_inventories);
			
			$grid->model()->whereIn('id', $softfurniture_inventories)->orderByRaw(DB::raw("FIELD(id, $referenceIdsStr)"));
			// Set disable action and tools
			$grid->disableActions();
			$grid->tools(function (Grid\Tools $tools) {
				$tools->disableButtonInRight();
				$tools->disableBatchActions();
			});

			// Set view for grid
			$grid->setView('admin::grid.table-merge');

			// Set variable for view
			$grid->setVariableCustom('input_of_form', 'form_btn_merge');

			// Exist request filter
            if ($request->get('start_date')) {
                $startDate = new DateTime($request->get('start_date'));
                $endDate = new DateTime($request->get('end_date'));
				$propertyId = $request->get('property_id');

                // Get merge row auto check
				//// Get merge row auto check in SoftFurniture_apply_inventory table
                $softFurniture_apply_inventory_mereg_row = $softFurniture_apply_inventory_table->getValueModel();
                $softFurniture_apply_inventory_mereg_row = $softFurniture_apply_inventory_mereg_row->where('value->request_date', '>=', $startDate);
                if($endDate){
                    $softFurniture_apply_inventory_mereg_row = $softFurniture_apply_inventory_mereg_row->where('value->request_date', '<=', $endDate);
                }
				if($propertyId){
                    $softFurniture_apply_inventory_mereg_row = $softFurniture_apply_inventory_mereg_row->where('value->Property', $propertyId);
                }
				
                $soft_furniture_inventory_mr = [];
                foreach($softFurniture_apply_inventory_mereg_row->get() as $row){
					foreach($row->getValue('soft_furniture_inventories') as $soft_furniture_inventory){
						$soft_furniture_inventory_mr[] = $soft_furniture_inventory->id;
					}
                }

				//// Get merge row auto check in SoftFurniture_inventory table
                $softFurniture_inventory_mereg_row = $softFurniture_inventory_table->getValueModel();
				$softFurniture_inventory_mereg_row = $softFurniture_inventory_mereg_row->whereIn('id', $soft_furniture_inventory_mr);

				$softFurniture_inventory_mr = [];
                foreach($softFurniture_inventory_mereg_row->get() as $row){
                    $softFurniture_inventory_mr[] = $row->id;
                }
				
                // Set background for row when checked
                $grid->rows(function ($row) use($softFurniture_inventory_mr) {
                    if ( in_array($row->column('id'), $softFurniture_inventory_mr)) {
                        $row->setAttributes([ 'data-row-id' => $row->model()['id'] ]);
                        $row->style("background-color:#ffffd5");
                    }
                });
            }
			 
			// Create row and set display table for this row
			$row = new Row($grid);
			$row->class([static::CLASSNAME_CUSTOM_VALUE_GRID, static::CLASSNAME_CUSTOM_VALUE_PREFIX . $softFurniture_inventory_table->table_name]);

			// Add row for content
			$content->row($row);

			///////////////////////
			// Create button merge
			$form = new WidgetForm();
			$form->method('GET');
			$form->attribute('id', 'form_btn_merge');
			
			if (isset($softFurniture_inventory_mr)) {
                // If check row merge, add request merge to action
                $softFurniture_inventory_mr = json_encode($softFurniture_inventory_mr);
				$form->html('
					<script>
						$(function() {
							var mr = ' . $softFurniture_inventory_mr . ';
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
				$form->html('<script>
						$(function() {
							$("#form_btn_merge").attr("action", "' . admin_url(self::$plugin->getRouteUri('sf-onsite')) . '/merge/table-merge?property_id=' . $request->get('property_id') . '&merge_row=" + $.admin.grid.selected().join());
						});
					</script>'
				);

				// When click change chekcbox
				$form->html('<script>
					$(function() {
						$(".grid-row-checkbox").on("ifChanged", function(){ 
							$("#form_btn_merge").attr("action", "' . admin_url(self::$plugin->getRouteUri('sf-onsite')) . '/merge/table-merge?property_id=' . $request->get('property_id') . '&merge_row=" + $.admin.grid.selected().join()); 
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
							$("#form_btn_merge").attr("action", "' . admin_url(self::$plugin->getRouteUri('sf-onsite')) . '/merge/table-merge?property_id=' . $request->get('property_id') . '&merge_row=" + $.admin.grid.selected().join()); 
						});
					});
					</script>'
				);
			}

			$form->disableReset();
			$form->disableSubmit();

			// Set button submit
			$html = '<button type="submit" form="form_btn_merge" class="btn btn-default pull-right">申請</button>';
			$form->html($html);

			// Set form for row
			$row = new Row($form);
			$row->class(["pull-right"]);
			// Add row for content
			$content->row($row);

            return new Box("Soft Furniture Onsite Inventory Counting", view('exment_furniture_management_system::sf-onsite/merge', 
                            [
                                'title' => "ソフトファニチャー棚卸",
                                'content' => $content,
                            ]
                        ));
        }
        
        public static function onsiteTableMerge(Request $request) {
            // Create content
			$content = new Content();

			$softFurniture_inventory_table = CustomTable::getEloquent(TABLE_SOFT_FURNITURE_INVENTORY);

            // Get merge row
            $ids_before_merge = $request->get('merge_row');
			$JudgmentResultMerges = [];
			$mergeItems = [];
			$idMergeItems = [];
			$flag_all_is_remove = true;

            $rows = collect(explode(',', $ids_before_merge))->filter();
            $rows->each(function ($id) use (&$request, &$idMergeItems, &$mergeItems, &$JudgmentResultMerges, &$flag_all_is_remove, &$softFurniture_inventory_table) {
				// {EでチェックされているレコードID}.furnitures(WHERE Property AND  Room_Numbe_Inventry)
                $softFurnitureInventory = $softFurniture_inventory_table->getValueModel()->where('id', $id)->where('value->Property_Inventory', '!=', null)->where('value->Room_Inventory', '!=', null)->first();
                if($softFurnitureInventory) {
					// Update Description_soft_inventory column in SoftFurniture_inventory table
					if($request->get('input_text_row_' . $id . '')){
						$softFurnitureInventory->setValue('Description_soft_inventory', $request->get('input_text_row_' . $id . ''));
						// Save
						$softFurnitureInventory->saveOrFail();
					}

					// 「正常」と「移動先要確認」の両方の判定がある場合
					if($softFurnitureInventory->getValue('JudgmentResult') != '移動先要確認') {
						$flag_all_is_remove = false;
						// 同じ判定のレコードが複数ある場合
						//「正常」と「棚卸移動」の両方の判定がある場合
						//「棚卸移動」と「移動先要確認」の両方の判定がある場合
						//「修理」もしくは「廃棄」と「移動先要確認」の両方の判定がある場合
						if(!in_array($softFurnitureInventory->getValue('JudgmentResult'), $JudgmentResultMerges)) {
							$JudgmentResultMerges[] = $softFurnitureInventory->getValue('JudgmentResult');
							$mergeItems[] = $softFurnitureInventory;
							$idMergeItems[] = $softFurnitureInventory->id;
						} else if (in_array($softFurnitureInventory->getValue('JudgmentResult'), $JudgmentResultMerges)){
							$indexExistJudgmentResult = array_search($softFurnitureInventory->getValue('JudgmentResult'), $JudgmentResultMerges);
							if($mergeItems[$indexExistJudgmentResult]['created_at'] <= $softFurnitureInventory->created_at) {
								// Unset old value
								unset($JudgmentResultMerges[$indexExistJudgmentResult]);
								unset($mergeItems[$indexExistJudgmentResult]);
								unset($idMergeItems[$indexExistJudgmentResult]);
								// Set new value for array
								$JudgmentResultMerges[] = $softFurnitureInventory->getValue('JudgmentResult');
								$mergeItems[] = $softFurnitureInventory; 
								$idMergeItems[] = $softFurnitureInventory->id;
							}
						}
					}
                }
            });
			
			// Case all record is 「移動先要確認」
			if($flag_all_is_remove == true){
				$rows->each(function ($id) use (&$request, &$idMergeItems, &$mergeItems, &$JudgmentResultMerges, &$softFurniture_inventory_table) {
					//{EでチェックされているレコードID}.furnitures(WHERE Property AND  Room_Numbe_Inventry)
					$softFurnitureInventory = $softFurniture_inventory_table->getValueModel()->where('id', $id)->where('value->Property_Inventory', '!=', null)->where('value->Room_Inventory', '!=', null)->first();
					if($softFurnitureInventory) {
						// Update Description_soft_inventory column in SoftFurniture_inventory table
						if($request->get('input_text_row_' . $id . '')){
							$softFurnitureInventory->setValue('Description_soft_inventory', $request->get('input_text_row_' . $id . ''));
							// Save
							$softFurnitureInventory->saveOrFail();
						}

						// 同じ判定のレコードが複数ある場合
						//「正常」と「棚卸移動」の両方の判定がある場合
						//「棚卸移動」と「移動先要確認」の両方の判定がある場合
						//「修理」もしくは「廃棄」と「移動先要確認」の両方の判定がある場合
						if(!in_array($softFurnitureInventory->getValue('JudgmentResult'), $JudgmentResultMerges)) {
							$JudgmentResultMerges[] = $softFurnitureInventory->getValue('JudgmentResult');
							$mergeItems[] = $softFurnitureInventory;
							$idMergeItems[] = $softFurnitureInventory->id;
						} else if (in_array($softFurnitureInventory->getValue('JudgmentResult'), $JudgmentResultMerges)){
							$indexExistJudgmentResult = array_search($softFurnitureInventory->getValue('JudgmentResult'), $JudgmentResultMerges);
							if($mergeItems[$indexExistJudgmentResult]['created_at'] <= $softFurnitureInventory->created_at) {
								// Unset old value
								unset($JudgmentResultMerges[$indexExistJudgmentResult]);
								unset($mergeItems[$indexExistJudgmentResult]);
								unset($idMergeItems[$indexExistJudgmentResult]);
								// Set new value for array
								$JudgmentResultMerges[] = $softFurnitureInventory->getValue('JudgmentResult');
								$mergeItems[] = $softFurnitureInventory; 
								$idMergeItems[] = $softFurnitureInventory->id;
							}
						}
					}
				});
			}
			
			$softFurniture_apply_inventory_merge_ids = [];
			$softFurniture_apply_inventory_merges = [];
			if($idMergeItems){
				$soft_furniture_inventories = [];
				foreach($idMergeItems as $idMergeItem){
					$softFurniture_apply_inventory_max_request_date = CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST)->getValueModel()->where(DB::raw("json_extract(value, '$.soft_furniture_inventories')"), 'LIKE',  '%"' . $idMergeItem . '"%');
					if($request->get('property_id')){
						$softFurniture_apply_inventory_max_request_date = $softFurniture_apply_inventory_max_request_date->where('value->Property', $request->get('property_id'));
					}
					$softFurniture_apply_inventory_max_request_date = $softFurniture_apply_inventory_max_request_date->orderBy('value->request_date', 'desc')->first();

					if(!in_array($softFurniture_apply_inventory_max_request_date->id, $softFurniture_apply_inventory_merge_ids)){
						$softFurniture_apply_inventory_merge_ids[] = $softFurniture_apply_inventory_max_request_date->id;
						$softFurniture_apply_inventory_merges[] = $softFurniture_apply_inventory_max_request_date;
						$soft_furniture_inventories[$softFurniture_apply_inventory_max_request_date->id][] = $idMergeItem;
					} else if(in_array($softFurniture_apply_inventory_max_request_date->id, $softFurniture_apply_inventory_merge_ids)) {
						$soft_furniture_inventories[$softFurniture_apply_inventory_max_request_date->id][] = $idMergeItem;
					}
				}
			}
			
			if($softFurniture_apply_inventory_merges){
				foreach($softFurniture_apply_inventory_merges as $rowMerge){
					$softFurnitureMergeApplyInv = CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST_MERGE)->getValueModel()->where('value->id_before_merge', $rowMerge->id)->first();
					if(!$softFurnitureMergeApplyInv){
						// Insert SoftFurniture_merge_apply_inventory table
						$merge=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST_MERGE)->getValueModel()->create();
						$merge->setValue('id_before_merge', $rowMerge->id);
						$merge->setValue('Property', $rowMerge->getValue('Property')->id);
						$merge->setValue('Address', $rowMerge->getValue('Address'));
						$merge->setValue('Manager', $rowMerge->getValue('Manager'));
						$merge->setValue('ManagerTel', $rowMerge->getValue('ManagerTel'));
						$merge->setValue('Comment', $rowMerge->getValue('Comment'));
						$merge->setValue('Application_start_date', $rowMerge->getValue('Application_start_date'));
						$merge->setValue('JudgmentResult', $rowMerge->getValue('JudgmentResult'));
						if($rowMerge->getValue('Room_Numbe_inventory')){
							$merge->setValue('Room_Numbe_inventory', $rowMerge->getValue('Room_Numbe_inventory')->id);
						}
						if($rowMerge->getValue('Floor_Name_inventory')){
							$merge->setValue('Floor_Name_inventory', $rowMerge->getValue('Floor_Name_inventory')->id);
						}
						$merge->setValue('request_date', date('Y-m-d H:i:s'));
	
						if($soft_furniture_inventories[$rowMerge->id]){
							$merge->setValue('soft_furniture_inventories', $soft_furniture_inventories[$rowMerge->id]);
						}
						
						$merge->save();
						
						$table = CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST_MERGE);
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
						
					} else if($softFurnitureMergeApplyInv){
						if($soft_furniture_inventories[$rowMerge->id]){
							$softFurnitureMergeApplyInv->getValue('soft_furniture_inventories')->contains(function ($value, $key) use(&$soft_furniture_inventories, &$rowMerge) {
								if(!in_array($value->id, $soft_furniture_inventories[$rowMerge->id])){
									$soft_furniture_inventories[$rowMerge->id][] = $value->id;
								}
							});
							
							// Update soft_furniture_inventories
							$softFurnitureMergeApplyInv->setValue('soft_furniture_inventories', $soft_furniture_inventories[$rowMerge->id]);
							// Save
							$softFurnitureMergeApplyInv->saveOrFail();
						}
					}
				}
			}

            // Get table merge
            $softFurniture_merge_apply_inv_table = CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST_MERGE);
            
            // Get view to display
            $SoftFurniture_merge_apply_inv_view = CustomView::getDefault($softFurniture_merge_apply_inv_table);
            
            // Set up display table
            $grid_item = $SoftFurniture_merge_apply_inv_view->grid_item->modal(false);
            $grid_item->callback($grid_item->getCallbackFilter());
            
            $grid = $grid_item->grid();
			// 対象家具の「SoftFurniture」のID順に並べる
            $grid->model()->whereIn('value->id_before_merge', $softFurniture_apply_inventory_merge_ids)->orderBy('id', 'desc');
                
            // Set disable action and tools
            $uri = admin_url('data/' . TABLE_SOFT_FURNITURE_REQUEST_MERGE);
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
            $row->class([static::CLASSNAME_CUSTOM_VALUE_GRID, static::CLASSNAME_CUSTOM_VALUE_PREFIX . $softFurniture_merge_apply_inv_table->table_name]);

            // Add row for content
            $content->row($row);

			// Set url is default action for button back
            $form = new WidgetForm();

            $form->disableReset();
            $form->disableSubmit();
            $form->html('
                    <script>
                        $(function() {
                            $(".back").attr("href", "' . admin_url(self::$plugin->getRouteUri('sf-onsite')) . '/merge");
                            $(".back").removeAttr("role");
                        });
                    </script>'
                );
            $content->row($form);

            return new Box("Soft Furniture Onsite Inventory Counting", view('exment_furniture_management_system::sf-onsite/table-merge', 
                            [
                                'title' => "ソフトファニチャー棚卸",
                                'content' => $content,
                            ]
                        ));
        }

		public static function onsiteAfterMerge() {
            // Create content
			$content = new Content();

            // Get table merge
            $softFurniture_merge_apply_inv_table = CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST_MERGE);
            
            // Get view to display
            $SoftFurniture_merge_apply_inv_view = CustomView::getDefault($softFurniture_merge_apply_inv_table);
            
            // Set up display table
            $grid_item = $SoftFurniture_merge_apply_inv_view->grid_item->modal(false);
            $grid_item->callback($grid_item->getCallbackFilter());
            
            $grid = $grid_item->grid();
			// 対象家具の「SoftFurniture」のID順に並べる
            $grid->model()->orderBy('id', 'desc');
                
            // Set disable action and tools
			$uri = admin_url('data/' . TABLE_SOFT_FURNITURE_REQUEST_MERGE);
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
            $row->class([static::CLASSNAME_CUSTOM_VALUE_GRID, static::CLASSNAME_CUSTOM_VALUE_PREFIX . $softFurniture_merge_apply_inv_table->table_name]);

            // Add row for content
            $content->row($row);

            return new Box("Soft Furniture Onsite Inventory Counting", view('exment_furniture_management_system::sf-onsite/after-merge', 
                            [
                                'title' => "ソフトファニチャー棚卸",
                                'content' => $content,
                            ]
                        ));
        }
		
		public static function onsiteSelectProperty(){
			
			$state='select_property';
			$property=CustomTable::getEloquent(TABLE_PROPERTIES);
			$query=$property->getValueModel()->query();
			$data=$query->get();
			
			return new Box("Soft Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::common/property-select',
								['title'=>"ソフトファニチャー棚卸",'routeUri'=>admin_url(self::$plugin->getRouteUri('sf-onsite')),'properties'=>$data]
							   )
						  );
		
		}
		
		public static function onsiteSearchRoom($property_id){
			
			$property=CustomTable::getEloquent(TABLE_PROPERTIES)->getValueModel($property_id);
			$room_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_ROOM);
			$room_list=$room_table->getValueModel()->where('parent_id',$property_id)->get();
			$floor_room_list=self::getFloorRoomList($room_list);
			
			return new Box("Soft Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::common/room-select',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('sf-onsite')),'property'=>$property,'floor_room_list'=>$floor_room_list,]
							   )
						  );
		
		}
		
		public static function onsiteRequestList(){
			
			$login_user=\Exment::user();
			$request_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST);
			$request_model=$request_table->getValueModel();
			$unrequested=$request_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('created_at','asc')->get()->first();
			$unrequested_new=$request_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('created_at','desc')->get()->first();
			$requested=$request_model->where('created_user_id',$login_user->base_user_id)->whereNotNull('value->request_date')->orderBy('value->request_date','desc')->get();
			
			$requested_date_list=[];
			
			foreach($requested as $item){
				
				$date=\Carbon\Carbon::parse($item->getValue('request_date'))->format('Y-m-d');
				
				if(!in_array($date,$requested_date_list,true))$requested_date_list[]=$date;
			
			}
			
			return new Box("Soft Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::sf-onsite/request',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('sf-onsite')),'unrequested'=>$unrequested,'unrequested_new'=>$unrequested_new,'requested_date_list'=>$requested_date_list,]
							   )
						  );
		
		}
		
		public static function onsiteRequestedDetail($date){
			
			$login_user=\Exment::user();
			$request_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST);
			$request_model=$request_table->getValueModel();
			$startDate=\Carbon\Carbon::parse($date)->format('Y-m-d H:i:s');
			$endDate=\Carbon\Carbon::parse($date)->addDay()->format('Y-m-d H:i:s');
			$requested=$request_model->where('created_user_id',$login_user->base_user_id)->where('value->request_date','>=',$startDate)->where('value->request_date','<',$endDate)->orderBy('value->request_date','asc')->get();
			
			return new Box("Soft Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::sf-onsite/detail',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('sf-onsite')),'request_list'=>$requested,]
							   )
						  );
		
		}
		
		public static function onsiteUnrequestedDetail(){
			
			$login_user=\Exment::user();
			$request_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST);
			$request_model=$request_table->getValueModel();
			$unrequested=$request_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('created_at','asc')->get();
			$unrequested_new=$request_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->orderBy('created_at','desc')->get()->first();
			$request_id=$unrequested_new->id;
			$property=$unrequested_new->getValue("Property");
			$property_id=$property->id;
			$room=$unrequested_new->getValue("Room_Numbe_inventory");
			$room_id=$room->id;
			
			return new Box("Soft Furniture Onsite Inventory Counting",
						   view('exment_furniture_management_system::sf-onsite/detail',
								['routeUri'=>admin_url(self::$plugin->getRouteUri('sf-onsite')),'request_list'=>$unrequested,'return_url'=>admin_url(self::$plugin->getRouteUri('sf-onsite'))."/property/".$property_id."/room/".$room_id."/result/".$request_id,'has_unrequested'=>true,]
							   )
						  );
		
		}
		
		public static function onsiteRequest(){
			
			$login_user=\Exment::user();
			$request_table=CustomTable::getEloquent(TABLE_SOFT_FURNITURE_REQUEST);
			$request_model=$request_table->getValueModel();
			$unrequested_list=$request_model->where('created_user_id',$login_user->base_user_id)->whereNull('value->request_date')->get();
			
			foreach($unrequested_list as $request){
				
				$request->setValue('request_date',\Carbon\Carbon::now());
				$request->save();$workflow=WorkFlow::getWorkflowByTable($request_table);
				$workflow_start_action=WorkflowAction::where('workflow_id',$workflow->id)->where('status_from','start')->get()->first();
				$workflow_status=WorkflowStatus::where('workflow_id',$workflow->id)->get();
				$workflow_start_status=WorkflowStatus::where('workflow_id',$workflow->id)->where('order',1)->get()->first();
				$workflow_value=WorkflowValue::where('morph_type',$request_table->table_name)->where('created_user_id',$login_user->base_user_id)->get();
				$status_to=$workflow_start_status->id;
				$morph_type=$request_table->table_name;
				$morph_id=$request->id;
				$createData=['workflow_id'=>$workflow->id,'morph_type'=>$morph_type,'morph_id'=>$morph_id,'workflow_action_id'=>$workflow_start_action->id,'workflow_status_from_id'=>null,'workflow_status_to_id'=>$status_to,'latest_flg'=>1,];
				
				$created_workflow_value=WorkflowValue::create($createData);
			
			}
		
		}
		
		protected static function getFloorRoomList($room_list){
			
			$floor_room_list=[];
			
			foreach($room_list as $room){
				
				$floor_name=$room->getValue("Floor_Name");
				
				if(!array_key_exists($floor_name,$floor_room_list))$floor_room_list[$floor_name]=[];
				$floor_room_list[$floor_name][]=["id"=>$room->id,"name"=>$room->getValue("Room_Numbe")];
			
			}
			
			return $floor_room_list;
		
		}
	
	}

}