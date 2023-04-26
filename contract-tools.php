<?php
require(__DIR__. '/library/rakit/rakid/vendor/autoload.php');
use Rakit\Validation\Validator;
require(__DIR__. '/library/vendor/autoload.php');
use eftec\bladeone\BladeOne;

require_once(dirname(__DIR__). '/contract-tools/model/model.php');
require_once(dirname(__DIR__). '/contract-tools/model/Shop.php');
require_once(dirname(__DIR__). '/contract-tools/model/Applicant.php');

require(__DIR__. '/library/vendor/vendor_phpspreadsheet/autoload.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

date_default_timezone_set('Asia/Tokyo');

/*
Plugin Name:Contract Tools 
Plugin URI: http://www.example.com/plugin
Description: 契約管理業務効率化ツール
Author: myu
Version: 0.1
Author URI: http://www.example.com
*/

class ContractTools {

	/**
	 * 
	 **/
	function __construct() {
		add_action('admin_menu', array($this, 'add_pages'));
		add_action('admin_menu', array($this, 'add_sub_menu'));
		add_action('init', array($this, 'export_csv'));
		add_action('init', array($this, 'export_pdf'));
	}

	/**
	 * 
	 **/
	function add_pages() {
		add_menu_page('契約管理ツール','契約管理ツール',  'level_8', 'contract-tools', array($this,'menu_top'), '', 26);
	}

	/**
	 * 
	 **/
	function add_sub_menu() {
		$cur_user = wp_get_current_user();

		switch ($cur_user->roles[0]) {
			case 'administrator':
				if (in_array($cur_user->user_login, array('admin'))) {
					//add_submenu_page('contract-tools', 'データ形式変換','データ形式変換', 'read', 'admin.php?page=conv-data');
					add_submenu_page('contract-tools', '申込データ一覧画面','申込データ一覧画面', 'read', 'shop-list', array(&$this, 'shop_list'));
	//				add_submenu_page('contract-tools', '新規登録','新規登録', 'read', 'agreement', array(&$this, 'agreement'));
	//				add_submenu_page('contract-tools', '提出用DL(mypage)','提出用DL(mypage)', 'read', 'download-doc', array(&$this, 'download_doc'));
	//				add_submenu_page('contract-tools', '必要書類UP(mypage)','必要書類UP(mypage)', 'read', 'upload-doc', array(&$this, 'upload_doc'));
				} else {
					add_submenu_page('contract-tools', '申込データ一覧画面','申込データ一覧画面', 'read', 'shop-list', array(&$this, 'shop_list'));
					$this->remove_menus();
				}
				break;
			default:
				add_submenu_page('contract-tools', '申込情報確認(my)','申込情報確認(my)', 'read', 'shop-list', array(&$this, 'shop_list'));
//				add_submenu_page('contract-tools', '新規登録','新規登録', 'read', 'agreement', array(&$this, 'agreement'));
				add_submenu_page('contract-tools', 'status',' - ステータス確認', 'read', 'status', array(&$this, 'status'));
				add_submenu_page('contract-tools', '規約確認',' - 規約確認', 'read', 'agreement', array(&$this, 'agreement'));
				add_submenu_page('contract-tools', 'step1',' - STEP. 1', 'read', 'step1', array(&$this, 'step1'));
				add_submenu_page('contract-tools', 'step2',' - STEP. 2', 'read', 'step2', array(&$this, 'step2'));
				add_submenu_page('contract-tools', 'step3',' - STEP. 3', 'read', 'step3', array(&$this, 'step3'));
				add_submenu_page('contract-tools', 'step4',' - STEP. 4', 'read', 'step4', array(&$this, 'step4'));
//				add_submenu_page('contract-tools', 'confirm',' - confirm', 'read', 'confirm', array(&$this, 'confirm'));
				add_submenu_page('contract-tools', '必要書類UP(my)','必要書類UP(my)', 'read', 'upload-doc', array(&$this, 'upload_doc'));
				add_submenu_page('contract-tools', '提出用DL(my)','提出用DL(my)', 'read', 'download-doc', array(&$this, 'download_doc'));
				$this->remove_menus();
				add_action( 'admin_bar_menu', 'remove_admin_bar_menus', 999 );
				break;
		}
	}

	/**
	 * 
	 **/
	function reload() {
		unset($_POST); 
		unset($p); 
//		echo '<script type="text/javascript">if (window.name != "any") {window.location.reload();window.name = "any";} else {window.name = "";}</script>';
	}

	/**
	 * 
	 **/
	function preStepProcess($step) {
		$prm = (object) $_GET;
		$prm->action = 'regist';
		$prm->step = $step;
		$p = (object) $_POST;

		$tb = new Applicant;
		$statusNums = array(
			'agreement' => 0, 
			'1st' => 1, 
			'2nd' => 2, 
			'3rd' => 3, 
			'4th' => 4, 
			'confirm' => 9
		);
		$step_num = $statusNums[$step];
		$tb->setStatus($p->cmd, $step_num);
		$p->status = $tb->getStatus();

		$rows = $tb->getDetailByMail();
		if ($p->cmd) {
			if ($p->biz_fg == '2') { $p->biz_number = ''; }

			// 各STEPの判定フラグを基に申込情報のデータをコピーする
			$p = $tb->copyDataByFlag($p);
//$this->vd($p);exit;

			$msg = $this->getValidMsg($step_num);
			if ($msg['msg'] != 'success') {
				// error
				if ($step_num == 3) {
					// エラー時のcheckboxの再設定
					$p->expenses = json_encode($p->expenses);
					$p->payment = json_encode($p->payment);
				}
//				$this->vd($p);
				$rows = $p;
			} else {
				$rows = $tb->regDetail($prm, $p);
//				$rows = $tb->getDetailByMail();

				// 次のSTEPへ遷移
				$next = intval($step_num)+1;
				$next_step = ($next <= 4) ? 'step'. $next : 'status';
				if ($msg['msg'] == 'success') { echo '<script>window.location.href = "'. home_url(). '/wp-admin/admin.php?page='. $next_step. '";</script>'; }
			}
		}

		$aliases = $tb->getAliases();
		$initForm = $tb->getInitForm();
		return [$prm, $p, $rows, $step_num, (object) $msg, $aliases, $initForm];
	}

	/**
	 * 
	 **/
	function postStepProcess() {
	}

	/**
	 * 規約確認画面
	 *
	 **/
	function agreement() {
		$blade = $this->set_view();
		$title = 'Agreement';
		list($prm, $p, $rows, $step_num, $aliases, $initForm) = $this->preStepProcess('agreement');
		echo $blade->run("agreement", compact('title', 'rows', 'prm', 'step_num', 'aliases', 'initForm'));
	}

	/**
	 * 
	 **/
	function step1() {
		$blade = $this->set_view();
		list($prm, $p, $rows, $step_num, $msg, $aliases, $initForm) = $this->preStepProcess('1st');
		echo $blade->run("shop-detail-1", compact('rows', 'prm', 'step_num', 'msg', 'aliases', 'initForm'));
	}

	/**
	 * 
	 **/
	function step2() {
		$blade = $this->set_view();
		list($prm, $p, $rows, $step_num, $msg, $aliases, $initForm) = $this->preStepProcess('2nd');
		echo $blade->run("shop-detail-2", compact('rows', 'prm', 'step_num', 'msg', 'aliases', 'initForm'));
	}

	/**
	 * 
	 **/
	function step3() {
		$blade = $this->set_view();

		$app = new Applicant;
		$curUser = $app->getCurUser();
		$email = $curUser->user_email;
		$applicant = $app->getApplicantByEmail($email);

		// File delete
		if ($_POST['cmd'] === 'cmd_del_file') {
			$delete_file = $_POST['delete_file'];
			// ファイル削除
			$ret = $app->delFile($delete_file, $applicant);
			// DBのgoods_imageを削除
			$result = $app->deleteGoodsImage($delete_file, $applicant);
			// POSTのgoods_image削除
			$_POST['goods_image1'] = '';
//$this->vd($_POST);
			echo '<script>window.location.href = "'. home_url(). '/wp-admin/admin.php?page=step3";</script>';
		}

		// validation後に画像のアップロード実施
		$f = (object) $_FILES;
		if (in_array($_POST['cmd'], array('cmd_regist', 'cmd_confirm')) && (
				(!empty($_FILES['goods_image']['name'][0])) || 
				(!empty($_FILES['goods_image']['name'][1])) || 
				(!empty($_FILES['goods_image']['name'][2]))
			)) {
//		if (!empty($_FILES) && ($msg['msg'] == 'success')) {
//$this->vd('upload in ');exit;
//$this->vd($f);
//$this->vd($_POST);exit;

			// File Upload
			$uploaddir = dirname(__DIR__). '/contract-tools/uploads/'. $applicant;
			foreach ($_FILES['goods_image']['name'] as $i => $fname) {
//				$uploadfileName = basename($_FILES['upload_doc']['name'][0]);
				$uploadfileName = sprintf('%s_goods_image_%d.jpg', $applicant, intval($i) + 1);
				$uploadfile = $uploaddir . '/' . $uploadfileName;
				$uploadBool[$i] = move_uploaded_file($_FILES['goods_image']['tmp_name'][$i], $uploadfile);

				// DBにファイル名を登録する
				$num = $i + 1;
				$_POST['goods_image'. $num] = (!empty($fname)) ? $uploadfileName : '';
			}

			//return ($uploadBool == true) ? $uploadfile : null;
		}

		list($prm, $p, $rows, $step_num, $msg, $aliases, $initForm) = $this->preStepProcess('3rd');

		echo $blade->run("shop-detail-3", compact('rows', 'prm', 'step_num', 'msg', 'aliases', 'initForm'));
	}

	/**
	 * 
	 **/
	function step4() {
		$blade = $this->set_view();
		list($prm, $p, $rows, $step_num, $msg, $aliases, $initForm) = $this->preStepProcess('4th');
		echo $blade->run("shop-detail-4", compact('rows', 'prm', 'step_num', 'msg', 'aliases', 'initForm'));
	}

	/**
	 * 
	 **/
	function confirm() {
		$blade = $this->set_view();
		list($prm, $p, $rows) = $this->preStepProcess('confirm');
		echo $blade->run("shop-detail-confirm", compact('rows', 'prm'));
	}

	/**
	 * 
	 **/
	function status() {
		$blade = $this->set_view();
		list($prm, $p, $rows, $step_num) = $this->preStepProcess('confirm');

		// 状態取得
		$tb = new Applicant;
		$status = $tb->getStatusForMenu();
		echo $blade->run("shop-detail-status", compact('status', 'step_num'));
	}

	/**
	 * 
	 **/
	function set_view() {
		$views = __DIR__. '/views';
		$cache = __DIR__. '/cache';
		$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
		return $blade;
	}

	/**
	 * 
	 **/
	function menu_top() {
		$blade = $this->set_view();

		$applicant = new Applicant();
		$list = $applicant->getList();

		$msg = $this->getValidMsg();
		$title = '<p>menu_top</p>';
		echo $blade->run("sample", compact('title','fugafuga', 'msg'));
	}

	/**
	 * バリデーション実行
	 * 
	 **/
	function getValidMsg($step_num = null) {
		$app = new Applicant();
		$ve = $app->getValidElement($step_num);

		// rakid
		$validator = new Validator;
		$validator->setMessages([
//			'required' => ':attribute を入力してください',
			'required' => 'を入力してください',
			'email' => ':email tidak valid',
			'min' => 'の文字数が不足しています。',
			'max' => 'が文字数をオーバーしています。',
			'regex' => 'をカタカナで入力してください。',
			'biz_number' => 'は、国税庁が指定する13桁の番号で入力してください。',
			'goods_image1' => 'が選択されていません。',
			// etc
		]);

		// 項目コピーのradioにチェックが入ってる場合、rulesを削除してValidation不要にする
		$ve = $app->initValidationRules($_POST, $ve);

		// 入力欄「その他」のradioにチェックが入ってる場合、rulesを変更してValidationする
		$ve = $app->changeValidationRules($_POST, $ve);

		// 必須：商品画像①のvalidation追加
		if ((!empty($_FILES)) && ($step_num == 3)) {
			$ve = $app->changeFileValidationRules($_POST + $_FILES, $ve);
		}

		// make it
		$validation = $validator->make($_POST + $_FILES, $ve['rules'], $ve['messages']);
		
		// then validate
		$validation->validate();
		
		if ($validation->fails()) {
			// handling errors
			$errors = $validation->errors();
			$msg = $errors->firstOfAll();
		} else {
			// validation passes
			$msg = array('msg' => 'success');
		}
		return $msg;
	}

	/**
	 * 申込データ一覧画面
	 *
	 **/
	public $_rows = 'test';
	function shop_list() {
		$blade = $this->set_view();
		$prm = (object) $_GET;
		$p = (object) $_POST;
//$this->vd($p);
		if ($prm->sync) {
			$this->sync($prm);
		}
		
		switch($prm->action) {
			case 'regist':
				$tb = new Applicant;
				switch ($prm->step) {
					case '1st':
						$rows = $tb->regDetail($prm, $p);
						echo $blade->run("shop-detail-1", compact('rows', 'prm'));
						break;
					case '2nd':
						$rows = $tb->regDetail($prm, $p);
						echo $blade->run("shop-detail-2", compact('rows', 'prm'));
						break;
					case '3rd':
						$rows = $tb->regDetail($prm, $p);
						echo $blade->run("shop-detail-3", compact('rows', 'prm'));
						break;
					case '4th':
						$rows = $tb->regDetail($prm, $p);
						echo $blade->run("shop-detail-4", compact('rows', 'prm'));
						break;
					case 'confirm':
						$rows = $tb->regDetail($prm, $p);
						echo $blade->run("shop-detail-confirm", compact('rows', 'prm'));
						break;
					case 'complete':
						$prm = $tb->getPrm();
						$rows = $tb->regDetail($prm);
						echo $blade->run("shop-detail-complete", compact('rows', 'prm'));
						break;
					default: 
/*
						$prm = $tb->getPrm();
						$rows = $tb->regDetail($prm);
*/
						echo $blade->run("shop-detail", compact('rows', 'prm'));
						break;
				}
				break;

			case '-1':
				$_GET['post'] = '-1';
				$_GET['action'] = 'search';
				if ($prm->export_all && $prm->service_type) {
					$_GET['action'] = 'export_all';
					echo 'case export_all';
					$this->export_csv($prm);
				}

			case 'export_pdf':
//				$_GET['action'] = 'export_pdf';
				echo 'case export_pdf';
				$this->export_pdf($prm);

			default:
				$tb = new Applicant;
				$initForm = $tb->getInitForm();
				$rows = $tb->getList();
				$formPage = 'shop-list';
				echo $blade->run("shop-list", compact('rows', 'formPage', 'initForm'));
				break;

			case 'search' :
				$tb = new Applicant;
				$initForm = $tb->getInitForm();
//				$prm = (!empty($prm->post)) ? (object) $prm : $tb->getPrm();
				$rows = $tb->getList($prm);
				$formPage = 'shop-list';
				echo $blade->run("shop-list", compact('rows', 'formPage', 'initForm'));
				break;
				
			case 'save':
				if (!empty($_POST)) {
					$prm = (object) $_POST;
//					$tb = new Postmeta;
//					$result = $tb->updShopDetail($prm, $p);
					if ($prm->cmd == 'save') {
						$prm->messages = array('error' => array('error is _field_company-name.')); // TEST DATA 
						$tb = new Applicant;
						$rows = $tb->updDetail($prm);

					}
					if (empty($prm->messages)) {
	//					$result = $tb->updShopDetail($prm, $p);
					} else {
						echo '<script>var msg = document.getElementById("msg"); msg.innerHTML = "'. $p->messages['error'][0]. '";</script>';
					}
				}
				$formPage = 'shop-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm'));
				break;

			case 'edit':
				$tb = new Applicant;
				$initForm = $tb->getInitForm();
				$rows = $tb->getDetail($prm);
				$p = $rows;
				$formPage = 'shop-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm', 'p', 'initForm'));
				break;

			case 'edit-exe':
				$prm = (object) $_GET;
				$p = (object) $_POST;
