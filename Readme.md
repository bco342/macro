# MACRO test-task

## Подготовка к запуску
Перед запуском приложения, необходимо скопировать в файл `.env` настройки переменных окружения из `.env.example` и настроить их.

## Запуск
Для запуска приложения необходимо запустить

```docker-compose -p macro up -d```

При этом в БД будет создана база данных estate_bd, запущены миграции импортирующие dump.sql и изменяющие структуру.

## API
API доступно по адресу http://localhost:8000/
доступны эндпойнты:
- список агентств `GET /api/agencies?page=1&per_page=10`
- список менеджеров `GET /api/managers?page=1&per_page=10&agency=1`
- список продавцов `GET /api/contacts?page=1&per_page=10&agency=1`
- список объектов `GET /api/estates?page=1&per_page=10&agency=1&contact=1&manager=1`

Все query-параметры - необязательные. 

## Импорт данных
Изначально, в БД нет данных. Необходимо импортировать их из excel-файлов.

По умолчанию, excel-файлы находятся в корневой подпапке `/data`
Т.к. в переменной окружения `EXCEL_PATH` прописан относительный путь до файла `estate.xlsx`,
при запуске консольной команды `bin/console import` без параметров происходит импорт именно этого файла.
```
root@a203566e1a60:/var/www/app# bin/console import
Start data import from file: ../data/estate.xlsx
51/51 [============================] 100%

Data imported successfully
```
## Обновление
Для импорта обновления необходимо указать путь до нужного файла.
```
root@a203566e1a60:/var/www/app# bin/console import /var/www/data/estate_update.xlsx
Start data import from file: ../data/estate_update.xlsx
8/8 [============================] 100%

Data imported successfully
```

## Миграции
Для работы с миграциями необходимо использовать команды:
- `bin/console migrate up`
- `bin/console migrate down`



