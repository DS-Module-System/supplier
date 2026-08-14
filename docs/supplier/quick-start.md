# Quick Start Guide - Supplier Module

## Бързо стартиране

Този ръководство ще ви помогне да започнете работа с модула за доставчици бързо и лесно.

## Предварителни изисквания

- Symfony Framework 6.0+
- Doctrine ORM
- MySQL/MariaDB база данни
- Composer

## Стъпка 1: Инсталация

1. **Изпълнете миграцията:**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

2. **Проверете дали всички зависимости са инсталирани:**
   ```bash
   composer install
   ```

## Стъпка 2: Конфигурация

1. **Проверете конфигурационните файлове:**
   - `config/roles/supplier.yaml` - роли за доставчици
   - `translations/supplier.bg.yaml` - български преводи
   - `translations/supplier.en.yaml` - английски преводи

2. **Добавете ролите в системата (ако е необходимо):**
   - `ROLE_SUPPLIER_VIEW`
   - `ROLE_SUPPLIER_CREATE`
   - `ROLE_SUPPLIER_EDIT`
   - `ROLE_SUPPLIER_DELETE`

## Стъпка 3: Тестване

1. **Отворете браузъра и навигирайте до:**
   ```
   http://your-domain/suppliers/
   ```

2. **Тествайте основните функции:**
   - Създаване на нов доставчик
   - Редактиране на съществуващ доставчик
   - Търсене в списъка с доставчици
   - Изтриване на доставчик

## Стъпка 4: API тестване

1. **Тествайте API за списък с доставчици:**
   ```bash
   curl "http://your-domain/suppliers/api/supplier-list?search=test"
   ```

2. **Тествайте API за данни за компания:**
   ```bash
   curl "http://your-domain/suppliers/api/supplier/get-company-data?eik=123456789&countryCode=BG"
   ```

## Често срещани проблеми

### Проблем: "Entity mapping is invalid"
**Решение:** Проверете дали всички Entity класове са правилно конфигурирани и дали миграцията е изпълнена.

### Проблем: "Route not found"
**Решение:** Проверете дали контролерът е правилно регистриран и дали маршрутите са конфигурирани.

### Проблем: "Translation not found"
**Решение:** Проверете дали преводните файлове са правилно заредени и дали ключовете съществуват.

## Следващи стъпки

1. **Персонализиране:** Променете шаблоните според вашите нужди
2. **Разширяване:** Добавете нови полета или функционалности
3. **Интеграция:** Интегрирайте модула с други части на системата
4. **Тестване:** Напишете unit и integration тестове

## Поддръжка

За въпроси и проблеми, моля проверете:
- Документацията на Symfony
- GitHub issues на проекта
- Stack Overflow

## Полезни команди

```bash
# Изчистване на кеша
php bin/console cache:clear

# Проверка на маршрутите
php bin/console debug:router

# Проверка на услугите
php bin/console debug:container

# Валидация на схемата
php bin/console doctrine:schema:validate
``` 