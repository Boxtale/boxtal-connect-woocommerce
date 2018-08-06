#!/usr/bin/env bash

docker cp . boxtal_woocommerce:/home/docker
docker exec -u root boxtal_woocommerce chown -R docker:docker /home/docker
docker exec -u root boxtal_woocommerce chmod -R +x /home/docker/factory/common/test
docker exec -u root boxtal_woocommerce bash /home/docker/factory/common/sync.sh /home/docker