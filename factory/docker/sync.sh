#!/usr/bin/env bash

docker cp . boxtal_connect_woocommerce:/home/docker
docker exec -u root boxtal_connect_woocommerce chown -R docker:docker /home/docker
docker exec -u root boxtal_connect_woocommerce chmod -R +x /home/docker/factory/common/test
docker exec -u root boxtal_connect_woocommerce bash /home/docker/factory/common/sync.sh /home/docker
docker exec -u root boxtal_connect_woocommerce bash /home/docker/factory/common/sync-properties.sh /home/docker