/*
				$tb = new Postmeta;
				$rows = $tb->getShopDetail($prm);
*/
					//$this->_rows = $tb->updShopDetail($prm, $p);
				// TODO: transaction, validation
				$tb = new Applicant;
				if (!empty($_POST)) {
					if ($p->cmd == 'save') {
						$p->messages = array('error' => array('error is _field_company-name.')); // TEST DATA 
$msg = $this->getValidMsg();		
$this->vd($msg);
						if ($msg['msg'] != 'success') {
						} else {
							$rows = $tb->updDetail($prm, $p);
						}

					}
					if (empty($p->messages)) {
	//					$result = $tb->updShopDetail($prm, $p);
					} else {
						echo '<script>var msg = document.getElementById("msg"); msg.innerHTML = "'. $p->messages['error'][0]. '";</script>';
					}
				}
				
				$rows = $tb->getDetail($prm);

				$formPage = 'shop-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm', 'p', 'msg'));

				break;

			case 'cancel':
				$prm = (object) $_GET;
				unset($_POST);
				$tb = new Applicant;
				$rows = $tb->getDetail($prm);
				$p = $rows;
				$formPage = 'shop-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm', 'p'));
				break;

			case 'preview':
				// 申込データプレビュー画面
				// (PDF保存形式でプレビューする)
				echo 'test preview';
				$app = new Applicant;
				$curUser = $app->getCurUser();
				if ($curUser->roles != 'administrator') {
					$applicant = htmlspecialchars($_GET['post']);
					$row = $app->getDetailByApplicantCode($applicant);

				} else {
					$row = null;
				}
				echo $blade->run("preview", compact('row', 'formPage', 'prm', 'p'));
				break;

			case 'init-status':
				$prm = (object) $_GET;
				unset($_POST);
				$applicant = $prm->post;
				$tb = new Applicant;
				$ret = $tb->initStatus($applicant);
				$result = ($ret == true) ? 'true' : 'false';
				echo '<script>window.location.href = "'. home_url(). '/wp-admin/admin.php?page=shop-list&init-status='. $result. '";</script>';
				break;
		}
	}

	/**
	 * CSVエクスポート (サービス種別別)
	 *
	 **/
	function export_csv($prm = null) {
		if(isset($_GET['export_all'])) {

			$serviceType = $_GET['service_type'];
			$app = new Applicant;
			$header = $app->getHeaderByServiceType($serviceType);
			$arr_data = $app->getListForExport($_GET['s'], $serviceType);

			switch ($serviceType) {
				case 'veritrans' :
//$this->vd($arr_data);exit;
					$spreadsheet = new Spreadsheet();
					$sheet = $spreadsheet->getActiveSheet();

					foreach($header as $j => $head) {
						$j = $j + 1;
						$sheet->setCellValueByColumnAndRow($j, 1, $head);
					}

					foreach($arr_data as $i => $data) {
						$i = $i + 2;
						$j = 1;
						foreach($data as $k => $v) {
							$v = preg_replace('/^=/', ' =', $v); // 先頭が"="の場合を回避
							$sheet->setCellValueByColumnAndRow($j, $i, $v);
							$j++;
						}
					}

					$writer = new Xlsx($spreadsheet);
					$filepath = sprintf('%s/contract-tools/downloads/vt_'.date('YmdHms').'.xlsx', dirname(__DIR__));

					if (!file_exists($filepath)) {
						touch($filepath);
					}

					$writer->save($filepath);

					if (file_exists($filepath)) {
						$filename = basename($filepath);
						$file_size = filesize($filepath);

						header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
						header("Content-Length: {$file_size}");
						header("Content-Disposition: attachment; filename=$filename");
						ob_end_clean();
						// ファイルを出力する前に、バッファの内容をクリア（ファイルの破損防止）
						readfile($filepath);
						exit();
					}
					break;

				case 'mf-kessai' :
					if ($arr_data) {
						header('Content-type: text/csv');
						header('Content-Disposition: attachment; filename="mf_'.date('YmdHms').'.csv"');
						header('Pragma: no-cache');
						header('Expires: 0');
						
						$file = fopen('php://output', 'w');

						fputcsv($file, mb_convert_encoding($header, 'SJIS-win', 'utf-8'));
						
						foreach($arr_data as $i => $data) {
							fputs($file, implode(",", mb_convert_encoding((array) $data, 'SJIS-win', 'utf-8')) . "\n");
						}
						exit();
					}
					break;
			}
		}
	}

	/**
	 * PDF出力機能
	 *
	 **/
	function export_pdf($prm = null) {
		if(isset($_GET['export_all'])) {
			$tb = new Postmeta;
			$header = $tb->getHeaderByServiceType($_GET['service_type']);
			$arr_data = $tb->getShopListFromPostmeta($_GET['s'], $_GET['service_type']);
			//$this->vd($header);exit();
			//$arr_data = array('test,aaaaa,aaaa,aaaaa', 'test,bbbb,bbbbb,bbbbbb');
			if ($arr_data) {
				header('Content-type: text/csv');
				header('Content-Disposition: attachment; filename="posts'.date('YmdHms').'.csv"');
				header('Pragma: no-cache');
				header('Expires: 0');
				
				$file = fopen('php://output', 'w');
				
				fputcsv($file, $header);
				
				foreach($arr_data as $mail => $data) {
					fputcsv($file, array(implode(',', (array) $data), get_the_time("Y/m/d")));
				}
				exit();
			}
		}
	}

	/**
	 * SYNC
	 *
	 **/
	function sync($prm = null) {

		// Userテーブルに登録があり、申込者テーブルに未登録のユーザー一覧を取得
		$app = new Applicant;
		$unUsers = $app->getUsersUnRegisterd();
		if (!empty($unUsers)) { 
			$syncCount = $app->initRegRecord($unUsers);
			if ($syncCount > 0) {
				echo sprintf('%s件 同期しました。', $syncCount);
			} else {
	
				echo sprintf('同期レコードはありません。');
			}
	
			$apps = $app->getApplicantCodes();
			
			// アップロード先ディレクトリ作成
	//		$applicant = 'test-0121-083416';
			foreach ($apps as $i => $applicant) {
				// アップロード先ディレクトリ
				$upDir = dirname(__DIR__). '/contract-tools/uploads/'. $applicant;
	
				// アップロード先ディレクトリが存在しない場合は作成
				if(!file_exists($upDir)){
					mkdir($upDir, 0777);
				}
			}
		}

		// Usersテーブルに登録がなく、申込者テーブルに登録があるユーザー一覧を取得（削除対象）
		$delUsers = $app->getUsersDeleted();
		if (!empty($delUsers)) {
			$delCount = $app->initDelRecord($delUsers);
	
			if ($delCount > 0) {
				echo sprintf('%s件 削除しました。', $delCount);
			} else {
				echo sprintf('削除レコードはありません。');
			}
		}

	}

	/**
	 * 申込情報確認用マイページ
	 *
	 **/
	function mypage_shop_list() {
		$blade = $this->set_view();
		$prm = (object) $_GET;
		switch($prm->action) {
			case 'save':
				if (!empty($_POST)) {
					$p = (object) $_POST;
					$tb = new Postmeta;
					$result = $tb->updShopDetail($prm, $p);
				}
			case 'cancel':
				unset($_POST);
			case 'edit':
			case 'edit-exe':
				$tb = new Applicant;
				$initForm = $tb->getInitForm();
				$rows = $tb->getDetail($prm);
				$p = $rows;
				$formPage = 'shop-list';
				echo $blade->run("shop-detail", compact('rows', 'formPage', 'prm', 'p', 'initForm'));
				break;

			default:
				$tb = new Applicant;
				$rows = $tb->getList($prm);
				$formPage = 'shop-list';
				echo $blade->run("shop-list", compact('rows', 'formPage'));
				break;
		}
	}

	/**
	 * 提出用ダウンロード画面
	 *
	 **/
	function download_doc() {
		$blade = $this->set_view();

		$app = new Applicant;
		$status = $app->checkAllStatus();
		$prm = $app->getPrm();

		$curUser = $app->getCurUser();
		$email = $curUser->user_email;
		$applicant = $app->getApplicantByEmail($email);

		$title = 'download-doc';
		echo $blade->run("download-doc", compact('title', 'prm', 'status', 'applicant'));
	}

	/**
	 * 必要書類アップロード画面
	 *
	 **/
	function upload_doc() {
		$blade = $this->set_view();
		$p = (object) $_POST;
		$f = (object) $_FILES;
		$prm = (object) $_GET;

		$app = new Applicant;
		$curUser = $app->getCurUser();
		$email = $curUser->user_email;
		$applicant = $app->getApplicantByEmail($email);

		if ((!empty($_FILES)) && ($p->action === "upload")) {
//$this->vd($f);
//$this->vd($p);

			// File Upload
			$uploaddir = dirname(__DIR__). '/contract-tools/uploads/'. $applicant;
			foreach ($_FILES['upload_doc']['name'] as $i => $fname) {
				if (empty($fname)) { continue; }
				$ftype = preg_replace('/^.+\./', '', $fname);
//				$uploadfileName = basename($_FILES['upload_doc']['name'][0]);
				$uploadfileName = sprintf('%s_file_%d', $applicant, intval($i) + 1);
				$tmpfile = $uploaddir . '/' . $uploadfileName;

				// 過去にアップしたファイル(拡張子問わず)があったら削除
				$exts = array('PDF', 'JPG', 'PNG', 'pdf', 'jpg', 'png'); // 対象拡張子
				foreach ($exts as $ext) {
					unlink($tmpfile. '.'. $ext);
				}

				// アップロード処理
				$uploadfile = $tmpfile. '.'. $ftype;
				$uploadBool[$i] = move_uploaded_file($_FILES['upload_doc']['tmp_name'][$i], $uploadfile);
			}

			//return ($uploadBool == true) ? $uploadfile : null;
		}

		// File Delete
		if ($p->action === "cmd_del_file") {
			$ret = $app->delFile($p->delete_file, $applicant);
			echo '<script>window.location.href = "'. home_url(). '/wp-admin/admin.php?page=upload-doc";</script>';
		}

		$prm->post = $applicant;
		$row = $app->getDetail($prm);
		$biz_fg = $row->biz_fg;
		echo $blade->run("upload-doc", compact('applicant', 'biz_fg'));
	}

	/**
	 *
	 **/
	function remove_menus() {
		remove_menu_page('index.php'); //ダッシュボード
		remove_menu_page('profile.php'); // プロフィール
		remove_menu_page('edit.php'); //投稿メニュー
//		remove_menu_page('edit.php?post_type=memo'); //カスタム投稿タイプmemo
		remove_menu_page('upload.php'); // メディア
		remove_menu_page('edit.php?post_type=page'); //固定ページ
		remove_menu_page('edit-comments.php'); //コメント
		remove_menu_page('themes.php'); //外観
		remove_menu_page('plugins.php'); //プラグイン
//		remove_menu_page('users.php'); //ユーザー
		remove_menu_page('tools.php'); //ツールメニュー 
		remove_menu_page('options-general.php'); //設定 
	}

	/**
	 *
	 **/
	function vd($d) {
		echo '<pre>';
//		var_dump($d);
		print_r($d);
		echo '</pre>';
	}
}

