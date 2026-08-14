# Supplier Module

## Описание

Модулът за доставчици предоставя функционалност за управление на доставчици в системата. Той включва CRUD операции за създаване, редактиране, изтриване и преглед на доставчици.

## Функционалности

- **Списък с доставчици** - преглед на всички доставчици с възможност за търсене и филтриране
- **Създаване на доставчик** - добавяне на нов доставчик в системата
- **Редактиране на доставчик** - промяна на данните на съществуващ доставчик
- **Изтриване на доставчик** - премахване на доставчик от системата
- **API функционалности** - REST API за интеграция с други системи
- **Валидация на ЕИК** - проверка на ЕИК номер чрез външни API

## Структура на данните

### Supplier Entity

- `id` - уникален идентификатор
- `name` - име на доставчика
- `vat` - ДДС номер
- `eek` - ЕИК номер (уникален)
- `address` - адрес
- `responsiblePerson` - отговорно лице
- `email` - имейл адрес
- `phone` - телефонен номер
- `countryCode` - код на държава
- `supplierNumber` - номер на доставчик

## Маршрути

- `GET /suppliers/` - списък с доставчици
- `GET /suppliers/create` - форма за създаване
- `POST /suppliers/create` - създаване на доставчик
- `GET /suppliers/{id}/edit` - форма за редактиране
- `POST /suppliers/{id}/edit` - редактиране на доставчик
- `GET /suppliers/api/supplier-list` - API за списък с доставчици
- `GET /suppliers/api/supplier/get-company-data` - API за данни за компания
- `GET /suppliers/api/supplier/compare-company/{id}` - API за сравнение на данни
- `GET /suppliers/api/supplier-data-for-invoice/{supplierId}` - API за данни за фактура

## Роли и права

- `ROLE_SUPPLIER_VIEW` - преглед на доставчици
- `ROLE_SUPPLIER_CREATE` - създаване на доставчици
- `ROLE_SUPPLIER_EDIT` - редактиране на доставчици
- `ROLE_SUPPLIER_DELETE` - изтриване на доставчици
- `ROLE_SUPPLIER_APPROVE` - одобряване на промени

## Инсталация

1. Изпълнете миграцията за създаване на таблицата:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

2. Добавете ролите в системата (ако е необходимо)

3. Конфигурирайте правата за достъп

## Използване

### Списък с доставчици

За достъп до списъка с доставчици, навигирайте до `/suppliers/`

### Създаване на доставчик

1. Навигирайте до `/suppliers/create`
2. Попълнете необходимите полета
3. Използвайте бутона "Вземи данни за компанията" за автоматично попълване на данните по ЕИК
4. Натиснете "Запази"

### Редактиране на доставчик

1. От списъка с доставчици, натиснете бутона за редактиране
2. Променете необходимите полета
3. Натиснете "Запази"

## API използване

### Вземане на списък с доставчици

```javascript
fetch('/suppliers/api/supplier-list?search=company_name')
  .then(response => response.json())
  .then(data => console.log(data));
```

### Вземане на данни за компания

```javascript
fetch('/suppliers/api/supplier/get-company-data?eik=123456789&countryCode=BG')
  .then(response => response.json())
  .then(data => console.log(data));
```

## Конфигурация

Модулът използва следните конфигурационни файлове:

- `config/roles/supplier.yaml` - роли и права
- `translations/supplier.bg.yaml` - български преводи
- `translations/supplier.en.yaml` - английски преводи

## Зависимости

- Symfony Framework
- Doctrine ORM
- KnpPaginatorBundle
- Twig Template Engine 