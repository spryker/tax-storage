include:
  - .install
  - .instances
{% if 'solr' in grains.roles %}
  - .solr
{% endif %}
{% if 'cronjobs' in grains.roles %}
  - .jenkins
{% endif %}