$ContractTools = new ContractTools;

/**
 *
 **/
function remove_admin_bar_menus ($wp_admin_bar) {
	$wp_admin_bar->remove_menu( 'wp-logo' ); //ロゴ
	$wp_admin_bar->remove_menu( 'about' ); //ロゴ / WordPressについて
	$wp_admin_bar->remove_menu( 'wporg' ); //ロゴ / WordPress.org
	$wp_admin_bar->remove_menu( 'documentation' ); //ロゴ / ドキュメンテーション
	$wp_admin_bar->remove_menu( 'support-forums' ); //ロゴ / サポート
	$wp_admin_bar->remove_menu( 'feedback' ); //ロゴ / フィードバック
/*
	$wp_admin_bar->remove_menu( 'site-name' ); //サイト名
	$wp_admin_bar->remove_menu( 'view-site' ); //サイト名 / サイトを表示
	$wp_admin_bar->remove_menu( 'updates' ); //更新
	$wp_admin_bar->remove_menu( 'comments' ); //コメント
	$wp_admin_bar->remove_menu( 'new-content' ); //新規
	$wp_admin_bar->remove_menu( 'new-post' ); //新規 / 投稿
	$wp_admin_bar->remove_menu( 'new-media' ); //新規 / メディア
	$wp_admin_bar->remove_menu( 'new-page' ); //新規 / 固定
	$wp_admin_bar->remove_menu( 'new-user' ); //新規 / ユーザー
	$wp_admin_bar->remove_menu( 'view' ); //投稿を表示
	$wp_admin_bar->remove_menu( 'customize' ); //カスタマイズ
	$wp_admin_bar->remove_menu( 'edit' );//〜を編集
	$wp_admin_bar->remove_menu( 'my-account' ); //こんにちは、[ユーザー名]さん
*/
	$wp_admin_bar->remove_menu( 'user-info' ); // ユーザー / [ユーザー名]
	$wp_admin_bar->remove_menu( 'edit-profile' ); //ユーザー / プロフィールを編
/*
	$wp_admin_bar->remove_menu( 'logout' ); //ユーザー / ログアウト
	$wp_admin_bar->remove_menu( 'menu-toggle' ); //メニュー
	$wp_admin_bar->remove_menu( 'search' ); //検索
*/
}

