# Installation

Place the dashboard.sql dump inside /mariadb folder.

Rune:
```sh
docker-compose up -d
```

### Login 

Login functionality is defined inside `App\Security\ReactLoginAuthenticator` class. The configuration for the login is in `app\config\packages\security.yaml` in firewalls.login section.

### Authentication

The API authentication is defined inside `App\Security\ReactTokenAuthenticator`. The configuration is in `app\config\packages\security.yaml` in `firewalls.api` section.

#### Event Listeners

Every time a token is successfully authenticated the class `App\Modules\Users\Listeners\Auth\JwtAuthenticatedListener` is called. The listener gets the instance of the authenticated listener, fetches the user permissions (via `app\src\Modules\Users\Services\Authorization.php` service) and sets the user permission inside the User::$roles property so they can later be used by the symfony's voters.


### Authorization

Authorization Service is in `app\src\Modules\Users\Services\Authorization.php`. 
Basic authorization rules can be used via `IsGranted`. Complex authorization rules resides in `app\src\Modules\Users\Rules`.
The logged in user permissions are initially fetched during the execution of `JwtAuthenticatedListener` and currently stored inside the Authorization service.


### Responses
The following library is used for returning complex json responses: https://fractal.thephpleague.com/, which also allow us to reuse response structures accross the app.

#### Pagination
The following classes are responsible for pagination: 
`app\src\Library\Resources\Pagination\PaginationData.php` 
`app\src\Library\Resources\Pagination\ResourcePaginator.php`
`app\src\Library\Resources\Serializers\ArraySerializer.php`


### Routing

All available routes are defined in `app\config\routes.yaml`. Currently there is no routes defined via attributes.

