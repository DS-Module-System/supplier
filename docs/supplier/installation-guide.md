# Installation Guide - Supplier Module

## Общ преглед

Този ръководство описва подробно процеса на инсталация на модула за доставчици в Symfony приложение.

## Системни изисквания

### Софтуерни изисквания
- PHP 8.1 или по-нова версия
- Symfony Framework 6.0+
- Composer 2.0+
- MySQL 5.7+ или MariaDB 10.2+
- Web server (Apache/Nginx)

### PHP разширения
- ext-ctype
- ext-iconv
- ext-json
- ext-mbstring
- ext-openssl
- ext-pdo_mysql
- ext-xml
- ext-zip

## Стъпка 1: Подготовка на средата

1. **Клонирайте проекта (ако не е вече направено):**
   ```bash
   git clone <repository-url>
   cd ds-erp-module-system-test
   ```

2. **Инсталирайте зависимостите:**
   ```bash
   composer install
   ```

3. **Копирайте environment файла:**
   ```bash
   cp .env .env.local
   ```

## Стъпка 2: Конфигурация на базата данни

1. **Редактирайте .env.local файла:**
   ```env
   DATABASE_URL="mysql://username:password@127.0.0.1:3306/database_name?serverVersion=8.0.32&charset=utf8mb4"
   ```

2. **Създайте базата данни:**
   ```bash
   php bin/console doctrine:database:create
   ```

## Стъпка 3: Изпълнение на миграции

1. **Проверете статуса на миграциите:**
   ```bash
   php bin/console doctrine:migrations:status
   ```

2. **Изпълнете миграциите:**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

3. **Проверете дали таблицата е създадена:**
   ```bash
   php bin/console doctrine:schema:validate
   ```

## Стъпка 4: Конфигурация на роли

1. **Проверете дали ролите са заредени:**
   ```bash
   php bin/console debug:container --parameter=supplier_roles
   ```

2. **Добавете ролите в базата данни (ако е необходимо):**
   ```sql
   INSERT INTO user_group (name, roles) VALUES 
   ('Supplier Manager', '["ROLE_SUPPLIER_VIEW","ROLE_SUPPLIER_CREATE","ROLE_SUPPLIER_EDIT","ROLE_SUPPLIER_DELETE"]');
   ```

## Стъпка 5: Конфигурация на преводи

1. **Проверете дали преводните файлове са правилно заредени:**
   ```bash
   php bin/console debug:translation supplier
   ```

2. **Изчистете кеша:**
   ```bash
   php bin/console cache:clear
   ```

## Стъпка 6: Конфигурация на web server

### Apache конфигурация

Създайте или редактирайте `.htaccess` файла в `public/` директорията:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

### Nginx конфигурация

Добавете следната конфигурация в nginx.conf:

```nginx
location / {
    try_files $uri /index.php$is_args$args;
}

location ~ ^/index\.php(/|$) {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_split_path_info ^(.+\.php)(/.*)$;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    fastcgi_param DOCUMENT_ROOT $realpath_root;
    internal;
}
```

## Стъпка 7: Тестване на инсталацията

1. **Проверете маршрутите:**
   ```bash
   php bin/console debug:router | grep supplier
   ```

2. **Тествайте достъпа до модула:**
   - Отворете браузъра
   - Навигирайте до `http://your-domain/suppliers/`
   - Проверете дали страницата се зарежда правилно

3. **Тествайте API endpoints:**
   ```bash
   curl -X GET "http://your-domain/suppliers/api/supplier-list"
   ```

## Стъпка 8: Конфигурация на права за достъп

1. **Създайте потребител с права за доставчици:**
   ```bash
   php bin/console app:create-user --email=supplier@example.com --roles=ROLE_SUPPLIER_VIEW,ROLE_SUPPLIER_CREATE,ROLE_SUPPLIER_EDIT
   ```

2. **Проверете правата за достъп:**
   - Влезте в системата с новосъздадения потребител
   - Проверете дали имате достъп до модула за доставчици

## Стъпка 9: Оптимизация

1. **Оптимизирайте за production:**
   ```bash
   composer install --no-dev --optimize-autoloader
   php bin/console cache:clear --env=prod
   ```

2. **Настройте кеширането:**
   ```bash
   php bin/console cache:warmup --env=prod
   ```

## Проверка на инсталацията

### Команди за проверка

```bash
# Проверка на схемата на базата данни
php bin/console doctrine:schema:validate

# Проверка на маршрутите
php bin/console debug:router

# Проверка на услугите
php bin/console debug:container --tag=controller.service_arguments

# Проверка на преводите
php bin/console debug:translation supplier --domain=supplier
```

### Очаквани резултати

- ✅ Всички миграции са изпълнени успешно
- ✅ Таблицата `supplier` съществува в базата данни
- ✅ Маршрутите за доставчици са регистрирани
- ✅ Преводните файлове са заредени
- ✅ Контролерът е регистриран като услуга

## Troubleshooting

### Проблем: "Class not found"
**Решение:** Изчистете кеша и проверете autoloader:
```bash
composer dump-autoload
php bin/console cache:clear
```

### Проблем: "Database connection failed"
**Решение:** Проверете DATABASE_URL в .env.local файла

### Проблем: "Route not found"
**Решение:** Проверете дали контролерът е правилно регистриран:
```bash
php bin/console debug:router | grep supplier
```

### Проблем: "Translation not found"
**Решение:** Проверете дали преводните файлове са в правилната директория и дали са правилно форматирани.

## Следващи стъпки

След успешната инсталация:

1. **Прочетете документацията:** `docs/supplier/README.md`
2. **Следвайте quick-start ръководството:** `docs/supplier/quick-start.md`
3. **Тествайте функционалностите:** Създайте тестови доставчик
4. **Персонализирайте според нуждите:** Променете шаблоните и логиката

## Поддръжка

За допълнителна помощ:
- Проверете Symfony документацията
- Създайте issue в GitHub repository
- Свържете се с development екипа 