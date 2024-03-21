$(document).ready(function () {

	const DEBUG = false;

	var dbExportAll, dbImportAll;
	dbExportAll = dbImportAll = {};


	function isTrue(jsonObj) {
		var res = true;
		if (!jsonObj) return false;
		if (count(jsonObj) === 0) return false;
		$.each(jsonObj, function (key, value) {
			if (!value) res = false;
		});
		return res;
	}

	function count(c) {
		var a = 0, b;
		for (b in c) b && a++;
		return a
	}

	function startDB() {
		if (DEBUG) console.log('startDB');
		$('body').addClass('loading');
		$('button').attr('disabled', 'disabled');
	}

	function finishFailDB() {
		$('body').removeClass('loading');
		$('body').addClass('loading-fail');
		$('button').removeAttr('disabled');
	}

	function finishSuccessDB() {
		if (DEBUG) console.log('finishSuccessDB');
		$('body').removeClass('loading');
		$('body').addClass('loading-success');
		$('button').removeAttr('disabled');
		// alert('Обновление завершено');
		// $('#messages').prepend('<p class="alert alert-success">Обновление завершено</p>');
	}


	function logMess(mess) {
		var str = '';
		if (mess) {
			$.each(mess, function (code, messages) {
				$.each(messages, function (index, message) {
					str += '<p class="alert alert-' + code + '">' + message + '</p>';
				});
			});
		}
		$('#messages').prepend(str);
	}

	function logMessStr(mess, status) {
		$('#messages').prepend('<p class="alert alert-' + status + '">' + mess + '</p>');
	}


	$('body').on('click', '#dbExportAll', function () {
		startDB();
		var date = new Date();
		const dateStr = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate() + '-' + date.getHours() + '-' + date.getMinutes() + '-' + date.getSeconds();
		if (DEBUG) console.log(dateStr);
		$.ajax({
			url: URL_TABLES,
			type: 'POST',
			// async: false,
			timeout: 99999999999,
			// data: data,
			dataType: 'json',
			beforeSend: function () {
				// console.log(jqXHR);
			},
			complete: function (jqXHR, textStatus) {
				if (DEBUG) {
					console.log('----------------------');
					console.log(textStatus);
					console.log(jqXHR);
					console.log('----------------------');
				}
			},
			error: function (json) {
				if (DEBUG) console.log(json);
				finishFailDB();
			},
			success: function (json) {
				if (json.messages) logMess(json.messages);
				if (json.data) {
					$.each(json.data, function (i, table) {
						dbExportAll[table] = false;
					});

					$.each(json.data, function (i, table) {
						// отправляем запрос на Экспорт
						$.ajax({
							url: URL_EXPORT,
							type: 'POST',
							// async: false,
							timeout: 99999999999,
							data: {
								date: dateStr,
								table: table
							},
							dataType: 'json',
							beforeSend: function () {
							},
							complete: function (jqXHR, textStatus) {
								if (DEBUG) {
									console.log('----------------------');
									console.log(textStatus);
									console.log(jqXHR);
									console.log('----------------------');
								}
							},
							error: function (json) {
								if (DEBUG) console.log(json);
								finishFailDB();
							},
							success: function (json) {
								if (DEBUG) {
									console.log(json);
								}
								if (json.messages) logMess(json.messages);
								dbExportAll[table] = true;
								if (isTrue(dbExportAll)) {
									finishSuccessDB();
									logMessStr('<b><span class="glyphicon glyphicon-ok"></span> Экспорт ВСЕХ таблиц ЗАВЕРШЁН', 'success');
								}
							}
						});
					});

				}
			}
		});
		return false;
	});

	$('body').on('click', '#dbImportAll', function () {
		// var date = new Date();
		// const dateStr = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate() + '-' + date.getHours() + '-' + date.getMinutes() + '-' + date.getSeconds();
		// if (DEBUG) console.log(dateStr);
		startDB();
		$.ajax({
			url: URL_TABLES,
			type: 'POST',
			// async: false,
			timeout: 99999999999,
			// data: data,
			dataType: 'json',
			beforeSend: function () {
				// console.log(jqXHR);
			},
			complete: function (jqXHR, textStatus) {
				if (DEBUG) {
					console.log('----------------------');
					console.log(textStatus);
					console.log(jqXHR);
					console.log('----------------------');
				}

			},
			error: function (json) {
				if (DEBUG) console.log(json);
				finishFailDB();
			},
			success: function (json) {
				if (json.messages) logMess(json.messages);
				if (json.data) {
					$.each(json.data, function (i, table) {
						dbImportAll[table] = false;
					});
					$.each(json.data, function (i, table) {
						// отправляем запрос на импорт
						// setTimeout(function () {
						console.log(table);
						$.ajax({
							url: URL_IMPORT,
							type: 'POST',
							// async: false,
							timeout: 99999999999,
							data: {
								// date: dateStr,
								table: table
							},
							dataType: 'json',
							beforeSend: function () {
							},
							complete: function (jqXHR, textStatus) {
								if (DEBUG) {
									console.log('----------------------');
									console.log(textStatus);
									console.log(jqXHR);
									console.log('----------------------');
								}
							},
							error: function (json) {
								if (DEBUG) console.log(json);
								finishFailDB();
							},
							success: function (json) {
								if (DEBUG) console.log(json);
								if (json.messages) logMess(json.messages);
								dbImportAll[table] = true;
								if (isTrue(dbImportAll)) {
									finishSuccessDB();
									logMessStr('<b><span class="glyphicon glyphicon-ok"></span> Импорт ВСЕХ таблиц' +
										' ЗАВЕРШЁН</b>', 'success');
								}
							}
						});
						// }, 1000);
					});
				}
			}
		});
		return false;
	});


	$('body').on('click', '#dbMigrate', function () {
		startDB();
		$.ajax({
			url: URL_MIGRATE,
			type: 'POST',
			timeout: 99999999999,
			// async: false,
			// data: data,
			dataType: 'json',
			beforeSend: function () {
				// console.log(jqXHR);
			},
			complete: function (jqXHR, textStatus) {
				if (DEBUG) {
					console.log('----------------------');
					console.log(textStatus);
					console.log(jqXHR);
					console.log('----------------------');
				}
			},
			error: function (json) {
				if (DEBUG) console.log(json);
			},
			success: function (json) {
				if (DEBUG) {
					console.log(json);
				}
				if (json.messages) logMess(json.messages);
				finishSuccessDB();
				logMessStr('<b>Обновление ЗАВЕРШЕНО <span class="glyphicon glyphicon-ok"></span></b>', 'success');
			}
		});
		return false;
	});

	$('body').on('click', '#dbRemove', function () {
		startDB();
		$.ajax({
			url: URL_REMOVE,
			type: 'POST',
			timeout: 99999999999,
			// async: false,
			// data: data,
			dataType: 'json',
			beforeSend: function () {
				// console.log(jqXHR);
			},
			complete: function (jqXHR, textStatus) {
				if (DEBUG) {
					console.log('----------------------');
					console.log(textStatus);
					console.log(jqXHR);
					console.log('----------------------');
				}
			},
			error: function (json) {
				if (DEBUG) console.log(json);
			},
			success: function (json) {
				if (DEBUG) {
					console.log(json);
				}
				if (json.messages) logMess(json.messages);
				finishSuccessDB();
				logMessStr('<b>Файлы бекапов удалены <span class="glyphicon glyphicon-ok"></span></b>', 'success');
			}
		});
		return false;
	});
});