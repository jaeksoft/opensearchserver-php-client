## Test suite for OpenSearchServer PHP Client

### Configuring 

Configure URL, login and admin for your instance in `bootstrap.php`.

### Running test

Make sure phpunit is installed and available in `vendor/bin/phpunit`.

```bash
cd vendor/opensearchserver
../bin/phpunit --configuration ./tests/phpunit.xml
```