/**
 * 【管理画面】ログイン後にユーザー別にリダイレクトを設定
 **/
function redirect_roll($user_login, $user){
	if($user->roles[0] == 'subscriber'){
		wp_redirect('/wp-admin/admin.php?page=shop-list'); // リダイレクトさせたいURLを指定
		exit();
	}
}
add_action('wp_login', 'redirect_roll', 10, 2);

/**
 * 
 **/
add_shortcode( 'export_detail2', 'add_export_detail2' );
function add_export_detail2() {
//	var_dump($_GET['post']);
	$pno = (int) htmlspecialchars($_GET['pno']);

	$app = new Applicant;
	$curUser = $app->getCurUser();
	$email = $curUser->user_email;
	$applicant = $app->getApplicantByEmail($email);
//	var_dump(array($email, $applicant));
	$initForm = $app->getInitForm();

	$views = __DIR__. '/views';
	$cache = __DIR__. '/cache';
	$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);

	$row = $app->getDetailByMail();
	switch ($pno) {
		case 1:
			$title = 'preview';
			echo $blade->run("preview", compact('row', 'initForm'));
			break;

		case 2:
			$title = 'preview2';
			echo $blade->run("preview2", compact('row'));
			break;
	}
}

/**
 * 
 **/
function login_logo() {
//	echo 'alert("get_bloginfo('template_directory'). '/images/login-logo.png';
//	echo '<script>console.log("test");</script>';
//	echo '<style type="text/css">.login h1 a {background-image: url('.get_bloginfo('template_directory').'/images/login-logo.png);width:84px;height:84px;background-size:84px 84px;}</style>';
	echo '<style type="text/css">.login h1 a {background-image: url(/wp-content/plugins/contract-tools/images/login-logo.png); width: 100px; height: 105px; }</style>';
}
add_action('login_head', 'login_logo');

/**
 * 
 **/
function theme_login_logo_url() {
	return esc_url( home_url( '/' ) );
}
add_filter('login_headerurl', 'theme_login_logo_url');

/**
 * 
 **/
function change_loginpage_username_label($label){
	if (in_array($GLOBALS['pagenow'], array('wp-login.php'))) {
		if ($label == 'ユーザー名またはメールアドレス') {
			$label = 'メールアドレス';
		}
	}
	return $label;
}
add_filter('gettext', 'change_loginpage_username_label');

/**
 *
 **/
function remove_plugin_menus() {
	$cur_user = wp_get_current_user();
	switch ($cur_user->roles[0]) {
		case 'administrator':
			if (!in_array($cur_user->user_login, array('admin'))) {
				remove_menu_page('jetpack'); //Jetpack.
				remove_menu_page('snippets'); //snippets.
			}
			break;
	}
}
add_action('admin_menu', 'remove_plugin_menus', 999);
