# Ostia

This is going to be a self-hosted music streaming service.  For now, it's a huge
work in progress, code changes rapidly and commit history isn't the prettiest.
Much of the code was written in one night, so it isn't pretty either.  But hey,
it already plays songs!

Of course, I'm not responsible for any illegal use.  Use common sense.

[Screenshots](https://github.com/logarytm/ostia/tree/master/docs).

## Architecture

This is what we have now. Again, we're moving fast and breaking things, so
expect change.

The server part is a Symfony 5 application using Twig templates.  React is used
to add dynamic behavior to the prerendered pages.  For example, the track list
is first rendered server-side in
[tracks.html.twig](https://github.com/logarytm/ostia/blob/master/templates/library/tracks.html.twig)
and then rerendered again when JS is ready, via the
[TrackListView](https://github.com/logarytm/ostia/blob/master/assets/tracks/TrackListView.tsx)
component, using the track list provided by the server (no extra requests
needed). This creates some code duplication but drastically improves both real
and perceived performance.  Progressive Enhancement FTW!

In the future, we may still need something along the lines of
[pjax](https://github.com/defunkt/jquery-pjax) so that music keeps playing while
the user navigates to a different page.

## How to run it

### Using Docker
Using Docker is recommended way to run the local development environment. Run
`docker-compose up -d` from the main directory to set up the containers.

By default, Ostia exposes the webserver on port `8000` and MySQL on `3309`. Please
make sure that these are not taken on your machine. Otherwise, you will have to
use `docker-compose.override.yml` to change the ports.

Once the containers are running, please create following `.env.local` file to
match the configuration:

```
DATABASE_URL=mysql://root:secret@db:3306/ostia?serverVersion=5.7
REGISTRATION_ENABLED=true
```

Then simply enter the containers (`docker-compose exec app bash`) and run following
commands:

```bash
composer install
npm install
npm run build
php bin/console doctrine:migrations:migrate
```

You can now visit http://127.0.0.1:8000 and create your user account.

### Using your own host and PHP's built-in dev server

> **Note:** When using PHP's built-in server you will be unable to rewind the songs
> due to the fact that it's missing support for some HTTP headers. If you wish to
> use that functionallity locally consider following Docker instructions above or
> setting up production-grade server (like nginx or Apache) on your host machine.

You need to have `ffmpeg` and `ffprobe` installed in your PATH.

First, create an `.env.local` file and set a proper `DATABASE_URL` (template is
in `.env`).

```sh
composer install
npm install
npm run build
php bin/console doctrine:migrations:migrate
bin/server # or php -S 127.0.0.1:8000 -t public/ on Windows
# Go to http://localhost:8000/register to create an account
```

`bin/server` is a wrapper for PHP's builin webserver.  It's not mandatory to use
it — it's just for convenience.  You can use `npm run dev-server` instead of
`npm run build` if you wish to work on the frontend.
