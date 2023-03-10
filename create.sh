# create a new package template
# no-copy-to-pkg

. "$(dirname "$0")/docker/scripts/boot_host.sh"

docker-compose run -T --rm php php /app/bin/create.php "$@"

