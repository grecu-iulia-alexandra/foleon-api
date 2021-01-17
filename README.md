## PROJECT OVERVIEW
- Symfony application that retrieves and creates books and authors;
- API has 4 endpoints:
    - GET `/book/{id}` : endpoint to retrieve specified book
    - GET `/author/{id}` : endpoint to retrieve specified author
    - POST `/book` : endpoint to create book
    - POST `/author` : endpoint to create author
- The application validates the request to ensure the integrity of the data
- When creating book we can use existing authors or create new ones if proper data is provided
- A caching system was implemented in order to improve the speed of the response
- Functional tests were added for all the existing endpoints. They can be run with the command : `bin/phpunit`

## PROJECT SETUP
- Prerequisites
    - PHP 7.4
    - MySql
To start the application use the following steps:
1. Run command: `git clone git@github.com:grecu-iulia-alexandra/foleon-api.git`
2. Update `.env`, `.env.test` files, specifically the `DATABASE_URL` variables
3. Run `composer install`
4. Create dev and test databases:
- `bin/console doctrine:database:create`
- `bin/console doctrine:database:create --env=test`
5. Add relevant tables and data:
- `bin/console doctrine:migrations:migrate`
- `bin/console doctrine:migrations:migrate --env=test`
- `bin/console doctrine:fixtures:load`
6. Start server with `symfony server:start`

## API REQUEST EXAMPLES
1. POST AUTHOR
`https://localhost:8000/author`
```
{
    "firstName" : "John",
    "lastName" : "Smith"
}
```
2. POST BOOK
`https://localhost:8000/book`
```
{
    "title" : "Test title",
    "publishingYear": 1990,
    "authors" : [
        {
            "firstName" : "Popescu",
            "lastName" : "Ion"
        },
        {
            "firstName" : "Popescu",
            "lastName" : "Maria"
        },
        209
    ]
}
```
3. All GET endpoints use the ids from the database as parameters