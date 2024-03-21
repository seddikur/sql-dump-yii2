<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Создание резервной копии, большой удалённой БД средствами YII2';
$this->params['breadcrumbs'][] = $this->title;
?>
	<div class="row">
		<div class="col-lg-12">
			<? // Dump::xp($data, 1, '$data'); ?>
			<div class="site-about">
				<h1><?=Html::encode($this->title)?></h1>

				<div class="row">
					<div class="col-lg-5">

						<div class="row">
							<div class="col-lg-12">
								<p>
									<button type="button" id="dbExportAll" class="btn btn-danger">
										<span class="glyphicon glyphicon-open"></span> Экспортировать ВСЕ таблицы из удалёной БД
									</button>
								</p>
								<p>
									<button type="button" id="dbImportAll" class="btn btn-success">
										<span class="glyphicon glyphicon-save"></span> Импортировать ВСЕ таблицы в локальную БД
									</button>
								</p>
								<p>
									<button type="button" id="dbMigrate" class="btn btn-primary">
										<span class="glyphicon glyphicon-refresh"></span> Применить изменения в локальной БД
									</button>
								</p>
							</div>


							<div class="col-lg-12">
								<hr>
								<p>
									<button type="button" id="dbRemove" class="btn btn-warning">
										<span class="glyphicon glyphicon-trash"></span> Удалить все бекапы
									</button>
								</p>
							</div>
						</div>

					</div>

					<div class="col-lg-7">
						<div style="max-height:550px; overflow: scroll; " id="messages"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
$urlTables = Url::to(['db/tables']);
$urlExport = Url::to(['db/export']);
$urlImport = Url::to(['db/import']);
$urlRemove = Url::to(['db/remove']);
$urlMigrate = Url::to(['db/migrate']);

$JS = <<<JS
const URL_TABLES ='{$urlTables}';
const URL_EXPORT ='{$urlExport}';
const URL_IMPORT ='{$urlImport}';
const URL_REMOVE ='{$urlRemove}';
const URL_MIGRATE ='{$urlMigrate}';
JS;

$this->registerJs($JS, $this::POS_HEAD);

