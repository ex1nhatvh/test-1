<?php 
namespace App\Plugins\FurnitureManagementSystem\Includes;


use Encore\Admin\Form\Field\Textarea;
use Encore\Admin\Widgets\Box;
use Exceedone\Exment\Model\CustomTable;
use Exceedone\Exment\Model\CustomView;
use Exceedone\Exment\Model\File as ExmentFile;
use Exceedone\Exment\Enums\FileType;
use Exceedone\Exment\Model\CustomForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Encore\Admin\Widgets\Form as WidgetForm;
use Exceedone\Exment\Enums\SystemTableName;
use Exceedone\Exment\Model\Define;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

if(!class_exists('FMSTableRequest')){
	
	final class FMSTableRequest{
		
		protected static $plugin=null;

		const CLASSNAME_CUSTOM_VALUE_SHOW = 'block_custom_value_show';
		const CLASSNAME_CUSTOM_VALUE_GRID = 'block_custom_value_grid';
		const CLASSNAME_CUSTOM_VALUE_FORM = 'block_custom_value_form';
		const CLASSNAME_CUSTOM_VALUE_PREFIX = 'custom_value_';
		
		public static function setPlugin($plugin){
			
			self::$plugin=$plugin;
		
		}
		
		
		public static function index($furniture_type,$request_type,$routeName,$request){
			// Check furniture type to redirect
			if($furniture_type == "ather") {
				$set_type = "soft";
			}
			else {
				$set_type = $furniture_type;
			}
			
			// Get table
			$custom_table = self::getTable($set_type,$request_type);

			// Create content
			$content = new Content();
			
			// Get view to display
			$custom_view = CustomView::getDefault($custom_table);

			// Set up display table
			$grid_item = $custom_view->grid_item->modal(false);
			$grid_item->callback($grid_item->getCallbackFilter());

			$grid = $grid_item->grid();

			// Set disable action edit and delete for display table
			$grid->actions(function ($actions) {
				$actions->disableDelete();
                $actions->disableEdit();
			});

			// Add button 新規 in over head display table
			$grid->tools(function (Grid\Tools $tools) use ($furniture_type, $request_type) {
				$tools->append(view('exment::tools.button', [
                    'href' => admin_url(self::$plugin->getRouteUri('request')) . '/' . $furniture_type . '/' . $request_type,
                    'label' => '新規',
                    'icon' => 'fa fa-plus',
                    'btn_class' => 'btn-success',
                ]));
				$tools->disableCreateButtonCustom();
			});

			// Create row and set display table for this row
			$row = new Row($grid);
			$row->class([static::CLASSNAME_CUSTOM_VALUE_GRID, static::CLASSNAME_CUSTOM_VALUE_PREFIX . $custom_table->table_name]);

			// Set row for content
			$content->row($row);
			
			return new Box("Request Table",view('exment_furniture_management_system::request-table/index',
					[
						'title' => '申請一覧',
						'content' => $content,
					]
				)
			);
		
		}

		public static function form($furniture_type,$request_type, $id,$routeName,$request){
			// Check furniture type to redirect
			if($furniture_type == "ather") {
				$set_type = "soft";
			}
			else {
				$set_type = $furniture_type;
			}

			// Get table
			$custom_table = CustomTable::getEloquent(self::getTable($set_type,$request_type));
			
			// Get value of table
			$custom_value = $custom_table->getValueModel($id);
			
			// Check value exist
			if(!$custom_value){
				return redirect(admin_url(self::$plugin->getRouteUri('request')) . '/' . $furniture_type . '/table/' . $request_type);
			}

			// Create content
			$content = new Content();

			// Get form to display
			$custom_form = CustomForm::getDefault($custom_table);
			$show_item = $custom_form->show_item->id($id)->modal(false);

			// Set row for content
			$content->row($show_item->createShowFormCommentAttachment());

			// Set option of file upload
			$fileOption = array_merge(
                Define::FILE_OPTION(),
                [
                    'showPreview' => true,
                    'deleteUrl' => admin_urls('auth', 'setting', 'filedelete'),
                    'deleteExtraData'      => [
                        '_token'           => csrf_token(),
                        '_method'          => 'PUT',
                        'delete_flg'       => 'avatar',
                    ]
                ]
            );
			
			// Create form
			$form = new WidgetForm();
			$form->disableReset();
			$form->disableSubmit();
			$form->attribute('id', COMMENT_ATTACHMENT_REQUEST_FROM);

			$form->action(admin_url(self::$plugin->getRouteUri('request')) . '/' . $furniture_type . '/table/' . $request_type . '/' . $id);

			$form->file('attachment1', 'ファイル添付')
				->options($fileOption)
				->removable()
				->attribute(['accept' => "*"]);
			

			$form->file('attachment2', 'ファイル添付')
				->options($fileOption)
				->removable()
				->attribute(['accept' => "*"]);

			$form->file('attachment3', 'ファイル添付')
				->options($fileOption)
				->removable()
				->attribute(['accept' => "*"]);
			
			// Set row for content
			$content->row($form);

			$comment_attachments = CustomTable::getEloquent(COMMENT_ATTACHMENT_REQUEST_FROM)->getValueModel()->where('value->Furniture_type', $furniture_type)->where('value->RequestForm_type', $request_type)->where('value->RequestForm_id', $id)->get();

			// Set row for content
			$content->row(function ($row) use ($comment_attachments) {
				// If exist revision, show it
				if($comment_attachments->toArray()) {
					$html = [];
					$No = 1;
					foreach($comment_attachments as $comment_attachment){
						$attachment1 = '';
						if($comment_attachment->getValue('attachment1')){
							$attachment1 = '<a href="' . \Exceedone\Exment\Model\File::getUrl($comment_attachment->getValue('attachment1')) . '" target="_blank" data-toggle="tooltip" title="" data-original-title="ダウンロード">' . \Exceedone\Exment\Model\File::getData($comment_attachment->getValue('attachment1'))->filename . '</a>，';
						}
						$attachment2 = '';
						if($comment_attachment->getValue('attachment2')){
							$attachment2 = '<a href="' . \Exceedone\Exment\Model\File::getUrl($comment_attachment->getValue('attachment2')) . '" target="_blank" data-toggle="tooltip" title="" data-original-title="ダウンロード">' . \Exceedone\Exment\Model\File::getData($comment_attachment->getValue('attachment2'))->filename . '</a>，';
						}
						$attachment3 = '';
						if($comment_attachment->getValue('attachment3')){
							$attachment3 = '<a href="' . \Exceedone\Exment\Model\File::getUrl($comment_attachment->getValue('attachment3')) . '" target="_blank" data-toggle="tooltip" title="" data-original-title="ダウンロード">' . \Exceedone\Exment\Model\File::getData($comment_attachment->getValue('attachment3'))->filename . '</a>，';
						}

						// Get user name create
						$user = CustomTable::getEloquent(SystemTableName::USER)->getValueModel()->where('id', $comment_attachment->created_user_id)->first();
						$html[] = '<div class="form-group">
										<label class="col-md-2  control-label">No.' . $No .'</label>
										<div class="col-md-9 ">
											<p style="padding-top:7px;">
											<a href="">
												' . $comment_attachment->getValue('create_date') . '
											</a>
												<small>
													&nbsp;(更新ユーザー&nbsp;:&nbsp;' . $user->getValue('user_name') . ')
												</small>
											</p>
										</div>
										<div class="col-md-2"></div>
										<div class="col-md-9">
											<p style="padding-top:7px;">
												<small>
													' . $comment_attachment->getValue('comment') . '
												</small>
											</p>
										</div>
										<div class="col-md-2"></div>
										<div class="col-md-9">
											<p style="padding-top:7px;">
												<small>
													' . $attachment1 . $attachment2 . $attachment3 . '
												</small>
											</p>
										</div>
									</div>';
						$No++;
					}
					// Sort desc list history
					krsort($html);

					$row->column(['xs' => 12, 'sm' => 6], (new Box(exmtrans("revision.update_history"), implode("", $html)))->style('info'));
				}
				// Comment
				$commentField = new Textarea('comment', ['Comment']);
				$commentField->setLabelClass(['d-none'])->setWidth(12, 0);
				$commentField->attribute('form', COMMENT_ATTACHMENT_REQUEST_FROM);
				$row->column(['xs' => 12, 'sm' => 6], (new Box(exmtrans("common.comment"), $commentField))->style('info'));
			});

			return new Box("Request Table Form",view('exment_furniture_management_system::request-table/form',
					[
						'title' => '新規',
						'content' => $content
					]
				)
			);
        }

		public static function saveCommentAttachment($furniture_type,$request_type,$routeName, $id, Request $request) {
			// Set up validation
			$rules = [
				'attachment1' => 'file|max:' . config('comment_attachment.attachment_file_max_size'),
				'attachment2' => 'file|max:' . config('comment_attachment.attachment_file_max_size'),
				'attachment3' => 'file|max:' . config('comment_attachment.attachment_file_max_size'),
				'comment' => '',
			];
			$validation = Validator::make($request->all(), $rules, [], [
				'comment' => 'コメント',
				'attachment1' => '添付ファイル１',
				'attachment2' => '添付ファイル２',
				'attachment3' => '添付ファイル３',
			]);
			if ($validation->fails()) {
				return back()->withInput()->withErrors($validation);
			}
			
			$value = [];
			$comment_attachment = CustomTable::getEloquent(COMMENT_ATTACHMENT_REQUEST_FROM)->getValueModel();

			// Set value
			$value['Furniture_type'] = $furniture_type;
			$value['RequestForm_type'] = $request_type;
			$value['RequestForm_id'] = $id;
			
			$attachment1File = $request->file('attachment1');
			if ($attachment1File) {
				// Upload file to local folder
				$exmentfile1 = ExmentFile::storeAs(FileType::CUSTOM_VALUE_COLUMN, $attachment1File, COMMENT_ATTACHMENT_REQUEST_FROM, $attachment1File->getClientOriginalName());
				// Set path for field attachment1 in database
				$value['attachment1'] = $exmentfile1->path;
			}
			$attachment2File = $request->file('attachment2');
			if ($attachment2File) {
				$exmentfile2 = ExmentFile::storeAs(FileType::CUSTOM_VALUE_COLUMN, $attachment2File, COMMENT_ATTACHMENT_REQUEST_FROM, $attachment2File->getClientOriginalName());
				$value['attachment2'] = $exmentfile2->path;
			}
			$attachment3File = $request->file('attachment3');
			if ($attachment3File) {
				$exmentfile3 = ExmentFile::storeAs(FileType::CUSTOM_VALUE_COLUMN, $attachment3File, COMMENT_ATTACHMENT_REQUEST_FROM, $attachment3File->getClientOriginalName());
				$value['attachment3'] = $exmentfile3->path;
			}
			$value['comment'] = $request->get('comment');
			$value['create_date'] = date('Y-m-d H:i:s');
			
			$comment_attachment->value = $value;
			
			// Save
			$comment_attachment->saveOrFail();
			
			return back();
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
	
	}

}