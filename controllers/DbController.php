<?php

namespace app\controllers;

use Yii;
use yii\db\Exception;
use app\models\DbWrap;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;

class DbController extends AppController{

	// /db
	public function actionIndex(){
		set_time_limit(0);
		$data = [];
		return $this->render('index', compact('data'));
	}

	// /db/export
	public function actionExport(){
		// экспорт
		ini_set('memory_limit', '200M');
		set_time_limit(0);
		//echo ini_get('max_execution_time');
		//exit(PHP_EOL . __FILE__ . '::' . __LINE__ . PHP_EOL);
		$json = [];
		if( ! Yii::$app->request->isAjax){
			$json['messages']['danger'][] = 'Отправление не правильный запрос';
		}else{
			try{
				$table = ( ! empty($_POST['table'])) ? $_POST['table'] : 'patients_mis';
				$date = ( ! empty($_POST['date'])) ? $_POST['date'] : date('Y-m-d_H-i-s');
				if(DbWrap::export($table, $date)){
					$time = time() - $_SERVER['REQUEST_TIME'];
					$json['messages']['success'][] = 'Экспорт таблицы ' . $table . ' успешно выполнен за ' . $time . ' Секунд ';
				}else
					$json['messages']['warning'][] = 'Возникла ошибка при выполнении экспорта таблицы ' . $table;
			}catch(Exception $e){
				$json['messages']['danger'][] = 'Возникла ошибка при выполнении экспорта таблицы ' . $table;
				$json['messages']['danger'][] = $e->getMessage();
				exit(json_encode($json));
			}
		}
		exit(json_encode($json));

	}

	// /db/import
	public function actionImport(){
		ini_set('memory_limit', '200M');
		set_time_limit(0);

		// import
		$json = [];
		if( ! Yii::$app->request->isAjax){
			$json['messages']['danger'][] = 'Отправление не правильный запрос';
		}else{
			try{
				//$table = ( ! empty($_POST['table'])) ? $_POST['table'] : 'acti';
				$table = ( ! empty($_POST['table'])) ? $_POST['table'] : 'patients_mis';
				if(DbWrap::importAll($table)){
					$time = time() - $_SERVER['REQUEST_TIME'];
					$json['messages']['success'][] = 'Импорт таблицы ' . $table . ' успешно выполнен за ' . $time . ' Секунд';
				}else{
					$json['messages']['warning'][] = 'Возникла ошибка при выполнении импорта таблицы ' . $table;
				}
			}catch(Exception $e){
				$json['messages']['danger'][] = 'Возникла ошибка при выполнении импорта таблицы ' . $table;
				$json['messages']['danger'][] = $e->getMessage();
				exit(json_encode($json));
			}
		}
		exit(json_encode($json));
	}

	// /db/remove
	public function actionRemove(){
		ini_set('memory_limit', '200M');
		set_time_limit(0);
		// remove
		$json = [];
		if( ! Yii::$app->request->isAjax){
			$json['messages']['danger'][] = 'Отправление не правильный запрос';
		}else{
			try{
				if(DbWrap::remove()){
					$time = time() - $_SERVER['REQUEST_TIME'];
					$json['messages']['success'][] = 'Удаление файлов бекапов БД  успешно выполнено за ' . $time . ' Секунд';
				}else{
					$json['messages']['warning'][] = 'Возникла ошибка при удаление файлов бекапов БД' ;
				}
			}catch(Exception $e){
				$json['messages']['danger'][] = 'Возникла КРИТИЧЕСКАЯ ошибка при удаление файлов бекапов БД' ;
				$json['messages']['danger'][] = $e->getMessage();
				exit(json_encode($json));
			}
		}
		exit(json_encode($json));
	}

	// /db/migrate
	public function actionMigrate(){
		ini_set('memory_limit', '200M');
		set_time_limit(0);
		//echo ini_get('max_execution_time');
		//exit(PHP_EOL . __FILE__ . '::' . __LINE__ . PHP_EOL);

		$json = [];
		if( ! Yii::$app->request->isAjax){
			$json['messages']['danger'][] = 'Отправление не правильный запрос';
		}else{
			$mess = '';
			try{
				if(DbWrap::migrate($mess)){
					$time = time() - $_SERVER['REQUEST_TIME'];
					$json['messages']['success'][] = 'Миграция успешно выполнена за ' . $time . ' Секунд';
					if($mess) $json['messages']['info'][] = 'Примечания: ' . $mess;
				}else
					$json['messages']['danger'][] = 'Возникла ошибка при выполнении миграции ';
				if($mess) $json['messages']['info'][] = 'Примечания: ' . $mess;
			}catch(Exception $e){
				$json['messages']['danger'][] = 'Возникла ошибка при выполнении миграции ';
				if($mess) $json['messages']['info'][] = 'Примечания: ' . $mess;
				exit(json_encode($json));
			}
		}
		exit(json_encode($json));
	}


	// /db/tables
	public function actionTables(){
		ini_set('memory_limit', '200M');
		set_time_limit(0);
		$json = [];
		if( ! Yii::$app->request->isAjax){
			$json['messages']['danger'][] = 'Отправление не правильный запрос';
		}else{
			try{
				if($tables = DbWrap::getRemoteTables()){
					$time = time() - $_SERVER['REQUEST_TIME'];
					$json['messages']['success'][] = 'Список таблиц успешно получен за ' . $time . ' Секунд';
					$json['data'] = $tables;
				}else{
					$json['messages']['danger'][] = 'Возникла ошибка при получении списка таблиц ';
				}
			}catch(Exception $e){
				$json['messages']['danger'][] = 'Возникла ошибка при получении списка таблиц ';
				exit(json_encode($json));
			}
		}
		exit(json_encode($json));
	}


}
