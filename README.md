# Порядок установки проекта

* Клонирование проекта 
* Запуск Docker `` docker compose up -d ``
* Переход в контейнер  `` docker-compose exec -it php bash ``
* Запуск установки расширений yii2 `` composer install ``
* Запуск миграций `` php yii migrate `