# Supplier

Номенклатура на доставчици: фирмени данни (ЕИК/ДДС), контакти и API помощници. Използва се от доставките.

## Функционалност

- CRUD на доставчици
- Търсене и филтриране
- API за списък, добавяне през форма, фирмени данни по ЕИК и данни за фактура
- Валидация на ЕИК

## Интеграция в системата

Copy-in модул: файловете се копират в хоста под `App\`.

- Пътища: `src/Controller|Entity|Form|Repository|Service/Supplier/`, `templates/supplier/`, `translations/supplier.*.yaml`, `config/roles/supplier.yaml`
- Меню: Доставчици (`supplier_list`) при `ROLE_SUPPLIER_VIEW`
- Роли: `ROLE_SUPPLIER_{VIEW,CREATE,EDIT,DELETE,APPROVE}`
- Маршрути: `/suppliers` и `/suppliers/api/...`

Използва се от **delivery**.

## Структура

- `SupplierController`
- Ентитет: `Supplier`
- `SupplierService`
- Форми: доставчик и търсене

## Зависимости

- **erp-core** — `EditForm` / `SearchForm`, `CoreUtils`

## Документация

- [docs/supplier/README.md](docs/supplier/README.md)
- [docs/supplier/installation-guide.md](docs/supplier/installation-guide.md)
- [docs/supplier/quick-start.md](docs/supplier/quick-start.md)
