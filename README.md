1. Создать бд(имя бд "weather")
2. Миграцию запустить
3. Запутить команду: php artisan db:seed --class=CityTableSeeder

ссылки для работы с API:
1. api/V1/weather?cityName=Москва&typeTemp=c получить погоду
2. api/V1/city?page=2&limit=10 получить список городов