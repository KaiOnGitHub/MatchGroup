# Matchgroup
Access mails: http://0.0.0.0:49584/

Start docker with xdebug enabled:

```
XDEBUG_MODE=debug docker compose up -d
```

## Deployment

1. Compile assets locally
```
php bin/console asset-map:compile
```

2. Push code to git
```
git commit && push
```

3. SSH into Digital Ocean Droplet
```
 ssh matchgroup 
 or
 ssh root@46.101.144.212 
```

4. Pull code from git
```
cd MatchGroup && git pull
```

5. Build and run docker containers (see Depolyment.md for secrets)
```
SERVER_NAME="matchgroup.tech, caddy:80" \
MAILER_DSN="mailgun+api://YOUR_API_KEY:mailgun.matchgroup.tech@default?region=eu" \
HTTP_PORT=80 \
HTTPS_PORT=443 \
HTTP3_PORT=443 \
APP_ENV=prod \
APP_SECRET=YOUR_SECERT \
POSTGRES_PASSWORD=YOUR_SECRET \
MERCURE_JWT_SECRET=YOUR_SECRET \
CADDY_MERCURE_JWT_SECRET=YOUR_SECRET \
MERCURE_PUBLIC_URL=https://matchgroup.tech/.well-known/mercure \
docker compose -f docker-compose.yml -f docker-compose.prod.yml up --build -d
```

6. In local env we need to delete the public/assets folder again to get live updates


Ideas:
- We could use [Symfony UX Notify](https://symfony.com/bundles/ux-notify/current/index.html) for Browser Notifications

<hr>


# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework, with full [HTTP/2](https://symfony.com/doc/current/weblink.html), HTTP/3 and HTTPS support.

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose up` (the logs will be displayed in the current shell)
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Features

* Production, development and CI ready
* [Installation of extra Docker Compose services](docs/extra-services.md) with Symfony Flex
* Automatic HTTPS (in dev and in prod!)
* HTTP/2, HTTP/3 and [Preload](https://symfony.com/doc/current/web_link.html) support
* Built-in [Mercure](https://symfony.com/doc/current/mercure.html) hub
* [Vulcain](https://vulcain.rocks) support
* Native [XDebug](docs/xdebug.md) integration
* Just 2 services (PHP FPM and Caddy server)
* Super-readable configuration

**Enjoy!**

## Docs

1. [Build options](docs/build.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using a Makefile](docs/makefile.md)
8. [Troubleshooting](docs/troubleshooting.md)

## License

Symfony Docker is available under the MIT License.

## Credits

Created by [Kévin Dunglas](https://dunglas.fr), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
