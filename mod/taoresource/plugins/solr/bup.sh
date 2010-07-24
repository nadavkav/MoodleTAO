#!/bin/sh
php export.php > /tmp/resource_export.txt
pg_dump  moodleintel -t mdl_taoresource_entry -t mdl_taoresource_metadata --data-only -h localhost -U moodle -W  > /tmp/mdl_resource_tables.sql
