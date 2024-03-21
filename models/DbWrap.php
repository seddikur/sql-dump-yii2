<?php

namespace app\models;

use Yii;
use yii\bootstrap\Modal;
use yii\caching\Cache;
use yii\db\ActiveRecord;
use yii\db\Exception;


// https://adw0rd.com/2009/6/7/mysqldump-and-cheat-sheet/
class DbWrap extends ActiveRecord{

	protected static $filenameCreate = 'create-tables.sql';
	protected static $countSelectRows = 1000;
	protected static $dirBackUp = __DIR__ . '/backup_db/remote';
	/*
	 * SET @OLD_CHARACTER_SET_CLIENT - указываем кодировку на клиенте
	 * SET NAMES - указываем нашу кодировку
	 * SET @OLD_FOREIGN_KEY_CHECKS - отключаем проверку целостности таблицы БД на время выполнения запроса
	 * SET @OLD_SQL_MODE - указываем режим работы mysql сервера
	 * */
	protected static $offlineCheckForeignKey = "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;" . PHP_EOL;


	public static function getDbRemote(){ return Yii::$app->get('db_remote'); }

	/*
	 * http://sitear.ru/material/mysql-backups
	 * */
	public static function export($table, $dateString){
		if( ! $table) return false;
		if( ! $dateString) return false;
		//exit(PHP_EOL . __FILE__ . '::' . __LINE__ . PHP_EOL);
		$typesString = [
			'date',
			'longtext',
			'text',
			'enum',
			'varchar',
		];
		$typesNoString = [
			'float',
			'double',
			'decimal',
			'bit',
			'int',
			'smallint',
			'mediumint',
			'bigint',
			'tinyint',
		];


		// выбираем подключение
		$dbRemote = DbWrap::getDbRemote();

		$dbName = self::getDsnAttribute('dbname', $dbRemote->dsn);
		$file = self::$dirBackUp . '/' . $dbName . '-' . $dateString . '/' . $dbName . '-' . $dateString . '%' . $table . '.sql';
		@mkdir(dirname($file), 0755, true);


		// Получим дамп на создание
		$sqlShowCreateTable = 'SHOW CREATE TABLE  `' . $table . '`;';
		$create = $dbRemote->createCommand($sqlShowCreateTable)->noCache()->queryOne()['Create Table'];

		$insertPrefix = "INSERT INTO `$table` (";
		// собираем типы данных
		$fieldTypes = [];
		$sqlColumns = "SHOW COLUMNS FROM `" . $table . "`;`";
		$dataColumns = $dbRemote->createCommand($sqlColumns)->noCache()->queryAll();
		foreach($dataColumns as $dataColumn){
			if($strpos = strpos($dataColumn['Type'], '(')) $fieldTypes[$dataColumn['Field']] = trim(substr($dataColumn['Type'], 0, $strpos));else
				$fieldTypes[$dataColumn['Field']] = trim($dataColumn['Type']);
			$insertPrefix .= '`' . $dataColumn['Field'] . '`,';
		}
		$insertPrefix = trim(trim($insertPrefix, ',')) . ') VALUES ';
		$create = self::$offlineCheckForeignKey . 'DROP TABLE IF EXISTS `' . $table . '`;' . PHP_EOL . $create . ';';

		//if($k == 0) self::w($file . self::$filenameCreate, self::$offlineCheckForeignKey . PHP_EOL);

		self::w($file, $create . PHP_EOL);

		$limit = (self::$countSelectRows) ? self::$countSelectRows : 100;
		// делаеем INSERT
		$sqlCount = "SELECT count(*) as `count` FROM `" . $table . "`";
		$count = (int) $dbRemote->createCommand($sqlCount)->noCache()->queryOne()['count'];
		$pages = ceil($count / $limit);

		for($page = 0; $page <= $pages; $page ++){
			//if($page == 0) self::w($file . $table . '.sql', self::$offlineCheckForeignKey . PHP_EOL);


			$offset = $page * $limit;
			if($offset > 0) $offset ++;
			//$max = $page + 1 * $limit;
			$sqlSelect = "SELECT * FROM `$table` LIMIT $offset, $limit;";
			//echo '<pre>$sqlSelect ', print_r($sqlSelect, true), '</pre>';

			$list = $dbRemote->createCommand($sqlSelect)->noCache()->queryAll();

			if( ! empty($list)){
				$insert = '';
				foreach($list as $item){

					$insert .= "(";
					$str = '';
					foreach($item as $k => $v){
						// определяем необходимость в кавычках
						if(in_array($fieldTypes[$k], $typesNoString)){
							if(empty($v)) $str .= "'$v',";else
								$str .= $v . ',';

						}else{
							$str .= "'" . addslashes($v) . "',";
						}
					}
					$insert .= str_replace(array(
						"\r",
						"\n",
					), "", trim($str, ',') . "),");
				}
				$insert = $insertPrefix . trim($insert, ',') . ';';
				self::w($file, $insert . PHP_EOL);
			}
		}
		return true;
	}

