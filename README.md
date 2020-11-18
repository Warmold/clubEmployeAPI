# clubEmployeAPI

---- DOC API ---- 

Composer
=> composer install 

Server
=> php -S localhost:8000-tpublic/

Fixture
=> bin/console hautelook:fixture:load

JWT
$ mkdir -p config/jwt
$ openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
$ openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem -pubout
JWT_PASSPHRASE=clubEmployes
