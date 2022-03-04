#### What?
- create, update column and table with mysql:
- input: `{"table":"abc","id":"abc", "column1":"abc"}`
- output:
  - create column, table if not exist and insert data to table or collumn
  - insert data if not exist in table, else update data if exist in table
#### How to run
run: `php artisan serve`
#### Test api

`Curl -X POST -H "Content-Type: application/json"
-d '{"table":"abc","id":"abc", "column1":"abc"}'
http://127.0.0.1:8000/api/newapi`