	public static function w($file, $content){
		$f = fopen($file, 'a');
		$res = fwrite($f, $content);
		fclose($f);
		return $res;
	}


	// Возвращает название хоста (например localhost)
	private static function getDsnAttribute($name, $dsn){
		if(preg_match('/' . $name . '=([^;]*)/', $dsn, $match)){
			return $match[1];
		}else{
			return '';
		}
	}

	// импортирует последний бекап
	public static function importAll($table){
		if( ! $table) return false;
		$globsBackups = glob(self::$dirBackUp . '/*');
		if(empty($globsBackups)) return false;
		rsort($globsBackups, SORT_STRING);
		//$files = glob($globsBackups[0] . '/*.sql');
		$file = glob($globsBackups[0] . '/*%' . $table . '.sql');

		if(empty($file)) return false;
		$file = $file[0];
		if( ! file_exists($file)) return false;
		$db = DbWrap::getDb();
		$dbName = self::getDsnAttribute('dbname', $db->dsn);
		$command = "mysql -u" . $db->username;
		if( ! empty($db->password)) $command .= " -p" . $db->password;
		$command .= " $dbName < " . $file;

		shell_exec($command);
		return true;
	}

	public static function isWindows(){
		$php_uname = php_uname();
		$arr = explode('Windows', $php_uname);
		if(count($arr) > 1) return true;
		return false;
	}

	// Удаляет все ранее созданные бекапы
	public static function remove(){
		if(self::isWindows()){
			$command = 'RD /S/q ' . str_replace('/', '\\', self::$dirBackUp) . '\*';
		}else{
			$command = "cd " . self::$dirBackUp . " && rm -rf *";
		}
		shell_exec($command);
		return true;
	}


	// импортирует последний бекап
	public static function importFavorites($table){
		if( ! $table) return false;
		$globsBackups = glob(self::$dirBackUp . '/*');
		if(empty($globsBackups)) return false;
		rsort($globsBackups, SORT_STRING);
		$file = glob($globsBackups[0] . '/*%' . $table . '.sql');
		if(empty($file)) return false;
		$file = $file[0];
		if( ! file_exists($file)) return false;
		$db = DbWrap::getDb();
		$dbName = self::getDsnAttribute('dbname', $db->dsn);
		$command = "mysql -u" . $db->username . " -p" . $db->password . " $dbName < " . $file;
		shell_exec($command);
		return true;
	}


	public static function migrate(&$mess = ''){
		$db = DbWrap::getDb();
		$migrations = [];
		$migrations[] = "ALTER TABLE `patients_mis` CHANGE COLUMN `birth` `birth` DATE NULL DEFAULT NULL AFTER `patronymic`, CHANGE COLUMN `regdate` `regdate` DATE NULL DEFAULT NULL AFTER `podr`;";
		$migrations[] = "ALTER TABLE `patients_mis` ADD COLUMN `lastdate` DATE NULL AFTER `insurance_id`, ADD COLUMN `nvisit` INT NULL AFTER `lastdate`;";

		$transaction = $db->beginTransaction();
		//echo '<pre>$transaction ', print_r(get_class_methods($transaction), true), '</pre>';
		//exit(PHP_EOL . __FILE__ . '::' . __LINE__ . PHP_EOL);
		try{
			foreach($migrations as $migrate) $db->createCommand($migrate)->execute();
			$transaction->commit();
			//$mess = 'Транзакция успешно выполнена';
			return true;
		}catch(Exception $e){
			// если хоть одно из сохранений не удалось, то откатываемся
			$transaction->rollback();
			$mess = 'Транзакция НЕ выполнена ОШИБКА <br><b>' . $e->getMessage() . '</b>';
			return false;
		}
	}

	public static function getRemoteTables(){
		$sqlShowTables = "SHOW TABLES";
		$db = DbWrap::getDbRemote();
		$tablesTemp = $db->createCommand($sqlShowTables)->queryAll();
		$tables = [];
		if(empty($tablesTemp)) return false;
		foreach($tablesTemp as $temp) $tables = array_merge($tables, array_values($temp));
		return $tables;
	}
}
