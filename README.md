
# ENV Setup
DB_PORT=3306

DB_DATABASE=laravel_db

DB_USERNAME=shaikat

DB_PASSWORD='shaikat'


DOCKER_APP_PORT=3445

DOCKER_APP_SSL_PORT=3446

DOCKER_DB_PORT=3447

DOCKER_PHPMYADMIN_PORT=2448

DOCKER_REDIS_PORT=3449

PAYPAL_SANDBOX_CLIENT_ID=AcdwWr5pI2rsG9WOytNbQVG6LLYwFz73L7Ofnd7bdpzvJMjUOKkGco757sLK3YrJJkeKaOvoCA7KoeHl


PAYPAL_SANDBOX_SECRET=EB_-zPD1AxYfh2mQI3H_ts_2weAT8_3Xu_qM1T_Fmkx9vjKD6KovOaB_FbXZLCp3lO6nhKSmoFRDOr8o


PAYPAL_MODE=sandbox

# Docker Command
go to project directory example: cd/your directory/this project

Inside Project Directory open command line

docker compose build

docker compose up -d

docker exec -it --user root laravel_backend /bin/bash

# Inside Bash Project setup

composer install

php artisan key:generate

php artisan migrate:fresh --seed

# Test
php artisan test
# API Documentation

http://localhost:3445/docs/api




﻿

POST
http://localhost:3445/api/login
http://localhost:3445/api/login
﻿

Body
form-data
email
test@gmail.com
password
12345678
POST
http://localhost:3445/api/register
http://localhost:3445/api/register
﻿

Query Params
Body
form-data
name
tomal
email
test@gmail.com
password
12345678
password_confirmation
12345678
GET
http://localhost:3445/api/products
http://localhost:3445/api/products
﻿

Authorization
Bearer Token
Token
<token>
POST
http://localhost:3445/api/products
http://localhost:3445/api/products
﻿

Authorization
Bearer Token
Token
<token>
Body
form-data
name
testxcdssddds
description
description
price
120
stock
200
GET
http://localhost:3445/api/products/2
http://localhost:3445/api/products/2
﻿

PUT
http://localhost:3445/api/products/1
http://localhost:3445/api/products/1
﻿

Request Headers
Body
raw (json)
json
{
"name":"sddasaaf",
"description":"new update",
"price":100,
"stock":20
}
DELETE
http://localhost:3445/api/products/1
http://localhost:3445/api/products/2
﻿

Authorization
Bearer Token
Token
<token>
GET
http://localhost:3445/api/orders
http://localhost:3445/api/orders
﻿

Authorization
Bearer Token
Token
<token>
POST
http://localhost:3445/api/orders
http://localhost:3445/api/orders
﻿

Authorization
Bearer Token
Token
<token>
Body
raw (json)
View More
json
{
"products": [
{
"product_id": 6,
"quantity": 2
},
{
"product_id": 7,
"quantity": 3
}
]
}
POST
http://localhost:3445/api/payment
http://localhost:3445/api/payment
﻿

Body
raw (json)
json
{
"amount": "50.00",
"description": "Laravel Book"
}
GET
http://localhost:3445/api/success?paymentID=PAYID-M5J3QBQ5UX26784JP5476034&PayerID=XYZ123
http://localhost:3445/api/success?paymentID=PAYID-M5J3QBQ5UX26784JP5476034&PayerID=XYZ123
﻿

Query Params
paymentID
PAYID-M5J3QBQ5UX26784JP5476034
PayerID
XYZ123
