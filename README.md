# Vacations scheduler

Synchronization of the vacations sheet and the calendar with vacations events.

This script uses vacations records in the given Google Sheet document to create or delete vacations events
in the given Google Calendar

### Google project setup

1. Create a project in Google Cloud Platform
2. Set APIs: `Google Calendar API`, `Google Sheets API`
3. Download OAuth credentials: open with the selected project Google Cloud Platform, then go to APIs & Services ->
Credentials -> OAuth 2.0 Client IDs -> "Download OAuth Client" icon -> Download JSON
(see https://stackoverflow.com/a/58468671/911350)
4. Rename downloaded file to `vacations-scheduler-credentials.json`, put it into the project's root
5. Set the ID of the given Google Calendar: Google Calendar -> Settings -> "Calendar ID" field
6. Set the ID of the given Google Sheet: get long ID and URL of the vacations' sheet

### Environment variables setup

You must set 2 environment variables:
- `VACATIONS_CALENDAR_ID` - Google Calendar ID of the #5 in the list above
- `VACATIONS_SHEET_ID` - Google Sheet ID of the #6 in the list above

You can set `SENTRY_DSN` to enable Sentry errors handling. You can get Sentry DSN from the Sentry's projects settings.

Environment variables may be set for the system/script, or they can be put into a `.env` file in the project's root.

### Run

The script can be started with `composer run-app` or with `php start.php` in project's root.

#### Dry run (no calendar changes)

The `dry` parameter can be added (`composer run-app dry`). In this mode the script will collect and schedule
all the changes, but no changes will be made to the Google Calendar events.

### Run as Docker image

You can build Docker image from the application, e.g. `docker image build --tag vacations-scheduler .`.

Then you can run the application with `docker run vacations-scheduler`.

## Application's characteristics

Patterns:
- the application is written with Domain-Driven Design methodology, using the "clean code" principle
- to get to the "clean code", the Dependency Injection pattern is used

Structure:
- the application is split by the modules as sub folders in the `./app` folder
- each of the module consists of the domain (application's logic) and the infrastructure (external libraries and dependencies) parts
- domain components do not use infrastructure components
- the application's entry point is `start.php`
- Dependency injection mapping is set in the declarative style in `./di-definitions.php`

Tests:
- tests are in the `./tests` folder
- all the domain code and the part of the infrastructure code are covered by the unit tests

---

# Vacations scheduler

Синхронизация таблицы с отпусками и календаря с отпусками.

Скрипт на основе записей об отпусках в документе Google Sheet создает или удаляет события в календаре Google Calendar.

### Настройка проекта Google

1. Создать проект в Google Cloud Platform
2. Указать API: `Google Calendar API`, `Google Sheets API`
3. Скачать учетные данные для OAuth: с выбранным проектом зайти в Google Cloud Platform, там APIs & Services ->
   Credentials -> OAuth 2.0 Client IDs -> иконка Download OAuth Client -> Download JSON
   (см. https://stackoverflow.com/a/58468671/911350)
4. Переименовать скачанный файл в `vacations-scheduler-credentials.json`, положить его в корень проекта
5. Определить ID гугл календаря: Google Calendar -> Настройки выбранного календаря -> Поле "Идентификатор календаря"
6. Определить ID гугл таблицы с отпусками: взять длинный ID и URL'а таблицы с отпусками

### Установка переменных окружения

Нужно обязательно установить 2 переменные окружения:
- `VACATIONS_CALENDAR_ID` - ID гугл календаря отпусков из п. 5
- `VACATIONS_SHEET_ID` - ID гугл таблицы с отпусками из п. 6

Для работы Sentry можно установить переменную `SENTRY_DSN` - значение нужно взять в Sentry для этого проекта.

Переменные окружения можно задать для скрипта средствами системы, а можно создать `.env` файл, и записать переменные в него

### Запуск

Запускать через `composer run-app` или из папки с проектом `php start.php`

#### Запуск без изменений

Можно запустить программу с параметром `dry` (`composer run-app dry`). В этом режиме программа соберет данные по отпускам,
но не произведет никаких изменений.

### Запуск в виде Docker image

Можно упаковать приложение в Docker image, например вот так `docker image build --tag vacations-scheduler .`.

Тогда можно будет запустить приложение командой `docker run vacations-scheduler`.

## Особенности приложения

Паттерны:
- приложение написано по методологии Domain-Driven Design, используя принцип "чистого кода"
- для достижения этого используется паттерн Dependency Injection

Структура:
- приложение разбито на модули в виде папок внутри `./app`
- каждый из модулей состоит из доменной (бизнес-логика приложения) и инфраструктурной (сторонние библиотеки, внешние зависимости) частей
- доменные компоненты не используют инфраструктурные компоненты
- входная точка: `start.php`
- маппинг для Dependency injection задается декларативно в `./di-definitions.php` 

Тесты:
- тесты находятся в папке `./tests`
- весь доменный код и часть инфраструктурного покрыты unit-тестами
